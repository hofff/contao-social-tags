<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Util;

use function is_string;

final class TypeUtil
{
    /** @param mixed $value */
    public static function isStringWithContent($value) : bool
    {
        if (! is_string($value)) {
            return false;
        }

        return $value !== '';
    }
}
