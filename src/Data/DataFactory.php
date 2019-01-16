<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use Contao\Model;

interface DataFactory
{
    public function generate(Model $referencePage, ?Model $currentPage = null) : Data;
}
