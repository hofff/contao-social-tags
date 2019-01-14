<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\OpenGraph;

use function strpos;

class OpenGraphImageData extends AbstractOpenGraphData
{
    /** @var string|null */
    protected $url;

    /** @var string|null */
    protected $mime;

    /** @var int|null */
    protected $height;

    /** @var int|null */
    protected $width;

    /** @var string|null */
    protected $secure;

    public function __construct(?string $url = null)
    {
        parent::__construct();
        $this->setURL($url);
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
        $this->hasURL() && $protocol->add(new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'image', $this->getURL()));

        return $protocol;
    }

    public function setSecureURL(?string $secure) : self
    {
        $this->secure = $secure;

        return $this;
    }

    public function hasSecureURL() : bool
    {
        return isset($this->secure);
    }

    public function getSecureURL() : ?string
    {
        return $this->secure;
    }

    public function getSecureURLData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();
        $this->hasSecureURL() && $protocol->add(
            new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'image:secure_url', $this->getSecureURL())
        );

        return $protocol;
    }

    public function setMIMEType(?string $mime) : self
    {
        if ($mime === null || strpos($mime, 'image/') !== 0) {
            $this->mime = null;

            return $this;
        }

        $this->mime = $mime;

        return $this;
    }

    public function hasMIMEType() : bool
    {
        return isset($this->mime);
    }

    public function getMIMEType() : ?string
    {
        return $this->mime;
    }

    public function getMIMETypeData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();
        $this->hasMIMEType() && $protocol->add(
            new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'image:type', $this->getMIMEType())
        );

        return $protocol;
    }

    public function setWidth(?int $width) : self
    {
        $this->width = $width;

        return $this;
    }

    public function hasWidth() : bool
    {
        return isset($this->width);
    }

    public function getWidth() : ?int
    {
        return $this->width;
    }

    public function getWidthData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();
        $this->hasWidth() && $protocol->add(
            new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'image:width', (string) $this->getWidth())
        );

        return $protocol;
    }

    public function setHeight(?int $height) : self
    {
        $this->height = $height;

        return $this;
    }

    public function hasHeight() : bool
    {
        return isset($this->height);
    }

    public function getHeight() : ?int
    {
        return $this->height;
    }

    public function getHeightData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();
        $this->hasHeight() && $protocol->add(
            new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'image:height', (string) $this->getHeight())
        );

        return $protocol;
    }

    public function isValid() : bool
    {
        return $this->hasURL();
    }

    public function getProtocol() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();
        $protocol->append($this->getURLData());
        $protocol->append($this->getSecureURLData());
        $protocol->append($this->getMIMETypeData());
        $protocol->append($this->getWidthData());
        $protocol->append($this->getHeightData());

        return $protocol;
    }
}
