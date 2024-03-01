<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Util;

use function is_string;

final class TypeUtil
{
    /** @psalm-assert-if-true string $value */
    public static function isStringWithContent(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        return $value !== '';
    }

    public static function stringOrNull(mixed $value): string|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }
}
