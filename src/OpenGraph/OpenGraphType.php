<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\OpenGraph;

use function sprintf;

class OpenGraphType extends OpenGraphProperty
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

        parent::setNamespace(OpenGraphProtocol::NS_OG);
        parent::setName('type');

        $this->setType($type);
        $this->setTypeNamespace($typeNamespace);
        $this->setTypePrefix($typePrefix);
    }

    public function getMetaTag() : string
    {
        $prefix            = $this->getTypeNamespaceDeclaration();
        $prefix && $prefix = ' ' . $prefix;
        $prefix            = sprintf(
            ' prefix="%s%s"',
            specialchars($this->getNamespaceDeclaration()),
            specialchars($prefix)
        );

        return sprintf(
            '<meta%s property="%s" content="%s" />',
            $prefix,
            specialchars($this->getPrefixedName()),
            specialchars($this->getContent())
        );
    }

    public function setType(?string $type) : self
    {
        $this->type = $type;

        return $this;
    }

    public function hasType() : bool
    {
        return isset($this->type);
    }

    public function getType() : ?string
    {
        return $this->type;
    }

    public function getTypeNamespaceDeclaration() : string
    {
        return $this->hasTypeNamespace() ? sprintf(
            '%s: %s',
            $this->getTypePrefix(),
            $this->getTypeNamespace()
        ) : '';
    }

    public function setTypeNamespace(?string $typeNamespace) : self
    {
        $this->typeNamespace = $typeNamespace;

        return $this;
    }

    public function hasTypeNamespace() : bool
    {
        return isset($this->typeNamespace);
    }

    public function getTypeNamespace() : ?string
    {
        return $this->typeNamespace;
    }

    public function setTypePrefix(?string $typePrefix) : self
    {
        $this->typePrefix = $typePrefix ?: 't';

        return $this;
    }

    public function getTypePrefix() : ?string
    {
        return $this->typePrefix;
    }

    /** @return $this */
    public function setNamespace(?string $namespace) : OpenGraphProperty
    {
        return $this;
    }

    /** @return $this */
    public function setName(?string $name) : OpenGraphProperty
    {
        return $this;
    }

    /** @return $this */
    public function setContent(?string $content) : OpenGraphProperty
    {
        $this->setType($content);

        return $this;
    }

    public function hasContent() : bool
    {
        return $this->hasType();
    }

    public function getContent() : ?string
    {
        return $this->hasTypeNamespace()
            ? sprintf('%s:%s', $this->getTypePrefix(), $this->getType())
            : $this->getType();
    }
}
