<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use Contao\Model;

interface Extractor
{
    public function supports(Model $reference, Model|null $fallback = null): bool;

    /** @return mixed */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
    public function extract(string $type, string $field, Model $reference, Model|null $fallback = null);
}
