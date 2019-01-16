<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;

final class FaqDcaListener
{
    public function initializePalette() : void
    {
        PaletteManipulator::create()
            ->addLegend('hofff_st_legend', 'answer_legend')
            ->addField('hofff_st', 'hofff_st_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_faq');
    }
}
