<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Dca;

use Hofff\Contao\SocialTags\Util\TypeUtil;
use function explode;
use function strpos;

final class OpenGraphTypeOptions
{
    /** @var string[] */
    private $types;

    /** @param string[] $types */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /** @return string[] */
    public function __invoke() : array
    {
        $options = [];
        foreach ($this->types as $strType) {
            if (strpos($strType, ' ') === false) {
                [$strGroup, $strName]                                = explode('.', $strType);
                TypeUtil::isStringWithContent($strName) || $strGroup = 'general';
                $options[$strGroup][]                                = $strType;
            } else {
                $custom[] = $strType;
            }
        }

        $custom && $options['custom'] = $custom;

        return $options;
    }
}
