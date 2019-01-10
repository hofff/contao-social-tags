<?php

$GLOBALS['BE_MOD']['design']['page']['bbit_st_facebookLint'] = ['Hofff\Contao\SocialTags\Action\FacebookLintAction', 'keyFacebookLint'];

$GLOBALS['TL_HOOKS']['generatePage'][] = [
    \Hofff\Contao\SocialTags\EventListener\Hook\SocialTagsInjectionListener::class,
    'onGeneratePage',
];

$GLOBALS['bbit_st']['TYPES'][] = 'website';
$GLOBALS['bbit_st']['TYPES'][] = 'article';
$GLOBALS['bbit_st']['TYPES'][] = 'profile';
$GLOBALS['bbit_st']['TYPES'][] = 'book';
$GLOBALS['bbit_st']['TYPES'][] = 'music.song';
$GLOBALS['bbit_st']['TYPES'][] = 'music.album';
$GLOBALS['bbit_st']['TYPES'][] = 'music.playlist';
$GLOBALS['bbit_st']['TYPES'][] = 'music.radio_station';
$GLOBALS['bbit_st']['TYPES'][] = 'video.movie';
$GLOBALS['bbit_st']['TYPES'][] = 'video.episode';
$GLOBALS['bbit_st']['TYPES'][] = 'video.tv_show';
$GLOBALS['bbit_st']['TYPES'][] = 'video.other';
