<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\OpenGraph;

use function sprintf;

class OpenGraphProperty
{
    /** @var string|null */
    private $namespace;

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $content;

    /** @var string|null */
    private $prefix;

    public function __construct(?string $namespace = null, ?string $name = null, ?string $content = null, ?string $prefix = null)
    {
        $this->setNamespace($namespace);
        $this->setName($name);
        $this->setContent($content);
        $this->setPrefix($prefix);
    }

    public function __toString() : string
    {
        return $this->getMetaTag();
    }

    public function getMetaTag() : string
    {
        return $this->isValid() ? sprintf(
            '<meta%s property="%s" content="%s" />',
            sprintf(' prefix="%s"', specialchars($this->getNamespaceDeclaration())),
            specialchars($this->getPrefixedName()),
            specialchars($this->getContent())
        ) : '';
    }

    public function getNamespaceDeclaration() : string
    {
        return sprintf('%s: %s', $this->getPrefix(), $this->getNamespace());
    }

    public function getPrefixedName() : string
    {
        return sprintf('%s:%s', $this->getPrefix(), $this->getName());
    }

    public function isValid() : bool
    {
        return $this->hasNamespace() && $this->hasName() && $this->hasContent();
    }

    public function setNamespace(?string $namespace) : self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function hasNamespace() : bool
    {
        return isset($this->namespace);
    }

    public function getNamespace() : ?string
    {
        return $this->namespace;
    }

    public function setName(?string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function hasName() : bool
    {
        return isset($this->name);
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setContent(?string $content) : self
    {
        $this->content = $content;

        return $this;
    }

    public function hasContent() : bool
    {
        return isset($this->content);
    }

    public function getContent() : ?string
    {
        return $this->content;
    }

    public function setPrefix(?string $prefix) : self
    {
        $this->prefix = $prefix ?: 'og';

        return $this;
    }

    public function getPrefix() : ?string
    {
        return $this->prefix;
    }
}
