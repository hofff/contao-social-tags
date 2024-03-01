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

use function is_file;

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

    /**
     * Retrieves an image from the reference or fallback object.
     *
     * Resolving an image is done by following steps:
     *  - Check if reference object enables hofff_st and has an image configured by $key
     *  - Check if reference object enables addImage and has an image configured by singleSRC
     *  - Check if fallback object provides an image by $key
     */
    protected function getImage(
        string $key,
        object $reference,
        object|null $fallback = null,
    ): FilesModel|null {
        if ($reference->hofff_st && $reference->{$key}) {
            $image = $reference->{$key};
        } elseif ($reference->addImage && $reference->singleSRC) {
            $image = $reference->singleSRC;
        } elseif ($fallback && $fallback->{$key}) {
            $image = $fallback->{$key};
        } else {
            return null;
        }

        return $this->getFileModel($image);
    }

    protected function getFileUrl(FilesModel|null $file = null): string|null
    {
        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            return $this->getBaseUrl() . $file->path;
        }

        return null;
    }

    protected function getFileModel(string $uuid): FilesModel|null
    {
        return $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($uuid);
    }

    protected function replaceInsertTags(string $value): string
    {
        return $this->insertTagParser->replaceInline($value);
    }

    protected function getCanonicalUrlForRequest(): string|null
    {
        $responseContext = $this->responseContextAccessor->getResponseContext();

        if (! $responseContext || ! $responseContext->has(HtmlHeadBag::class)) {
            return null;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (! $request) {
            return null;
        }

        return $responseContext->get(HtmlHeadBag::class)->getCanonicalUriForRequest($request);
    }
}
