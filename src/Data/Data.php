<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use Countable;
use IteratorAggregate;

/** @extends IteratorAggregate<Property> */
interface Data extends IteratorAggregate, Countable
{
    public function __toString(): string;

    public function getProtocol(): Protocol;
}
