<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\TwitterCards;

use Hofff\Contao\SocialTags\Data\AbstractData;
use Hofff\Contao\SocialTags\Data\Property;
use Hofff\Contao\SocialTags\Data\Protocol;

abstract class CardData extends AbstractData
{
    protected const TYPE = null;

    public function __construct(
        private readonly string $title,
        private readonly string|null $site = null,
        private readonly string|null $description = null,
        private readonly string|null $image = null,
    ) {
        parent::__construct();
    }

    public function getProtocol(): Protocol
    {
        $protocol = new Protocol();
        $protocol->append($this->getCardTypeData());
        $protocol->append($this->getSiteData());
        $protocol->append($this->getTitleData());
        $protocol->append($this->getDescriptionData());
        $protocol->append($this->getImageData());

        return $protocol;
    }

    private function getCardTypeData(): Protocol
    {
        $protocol = new Protocol();
        $protocol->add(new Property(null, 'card', self::TYPE, 'twitter'));

        return $protocol;
    }

    private function getSiteData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->site) {
            $protocol->add(new Property(null, 'site', $this->site, 'twitter'));
        }

        return $protocol;
    }

    private function getImageData(): Protocol
    {
        $protocol = new Protocol();
        $protocol->add(new Property(null, 'image', $this->image, 'twitter'));

        return $protocol;
    }

    private function getTitleData(): Protocol
    {
        $protocol = new Protocol();
        $protocol->add(new Property(null, 'title', $this->title, 'twitter'));

        return $protocol;
    }

    private function getDescriptionData(): Protocol
    {
        $protocol = new Protocol();
        $protocol->add(new Property(null, 'description', $this->description, 'twitter'));

        return $protocol;
    }
}
