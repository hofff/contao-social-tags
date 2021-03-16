<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\FilesModel;
use Hofff\Contao\SocialTags\Data\Extractor;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractExtractor implements Extractor
{
    /** @var RequestStack */
    protected $requestStack;

    /** @var ContaoFrameworkInterface */
    protected $framework;

    /** @var string */
    protected $projectDir;

    public function __construct(ContaoFrameworkInterface $framework, RequestStack $requestStack, string $projectDir)
    {
        $this->framework    = $framework;
        $this->projectDir   = $projectDir;
        $this->requestStack = $requestStack;
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

    protected function getFileModel(string $uuid): ?FilesModel
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
