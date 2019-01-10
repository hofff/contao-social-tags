<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Dca;

final class PageDcaListener
{
    public function typeOptions(): array
    {
        $arrOptions = [];
        foreach ($GLOBALS['bbit_st']['TYPES'] as $strType) {
            if (strpos($strType, ' ') === false) {
                list($strGroup, $strName) = explode('.', $strType);
                strlen($strName) || $strGroup = 'general';
                $arrOptions[$strGroup][] = $strType;
            } else {
                $arrCustom[] = $strType;
            }
        }
        $arrCustom && $arrOptions['custom'] = $arrCustom;

        return $arrOptions;
    }
}
