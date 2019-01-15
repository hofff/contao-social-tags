<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use Countable;
use IteratorAggregate;

interface Data extends IteratorAggregate, Countable
{
    public function __toString() : string;

    public function getProtocol() : Protocol;
}
