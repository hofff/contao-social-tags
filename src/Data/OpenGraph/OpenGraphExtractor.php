<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Hofff\Contao\SocialTags\Data\Extractor;

/**
 * @template TReference of object
 * @template TFallback of object
 */
interface OpenGraphExtractor extends Extractor
{
    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphImageData(object $reference, object|null $fallback = null): OpenGraphImageData;

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphTitle(object $reference, object|null $fallback = null): string;

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphUrl(object $reference, object|null $fallback = null): string|null;

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphDescription(object $reference, object|null $fallback = null): string|null;

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphSiteName(object $reference, object|null $fallback = null): string;

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphType(object $reference, object|null $fallback = null): OpenGraphType;
}
