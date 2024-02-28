<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Contao\Model;
use Hofff\Contao\SocialTags\Data\Data;
use Hofff\Contao\SocialTags\Data\DataFactory;
use Hofff\Contao\SocialTags\Data\Extractor;

final class OpenGraphFactory implements DataFactory
{
    /** @var Extractor */
    private $extractor;

    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    public function generate(Model $referencePage, Model|null $currentPage = null): Data
    {
        $basicData = new OpenGraphBasicData();

        if (! $this->extractor->supports($referencePage, $currentPage)) {
            return $basicData;
        }

        $basicData
            ->setTitle($this->extractor->extract('openGraph', 'title', $referencePage, $currentPage))
            ->setType($this->extractor->extract('openGraph', 'type', $referencePage, $currentPage))
            ->setImageData($this->extractor->extract('openGraph', 'imageData', $referencePage, $currentPage))
            ->setURL($this->extractor->extract('openGraph', 'url', $referencePage, $currentPage))
            ->setDescription($this->extractor->extract('openGraph', 'description', $referencePage, $currentPage))
            ->setSiteName($this->extractor->extract('openGraph', 'siteName', $referencePage, $currentPage));

        return $basicData;
    }
}
