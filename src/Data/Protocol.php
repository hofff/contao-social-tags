<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use ArrayIterator;
use Traversable;

use function array_splice;
use function count;

class Protocol extends AbstractData
{
    public const NS_OG      = 'http://ogp.me/ns#';
    public const NS_MUSIC   = 'http://ogp.me/ns/music#';
    public const NS_VIDEO   = 'http://ogp.me/ns/video#';
    public const NS_ARTICLE = 'http://ogp.me/ns/article#';
    public const NS_BOOK    = 'http://ogp.me/ns/book#';
    public const NS_PROFILE = 'http://ogp.me/ns/profile#';
    public const NS_WEBSITE = 'http://ogp.me/ns/website#';

    /** @var Property[] */
    protected array $properties = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function getMetaTags(): string
    {
        $return = '';
        foreach ($this as $property) {
            $return .= $property->getMetaTag() . "\n";
        }

        return $return;
    }

    public function add(Property $property): void
    {
        $this->properties[] = $property;
    }

    public function append(Data $data): void
    {
        /** @psalm-var Property $property */
        foreach ($data as $property) {
            $this->add(clone $property);
        }
    }

    public function get(int $index): Property|null
    {
        return $this->properties[$index] ?? null;
    }

    public function remove(int $index): void
    {
        array_splice($this->properties, $index, 1);
    }

    public function getProtocol(): Protocol
    {
        return $this;
    }

    /** {@inheritDoc} */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->properties);
    }

    public function count(): int
    {
        return count($this->properties);
    }
}
