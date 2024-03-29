<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use function array_unique;
use function array_values;

final class ExtractorResolver
{
    /** @param iterable<Extractor> $extractors */
    public function __construct(private readonly iterable $extractors)
    {
    }

    /**
     * @param class-string<T> $expectedExtractor
     *
     * @return T|null
     *
     * @template T of Extractor
     */
    public function resolve(string $expectedExtractor, object $reference, object|null $fallback): Extractor|null
    {
        foreach ($this->extractors as $extractor) {
            if (! $extractor instanceof $expectedExtractor) {
                continue;
            }

            if (! $extractor->supports($reference, $fallback)) {
                continue;
            }

            return $extractor;
        }

        return null;
    }

    /** @return list<string> */
    public function supportedDataContainers(): array
    {
        static $dataContainers = null;

        if ($dataContainers !== null) {
            return $dataContainers;
        }

        $dataContainers = [];

        foreach ($this->extractors as $extractor) {
            foreach ($extractor->supportedDataContainers() as $dataContainer) {
                $dataContainers[] = $dataContainer;
            }
        }

        return array_values(array_unique($dataContainers));
    }
}
