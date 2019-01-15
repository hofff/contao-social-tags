<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\File;
use Contao\FilesModel;
use Contao\PageModel;
use Hofff\Contao\SocialTags\Data\Data;
use Hofff\Contao\SocialTags\Data\DataFactory;
use Hofff\Contao\SocialTags\Util\TypeUtil;
use Symfony\Component\HttpFoundation\RequestStack;
use function explode;
use function is_file;
use function str_replace;
use function strip_tags;
use function trim;

final class OpenGraphFactory implements DataFactory
{
    /** @var ContaoFrameworkInterface */
    private $framework;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack, ContaoFrameworkInterface $framework)
    {
        $this->framework    = $framework;
        $this->requestStack = $requestStack;
    }

    public function generateForPage(PageModel $referencePage, PageModel $currentPage) : Data
    {
        $basicData = new OpenGraphBasicData();
        $basicData
            ->setTitle($this->getTitle($referencePage, $currentPage))
            ->setType($this->getOpenGraphType($referencePage))
            ->setImageData($this->generateImageData($referencePage->hofff_st_og_image))
            ->setURL($this->getUrl($referencePage, $currentPage))
            ->setDescription($this->getDescription($referencePage, $currentPage))
            ->setSiteName($this->getSiteName($referencePage, $currentPage));

        return $basicData;
    }

    /**
     * @param string|resource $strImage
     */
    public function generateImageData($strImage) : OpenGraphImageData
    {
        $imageData = new OpenGraphImageData();

        $file              = FilesModel::findByUuid($strImage);
        $file && $strImage = $file->path;

        if (is_file(TL_ROOT . '/' . $strImage)) {
            $objImage = new File($strImage);
            $imageData->setURL($this->getBaseUrl() . $strImage);
            $imageData->setMIMEType($objImage->mime);
            $imageData->setWidth($objImage->width);
            $imageData->setHeight($objImage->height);
        }

        return $imageData;
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

    private function getTitle(PageModel $referencePage, PageModel $currentPage) : string
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

    private function getUrl(PageModel $referencePage, PageModel $currentPage) : string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_url)) {
            return $this->replaceInsertTags($referencePage->hofff_st_og_url);
        }

        if ($currentPage->id === $GLOBALS['objPage']->id) {
            return $this->getBaseUrl() . $this->getRequestUri();
        }

        return $currentPage->getAbsoluteUrl();
    }

    private function getDescription(?PageModel $referencePage, ?PageModel $currentPage) : ?string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_description)) {
            return $this->replaceInsertTags($referencePage->hofff_st_og_description);
        }

        $description = $currentPage->description;
        $description = trim(str_replace(["\n", "\r"], [' ', ''], $description));

        return $description ?: null;
    }

    private function getSiteName(?PageModel $referencePage, ?PageModel $currentPage) : string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_og_site)) {
            return $this->replaceInsertTags($referencePage->hofff_st_og_site);
        }

        return strip_tags($currentPage->rootTitle);
    }

    private function getOpenGraphType(?PageModel $referencePage) : OpenGraphType
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
}
