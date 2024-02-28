<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\TwitterCards;

use Contao\Model;
use Hofff\Contao\SocialTags\Data\Data;
use Hofff\Contao\SocialTags\Data\DataFactory;
use Hofff\Contao\SocialTags\Data\Extractor;
use Hofff\Contao\SocialTags\Data\Protocol;

final class TwitterCardsFactory implements DataFactory
{
    public function __construct(private readonly Extractor $extractor)
    {
    }

    public function generate(Model $reference, Model|null $fallback = null): Data
    {
        if (! $this->extractor->supports($reference, $fallback)) {
            return new Protocol();
        }

        $type = $fallback->hofff_st_twitter_type ?? null;
        if ($reference->hofff_st && $reference->hofff_st_twitter_type) {
            $type = $reference->hofff_st_twitter_type;
        }

        switch ($type) {
            case 'hofff_st_twitter_summary':
                return new SummaryCardData(
                    $this->extractor->extract('twitter', 'title', $reference, $fallback),
                    $this->extractor->extract('twitter', 'site', $reference, $fallback),
                    $this->extractor->extract('twitter', 'description', $reference, $fallback),
                    $this->extractor->extract('twitter', 'image', $reference, $fallback),
                );

            case 'hofff_st_twitter_summary_large_image':
                return new SummaryWithLargeImageCardData(
                    $this->extractor->extract('twitter', 'title', $reference, $fallback),
                    $this->extractor->extract('twitter', 'site', $reference, $fallback),
                    $this->extractor->extract('twitter', 'description', $reference, $fallback),
                    $this->extractor->extract('twitter', 'image', $reference, $fallback),
                    $this->extractor->extract('twitter', 'creator', $reference, $fallback),
                );

            default:
                return new Protocol();
        }
    }
}
