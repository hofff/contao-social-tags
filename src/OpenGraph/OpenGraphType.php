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

    protected function getTypeNamespaceDeclaration() : string
    {
        return $this->typeNamespace
            ? sprintf('%s: %s', $this->typePrefix, $this->typeNamespace)
            : '';
    }

    public function setTypeNamespace(?string $typeNamespace) : self
    {
        $this->typeNamespace = $typeNamespace;

        return $this;
    }

    public function setTypePrefix(?string $typePrefix) : self
    {
        $this->typePrefix = $typePrefix ?: 't';

        return $this;
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

    protected function getContent() : ?string
    {
        return $this->typeNamespace
            ? sprintf('%s:%s', $this->typePrefix, $this->type)
            : $this->type;
    }
}
