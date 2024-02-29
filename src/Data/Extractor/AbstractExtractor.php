<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;
use Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor;
use Contao\FilesModel;
use Hofff\Contao\SocialTags\Data\Extractor;
use Symfony\Component\HttpFoundation\RequestStack;

/** @SuppressWarnings(PHPMD.LongVariable) */
abstract class AbstractExtractor implements Extractor
{
    public function __construct(
        protected ContaoFramework $framework,
        protected RequestStack $requestStack,
        protected ResponseContextAccessor $responseContextAccessor,
        protected InsertTagParser $insertTagParser,
        protected string $projectDir,
    ) {
    }

    protected function getBaseUrl(): string
    {
        static $baseUrl;

        if ($baseUrl !== null) {
            return $baseUrl;
        }

        $request = $this->requestStack->getMainRequest();
        if (! $request) {
            return '';
        }

        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath() . '/';

        return $baseUrl;
    }

    protected function getRequestUri(): string
    {
        $request = $this->requestStack->getMainRequest();
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
        return $this->insertTagParser->replaceInline($content);
    }

    protected function getCanonicalUrlForRequest(): string|null
    {
        $responseContext = $this->responseContextAccessor->getResponseContext();

        if (! $responseContext?->has(HtmlHeadBag::class)) {
            return null;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (! $request) {
            return null;
        }

        return $responseContext->get(HtmlHeadBag::class)->getCanonicalUriForRequest($request);
    }
}
