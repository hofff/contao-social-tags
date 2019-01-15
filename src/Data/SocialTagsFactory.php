<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\PageModel;
use Doctrine\DBAL\Connection;
use PDO;

final class SocialTagsFactory
{
    /** @var Connection */
    private $connection;

    /** @var ContaoFrameworkInterface */
    private $framework;

    /** @var DataFactory[] */
    private $dataFactories;

    /**
     * @param DataFactory[] $dataFactories
     */
    public function __construct(Connection $connection, ContaoFrameworkInterface $framework, iterable $dataFactories)
    {
        $this->connection    = $connection;
        $this->framework     = $framework;
        $this->dataFactories = $dataFactories;
    }

    public function generateByPageId(int $pageId): Protocol
    {
        $protocol    = new Protocol();
        $currentPage = $this->getOriginPage($pageId);

        if (! $currentPage) {
            return $protocol;
        }

        $referencePage = $currentPage;
        $modes         = ['hofff_st_tree'];
        $pageTrail     = $referencePage->trail;

        switch ($referencePage->hofff_st) {
            case 'hofff_st_disablePage':
            case 'hofff_st_disableTree':
                return $protocol;
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
            return $protocol;
        }

        foreach ($this->dataFactories as $factory) {
            $protocol->append($factory->generateForPage($referencePage, $currentPage));
        }

        return $protocol;
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
}
