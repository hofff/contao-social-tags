<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\TwitterCards;

use Hofff\Contao\SocialTags\Data\AbstractData;
use Hofff\Contao\SocialTags\Data\Property;
use Hofff\Contao\SocialTags\Data\Protocol;

abstract class CardData extends AbstractData
{
    protected const TYPE = null;

    /** @var string */
    private $title;

    /** @var string|null */
    private $site;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $image;

    public function __construct(string $title, ?string $site = null, ?string $description = null, ?string $image = null)
    {
        parent::__construct();

        $this->title       = $title;
        $this->site        = $site;
        $this->description = $description;
        $this->image       = $image;
    }

    public function getProtocol() : Protocol
    {
        $protocol = new Protocol();
        $protocol->append($this->getCardTypeData());
        $protocol->append($this->getSiteData());
        $protocol->append($this->getTitleData());
        $protocol->append($this->getDescriptionData());
        $protocol->append($this->getImageData());

        return $protocol;
    }

    private function getCardTypeData() : Protocol
    {
        $protocol = new Protocol();
        $protocol->add(new Property(null, 'card', static::TYPE, 'twitter'));

        return $protocol;
    }

    private function getSiteData() : Protocol
    {
        $protocol = new Protocol();

        if ($this->site) {
            $protocol->add(new Property(null, 'site', $this->site, 'twitter'));
        }

        return $protocol;
    }

    private function getImageData() : Protocol
    {
        $protocol = new Protocol();

        if ($this->site) {
            $protocol->add(new Property(null, 'image', $this->image, 'twitter'));
        }

        return $protocol;
    }

    private function getTitleData() : Protocol
    {
        $protocol = new Protocol();

        if ($this->site) {
            $protocol->add(new Property(null, 'title', $this->title, 'twitter'));
        }

        return $protocol;
    }

    private function getDescriptionData() : Protocol
    {
        $protocol = new Protocol();

        if ($this->site) {
            $protocol->add(new Property(null, 'description', $this->description, 'twitter'));
        }

        return $protocol;
    }
}
