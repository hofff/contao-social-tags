<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Hofff\Contao\SocialTags\Data\Data;

final class SocialTagsDataInjectionListener extends SocialTagsDataAwareListener
{
    public function onGeneratePage() : void
    {
        $socialTagsData = $this->getSocialTagsData();
        if (! $socialTagsData instanceof Data) {
            return;
        }

        $GLOBALS['TL_HEAD'][] = $socialTagsData->getProtocol()->getMetaTags();
    }
}
