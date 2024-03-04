<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Hofff\Contao\SocialTags\Data\Data;

#[AsHook('generatePage', priority: -1)]
final class SocialTagsDataInjectionListener extends SocialTagsDataAwareListener
{
    /** @SuppressWarnings(PHPMD.Superglobals) */
    public function __invoke(): void
    {
        $socialTagsData = $this->getSocialTagsData();
        if (! $socialTagsData instanceof Data) {
            return;
        }

        $GLOBALS['TL_HEAD'][] = $socialTagsData->getProtocol()->getMetaTags();
    }
}
