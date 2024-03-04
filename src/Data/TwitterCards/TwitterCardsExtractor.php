<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\TwitterCards;

use Hofff\Contao\SocialTags\Data\Extractor;

/**
 * @template TReference of object
 * @template TFallback of object
 */
interface TwitterCardsExtractor extends Extractor
{
    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractTwitterTitle(object $reference, object|null $fallback = null): string;

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractTwitterSite(object $reference, object|null $fallback = null): string|null;

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractTwitterDescription(object $reference, object|null $fallback = null): string|null;

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractTwitterImage(object $reference, object|null $fallback = null): string|null;

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractTwitterCreator(object $reference, object|null $fallback = null): string|null;
}
