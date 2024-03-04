<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use Traversable;

abstract class AbstractData implements Data
{
    protected function __construct()
    {
    }

    public function __toString(): string
    {
        return $this->getProtocol()->getMetaTags();
    }

    /** @return Traversable<Property> */
    public function getIterator(): Traversable
    {
        return $this->getProtocol()->getIterator();
    }

    public function count(): int
    {
        return $this->getProtocol()->count();
    }
}
