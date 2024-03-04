<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

interface DataFactory
{
    public function generate(object $reference, object|null $fallback = null): Data;
}
