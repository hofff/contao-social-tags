<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FilesModel;
use Hofff\Contao\SocialTags\Data\Extractor;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractExtractor implements Extractor
{
    public function __construct(
        protected ContaoFramework $framework,
        protected RequestStack $requestStack,
        protected string $projectDir,
    ) {
    }

    protected function getBaseUrl(): string
    {
        static $baseUrl;

        if ($baseUrl !== null) {
            return $baseUrl;
        }

        $request = $this->requestStack->getMasterRequest();
        if (! $request) {
            return '';
        }

        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath() . '/';

        return $baseUrl;
    }

    protected function getRequestUri(): string
    {
        $request = $this->requestStack->getMasterRequest();
        if (! $request) {
            return '/';
        }

        return $request->getRequestUri();
    }

    protected function getFileModel(string $uuid): FilesModel|null
    {
        return $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($uuid);
    }

    protected function replaceInsertTags(string $content): string
    {
        $controller = $this->framework->getAdapter(Controller::class);

        $content = $controller->__call('replaceInsertTags', [$content, false]);
        $content = $controller->__call('replaceInsertTags', [$content, true]);

        return $content;
    }
}
