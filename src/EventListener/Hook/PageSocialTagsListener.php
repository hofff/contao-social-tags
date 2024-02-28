<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Contao\PageModel;
use Hofff\Contao\SocialTags\Data\SocialTagsFactory;
use Symfony\Component\HttpFoundation\RequestStack;

final class PageSocialTagsListener extends SocialTagsDataAwareListener
{
    public function __construct(RequestStack $requestStack, private readonly SocialTagsFactory $factory)
    {
        parent::__construct($requestStack);
    }

    public function onGeneratePage(PageModel $page): void
    {
        if ($page->hofff_st === null || $this->getSocialTagsData()) {
            return;
        }

        $this->setSocialTagsData($this->factory->generateByPageId((int) $page->id));
    }
}
