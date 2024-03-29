<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\TwitterCards;

use Hofff\Contao\SocialTags\Data\Property;
use Hofff\Contao\SocialTags\Data\Protocol;

final class SummaryWithLargeImageCardData extends CardData
{
    protected const TYPE = 'summary_large_image';

    public function __construct(
        string $title,
        string|null $site = null,
        string|null $description = null,
        string|null $image = null,
        private readonly string|null $creator = null,
    ) {
        parent::__construct($title, $site, $description, $image);
    }

    public function getProtocol(): Protocol
    {
        $protocol = parent::getProtocol();
        $protocol->append($this->getCreatorData());

        return $protocol;
    }

    private function getCreatorData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->creator !== null) {
            $protocol->add(new Property(null, 'creator', $this->creator, 'twitter'));
        }

        return $protocol;
    }
}
