<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Hofff\Contao\SocialTags\HofffContaoSocialTagsBundle;

final class Plugin implements BundlePluginInterface
{
    /** @return BundleConfig[] */
    public function getBundles(ParserInterface $parser) : array
    {
        return [BundleConfig::create(HofffContaoSocialTagsBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}