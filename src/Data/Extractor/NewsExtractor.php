<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\File;
use Contao\FilesModel;
use Contao\Model;
use Contao\News;
use Contao\NewsModel;
use Contao\PageModel;
use Contao\StringUtil;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphImageData;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphType;
use Hofff\Contao\SocialTags\Util\TypeUtil;

use function array_pad;
use function explode;
use function is_file;
use function method_exists;
use function str_replace;
use function strip_tags;
use function stripos;
use function trim;
use function ucfirst;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class NewsExtractor extends AbstractExtractor
{
    public function supports(Model $reference, Model|null $fallback = null): bool
    {
        if (! $reference instanceof NewsModel) {
            return false;
        }

        return $fallback instanceof PageModel;
    }

    public function extract(string $type, string $field, Model $reference, Model|null $fallback = null): mixed
    {
        $methodName = 'extract' . ucfirst($type) . ucfirst($field);

        if ($methodName !== __FUNCTION__ && method_exists($this, $methodName)) {
            return $this->$methodName($reference, $fallback);
        }

        return null;
    }

    private function extractTwitterTitle(NewsModel $newsModel): string|null
    {
        if ($newsModel->hofff_st && TypeUtil::isStringWithContent($newsModel->hofff_st_twitter_title)) {
            return $this->replaceInsertTags($newsModel->hofff_st_twitter_title);
        }

        return $this->getNewsTitle($newsModel);
    }

    private function extractTwitterSite(NewsModel $newsModel, PageModel $referencePage): string|null
    {
        if ($newsModel->hofff_st && $newsModel->hofff_st_twitter_site) {
            return $newsModel->hofff_st_twitter_site;
        }

        return $referencePage->hofff_st_twitter_site ?: null;
    }

    private function extractTwitterDescription(NewsModel $newsModel): string|null
    {
        if ($newsModel->hofff_st && TypeUtil::isStringWithContent($newsModel->hofff_st_twitter_description)) {
            return $this->replaceInsertTags($newsModel->hofff_st_twitter_description);
        }

        return $this->getNewsDescription($newsModel) ?? null;
    }

    private function extractTwitterImage(NewsModel $newsModel, PageModel $referencePage): string|null
    {
        $file = $this->getImage('hofff_st_twitter_image', $newsModel, $referencePage);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            return $this->getBaseUrl() . $file->path;
        }

        return null;
    }

    private function extractTwitterCreator(NewsModel $newsModel, PageModel $referencePage): string|null
    {
        if ($newsModel->hofff_st && $newsModel->hofff_st_twitter_creator) {
            return $newsModel->hofff_st_twitter_creator;
        }

        return $referencePage->hofff_st_twitter_creator ?: null;
    }

    /** @param string|resource $strImage */
    private function extractOpenGraphImageData(NewsModel $newsModel, PageModel $referencePage): OpenGraphImageData
    {
        $imageData = new OpenGraphImageData();
        $file      = $this->getImage('hofff_st_og_image', $newsModel, $referencePage);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            $objImage = new File($file->path);
            $imageData->setURL($this->getBaseUrl() . $file->path);
            $imageData->setMIMEType($objImage->mime);
            $imageData->setWidth($objImage->width);
            $imageData->setHeight($objImage->height);
        }

        return $imageData;
    }

    private function extractOpenGraphTitle(NewsModel $newsModel): string|null
    {
        if ($newsModel->hofff_st && TypeUtil::isStringWithContent($newsModel->hofff_st_og_title)) {
            return $this->replaceInsertTags($newsModel->hofff_st_og_title);
        }

        return $this->getNewsTitle($newsModel) ?? null;
    }

    private function extractOpenGraphUrl(NewsModel $newsModel): string
    {
        if ($newsModel->hofff_st && TypeUtil::isStringWithContent($newsModel->hofff_st_og_url)) {
            return $this->replaceInsertTags($newsModel->hofff_st_og_url);
        }

        $newsUrl = News::generateNewsUrl($newsModel, false, true);

        // Prepend scheme and host if URL is not absolute
        if (stripos($newsUrl, 'http') !== 0) {
            $newsUrl = $this->getBaseUrl() . $newsUrl;
        }

        return $newsUrl;
    }

    private function extractOpenGraphDescription(NewsModel $newsModel): string|null
    {
        if ($newsModel->hofff_st && TypeUtil::isStringWithContent($newsModel->hofff_st_og_description)) {
            return $this->replaceInsertTags($newsModel->hofff_st_og_description);
        }

        return $this->getNewsDescription($newsModel) ?? null;
    }

    private function extractOpenGraphSiteName(NewsModel $newsModel, PageModel $fallback): string
    {
        if ($newsModel->hofff_st && TypeUtil::isStringWithContent($newsModel->hofff_st_og_site)) {
            return $this->replaceInsertTags($newsModel->hofff_st_og_site);
        }

        return strip_tags($fallback->rootTitle);
    }

    private function extractOpenGraphType(NewsModel $newsModel): OpenGraphType
    {
        if ($newsModel->hofff_st && TypeUtil::isStringWithContent($newsModel->hofff_st_og_type)) {
            [$namespace, $type] = array_pad(explode(' ', $newsModel->hofff_st_og_type, 2), 2, null);

            if ($type === null) {
                return new OpenGraphType($namespace);
            }

            return new OpenGraphType($type, $namespace);
        }

        return new OpenGraphType('article');
    }

    /**
     * Returns the meta description if present, otherwise the shortened teaser.
     */
    private function getNewsDescription(NewsModel $model): string|null
    {
        if (TypeUtil::isStringWithContent($model->description)) {
            return $this->replaceInsertTags(trim(str_replace(["\n", "\r"], [' ', ''], $model->description)));
        }

        if (! TypeUtil::isStringWithContent($model->teaser)) {
            return null;
        }

        // Generate the description from the teaser the same way as the news reader does
        $description = $this->replaceInsertTags($model->teaser);
        $description = strip_tags($description);
        $description = str_replace("\n", ' ', $description);

        return StringUtil::substr($description, 320);
    }

    /** Returns the meta title if present, otherwise the headline. */
    private function getNewsTitle(NewsModel $model): string|null
    {
        $title = $model->pageTitle ?: $model->headline;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return null;
    }

    /**
     * Retrieves an image from the news for a given key.
     *
     * It fallbacks to the news image or page image if not defined.
     */
    private function getImage(string $key, NewsModel $newsModel, PageModel $referencePage): FilesModel|null
    {
        if ($newsModel->hofff_st && $newsModel->{$key}) {
            $image = $newsModel->{$key};
        } elseif ($newsModel->addImage && $newsModel->singleSRC) {
            $image = $newsModel->singleSRC;
        } elseif ($referencePage->{$key}) {
            $image = $referencePage->{$key};
        } else {
            return null;
        }

        return $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($image);
    }
}
