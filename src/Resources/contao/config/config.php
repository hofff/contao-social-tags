<?php

declare(strict_types=1);

use Hofff\Contao\SocialTags\EventListener\Hook\SocialTagsInjectionListener;

$GLOBALS['BE_MOD']['design']['page']['hofff_st_facebookLint'] = ['Hofff\Contao\SocialTags\Action\FacebookLintAction', 'keyFacebookLint'];

$GLOBALS['TL_HOOKS']['generatePage'][] = [
    SocialTagsInjectionListener::class,
    'onGeneratePage',
];

$GLOBALS['hofff_st']['TYPES'][] = 'website';
$GLOBALS['hofff_st']['TYPES'][] = 'article';
$GLOBALS['hofff_st']['TYPES'][] = 'profile';
$GLOBALS['hofff_st']['TYPES'][] = 'book';
$GLOBALS['hofff_st']['TYPES'][] = 'music.song';
$GLOBALS['hofff_st']['TYPES'][] = 'music.album';
$GLOBALS['hofff_st']['TYPES'][] = 'music.playlist';
$GLOBALS['hofff_st']['TYPES'][] = 'music.radio_station';
$GLOBALS['hofff_st']['TYPES'][] = 'video.movie';
$GLOBALS['hofff_st']['TYPES'][] = 'video.episode';
$GLOBALS['hofff_st']['TYPES'][] = 'video.tv_show';
$GLOBALS['hofff_st']['TYPES'][] = 'video.other';
