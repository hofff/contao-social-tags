<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Hofff\Contao\SocialTags\Data\AbstractData;
use Hofff\Contao\SocialTags\Data\Property;
use Hofff\Contao\SocialTags\Data\Protocol;

use function strpos;

class OpenGraphImageData extends AbstractData
{
    protected string|null $url;

    protected string|null $mime = null;

    protected int|null $height = null;

    protected int|null $width = null;

    protected string|null $secure = null;

    public function __construct(string|null $url = null)
    {
        parent::__construct();

        $this->setURL($url);
    }

    public function setURL(string|null $url): self
    {
        $this->url = $url;

        return $this;
    }

    protected function getURLData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->url) {
            $protocol->add(new Property(Protocol::NS_OG, 'image', $this->url));
        }

        return $protocol;
    }

    public function setSecureURL(string|null $secure): self
    {
        $this->secure = $secure;

        return $this;
    }

    protected function getSecureURLData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->secure) {
            $protocol->add(
                new Property(Protocol::NS_OG, 'image:secure_url', $this->secure),
            );
        }

        return $protocol;
    }

    public function setMIMEType(string|null $mime): self
    {
        if ($mime === null || strpos($mime, 'image/') !== 0) {
            $this->mime = null;

            return $this;
        }

        $this->mime = $mime;

        return $this;
    }

    protected function getMIMETypeData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->mime) {
            $protocol->add(
                new Property(Protocol::NS_OG, 'image:type', $this->mime),
            );
        }

        return $protocol;
    }

    public function setWidth(int|null $width): self
    {
        $this->width = $width;

        return $this;
    }

    protected function getWidthData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->width) {
            $protocol->add(
                new Property(Protocol::NS_OG, 'image:width', (string) $this->width),
            );
        }

        return $protocol;
    }

    public function setHeight(int|null $height): self
    {
        $this->height = $height;

        return $this;
    }

    protected function getHeightData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->height) {
            $protocol->add(
                new Property(Protocol::NS_OG, 'image:height', (string) $this->height),
            );
        }

        return $protocol;
    }

    public function isValid(): bool
    {
        return (bool) $this->url;
    }

    public function getProtocol(): Protocol
    {
        $protocol = new Protocol();
        $protocol->append($this->getURLData());
        $protocol->append($this->getSecureURLData());
        $protocol->append($this->getMIMETypeData());
        $protocol->append($this->getWidthData());
        $protocol->append($this->getHeightData());

        return $protocol;
    }
}
