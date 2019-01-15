<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Contao\PageModel;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphFactory;
use Symfony\Component\HttpFoundation\RequestStack;

final class PageSocialTagsListener extends SocialTagsDataAwareListener
{
    /** @var OpenGraphFactory */
    private $factory;

    public function __construct(RequestStack $requestStack, OpenGraphFactory $factory)
    {
        parent::__construct($requestStack);

        $this->factory = $factory;
    }

    public function onGeneratePage(PageModel $page) : void
    {
        if ($page->hofff_st === null || $this->getSocialTagsData()) {
            return;
        }

        $this->setSocialTagsData($this->factory->generateBasicDataByPageID((int) $page->id));
    }
}
