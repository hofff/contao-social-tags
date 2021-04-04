<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\File;
use Contao\FilesModel;
use Contao\Model;
use Contao\PageModel;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphImageData;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphType;
use Hofff\Contao\SocialTags\Util\TypeUtil;

use function explode;
use function is_file;
use function method_exists;
use function str_replace;
use function strip_tags;
use function substr;
use function trim;
use function ucfirst;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
final class PageExtractor extends AbstractExtractor
{
    public function supports(Model $reference, ?Model $fallback = null): bool
    {
        if (! $reference instanceof PageModel) {
            return false;
        }

        return $fallback instanceof PageModel;
    }

    /** @return mixed */
    public function extract(string $type, string $field, Model $reference, ?Model $fallback = null)
    {
        $methodName = 'extract' . ucfirst($type) . ucfirst($field);

        if ($methodName !== __FUNCTION__ && method_exists($this, $methodName)) {
            return $this->$methodName($reference, $fallback);
        }

        return null;
    }

    private function extractTwitterTitle(PageModel $referencePage, PageModel $currentPage): ?string
    {
        $title = $referencePage->hofff_st_twitter_title;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        $title = $currentPage->pageTitle;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return strip_tags($currentPage->title);
    }

    private function extractTwitterSite(PageModel $referencePage): ?string
    {
        return $referencePage->hofff_st_twitter_site ?: null;
    }

    private function extractTwitterDescription(PageModel $referencePage, PageModel $currentPage): ?string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_twitter_description)) {
            return $this->replaceInsertTags($referencePage->hofff_st_twitter_description);
        }

        $description = $currentPage->description ?? '';
        $description = trim(str_replace(["\n", "\r"], [' ', ''], $description));

        return $description ?: null;
    }

    private function extractTwitterImage(PageModel $referencePage): ?string
    {
        $file = $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($referencePage->hofff_st_twitter_image);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            return $this->getBaseUrl() . $file->path;
        }

        return null;
    }

    private function extractTwitterCreator(PageModel $referencePage): ?string
    {
        return $referencePage->hofff_st_twitter_creator ?: null;
    }

    /**
     * @param string|resource $strImage
     */
    private function extractOpenGraphImageData(PageModel $referencePage): OpenGraphImageData
    {
        $imageData = new OpenGraphImageData();

        $file = FilesModel::findByUuid($referencePage->hofff_st_og_image);

        if ($file && is_file(TL_ROOT . '/' . $file->path)) {
            $objImage = new File($file->path);
            $imageData->setURL($this->getBaseUrl() . $file->path);
            $imageData->setMIMEType($objImage->mime);
            $imageData->setWidth($objImage->width);
            $imageData->setHeight($objImage->height);
        }

        return $imageData;
    }

    private function extractOpenGraphTitle(PageModel $referencePage, PageModel $currentPage): string
    {
        $title = $referencePage->hofff_st_og_title;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        $title = $currentPage->pageTitle;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return strip_tags($currentPage->title);
    }

    /** @SuppressWarnings(PHPMD.Superglobals) */
    private function extractOpenGraphUrl(PageModel $referencePage, PageModel $currentPage): string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_url)) {
            return $this->replaceInsertTags($referencePage->hofff_st_og_url);
        }

        if ($currentPage->id === $GLOBALS['objPage']->id) {
            return $this->getBaseUrl() . substr($this->getRequestUri(), 1);
        }

        return $currentPage->getAbsoluteUrl();
    }

    private function extractOpenGraphDescription(PageModel $referencePage, PageModel $currentPage): ?string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_description)) {
            return $this->replaceInsertTags($referencePage->hofff_st_og_description);
        }

        $description = $currentPage->description ?? '';
        $description = trim(str_replace(["\n", "\r"], [' ', ''], $description));

        return $description ?: null;
    }

    private function extractOpenGraphSiteName(PageModel $referencePage, PageModel $currentPage): string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_site)) {
            return $this->replaceInsertTags($referencePage->hofff_st_og_site);
        }

        return strip_tags($currentPage->rootTitle);
    }

    private function extractOpenGraphType(PageModel $referencePage): OpenGraphType
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_type)) {
            [$namespace, $type] = array_pad(explode(' ', $referencePage->hofff_st_og_type, 2), 2, null);

            if ($type === null) {
                return new OpenGraphType($namespace);
            }

            return new OpenGraphType($type, $namespace);
        }

        return new OpenGraphType('website');
    }
}
