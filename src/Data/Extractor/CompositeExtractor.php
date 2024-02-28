<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\Model;
use Hofff\Contao\SocialTags\Data\Extractor;

final class CompositeExtractor implements Extractor
{
    /** @var Extractor[] */
    private $extractors;

    /** @param Extractor[] $extractors */
    public function __construct(iterable $extractors)
    {
        $this->extractors = $extractors;
    }

    public function supports(Model $reference, Model|null $fallback = null): bool
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($reference, $fallback)) {
                return true;
            }
        }

        return false;
    }

    public function extract(string $type, string $field, Model $reference, Model|null $fallback = null): mixed
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($reference, $fallback)) {
                return $extractor->extract($type, $field, $reference, $fallback);
            }
        }

        return null;
    }
}
