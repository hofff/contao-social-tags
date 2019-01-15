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

    protected function getURLData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();

        if ($this->url) {
            $protocol->add(new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'image', $this->url));
        }

        return $protocol;
    }

    public function setSecureURL(?string $secure) : self
    {
        $this->secure = $secure;

        return $this;
    }

    protected function getSecureURLData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();

        if ($this->secure) {
            $protocol->add(
                new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'image:secure_url', $this->secure)
            );
        }

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

    protected function getMIMETypeData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();

        if ($this->mime) {
            $protocol->add(
                new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'image:type', $this->mime)
            );
        }

        return $protocol;
    }

    public function setWidth(?int $width) : self
    {
        $this->width = $width;

        return $this;
    }

    protected function getWidthData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();

        if ($this->width) {
            $protocol->add(
                new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'image:width', (string) $this->width)
            );
        }

        return $protocol;
    }

    public function setHeight(?int $height) : self
    {
        $this->height = $height;

        return $this;
    }

    protected function getHeightData() : OpenGraphProtocol
    {
        $protocol = new OpenGraphProtocol();

        if ($this->height) {
            $protocol->add(
                new OpenGraphProperty(OpenGraphProtocol::NS_OG, 'image:height', (string) $this->height)
            );
        }

        return $protocol;
    }

    public function isValid() : bool
    {
        return (bool) $this->url;
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
