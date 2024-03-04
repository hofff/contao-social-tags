<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Action;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use function assert;
use function urlencode;

final class FacebookLintAction
{
    private const FACEBOOK_LINT_URL = 'https://developers.facebook.com/tools/debug/?q=';

    public function __construct(private ContaoFramework $framework)
    {
    }

    public function __invoke(int $pageId): Response
    {
        $this->framework->initialize();

        $pageModel = $this->framework->getAdapter(PageModel::class)->findWithDetails($pageId);
        assert($pageModel instanceof PageModel || $pageModel === null);
        $target = self::FACEBOOK_LINT_URL;

        if ($pageModel) {
            $target .= urlencode($pageModel->getAbsoluteUrl());
        }

        return new RedirectResponse($target);
    }
}
