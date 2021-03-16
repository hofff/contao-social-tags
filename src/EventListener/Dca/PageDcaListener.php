<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Dca;

use Contao\Image;
use Contao\StringUtil;
use Symfony\Component\Routing\RouterInterface;

use function sprintf;

final class PageDcaListener
{
    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /** @param mixed[] $row */
    public function facebookLinkButton(
        array $row,
        ?string $href,
        string $label,
        string $title,
        string $icon,
        string $attributes
    ): string {
        return sprintf(
            '<a href="%s" title="%s"%s>%s</a>',
            $this->router->generate('hofff_contao_social_tags_facebook_lint', ['pageId' => $row['id']]),
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }
}
