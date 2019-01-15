<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\TwitterCards;

use Hofff\Contao\SocialTags\Data\Property;
use Hofff\Contao\SocialTags\Data\Protocol;

final class SummaryWithLargeImageCardData extends CardData
{
    protected const TYPE = 'summary_large_image';

    /** @var string|null */
    private $creator;

    public function __construct(
        string $title,
        ?string $site = null,
        ?string $description = null,
        ?string $image = null,
        ?string $creator = null
    ) {
        parent::__construct($title, $site, $description, $image);

        $this->creator = $creator;
    }

    public function getProtocol() : Protocol
    {
        $protocol = parent::getProtocol();
        $protocol->append($this->getCreatorData());

        return $protocol;
    }

    private function getCreatorData(): Protocol
    {
        $protocol = new Protocol();

        if ($this->creator) {
            $protocol->add(new Property(null, 'creator', $this->creator, 'twitter'));
        }

        return $protocol;
    }
}
