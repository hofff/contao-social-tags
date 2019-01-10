<?php

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Contao\PageModel;
use Hofff\Contao\SocialTags\OpenGraph\OpenGraphData;

final class SocialTagsInjectionListener
{
    /** @var OpenGraphData|null */
    protected $openGraphData;

    public function setOpenGraphData(OpenGraphData $objOGD)
    {
        $this->openGraphData = $objOGD;
    }

    public function hasOpenGraphData()
    {
        return isset($this->openGraphData);
    }

    public function onGeneratePage(PageModel $page): void
    {
        if ($page->bbit_st === null) {
            return;
        }

        if ($this->openGraphData) {
            $GLOBALS['TL_HEAD'][] = $this->openGraphData->getProtocol()->getMetaTags();
        }
    }

    private function __clone()
    {
    }
}