<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\File;
use Contao\FilesModel;
use Contao\Model;
use Contao\News;
use Contao\NewsModel;
use Contao\PageModel;
use Contao\StringUtil;
use function explode;
use function is_file;
use function method_exists;
use function str_replace;
use function strip_tags;
use function trim;
use function ucfirst;
use Hofff\Contao\SocialTags\Data\Extractor;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphImageData;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphType;
use Hofff\Contao\SocialTags\Util\TypeUtil;
use Symfony\Component\HttpFoundation\RequestStack;

final class NewsExtractor implements Extractor
{
    /** @var ContaoFrameworkInterface */
    private $framework;

    /** @var RequestStack */
    private $requestStack;

    /** @var string */
    private $projectDir;

    public function __construct(ContaoFrameworkInterface $framework, RequestStack $requestStack, string $projectDir)
    {
        $this->framework    = $framework;
        $this->projectDir   = $projectDir;
        $this->requestStack = $requestStack;
    }

    public function supports(Model $reference, ?Model $fallback = null) : bool
    {
        if (! $reference instanceof NewsModel) {
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

    private function extractTwitterTitle(NewsModel $newsModel) : ?string
    {
        $title = $newsModel->hofff_st_twitter_title;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return $this->getNewsTitle($newsModel) ?: null;
    }

    private function extractTwitterSite(NewsModel $newsModel) : ?string
    {
        return $newsModel->hofff_st_twitter_site ?: null;
    }

    private function extractTwitterDescription(NewsModel $newsModel) : ?string
    {
        if (TypeUtil::isStringWithContent($newsModel->hofff_st_twitter_description)) {
            return $this->replaceInsertTags($newsModel->hofff_st_twitter_description);
        }

        return $this->getNewsDescription($newsModel) ?: null;
    }

    private function extractTwitterImage(NewsModel $newsModel) : ?string
    {
        $file = $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($newsModel->hofff_st_twitter_image);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            return $this->getBaseUrl() . $file->path;
        }

        return null;
    }

    private function extractTwitterCreator(NewsModel $newsModel) : ?string
    {
        return $newsModel->hofff_st_twitter_creator ?: null;
    }

    /**
     * @param string|resource $strImage
     */
    private function extractOpenGraphImageData(NewsModel $newsModel) : OpenGraphImageData
    {
        $imageData = new OpenGraphImageData();

        $file = FilesModel::findByUuid($newsModel->hofff_st_og_image);

        if ($file && is_file(TL_ROOT . '/' . $file->path)) {
            $objImage = new File($file->path);
            $imageData->setURL($this->getBaseUrl() . $file->path);
            $imageData->setMIMEType($objImage->mime);
            $imageData->setWidth($objImage->width);
            $imageData->setHeight($objImage->height);
        }

        return $imageData;
    }

    private function extractOpenGraphTitle(NewsModel $newsModel) : ?string
    {
        $title = $newsModel->hofff_st_og_title;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return $this->getNewsTitle($newsModel) ?: null;
    }

    private function extractOpenGraphUrl(NewsModel $newsModel) : string
    {
        if (TypeUtil::isStringWithContent($newsModel->hofff_st_og_url)) {
            return $this->replaceInsertTags($newsModel->hofff_st_og_url);
        }

        if ($newsModel->id === $GLOBALS['objPage']->id) {
            return $this->getBaseUrl() . $this->getRequestUri();
        }

        return News::generateNewsUrl($newsModel, false, true);
    }

    private function extractOpenGraphDescription(NewsModel $newsModel) : ?string
    {
        if (TypeUtil::isStringWithContent($newsModel->hofff_st_og_description)) {
            return $this->replaceInsertTags($newsModel->hofff_st_og_description);
        }

        return $this->getNewsDescription($newsModel);
    }

    private function extractOpenGraphSiteName(NewsModel $newsModel, PageModel $fallback) : string
    {
        if (TypeUtil::isStringWithContent($newsModel->hofff_st_og_site)) {
            return $this->replaceInsertTags($newsModel->hofff_st_og_site);
        }

        return strip_tags($fallback->rootTitle);
    }

    private function extractOpenGraphType(NewsModel $newsModel) : OpenGraphType
    {
        if (TypeUtil::isStringWithContent($newsModel->hofff_st_og_type)) {
            [$namespace, $type] = explode(' ', $newsModel->hofff_st_og_type, 2);

            if ($type === null) {
                return new OpenGraphType($namespace);
            }

            return new OpenGraphType($type, $namespace);
        }

        return new OpenGraphType('website');
    }

    private function replaceInsertTags(string $content) : string
    {
        $controller = $this->framework->getAdapter(Controller::class);

        $content = $controller->__call('replaceInsertTags', [$content, false]);
        $content = $controller->__call('replaceInsertTags', [$content, true]);

        return $content;
    }

    private function getBaseUrl() : string
    {
        static $baseUrl;

        if ($baseUrl !== null) {
            return $baseUrl;
        }

        $request = $this->requestStack->getMasterRequest();
        if (! $request) {
            return '';
        }

        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath() . '/';

        return $baseUrl;
    }

    private function getRequestUri() : string
    {
        $request = $this->requestStack->getMasterRequest();
        if (! $request) {
            return '';
        }

        return $request->getRequestUri();
    }

    /**
     * Returns the meta description if present, otherwise the shortened teaser.
     */
    private function getNewsDescription(NewsModel $model): string
    {
        if (!empty($model->description)) {
            return $this->replaceInsertTags(trim(str_replace(["\n", "\r"], [' ', ''], $model->description)));
        }

        // Generate the description from the teaser the same way as the news reader does
        $description = $this->replaceInsertTags($model->teaser, false);
		$description = strip_tags($description);
		$description = str_replace("\n", ' ', $description);
        $description = StringUtil::substr($description, 320);
        
        return $description;
    }

    /**
     * Returns the meta title if present, otherwise the headline.
     */
    private function getNewsTitle(NewsModel $model): string
    {
        return $this->replaceInsertTags($model->pageTitle ?: $model->headline);
    }
}
