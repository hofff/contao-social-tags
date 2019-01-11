<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\OpenGraph;

abstract class AbstractOpenGraphData implements OpenGraphData
{
    protected function __construct()
    {
    }

    public function __toString() : string
    {
        return $this->getProtocol()->getMetaTags();
    }

    /** @return OpenGraphData[] */
    public function getIterator() : iterable
    {
        return $this->getProtocol()->getIterator();
    }

    public function count() : int
    {
        return $this->getProtocol()->count();
    }
}
