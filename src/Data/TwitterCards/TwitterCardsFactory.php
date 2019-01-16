<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\TwitterCards;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\FilesModel;
use Contao\PageModel;
use Hofff\Contao\SocialTags\Data\Data;
use Hofff\Contao\SocialTags\Data\DataFactory;
use Hofff\Contao\SocialTags\Data\Protocol;
use Hofff\Contao\SocialTags\Util\TypeUtil;
use function is_file;
use function str_replace;
use function strip_tags;
use function trim;

final class TwitterCardsFactory implements DataFactory
{
    /** @var ContaoFrameworkInterface */
    private $framework;

    /** @var string */
    private $projectDir;

    public function __construct(ContaoFrameworkInterface $framework, string $projectDir)
    {
        $this->framework  = $framework;
        $this->projectDir = $projectDir;
    }

    public function generateForPage(PageModel $referencePage, PageModel $currentPage) : Data
    {
        switch ($referencePage->hofff_st_twitter_type) {
            case 'hofff_st_twitter_summary':
                return new SummaryCardData(
                    $this->getTitle($referencePage, $currentPage),
                    $this->getSite($referencePage),
                    $this->getDescription($referencePage, $currentPage),
                    $this->getImage($referencePage)
                );

            case 'hofff_st_twitter_summary_large_image':
                return new SummaryWithLargeImageCardData(
                    $this->getTitle($referencePage, $currentPage),
                    $this->getSite($referencePage),
                    $this->getDescription($referencePage, $currentPage),
                    $this->getImage($referencePage),
                    $this->getCreator($referencePage)
                );

            default:
                return new Protocol();
        }
    }

    private function getTitle(PageModel $referencePage, PageModel $currentPage) : ?string
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

    private function getSite(PageModel $referencePage) : ?string
    {
        return $referencePage->hofff_st_twitter_site ?: null;
    }

    private function getDescription(PageModel $referencePage, PageModel $currentPage) : ?string
    {
        if (TypeUtil::isStringWithContent($referencePage->hofff_st_twitter_description)) {
            return $this->replaceInsertTags($referencePage->hofff_st_twitter_description);
        }

        $description = $currentPage->description;
        $description = trim(str_replace(["\n", "\r"], [' ', ''], $description));

        return $description ?: null;
    }

    private function getImage(PageModel $referencePage) : ?string
    {
        $file = $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($referencePage->hofff_st_twitter_image);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            return $file->path;
        }

        return null;
    }

    private function getCreator(PageModel $referencePage) : ?string
    {
        return $referencePage->hofff_st_twitter_creator ?: null;
    }

    private function replaceInsertTags(string $content) : string
    {
        $controller = $this->framework->getAdapter(Controller::class);

        $content = $controller->__call('replaceInsertTags', [$content, false]);
        $content = $controller->__call('replaceInsertTags', [$content, true]);

        return $content;
    }
}
