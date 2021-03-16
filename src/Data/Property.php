<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use function sprintf;

class Property
{
    /** @var string|null */
    private $namespace;

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $content;

    /** @var string|null */
    private $prefix;

    public function __construct(
        ?string $namespace = null,
        ?string $name = null,
        ?string $content = null,
        ?string $prefix = null
    ) {
        $this->setNamespace($namespace);
        $this->setName($name);
        $this->setContent($content);
        $this->setPrefix($prefix);
    }

    public function __toString(): string
    {
        return $this->getMetaTag();
    }

    public function getMetaTag(): string
    {
        if (! $this->isValid()) {
            return '';
        }

        if ($this->namespace && $this->prefix) {
            return sprintf(
                '<meta%s property="%s" content="%s">',
                sprintf(' prefix="%s"', specialchars($this->getNamespaceDeclaration())),
                specialchars($this->getPrefixedName()),
                specialchars($this->content)
            );
        }

        return sprintf(
            '<meta property="%s" content="%s">',
            specialchars($this->getPrefixedName()),
            specialchars($this->content)
        );
    }

    protected function getNamespaceDeclaration(): string
    {
        return sprintf('%s: %s', $this->prefix, $this->namespace);
    }

    protected function getPrefixedName(): string
    {
        return sprintf('%s:%s', $this->prefix, $this->name);
    }

    public function isValid(): bool
    {
        return $this->name && $this->content;
    }

    public function setNamespace(?string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix ?: 'og';

        return $this;
    }
}
