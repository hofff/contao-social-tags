<?php

namespace Hofff\Contao\SocialTags\OpenGraph;

use Countable;
use IteratorAggregate;

interface OpenGraphData extends IteratorAggregate, Countable
{

    public function __toString();

    public function getProtocol();

}
