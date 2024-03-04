<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Framework\ContaoFramework;
use Hofff\Contao\SocialTags\Data\ExtractorResolver;

use function in_array;

#[AsHook('loadDataContainer')]
final class LoadLanguageFileListener
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ExtractorResolver $extractorResolver,
    ) {
    }

    public function __invoke(string $dataContainer): void
    {
        if (! in_array($dataContainer, $this->extractorResolver->supportedDataContainers(), true)) {
            return;
        }

        $this->framework->getAdapter(Controller::class)->loadLanguageFile('hofff_st');
    }
}
