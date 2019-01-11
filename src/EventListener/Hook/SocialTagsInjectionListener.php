<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Contao\PageModel;
use Hofff\Contao\SocialTags\OpenGraph\OpenGraphData;

final class SocialTagsInjectionListener
{
    /** @var OpenGraphData|null */
    protected $openGraphData;

    public function setOpenGraphData(OpenGraphData $objOGD) : void
    {
        $this->openGraphData = $objOGD;
    }

    public function hasOpenGraphData() : bool
    {
        return isset($this->openGraphData);
    }

    public function onGeneratePage(PageModel $page) : void
    {
        if ($page->bbit_st === null) {
            return;
        }

        if (! $this->openGraphData) {
            return;
        }

        $GLOBALS['TL_HEAD'][] = $this->openGraphData->getProtocol()->getMetaTags();
    }

    private function __clone()
    {
    }
}
