<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Contao\PageModel;
use Hofff\Contao\SocialTags\Data\SocialTagsFactory;
use Symfony\Component\HttpFoundation\RequestStack;

final class PageSocialTagsListener extends SocialTagsDataAwareListener
{
    /** @var SocialTagsFactory */
    private $factory;

    public function __construct(RequestStack $requestStack, SocialTagsFactory $factory)
    {
        parent::__construct($requestStack);

        $this->factory = $factory;
    }

    public function onGeneratePage(PageModel $page): void
    {
        if ($page->hofff_st === null || $this->getSocialTagsData()) {
            return;
        }

        $this->setSocialTagsData($this->factory->generateByPageId((int) $page->id));
    }
}
