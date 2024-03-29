<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Dca;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\Image;
use Contao\StringUtil;
use Symfony\Component\Routing\RouterInterface;

use function sprintf;

final class PageDcaListener
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    /**
     * @param mixed[] $row
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[AsCallback('tl_page', 'list.operations.hofff_st_og_facebookLint.button')]
    public function facebookLinkButton(
        array $row,
        string|null $href,
        string $label,
        string $title,
        string $icon,
        string $attributes,
    ): string {
        return sprintf(
            '<a href="%s" title="%s"%s>%s</a>',
            $this->router->generate('hofff_contao_social_tags_facebook_lint', ['pageId' => $row['id']]),
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml($icon, $label),
        );
    }
}
