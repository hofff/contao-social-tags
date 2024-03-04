<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\TwitterCards;

use Hofff\Contao\SocialTags\Data\Data;
use Hofff\Contao\SocialTags\Data\DataFactory;
use Hofff\Contao\SocialTags\Data\ExtractorResolver;
use Hofff\Contao\SocialTags\Data\Protocol;

final class TwitterCardsFactory implements DataFactory
{
    public function __construct(private readonly ExtractorResolver $resolver)
    {
    }

    public function generate(object $reference, object|null $fallback = null): Data
    {
        $extractor = $this->resolver->resolve(TwitterCardsExtractor::class, $reference, $fallback);
        if (! $extractor instanceof TwitterCardsExtractor) {
            return new Protocol();
        }

        $type = $fallback?->hofff_st_twitter_type ?? null;
        if ($reference->hofff_st && $reference->hofff_st_twitter_type) {
            $type = $reference->hofff_st_twitter_type;
        }

        return match ($type) {
            'hofff_st_twitter_summary' => new SummaryCardData(
                $extractor->extractTwitterTitle($reference, $fallback),
                $extractor->extractTwitterSite($reference, $fallback),
                $extractor->extractTwitterDescription($reference, $fallback),
                $extractor->extractTwitterImage($reference, $fallback),
            ),
            'hofff_st_twitter_summary_large_image' => new SummaryWithLargeImageCardData(
                $extractor->extractTwitterTitle($reference, $fallback),
                $extractor->extractTwitterSite($reference, $fallback),
                $extractor->extractTwitterDescription($reference, $fallback),
                $extractor->extractTwitterImage($reference, $fallback),
                $extractor->extractTwitterCreator($reference, $fallback),
            ),
            default => new Protocol(),
        };
    }
}
