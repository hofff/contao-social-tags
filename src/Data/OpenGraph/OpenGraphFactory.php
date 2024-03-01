<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Contao\Model;
use Hofff\Contao\SocialTags\Data\Data;
use Hofff\Contao\SocialTags\Data\DataFactory;
use Hofff\Contao\SocialTags\Data\ExtractorResolver;

final class OpenGraphFactory implements DataFactory
{
    public function __construct(private readonly ExtractorResolver $resolver)
    {
    }

    public function generate(Model $reference, Model|null $fallback = null): Data
    {
        $basicData = new OpenGraphBasicData();
        $extractor = $this->resolver->resolve(OpenGraphExtractor::class, $reference, $fallback);

        if (! $extractor instanceof OpenGraphExtractor) {
            return $basicData;
        }

        $basicData
            ->setTitle($extractor->extractOpenGraphTitle($reference, $fallback))
            ->setType($extractor->extractOpenGraphType($reference, $fallback))
            ->setImageData($extractor->extractOpenGraphImageData($reference, $fallback))
            ->setURL($extractor->extractOpenGraphUrl($reference, $fallback))
            ->setDescription($extractor->extractOpenGraphDescription($reference, $fallback))
            ->setSiteName($extractor->extractOpenGraphSiteName($reference, $fallback));

        return $basicData;
    }
}
