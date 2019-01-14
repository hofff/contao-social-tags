<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\File;
use Doctrine\DBAL\Connection;
use FilesModel;
use Hofff\Contao\SocialTags\OpenGraph\OpenGraphBasicData;
use Hofff\Contao\SocialTags\OpenGraph\OpenGraphImageData;
use Hofff\Contao\SocialTags\OpenGraph\OpenGraphType;
use PDO;
use function array_slice;
use function explode;
use function is_file;
use function str_replace;
use function strip_tags;
use function strlen;
use Symfony\Component\HttpFoundation\RequestStack;
use function trim;

final class ContaoOpenGraphFactory
{
    /** @var Connection */
    private $connection;

    /** @var ContaoFrameworkInterface */
    private $framework;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(Connection $connection, RequestStack $requestStack, ContaoFrameworkInterface $framework)
    {
        $this->connection = $connection;
        $this->framework = $framework;
        $this->requestStack = $requestStack;
    }

    public function generateBasicDataByPageID(int $pageId) : OpenGraphBasicData
    {
        $objOGBD = new OpenGraphBasicData();
        $objPage = $objOrigin = $pageId == $GLOBALS['objPage']->id ? $GLOBALS['objPage'] : $this->getPageDetails($pageId);

        if (! $objPage) {
            return $objOGBD;
        }

        $arrModes = ['hofff_st_tree'];
        $arrTrail = $objPage->trail;

        switch ($objPage->hofff_st) {
            case 'hofff_st_disablePage':
            case 'hofff_st_disableTree':
                return $objOGBD;
                break;

            case 'hofff_st_page':
            case 'hofff_st_tree':
                // data in current page available
                break;

            case 'hofff_st_root':
                $arrTrail = array_slice($arrTrail, 0, 1);
            // No break
            case 'hofff_st_parent':
                $arrModes[] = 'hofff_st_page';
                unset($objPage);
                break;

            default:
                $arrModes[] = 'hofff_st_disableTree';
                unset($objPage);
                break;
        }

        if (! $objPage) {
            $queryBuilder = $this->connection->createQueryBuilder();
            $trailSet     = implode(',', $arrTrail);
            $statement    = $queryBuilder
                ->select('p.*')
                ->from('tl_page', 'p')
                ->andWhere('p.id IN (:trail)')
                ->andWhere('p.hofff_st IN (:modes)')
                ->andWhere('(p.hofff_st != \'hofff_st_disableTree\' OR FIND_IN_SET(p.id, :trailSet) > (
                            SELECT COALESCE(MAX(FIND_IN_SET(p2.id, :trailSet)), -1)
                            FROM   tl_page AS p2
                            WHERE  p2.id IN (:trail)
                            AND	   p2.hofff_st = \'hofff_st_parent\'
                        ))'
                )
                ->orderBy('FIND_IN_SET(p.id, :trailSet)')
                ->setParameter('trail', $arrTrail, Connection::PARAM_STR_ARRAY)
                ->setParameter('trailSet', $trailSet)
                ->setParameter('modes', $arrModes, Connection::PARAM_STR_ARRAY)
                ->setMaxResults(1)
                ->execute();

            \dump($arrModes);
            \dump($arrTrail);
            \dump($statement->rowCount());
            $objPage = $statement->fetch(PDO::FETCH_OBJ);
        }

        \dump($objPage);

        if (! $objPage || $objPage->hofff_st === 'hofff_st_disableTree') {
            return $objOGBD;
        }

        if (strlen($objPage->hofff_st_title)) {
            $strTitle = $this->replaceInsertTags($objPage->hofff_st_title);
        } elseif (strlen($objOrigin->pageTitle)) {
            $strTitle = $objOrigin->pageTitle;
        } else {
            $strTitle = strip_tags($objOrigin->title);
        }
        $objOGBD->setTitle($strTitle);

        if (strlen($objPage->hofff_st_type)) {
            [$strNamespace, $strType] = explode(' ', $objPage->hofff_st_type, 2);
            if ($strType === null) {
                $strType = $strNamespace;
                unset($strNamespace);
            }
        } else {
            $strType = 'website';
        }
        $objOGBD->setType(new OpenGraphType($strType, $strNamespace));

        $objOGBD->setImageData($this->generateImageData($objPage->hofff_st_image));

        if (strlen($objPage->hofff_st_url)) {
            $strURL = $this->replaceInsertTags($objPage->hofff_st_url);
        } elseif ($objOrigin->id === $GLOBALS['objPage']->id) {
            $strURL = $this->getBaseUrl() . $this->getRequestUri();
        } else {
            $strURL = $this->getBaseUrl() . $this->generateFrontendURL($objOrigin->row());
        }
        $objOGBD->setURL($strURL);

        if (strlen($objPage->hofff_st_description)) {
            $strDescription = $this->replaceInsertTags($objPage->hofff_st_description);
        } else {
            $strDescription = $objOrigin->description;
        }
        $strDescription = trim(str_replace(["\n", "\r"], [' ', ''], $strDescription));
        strlen($strDescription) && $objOGBD->setDescription($strDescription);

        if (strlen($objPage->hofff_st_site)) {
            $strSiteName = $this->replaceInsertTags($objPage->hofff_st_site);
        } else {
            $strSiteName = strip_tags($objOrigin->rootTitle);
        }
        strlen($strSiteName) && $objOGBD->setSiteName($strSiteName);

        return $objOGBD;
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

    private function replaceInsertTags(string $content): string
    {
        $controller = $this->framework->getAdapter(Controller::class);

        $content = $controller->__call('replaceInsertTags', [$content, false]);
        $content = $controller->__call('replaceInsertTags', [$content, true]);

        return $content;
    }

    private function getBaseUrl(): string
    {
        static $baseUrl;

        if ($baseUrl !== null) {
            return $baseUrl;
        }

        $request = $this->requestStack->getMasterRequest();
        if (!$request) {
            return '';
        }

        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath() . '/';

        return $baseUrl;
    }

    private function getRequestUri(): string
    {
        $request = $this->requestStack->getMasterRequest();
        if (!$request) {
            return '';
        }

        return $request->getRequestUri();
    }
}
