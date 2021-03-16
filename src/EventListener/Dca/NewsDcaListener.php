<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;

final class NewsDcaListener
{
    public function initializePalette(): void
    {
        PaletteManipulator::create()
            ->addLegend('hofff_st_legend', 'teaser_legend', PaletteManipulator::POSITION_BEFORE)
            ->addField('hofff_st', 'hofff_st_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_news');
    }
}
