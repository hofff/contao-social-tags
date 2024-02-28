<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use Contao\StringUtil;

use function sprintf;
use function strip_tags;

class Property
{
    private string|null $namespace;

    private string|null $name;

    private string|null $content;

    private string|null $prefix;

    public function __construct(
        string|null $namespace = null,
        string|null $name = null,
        string|null $content = null,
        string|null $prefix = null,
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
                sprintf(' prefix="%s"', StringUtil::specialchars($this->getNamespaceDeclaration())),
                StringUtil::specialchars($this->getPrefixedName()),
                StringUtil::specialchars(strip_tags($this->content)),
            );
        }

        return sprintf(
            '<meta property="%s" content="%s">',
            StringUtil::specialchars($this->getPrefixedName()),
            StringUtil::specialchars(strip_tags($this->content)),
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

    public function setNamespace(string|null $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function setName(string|null $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setContent(string|null $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setPrefix(string|null $prefix): self
    {
        $this->prefix = $prefix ?: 'og';

        return $this;
    }
}
