<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\CalendarEventsModel;
use Contao\Events;
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
 * @implements OpenGraphExtractor<CalendarEventsModel, PageModel>
 * @implements TwitterCardsExtractor<CalendarEventsModel, PageModel>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class CalendarEventsExtractor extends AbstractExtractor implements OpenGraphExtractor, TwitterCardsExtractor
{
    /** @use OpenGraphExtractorPlugin<CalendarEventsModel, PageModel> */
    use OpenGraphExtractorPlugin;
    /** @use TwitterCardsExtractorPlugin<CalendarEventsModel, PageModel> */
    use TwitterCardsExtractorPlugin;

    public function supports(object $reference, object|null $fallback = null): bool
    {
        if (! $reference instanceof CalendarEventsModel) {
            return false;
        }

        return $fallback instanceof PageModel;
    }

    /** {@inheritDoc} */
    public function supportedDataContainers(): array
    {
        return ['tl_calendar_events'];
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

        // Generate the description from the teaser the same way as the event reader does
        $description = $this->replaceInsertTags($reference->teaser);
        $description = strip_tags($description);
        $description = str_replace("\n", ' ', $description);

        return StringUtil::substr($description, 320);
    }

    protected function getContentTitle(object $reference): string
    {
        return $reference->pageTitle ?: $reference->title;
    }

    protected function getContentUrl(object $reference): string
    {
        $eventUrl = Events::generateEventUrl($reference, true);

        // Prepend scheme and host if URL is not absolute
        if (stripos($eventUrl, 'http') !== 0) {
            $eventUrl = $this->getBaseUrl() . $eventUrl;
        }

        return $eventUrl;
    }

    protected function defaultOpenGraphType(): OpenGraphType
    {
        return new OpenGraphType('article');
    }
}
