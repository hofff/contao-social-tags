<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Contao\Model;
use Hofff\Contao\SocialTags\Data\Data;
use Hofff\Contao\SocialTags\Data\DataFactory;
use Hofff\Contao\SocialTags\Data\Extractor;

final class OpenGraphFactory implements DataFactory
{
    public function __construct(private readonly Extractor $extractor)
    {
    }

    public function generate(Model $reference, Model|null $fallback = null): Data
    {
        $basicData = new OpenGraphBasicData();

        if (! $this->extractor->supports($reference, $fallback)) {
            return $basicData;
        }

        $basicData
            ->setTitle($this->extractor->extract('openGraph', 'title', $reference, $fallback))
            ->setType($this->extractor->extract('openGraph', 'type', $reference, $fallback))
            ->setImageData($this->extractor->extract('openGraph', 'imageData', $reference, $fallback))
            ->setURL($this->extractor->extract('openGraph', 'url', $reference, $fallback))
            ->setDescription($this->extractor->extract('openGraph', 'description', $reference, $fallback))
            ->setSiteName($this->extractor->extract('openGraph', 'siteName', $reference, $fallback));

        return $basicData;
    }
}
