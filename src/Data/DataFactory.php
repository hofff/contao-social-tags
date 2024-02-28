<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use Contao\Model;

interface DataFactory
{
    public function generate(Model $reference, Model|null $currentPage = null): Data;
}
