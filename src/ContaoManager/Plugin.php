<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\ContaoManager;

use Contao\CalendarBundle\ContaoCalendarBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\FaqBundle\ContaoFaqBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Contao\NewsBundle\ContaoNewsBundle;
use Hofff\Contao\SocialTags\HofffContaoSocialTagsBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

final class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    /**
     * @return BundleConfig[]
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(HofffContaoSocialTagsBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    ContaoNewsBundle::class,
                    ContaoFaqBundle::class,
                    ContaoCalendarBundle::class,
                ]),
        ];
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel): RouteCollection|null
    {
        $loader = $resolver->resolve(__DIR__ . '/../Resources/config/routing.xml');
        if (! $loader) {
            return null;
        }

        return $loader->load(__DIR__ . '/../Resources/config/routing.xml');
    }
}
