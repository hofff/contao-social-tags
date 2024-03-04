<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\DependencyInjection;

use Hofff\Contao\SocialTags\Data\DataFactory;
use Hofff\Contao\SocialTags\Data\Extractor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class HofffContaoSocialTagsExtension extends Extension
{
    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config'),
        );

        $loader->load('config.xml');
        $loader->load('services.xml');
        $loader->load('listeners.xml');

        $container->registerForAutoconfiguration(Extractor::class)->addTag(Extractor::class);
        $container->registerForAutoconfiguration(DataFactory::class)->addTag(DataFactory::class);
    }
}
