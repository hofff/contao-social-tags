<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\TwitterCards;

use Contao\PageModel;
use Hofff\Contao\SocialTags\Data\Data;
use Hofff\Contao\SocialTags\Data\DataFactory;
use Hofff\Contao\SocialTags\Data\Protocol;

final class TwitterCardsFactory implements DataFactory
{
    public function generateForPage(PageModel $referencePage, PageModel $currentPage) : Data
    {
        $protocol = new Protocol();

        return $protocol;
    }
}
