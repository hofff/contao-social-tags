<?php

declare(strict_types=1);

use Hofff\Contao\SocialTags\EventListener\Hook\FaqReaderListener;
use Hofff\Contao\SocialTags\EventListener\Hook\NewsReaderListener;
use Hofff\Contao\SocialTags\EventListener\Hook\PageSocialTagsListener;
use Hofff\Contao\SocialTags\EventListener\Hook\SocialTagsDataInjectionListener;

$GLOBALS['TL_HOOKS']['generatePage'][] = [
    PageSocialTagsListener::class,
    'onGeneratePage',
];

$GLOBALS['TL_HOOKS']['generatePage'][] = [
    SocialTagsDataInjectionListener::class,
    'onGeneratePage',
];

$GLOBALS['TL_HOOKS']['getContentElement'][] = [
    NewsReaderListener::class,
    'onGetContentElement',
];

$GLOBALS['TL_HOOKS']['getFrontendModule'][] = [
    NewsReaderListener::class,
    'onGetFrontendModule',
];

$GLOBALS['TL_HOOKS']['getFrontendModule'][] = [
    FaqReaderListener::class,
    'onGetFrontendModule',
];
