<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\FaqModel;
use Contao\File;
use Contao\FilesModel;
use Contao\Model;
use Contao\PageModel;
use Hofff\Contao\SocialTags\Data\Extractor;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphImageData;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphType;
use Hofff\Contao\SocialTags\Util\TypeUtil;
use Symfony\Component\HttpFoundation\RequestStack;
use function explode;
use function is_file;
use function method_exists;
use function strip_tags;
use function ucfirst;

final class FaqExtractor extends AbstractExtractor
{
    public function supports(Model $reference, ?Model $fallback = null) : bool
    {
        if (! $reference instanceof FaqModel) {
            return false;
        }

        if (! $fallback instanceof PageModel) {
            return false;
        }

        return true;
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

    private function extractTwitterTitle(FaqModel $faqModel) : ?string
    {
        if ($faqModel->hofff_st && TypeUtil::isStringWithContent($faqModel->hofff_st_twitter_title)) {
            return $this->replaceInsertTags($faqModel->hofff_st_twitter_title);
        }

        $title = $faqModel->question;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return null;
    }

    private function extractTwitterSite(FaqModel $faqModel, PageModel $referencePage) : ?string
    {
        if ($faqModel->hofff_st && $faqModel->hofff_st_twitter_site) {
            return $faqModel->hofff_st_twitter_site;
        }

        return $faqModel->hofff_st_twitter_site ?: null;
    }

    private function extractTwitterDescription(FaqModel $faqModel) : ?string
    {
        if ($faqModel->hofff_st && TypeUtil::isStringWithContent($faqModel->hofff_st_twitter_description)) {
            return $this->replaceInsertTags($faqModel->hofff_st_twitter_description);
        }

        return null;
    }

    private function extractTwitterImage(FaqModel $faqModel) : ?string
    {
        if (!$faqModel->hofff_st) {
            return null;
        }

        $file = $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($faqModel->hofff_st_twitter_image);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            return $this->getBaseUrl() . $file->path;
        }

        return null;
    }

    private function extractTwitterCreator(FaqModel $faqModel, PageModel $referencePage) : ?string
    {
        if ($faqModel->hofff_st && $faqModel->hofff_st_twitter_creator) {
            return $faqModel->hofff_st_twitter_creator;
        }

        return $referencePage->hofff_st_twitter_creator ?: null;
    }

    /**
     * @param string|resource $strImage
     */
    private function extractOpenGraphImageData(FaqModel $faqModel) : OpenGraphImageData
    {
        $imageData = new OpenGraphImageData();
        if (!$faqModel->hofff_st) {
            return $imageData;
        }

        $file = FilesModel::findByUuid($faqModel->hofff_st_og_image);

        if ($file && is_file(TL_ROOT . '/' . $file->path)) {
            $objImage = new File($file->path);
            $imageData->setURL($this->getBaseUrl() . $file->path);
            $imageData->setMIMEType($objImage->mime);
            $imageData->setWidth($objImage->width);
            $imageData->setHeight($objImage->height);
        }

        return $imageData;
    }

    private function extractOpenGraphTitle(FaqModel $faqModel) : ?string
    {
        if ($faqModel->hofff_st && TypeUtil::isStringWithContent($faqModel->hofff_st_og_title)) {
            return $this->replaceInsertTags($faqModel->hofff_st_og_title);
        }

        $title = $faqModel->question;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return '';
    }

    private function extractOpenGraphUrl(FaqModel $faqModel) : ?string
    {
        if ($faqModel->hofff_st && TypeUtil::isStringWithContent($faqModel->hofff_st_og_url)) {
            return $this->replaceInsertTags($faqModel->hofff_st_og_url);
        }

        return self::generateFaqUrl($faqModel, true);
    }

    private function extractOpenGraphDescription(FaqModel $faqModel) : ?string
    {
        if ($faqModel->hofff_st && TypeUtil::isStringWithContent($faqModel->hofff_st_og_description)) {
            return $this->replaceInsertTags($faqModel->hofff_st_og_description);
        }

        return null;
    }

    private function extractOpenGraphSiteName(FaqModel $faqModel, PageModel $fallback) : string
    {
        if ($faqModel->hofff_st && TypeUtil::isStringWithContent($faqModel->hofff_st_og_site)) {
            return $this->replaceInsertTags($faqModel->hofff_st_og_site);
        }

        return strip_tags($fallback->rootTitle);
    }

    private function extractOpenGraphType(FaqModel $faqModel) : OpenGraphType
    {
        if ($faqModel->hofff_st && TypeUtil::isStringWithContent($faqModel->hofff_st_og_type)) {
            [$namespace, $type] = explode(' ', $faqModel->hofff_st_og_type, 2);

            if ($type === null) {
                return new OpenGraphType($namespace);
            }

            return new OpenGraphType($type, $namespace);
        }

        return new OpenGraphType('website');
    }

    private static function generateFaqUrl(FaqModel $faqModel, bool $absolute = false) : ?string
    {
        /** @var FaqCategoryModel $faqCategory */
        $faqCategory = $faqModel->getRelated('pid');
        $jumpTo      = (int) $faqCategory->jumpTo;

        if ($jumpTo < 1) {
            return null;
        }

        $target = PageModel::findByPk($jumpTo);

        if ($target === null) {
            return null;
        }

        $params = (Config::get('useAutoItem') ? '/' : '/items/') . ($faqModel->alias ?: $faqModel->id);

        return ampersand($absolute ? $target->getAbsoluteUrl($params) : $target->getFrontendUrl($params));
    }

    /**
     * Retrieves an image from the news for a given key. It fallbacks to the news image or page image if not defined.
     */
    private function getImage(string $key, NewsModel $newsModel, PageModel $referencePage): ?FilesModel
    {
        $image = null;
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
