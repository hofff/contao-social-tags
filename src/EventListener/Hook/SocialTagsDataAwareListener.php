<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Hofff\Contao\SocialTags\Data\Data;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class SocialTagsDataAwareListener
{
    public function __construct(protected RequestStack $requestStack)
    {
    }

    protected function getSocialTagsData(): Data|null
    {
        $request = $this->requestStack->getMainRequest();
        if (! $request) {
            return null;
        }

        if (! $request->attributes->has(Data::class)) {
            return null;
        }

        $openGraphData = $request->attributes->get(Data::class);
        if ($openGraphData instanceof Data) {
            return $openGraphData;
        }

        return null;
    }

    protected function setSocialTagsData(Data $openGraphData): void
    {
        $request = $this->requestStack->getMainRequest();
        if (! $request) {
            // Silently break. Shouldn't be the case for a regular call
            return;
        }

        $request->attributes->set(Data::class, $openGraphData);
    }
}
