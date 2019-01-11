<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\OpenGraph;

class OpenGraphBasicData extends AbstractOpenGraphData
{
    /** @var string|null */
    protected $site;

    /** @var string|null */
    protected $title;

    /** @var string|null */
    protected $url;

    /** @var OpenGraphImageData|null */
    protected $image;

    /** @var string|null */
    protected $type;

    /** @var string|null */
    protected $description;

    public function __construct(
        ?string $title = null,
        ?OpenGraphType $type = null,
        ?OpenGraphImageData $image = null,
        ?string $url = null
    ) {
        parent::__construct();

        $this->title = $title;
        $this->type  = $type;
        $this->image = $image;
        $this->url   = $url;
    }

    public function setTitle(string $title) : self
    {
        if ($title !== '') {
            $this->title = $title;
        } else {
            $this->title = null;
        }

        return $this;
    }

    public function hasTitle() : bool
    {
        return isset($this->title);
    }

    public function getTitle() : ?string
    {
        return $this->title;
    }

    public function getTitleData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();
        $this->hasTitle() && $protocol->add(
            new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'title', $this->getTitle())
        );

        return $protocol;
    }

    public function setType(?OpenGraphType $type = null) : self
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

    public function getTypeData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();
        $this->hasType() && $protocol->add($this->getType());

        return $protocol;
    }

    public function setImage(?string $url) : self
    {
        if ($url === null) {
            $this->image = $url;
        } else {
            $this->image = new OpenGraphImageData($url);
        }

        return $this;
    }

    public function hasImage() : bool
    {
        return isset($this->image);
    }

    public function getImage() : ?string
    {
        return $this->hasImage() ? $this->image->getURL() : null;
    }

    public function setImageData(OpenGraphImageData $image) : void
    {
        $this->image = $image;
    }

    public function getImageData() : OpenGraphData
    {
        return $this->hasImage() ? $this->image : new OpenGraphProtocol();
    }


    public function setURL(?string $url) : self
    {
        $this->url = $url;

        return $this;
    }

    public function hasURL() : bool
    {
        return isset($this->url);
    }

    public function getURL() : ?string
    {
        return $this->url;
    }

    public function getURLData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();
        $this->hasURL() && $protocol->add(new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'url', $this->getURL()));

        return $protocol;
    }


    public function setDescription(?string $description) : self
    {
        $this->description = $description;

        return $this;
    }

    public function hasDescription() : bool
    {
        return isset($this->description);
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function getDescriptionData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();
        $this->hasDescription() && $protocol->add(
            new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'description', $this->getDescription())
        );

        return $protocol;
    }

    public function setSiteName(string $site) : self
    {
        if ($site !== '') {
            $this->site = $site;
        } else {
            $this->site = null;
        }

        return $this;
    }

    public function hasSiteName() : bool
    {
        return isset($this->site);
    }

    public function getSiteName() : ?string
    {
        return $this->site;
    }

    public function getSiteNameData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();
        $this->hasSiteName() && $protocol->add(
            new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'site_name', $this->getSiteName())
        );

        return $protocol;
    }

    public function isValid() : bool
    {
        if (! $this->hasTitle()) {
            return false;
        }

        if (! $this->hasType()) {
            return false;
        }

        if (! $this->hasImage()) {
            return false;
        }

        if (! $this->hasURL()) {
            return false;
        }

        return $this->getImageData()->isValid();
    }

    public function getProtocol() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();

        $protocol->append($this->getTitleData());
        $protocol->append($this->getTypeData());
        $protocol->append($this->getImageData());
        $protocol->append($this->getURLData());
        $protocol->append($this->getDescriptionData());
        $protocol->append($this->getSiteNameData());

        return $protocol;
    }
}
