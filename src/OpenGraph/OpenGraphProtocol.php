<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\OpenGraph;

use ArrayIterator;
use function array_splice;
use function count;

class OpenGraphProtocol extends AbstractOpenGraphData
{
    public const NS_OG      = 'http://ogp.me/ns#';
    public const NS_MUSIC   = 'http://ogp.me/ns/music#';
    public const NS_VIDEO   = 'http://ogp.me/ns/video#';
    public const NS_ARTICLE = 'http://ogp.me/ns/article#';
    public const NS_BOOK    = 'http://ogp.me/ns/book#';
    public const NS_PROFILE = 'http://ogp.me/ns/profile#';
    public const NS_WEBSITE = 'http://ogp.me/ns/website#';

    /** @var OpenGraphData[] */
    protected $properties;

    public function __construct()
    {
        parent::__construct();
        $this->clear();
    }

    public function getMetaTags() : string
    {
        $return = '';
        foreach ($this as $property) {
            $return .= $property->getMetaTag() . "\n";
        }

        return $return;
    }

    public function add(OpenGraphProperty $property) : void
    {
        $this->properties[] = $property;
    }

    public function append(OpenGraphData $data) : void
    {
        foreach ($data as $property) {
            $this->add(clone $property);
        }
    }

    public function get(int $index) : ?OpenGraphData
    {
        return $this->properties[$index] ?? null;
    }

    public function remove(int $index) : void
    {
        array_splice($this->properties, $index, 1);
    }

    public function clear() : void
    {
        $this->properties = [];
    }

    public function getProtocol() : OpenGraphProtocol
    {
        return $this;
    }

    /** @return OpenGraphData[] */
    public function getIterator() : iterable
    {
        return new ArrayIterator($this->properties);
    }

    public function count() : int
    {
        return count($this->properties);
    }
}
