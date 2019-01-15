<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Hofff\Contao\SocialTags\Data\AbstractData;
use Hofff\Contao\SocialTags\Data\Protocol;

class OpenGraphBasicData extends AbstractData
{
    /** @var string|null */
    protected $siteName;

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

    protected function getTitleData() : Protocol
    {
        $protocol = new Protocol();

        if ($this->title) {
            $protocol->add(new OpenGraphProperty(Protocol::NS_OG, 'title', $this->title));
        }

        return $protocol;
    }

    public function setType(?OpenGraphType $type = null) : self
    {
        $this->type = $type;

        return $this;
    }

    protected function getTypeData() : Protocol
    {
        $protocol = new Protocol();

        if ($this->type) {
            $protocol->add($this->type);
        }

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

    public function setImageData(OpenGraphImageData $image) : self
    {
        $this->image = $image;

        return $this;
    }

    protected function getImageData() : OpenGraphImageData
    {
        return $this->image ? $this->image : new OpenGraphImageData();
    }

    public function setURL(?string $url) : self
    {
        $this->url = $url;

        return $this;
    }

    public function getURL() : ?string
    {
        return $this->url;
    }

    protected function getURLData() : Protocol
    {
        $protocol = new Protocol();

        if ($this->url) {
            $protocol->add(new OpenGraphProperty(Protocol::NS_OG, 'url', $this->url));
        }

        return $protocol;
    }

    public function setDescription(?string $description) : self
    {
        $this->description = $description;

        return $this;
    }

    protected function getDescriptionData() : Protocol
    {
        $protocol = new Protocol();

        if ($this->description) {
            $protocol->add(new OpenGraphProperty(Protocol::NS_OG, 'description', $this->description));
        }

        return $protocol;
    }

    public function setSiteName(?string $site) : self
    {
        if ($site !== '') {
            $this->siteName = $site;
        } else {
            $this->siteName = null;
        }

        return $this;
    }

    public function getSiteName() : ?string
    {
        return $this->siteName;
    }

    protected function getSiteNameData() : Protocol
    {
        $protocol = new Protocol();

        if ($this->siteName) {
            $protocol->add(new OpenGraphProperty(Protocol::NS_OG, 'site_name', $this->siteName));
        }

        return $protocol;
    }

    public function isValid() : bool
    {
        if (! $this->title) {
            return false;
        }

        if (! $this->type) {
            return false;
        }

        if (! $this->image) {
            return false;
        }

        if (! $this->url) {
            return false;
        }

        return $this->getImageData()->isValid();
    }

    public function getProtocol() : Protocol
    {
        $protocol = new Protocol();

        $protocol->append($this->getTitleData());
        $protocol->append($this->getTypeData());
        $protocol->append($this->getImageData());
        $protocol->append($this->getURLData());
        $protocol->append($this->getDescriptionData());
        $protocol->append($this->getSiteNameData());

        return $protocol;
    }
}
