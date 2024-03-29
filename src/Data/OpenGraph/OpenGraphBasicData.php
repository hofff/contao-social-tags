<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Hofff\Contao\SocialTags\Data\AbstractData;
use Hofff\Contao\SocialTags\Data\Property;
use Hofff\Contao\SocialTags\Data\Protocol;

class OpenGraphBasicData extends AbstractData
{
    protected string|null $siteName = null;

    protected string|null $description = null;

    public function __construct(
        protected string|null $title = null,
        protected OpenGraphType|null $type = null,
        protected OpenGraphImageData|null $image = null,
        protected string|null $url = null,
    ) {
        parent::__construct();
    }

    public function setTitle(string $title): self
    {
        if ($title !== '') {
            $this->title = $title;
        } else {
            $this->title = null;
        }

        return $this;
    }

    protected function getTitleData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->title !== null) {
            $protocol->add(new Property(Protocol::NS_OG, 'title', $this->title));
        }

        return $protocol;
    }

    public function setType(OpenGraphType|null $type = null): self
    {
        $this->type = $type;

        return $this;
    }

    protected function getTypeData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->type !== null) {
            $protocol->add($this->type);
        }

        return $protocol;
    }

    public function setImage(string|null $url): self
    {
        if ($url === null) {
            $this->image = $url;
        } else {
            $this->image = new OpenGraphImageData($url);
        }

        return $this;
    }

    public function setImageData(OpenGraphImageData $image): self
    {
        $this->image = $image;

        return $this;
    }

    protected function getImageData(): OpenGraphImageData
    {
        return $this->image ? $this->image : new OpenGraphImageData();
    }

    public function setURL(string|null $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getURL(): string|null
    {
        return $this->url;
    }

    protected function getURLData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->url !== null) {
            $protocol->add(new Property(Protocol::NS_OG, 'url', $this->url));
        }

        return $protocol;
    }

    public function setDescription(string|null $description): self
    {
        $this->description = $description;

        return $this;
    }

    protected function getDescriptionData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->description !== null) {
            $protocol->add(new Property(Protocol::NS_OG, 'description', $this->description));
        }

        return $protocol;
    }

    public function setSiteName(string|null $site): self
    {
        if ($site !== '') {
            $this->siteName = $site;
        } else {
            $this->siteName = null;
        }

        return $this;
    }

    public function getSiteName(): string|null
    {
        return $this->siteName;
    }

    protected function getSiteNameData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->siteName !== null) {
            $protocol->add(new Property(Protocol::NS_OG, 'site_name', $this->siteName));
        }

        return $protocol;
    }

    public function isValid(): bool
    {
        if ($this->title === null) {
            return false;
        }

        if ($this->type === null) {
            return false;
        }

        if ($this->image === null) {
            return false;
        }

        if ($this->url === null) {
            return false;
        }

        return $this->getImageData()->isValid();
    }

    public function getProtocol(): Protocol
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
