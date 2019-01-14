<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Dca;

use Contao\Image;
use Contao\StringUtil;
use Symfony\Component\Routing\RouterInterface;
use function explode;
use function is_string;
use function strlen;
use function strpos;

final class PageDcaListener
{
    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /** @return string[] */
    public function typeOptions() : array
    {
        $arrOptions = [];
        foreach ($GLOBALS['hofff_st']['TYPES'] as $strType) {
            if (strpos($strType, ' ') === false) {
                [$strGroup, $strName]         = explode('.', $strType);
                (is_string($strName) && strlen($strName)) || $strGroup= 'general';
                $arrOptions[$strGroup][]      = $strType;
            } else {
                $arrCustom[] = $strType;
            }
        }
        $arrCustom && $arrOptions['custom'] = $arrCustom;

        return $arrOptions;
    }

    public function facebookLinkButton(
        array $row,
        ?string $href,
        string $label,
        string $title,
        string $icon,
        string $attributes
    ) : string {
        return sprintf(
            '<a href="%s" title="%s"%s>%s</a>',
            $this->router->generate('hofff_contao_social_tags_facebook_lint', ['pageId' => $row['id']]),
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }
}
