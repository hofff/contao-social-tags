<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\File;
use Contao\PageModel;
use Doctrine\DBAL\Connection;
use FilesModel;
use Hofff\Contao\SocialTags\Util\TypeUtil;
use PDO;
use Symfony\Component\HttpFoundation\RequestStack;
use function array_slice;
use function explode;
use function implode;
use function is_file;
use function str_replace;
use function strip_tags;
use function trim;

final class OpenGraphFactory
{
    /** @var Connection */
    private $connection;

    /** @var ContaoFrameworkInterface */
    private $framework;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(Connection $connection, RequestStack $requestStack, ContaoFrameworkInterface $framework)
    {
        $this->connection   = $connection;
        $this->framework    = $framework;
        $this->requestStack = $requestStack;
    }

    public function generateBasicDataByPageID(int $pageId) : OpenGraphBasicData
    {
        $basicData   = new OpenGraphBasicData();
        $currentPage = $this->getOriginPage($pageId);

        if (! $currentPage) {
            return $basicData;
        }

        $referencePage = $currentPage;
        $modes         = ['hofff_st_tree'];
        $pageTrail     = $referencePage->trail;

        switch ($referencePage->hofff_st) {
            case 'hofff_st_disablePage':
            case 'hofff_st_disableTree':
                return $basicData;
                break;

            case 'hofff_st_page':
            case 'hofff_st_tree':
                // data in current page available
                break;

            case 'hofff_st_root':
                $pageTrail = array_slice($pageTrail, 0, 1);
            // No break
            case 'hofff_st_parent':
                $modes[] = 'hofff_st_page';
                unset($referencePage);
                break;

            default:
                $modes[] = 'hofff_st_disableTree';
                unset($referencePage);
                break;
        }

        if (! $referencePage) {
            $referencePage = $this->loadReferencePage($pageTrail, $modes);
        }

        if (! $referencePage || $referencePage->hofff_st === 'hofff_st_disableTree') {
            return $basicData;
        }

        $basicData
            ->setTitle($this->getTitle($referencePage, $currentPage))
            ->setType($this->getOpenGraphType($referencePage))
            ->setImageData($this->generateImageData($referencePage->hofff_st_image))
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

    private function getOriginPage(int $pageId) : ?PageModel
    {
        if ($pageId === $GLOBALS['objPage']->id) {
            return $GLOBALS['objPage'];
        }

        return $this->framework->getAdapter(PageModel::class)->findWithDetails($pageId);
    }

    /**
     * @param string[] $pageTrail
     * @param string[] $modes
     */
    private function loadReferencePage(array $pageTrail, array $modes) : ?PageModel
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $trailSet     = implode(',', $pageTrail);
        $statement    = $queryBuilder
            ->select('p.id')
            ->from('tl_page', 'p')
            ->andWhere('p.id IN (:trail)')
            ->andWhere('p.hofff_st IN (:modes)')
            ->andWhere('(p.hofff_st != \'hofff_st_disableTree\' OR FIND_IN_SET(p.id, :trailSet) > (
                            SELECT COALESCE(MAX(FIND_IN_SET(p2.id, :trailSet)), -1)
                            FROM   tl_page AS p2
                            WHERE  p2.id IN (:trail)
                            AND	   p2.hofff_st = \'hofff_st_parent\'
                        ))')
            ->orderBy('FIND_IN_SET(p.id, :trailSet)')
            ->setParameter('trail', $pageTrail, Connection::PARAM_STR_ARRAY)
            ->setParameter('trailSet', $trailSet)
            ->setParameter('modes', $modes, Connection::PARAM_STR_ARRAY)
            ->setMaxResults(1)
            ->execute();

        $pageId = $statement->fetch(PDO::FETCH_COLUMN);

        return $this->framework->getAdapter(PageModel::class)->findByPK($pageId);
    }

    private function getTitle(PageModel $referencePage, PageModel $currentPage) : string
    {
        $title = $referencePage->hofff_st_title;
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
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_url)) {
            return $this->replaceInsertTags($referencePage->hofff_st_url);
        }

        if ($currentPage->id === $GLOBALS['objPage']->id) {
            return $this->getBaseUrl() . $this->getRequestUri();
        }

        return $currentPage->getAbsoluteUrl();
    }

    private function getDescription(?PageModel $referencePage, ?PageModel $currentPage) : ?string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_description)) {
            return $this->replaceInsertTags($referencePage->hofff_st_description);
        }

        $description = $currentPage->description;
        $description = trim(str_replace(["\n", "\r"], [' ', ''], $description));

        return $description ?: null;
    }

    private function getSiteName(?PageModel $referencePage, ?PageModel $currentPage) : string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_site)) {
            return $this->replaceInsertTags($referencePage->hofff_st_site);
        }

        return strip_tags($currentPage->rootTitle);
    }

    private function getOpenGraphType(?PageModel $referencePage) : OpenGraphType
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_type)) {
            [$namespace, $type] = explode(' ', $referencePage->hofff_st_type, 2);

            if ($type === null) {
                return new OpenGraphType($namespace);
            }

            return new OpenGraphType($type, $namespace);
        }

        return new OpenGraphType('website');
    }
}
