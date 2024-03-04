<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\News;
use Contao\NewsModel;
use Contao\PageModel;
use Contao\StringUtil;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphExtractor;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphExtractorPlugin;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphType;
use Hofff\Contao\SocialTags\Data\TwitterCards\TwitterCardsExtractor;
use Hofff\Contao\SocialTags\Data\TwitterCards\TwitterCardsExtractorPlugin;
use Hofff\Contao\SocialTags\Util\TypeUtil;

use function str_replace;
use function strip_tags;
use function stripos;
use function trim;

/**
 * @implements OpenGraphExtractor<NewsModel, PageModel>
 * @implements TwitterCardsExtractor<NewsModel, PageModel>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class NewsExtractor extends AbstractExtractor implements OpenGraphExtractor, TwitterCardsExtractor
{
    /** @use OpenGraphExtractorPlugin<NewsModel, PageModel> */
    use OpenGraphExtractorPlugin;
    /** @use TwitterCardsExtractorPlugin<NewsModel, PageModel> */
    use TwitterCardsExtractorPlugin;

    public function supports(object $reference, object|null $fallback = null): bool
    {
        if (! $reference instanceof NewsModel) {
            return false;
        }

        return $fallback instanceof PageModel;
    }

    /** {@inheritDoc} */
    public function supportedDataContainers(): array
    {
        return ['tl_news'];
    }

    /**
     * Returns the meta description if present, otherwise the shortened teaser.
     */
    protected function getContentDescription(object $reference): string|null
    {
        if (TypeUtil::isStringWithContent($reference->description)) {
            return $this->replaceInsertTags(trim(str_replace(["\n", "\r"], [' ', ''], $reference->description)));
        }

        if (! TypeUtil::isStringWithContent($reference->teaser)) {
            return null;
        }

        // Generate the description from the teaser the same way as the news reader does
        $description = $this->replaceInsertTags($reference->teaser);
        $description = strip_tags($description);
        $description = str_replace("\n", ' ', $description);

        return StringUtil::substr($description, 320);
    }

    protected function getContentTitle(object $reference): string
    {
        return (string) ($reference->pageTitle ?: $reference->headline);
    }

    protected function defaultOpenGraphType(): OpenGraphType
    {
        return new OpenGraphType('article');
    }

    protected function getContentUrl(object $reference): string
    {
        $newsUrl = News::generateNewsUrl($reference, false, true);

        // Prepend scheme and host if URL is not absolute
        if (stripos($newsUrl, 'http') !== 0) {
            $newsUrl = $this->getBaseUrl() . $newsUrl;
        }

        return $newsUrl;
    }
}
