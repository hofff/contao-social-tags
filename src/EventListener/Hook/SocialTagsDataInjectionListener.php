<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Hofff\Contao\SocialTags\OpenGraph\OpenGraphData;

final class SocialTagsDataInjectionListener extends SocialTagsDataAwareListener
{
    public function onGeneratePage() : void
    {
        $socialTagsData = $this->getSocialTagsData();
        if (!$socialTagsData instanceof OpenGraphData) {
            return;
        }

        $GLOBALS['TL_HEAD'][] = $socialTagsData->getProtocol()->getMetaTags();
    }
}
