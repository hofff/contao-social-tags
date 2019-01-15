<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use Contao\PageModel;

interface DataFactory
{
    public function generateForPage(PageModel $referencePage, PageModel $currentPage): Data;
}
