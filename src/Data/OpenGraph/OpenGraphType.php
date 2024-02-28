<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Contao\StringUtil;
use Hofff\Contao\SocialTags\Data\Property;
use Hofff\Contao\SocialTags\Data\Protocol;

use function sprintf;

class OpenGraphType extends Property
{
    private string|null $typePrefix = null;

    public function __construct(
        private string|null $type = null,
        private string|null $typeNamespace = null,
        string|null $typePrefix = null,
    ) {
        parent::__construct();

        parent::setNamespace(Protocol::NS_OG);

        parent::setName('type');

        $this->setTypePrefix($typePrefix);
    }

    public function getMetaTag(): string
    {
        $prefix = $this->getTypeNamespaceDeclaration();
        if ($prefix !== '') {
            $prefix = ' ' . $prefix;
        }

        $prefix = sprintf(
            ' prefix="%s%s"',
            StringUtil::specialchars($this->getNamespaceDeclaration()),
            StringUtil::specialchars($prefix),
        );

        return sprintf(
            '<meta%s property="%s" content="%s" />',
            $prefix,
            StringUtil::specialchars($this->getPrefixedName()),
            StringUtil::specialchars((string) $this->getContent()),
        );
    }

    public function setType(string|null $type): self
    {
        $this->type = $type;

        return $this;
    }

    protected function getTypeNamespaceDeclaration(): string
    {
        return $this->typeNamespace !== null
            ? sprintf('%s: %s', (string) $this->typePrefix, $this->typeNamespace)
            : '';
    }

    public function setTypeNamespace(string|null $typeNamespace): self
    {
        $this->typeNamespace = $typeNamespace;

        return $this;
    }

    public function setTypePrefix(string|null $typePrefix): self
    {
        $this->typePrefix = $typePrefix ?? 't';

        return $this;
    }

    /** @return $this */
    public function setNamespace(string|null $namespace): Property
    {
        return $this;
    }

    /** @return $this */
    public function setName(string|null $name): Property
    {
        return $this;
    }

    /** @return $this */
    public function setContent(string|null $content): Property
    {
        $this->setType($content);

        return $this;
    }

    protected function getContent(): string|null
    {
        return $this->typeNamespace !== null
            ? sprintf('%s:%s', (string) $this->typePrefix, (string) $this->type)
            : $this->type;
    }
}
