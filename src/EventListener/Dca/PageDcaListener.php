<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Dca;

use function explode;
use function strlen;
use function strpos;

final class PageDcaListener
{
    /** @return string[] */
    public function typeOptions() : array
    {
        $arrOptions = [];
        foreach ($GLOBALS['hofff_st']['TYPES'] as $strType) {
            if (strpos($strType, ' ') === false) {
                [$strGroup, $strName]         = explode('.', $strType);
                strlen($strName) || $strGroup = 'general';
                $arrOptions[$strGroup][]      = $strType;
            } else {
                $arrCustom[] = $strType;
            }
        }
        $arrCustom && $arrOptions['custom'] = $arrCustom;

        return $arrOptions;
    }
}
