<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data;

use Contao\StringUtil;

use function sprintf;
use function strip_tags;

class Property
{
    private string|null $prefix = null;

    public function __construct(
        private string|null $namespace = null,
        private string|null $name = null,
        private string|null $content = null,
        string|null $prefix = null,
    ) {
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

        if ($this->namespace !== null && $this->prefix !== null) {
            return sprintf(
                '<meta%s property="%s" content="%s">',
                sprintf(' prefix="%s"', StringUtil::specialchars($this->getNamespaceDeclaration())),
                StringUtil::specialchars($this->getPrefixedName()),
                StringUtil::specialchars(strip_tags((string) $this->content)),
            );
        }

        return sprintf(
            '<meta property="%s" content="%s">',
            StringUtil::specialchars($this->getPrefixedName()),
            StringUtil::specialchars(strip_tags((string) $this->content)),
        );
    }

    protected function getNamespaceDeclaration(): string
    {
        return sprintf('%s: %s', (string) $this->prefix, (string) $this->namespace);
    }

    protected function getPrefixedName(): string
    {
        return sprintf('%s:%s', (string) $this->prefix, (string) $this->name);
    }

    public function isValid(): bool
    {
        return $this->name !== null && $this->content !== null;
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
        $this->prefix = $prefix ?? 'og';

        return $this;
    }
}
