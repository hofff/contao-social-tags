<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Contao\StringUtil;
use Hofff\Contao\SocialTags\Data\Property;
use Hofff\Contao\SocialTags\Data\Protocol;

use function sprintf;

class OpenGraphType extends Property
{
    /** @var string|null */
    private $type;

    /** @var string|null */
    private $typeNamespace;

    /** @var string|null */
    private $typePrefix;

    public function __construct(?string $type = null, ?string $typeNamespace = null, ?string $typePrefix = null)
    {
        parent::__construct();

        parent::setNamespace(Protocol::NS_OG);
        parent::setName('type');

        $this->setType($type);
        $this->setTypeNamespace($typeNamespace);
        $this->setTypePrefix($typePrefix);
    }

    public function getMetaTag(): string
    {
        $prefix            = $this->getTypeNamespaceDeclaration();
        $prefix && $prefix = ' ' . $prefix;
        $prefix            = sprintf(
            ' prefix="%s%s"',
            StringUtil::specialchars($this->getNamespaceDeclaration()),
            StringUtil::specialchars($prefix)
        );

        return sprintf(
            '<meta%s property="%s" content="%s" />',
            $prefix,
            StringUtil::specialchars($this->getPrefixedName()),
            StringUtil::specialchars($this->getContent())
        );
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    protected function getTypeNamespaceDeclaration(): string
    {
        return $this->typeNamespace
            ? sprintf('%s: %s', $this->typePrefix, $this->typeNamespace)
            : '';
    }

    public function setTypeNamespace(?string $typeNamespace): self
    {
        $this->typeNamespace = $typeNamespace;

        return $this;
    }

    public function setTypePrefix(?string $typePrefix): self
    {
        $this->typePrefix = $typePrefix ?: 't';

        return $this;
    }

    /** @return $this */
    public function setNamespace(?string $namespace): Property
    {
        return $this;
    }

    /** @return $this */
    public function setName(?string $name): Property
    {
        return $this;
    }

    /** @return $this */
    public function setContent(?string $content): Property
    {
        $this->setType($content);

        return $this;
    }

    protected function getContent(): ?string
    {
        return $this->typeNamespace
            ? sprintf('%s:%s', $this->typePrefix, $this->type)
            : $this->type;
    }
}
