<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\File;
use Contao\FilesModel;
use Contao\PageModel;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphExtractor;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphImageData;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphType;
use Hofff\Contao\SocialTags\Data\TwitterCards\TwitterCardsExtractor;
use Hofff\Contao\SocialTags\Util\TypeUtil;

use function array_pad;
use function explode;
use function is_file;
use function str_replace;
use function strip_tags;
use function substr;
use function trim;

/**
 * @implements OpenGraphExtractor<PageModel, PageModel>
 * @implements TwitterCardsExtractor<PageModel, PageModel>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
final class PageExtractor extends AbstractExtractor implements OpenGraphExtractor, TwitterCardsExtractor
{
    public function supports(object $reference, object|null $fallback = null): bool
    {
        if (! $reference instanceof PageModel) {
            return false;
        }

        return $fallback instanceof PageModel;
    }

    public function extractTwitterTitle(object $reference, object|null $fallback = null): string
    {
        $title = $reference->hofff_st_twitter_title;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        if (! $fallback) {
            return '';
        }

        $title = $fallback->pageTitle;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return strip_tags($fallback->title);
    }

    public function extractTwitterSite(object $reference, object|null $fallback = null): string|null
    {
        return $reference->hofff_st_twitter_site ?: null;
    }

    public function extractTwitterDescription(object $reference, object|null $fallback = null): string|null
    {
        if (TypeUtil::isStringWithContent($reference->hofff_st_twitter_description)) {
            return $this->replaceInsertTags($reference->hofff_st_twitter_description);
        }

        $description = $fallback->description ?? '';
        $description = trim(str_replace(["\n", "\r"], [' ', ''], $description));

        return $description ?: null;
    }

    public function extractTwitterImage(object $reference, object|null $fallback = null): string|null
    {
        $file = $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($reference->hofff_st_twitter_image);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            return $this->getBaseUrl() . $file->path;
        }

        return null;
    }

    public function extractTwitterCreator(object $reference, object|null $fallback = null): string|null
    {
        return $reference->hofff_st_twitter_creator ?: null;
    }

    public function extractOpenGraphImageData(object $reference, object|null $fallback = null): OpenGraphImageData
    {
        $imageData = new OpenGraphImageData();

        $file = FilesModel::findByUuid($reference->hofff_st_og_image);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            $objImage = new File($file->path);
            $imageData->setURL($this->getBaseUrl() . $file->path);
            $imageData->setMIMEType($objImage->mime);
            $imageData->setWidth($objImage->width);
            $imageData->setHeight($objImage->height);
        }

        return $imageData;
    }

    public function extractOpenGraphTitle(object $reference, object|null $fallback = null): string
    {
        $title = $reference->hofff_st_og_title;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        if (! $fallback) {
            return '';
        }

        $title = $fallback->pageTitle;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return strip_tags($fallback->title);
    }

    /** @SuppressWarnings(PHPMD.Superglobals) */
    public function extractOpenGraphUrl(object $reference, object|null $fallback = null): string
    {
        if (TypeUtil::isStringWithContent($reference->hofff_st_og_url)) {
            return $this->replaceInsertTags($reference->hofff_st_og_url);
        }

        if ($reference->id === $GLOBALS['objPage']->id) {
            $canonical = $this->getCanonicalUrlForRequest();

            if ($reference->enableCanonical && $canonical !== null) {
                return $canonical;
            }

            return $this->getBaseUrl() . substr($this->getRequestUri(), 1);
        }

        return $reference->getAbsoluteUrl();
    }

    public function extractOpenGraphDescription(object $reference, object|null $fallback = null): string|null
    {
        if (TypeUtil::isStringWithContent($reference->hofff_st_og_description)) {
            return $this->replaceInsertTags($reference->hofff_st_og_description);
        }

        $description = $fallback->description ?? '';
        $description = trim(str_replace(["\n", "\r"], [' ', ''], $description));

        return $description ?: null;
    }

    public function extractOpenGraphSiteName(object $reference, object|null $fallback = null): string
    {
        if (TypeUtil::isStringWithContent($reference->hofff_st_og_site)) {
            return $this->replaceInsertTags($reference->hofff_st_og_site);
        }

        return strip_tags((string) $fallback?->rootTitle);
    }

    public function extractOpenGraphType(object $reference, object|null $fallback = null): OpenGraphType
    {
        if (TypeUtil::isStringWithContent($reference->hofff_st_og_type)) {
            [$namespace, $type] = array_pad(explode(' ', $reference->hofff_st_og_type, 2), 2, null);

            if ($type === null) {
                return new OpenGraphType($namespace);
            }

            return new OpenGraphType($type, $namespace);
        }

        return new OpenGraphType('website');
    }
}
