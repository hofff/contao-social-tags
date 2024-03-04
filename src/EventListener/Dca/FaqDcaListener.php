<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;

final class FaqDcaListener
{
    #[AsCallback('tl_faq', 'config.onload')]
    public function initializePalette(): void
    {
        PaletteManipulator::create()
            ->addLegend('hofff_st_legend', 'answer_legend')
            ->addField('hofff_st', 'hofff_st_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_faq');
    }
}
