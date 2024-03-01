<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

interface Extractor
{
    public function supports(object $reference, object|null $fallback = null): bool;
}
