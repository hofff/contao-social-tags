<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\Config;
use Contao\FaqCategoryModel;
use Contao\FaqModel;
use Contao\PageModel;
use Contao\StringUtil;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphExtractor;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphExtractorPlugin;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphType;
use Hofff\Contao\SocialTags\Data\TwitterCards\TwitterCardsExtractor;
use Hofff\Contao\SocialTags\Data\TwitterCards\TwitterCardsExtractorPlugin;

/**
 * @implements OpenGraphExtractor<FaqModel, PageModel>
 * @implements TwitterCardsExtractor<FaqModel, PageModel>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class FaqExtractor extends AbstractExtractor implements OpenGraphExtractor, TwitterCardsExtractor
{
    /** @use OpenGraphExtractorPlugin<FaqModel, PageModel> */
    use OpenGraphExtractorPlugin;
    /** @use TwitterCardsExtractorPlugin<FaqModel, PageModel> */
    use TwitterCardsExtractorPlugin;

    public function supports(object $reference, object|null $fallback = null): bool
    {
        if (! $reference instanceof FaqModel) {
            return false;
        }

        return $fallback instanceof PageModel;
    }

    protected function getContentTitle(object $reference): string
    {
        return (string) $reference->question;
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function getContentDescription(object $reference): string|null
    {
        return null;
    }

    protected function getContentUrl(object $reference): string
    {
        /** @psalm-var FaqCategoryModel $faqCategory */
        $faqCategory = $reference->getRelated('pid');
        /** @psalm-suppress RedundantCastGivenDocblockType */
        $jumpTo = (int) $faqCategory->jumpTo;

        if ($jumpTo < 1) {
            return '';
        }

        $target = PageModel::findByPk($jumpTo);

        if ($target === null) {
            return '';
        }

        $params = (Config::get('useAutoItem') ? '/' : '/items/') . ($reference->alias ?: $reference->id);

        return StringUtil::ampersand($target->getAbsoluteUrl($params));
    }

    protected function defaultOpenGraphType(): OpenGraphType
    {
        return new OpenGraphType('website');
    }
}
