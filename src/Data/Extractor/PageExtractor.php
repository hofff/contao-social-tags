<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
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
use function str_replace;
use function strip_tags;
use function substr;
use function trim;
use function ucfirst;

final class PageExtractor implements Extractor
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
        if (! $reference instanceof PageModel) {
            return false;
        }

        if ($fallback instanceof PageModel) {
            return true;
        }

        return false;
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

    private function extractTwitterTitle(PageModel $referencePage, PageModel $currentPage) : ?string
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

    private function extractTwitterSite(PageModel $referencePage) : ?string
    {
        return $referencePage->hofff_st_twitter_site ?: null;
    }

    private function extractTwitterDescription(PageModel $referencePage, PageModel $currentPage) : ?string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_twitter_description)) {
            return $this->replaceInsertTags($referencePage->hofff_st_twitter_description);
        }

        $description = $currentPage->description;
        $description = trim(str_replace(["\n", "\r"], [' ', ''], $description));

        return $description ?: null;
    }

    private function extractTwitterImage(PageModel $referencePage) : ?string
    {
        $file = $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($referencePage->hofff_st_twitter_image);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            return $this->getBaseUrl() . $file->path;
        }

        return null;
    }

    private function extractTwitterCreator(PageModel $referencePage) : ?string
    {
        return $referencePage->hofff_st_twitter_creator ?: null;
    }

    /**
     * @param string|resource $strImage
     */
    private function extractOpenGraphImageData(PageModel $referencePage) : OpenGraphImageData
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

    private function extractOpenGraphTitle(PageModel $referencePage, PageModel $currentPage) : string
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

    private function extractOpenGraphUrl(PageModel $referencePage, PageModel $currentPage) : string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_url)) {
            return $this->replaceInsertTags($referencePage->hofff_st_og_url);
        }

        if ($currentPage->id === $GLOBALS['objPage']->id) {
            return $this->getBaseUrl() . substr($this->getRequestUri(), 1);
        }

        return $currentPage->getAbsoluteUrl();
    }

    private function extractOpenGraphDescription(PageModel $referencePage, PageModel $currentPage) : ?string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_description)) {
            return $this->replaceInsertTags($referencePage->hofff_st_og_description);
        }

        $description = $currentPage->description;
        $description = trim(str_replace(["\n", "\r"], [' ', ''], $description));

        return $description ?: null;
    }

    private function extractOpenGraphSiteName(PageModel $referencePage, PageModel $currentPage) : string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_site)) {
            return $this->replaceInsertTags($referencePage->hofff_st_og_site);
        }

        return strip_tags($currentPage->rootTitle);
    }

    private function extractOpenGraphOpenGraphType(PageModel $referencePage) : OpenGraphType
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_type)) {
            [$namespace, $type] = explode(' ', $referencePage->hofff_st_og_type, 2);

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
            return '/';
        }

        return $request->getRequestUri();
    }
}
