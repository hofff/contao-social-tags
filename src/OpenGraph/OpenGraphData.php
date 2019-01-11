<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\OpenGraph;

use Countable;
use IteratorAggregate;

interface OpenGraphData extends IteratorAggregate, Countable
{
    public function __toString() : string;

    public function getProtocol() : OpenGraphProtocol;
}
