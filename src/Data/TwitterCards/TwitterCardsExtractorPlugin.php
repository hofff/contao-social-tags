<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\TwitterCards;

use Contao\FilesModel;
use Hofff\Contao\SocialTags\Data\Extractor\AbstractExtractor;
use Hofff\Contao\SocialTags\Util\TypeUtil;

/**
 * @template TReference of object
 * @template TFallback of object
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
trait TwitterCardsExtractorPlugin
{
    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractTwitterTitle(object $reference, object|null $fallback = null): string
    {
        $title = $reference->hofff_st && TypeUtil::isStringWithContent($reference->hofff_st_twitter_title)
            ? $reference->hofff_st_twitter_title
            : $this->getContentTitle($reference);

        return $this->replaceInsertTags($title);
    }

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractTwitterSite(object $reference, object|null $fallback = null): string|null
    {
        $site = $reference->hofff_st && $reference->hofff_st_twitter_site
            ?  $reference->hofff_st_twitter_site
            : $fallback?->hofff_st_twitter_site;

        return TypeUtil::stringOrNull($site);
    }

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractTwitterDescription(object $reference, object|null $fallback = null): string|null
    {
        $description = $reference->hofff_st && TypeUtil::isStringWithContent($reference->hofff_st_twitter_description)
            ? $reference->hofff_st_twitter_description
            : $this->getContentDescription($reference);

        if ($description === null) {
            return null;
        }

        return $this->replaceInsertTags($description);
    }

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractTwitterImage(object $reference, object|null $fallback = null): string|null
    {
        return $this->getFileUrl($this->getImage('hofff_st_twitter_image', $reference, $fallback));
    }

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractTwitterCreator(object $reference, object|null $fallback = null): string|null
    {
        if ($reference->hofff_st && $reference->hofff_st_twitter_creator) {
            return $reference->hofff_st_twitter_creator;
        }

        /** @psalm-suppress RiskyTruthyFalsyComparison */
        return $fallback?->hofff_st_twitter_creator ?: null;
    }

    /**
     * Get the content title from the reference.
     *
     * Insert tags does not have to be replaced as it has to be done by the caller of this method.
     *
     * @param TReference $reference
     */
    abstract protected function getContentTitle(object $reference): string;

    /**
     * Get the content description from the reference.
     *
     * Insert tags does not have to be replaced as it has to be done by the caller of this method.
     *
     * @param TReference $reference
     */
    abstract protected function getContentDescription(object $reference): string|null;

    abstract protected function replaceInsertTags(string $value): string;

    abstract protected function getFileUrl(FilesModel|null $file): string|null;

    /** @see AbstractExtractor::getImage() */
    abstract protected function getImage(
        string $key,
        object $reference,
        object|null $fallback = null,
    ): FilesModel|null;
}
