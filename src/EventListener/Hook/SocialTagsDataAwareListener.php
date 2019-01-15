<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Hofff\Contao\SocialTags\OpenGraph\OpenGraphData;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class SocialTagsDataAwareListener
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    protected function getSocialTagsData() : ?OpenGraphData
    {
        $request = $this->requestStack->getMasterRequest();
        if (! $request) {
            return null;
        }

        if (! $request->attributes->has(OpenGraphData::class)) {
            return null;
        }

        $openGraphData = $request->attributes->get(OpenGraphData::class);
        if ($openGraphData instanceof OpenGraphData) {
            return $openGraphData;
        }

        return null;
    }

    protected function setSocialTagsData(OpenGraphData $openGraphData) : void
    {
        $request = $this->requestStack->getMasterRequest();
        if (! $request) {
            // Silently break. Shouldn't be the case for a regular call
            return;
        }

        $request->attributes->set(OpenGraphData::class, $openGraphData);
    }
}
