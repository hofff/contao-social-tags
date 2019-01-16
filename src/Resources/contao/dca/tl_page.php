<?php

declare(strict_types=1);

use Hofff\Contao\SocialTags\EventListener\Dca\PageDcaListener;

$this->loadLanguageFile('hofff_st');

$GLOBALS['TL_DCA']['tl_page']['list']['operations']['hofff_st_og_facebookLint'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_page']['hofff_st_og_facebookLint'],
    'icon'       => 'bundles/hofffcontaosocialtags/images/og.png',
    'attributes' => ' onclick="window.open(this.href); return false;"',
    'button_callback' => [PageDcaListener::class, 'facebookLinkButton'],
];

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'hofff_st';
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'hofff_st_twitter_type';

foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $strKey => &$strPalette) {
    if ($strKey === '__selector__') {
        continue;
    }

    $strPalette = preg_replace(
        '@(\{meta_legend\}[^;]*;)@',
        '$1{hofff_st_legend},hofff_st;',
        $strPalette
    );
}

$GLOBALS['TL_DCA']['tl_page']['subpalettes']['hofff_st_hofff_st_page'] =
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['hofff_st_hofff_st_tree'] = 'hofff_st_og_type'
    . ',hofff_st_og_title,hofff_st_og_site'
    . ',hofff_st_og_url'
    . ',hofff_st_og_image'
    . ',hofff_st_og_description'
    . ',hofff_st_twitter_type';

$GLOBALS['TL_DCA']['tl_page']['subpalettes']['hofff_st_twitter_type_hofff_st_twitter_summary'] =
    ',hofff_st_twitter_title'
    . ',hofff_st_twitter_image'
    . ',hofff_st_twitter_site'
    . ',hofff_st_twitter_description';

$GLOBALS['TL_DCA']['tl_page']['subpalettes']['hofff_st_twitter_type_hofff_st_twitter_summary_large_image'] =
    ',hofff_st_twitter_title'
    . ',hofff_st_twitter_image'
    . ',hofff_st_twitter_site'
    . ',hofff_st_twitter_creator'
    . ',hofff_st_twitter_description';

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['hofff_st'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => [
        'hofff_st_page',
        'hofff_st_tree',
        'hofff_st_parent',
        'hofff_st_root',
        'hofff_st_disablePage',
        'hofff_st_disableTree',
    ],
    'reference' => &$GLOBALS['TL_LANG']['tl_page']['hofff_stOptions'],
    'eval'      => [
        'chosen'             => true,
        'submitOnChange'     => true,
        'includeBlankOption' => true,
        'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_page']['hofff_stOptions'][''],
        'tl_class'           => '',
    ],
    'sql' => 'varchar(255) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_og_type'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['type'],
    'exclude'   => true,
    'inputType' => 'select',
    'default'   => 'website',
    'options_callback'   => [PageDcaListener::class, 'openGraphTypeOptions'],
    'reference' => &$GLOBALS['TL_LANG']['hofff_st']['types'],
    'eval'      => [
        'mandatory'      => true,
        'chosen'         => true,
        'submitOnChange' => true,
        'tl_class'       => 'w50',
    ],
    'sql' => 'varchar(255) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_og_title'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_og_title'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'clr w50',
    ],
    'sql' => 'varchar(255) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_og_site'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_og_site'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'w50',
    ],
    'sql' => 'varchar(255) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_og_url'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_og_url'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 1022,
        'decodeEntities' => true,
        'rgxp'           => 'url',
        'tl_class'       => 'clr long',
    ],
    'sql' => 'varchar(1022) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_og_image'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_og_image'],
    'exclude'   => true,
    'inputType' => 'fileTree',
    'eval'      => [
        'mandatory'  => true,
        'files'      => true,
        'fieldType'  => 'radio',
        'extensions' => 'gif,jpg,jpeg,png',
        'filesOnly'  => true,
        'tl_class'   => 'clr',
    ],
    'sql' => 'binary(16) NULL',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_og_description'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_og_description'],
    'exclude'   => true,
    'inputType' => 'textarea',
    'eval'      => [
        'style'    => 'height: 60px;',
        'tl_class' => 'clr',
    ],
    'sql' => 'varchar(1022) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_twitter_type'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_twitter_type'],
    'exclude'   => true,
    'inputType' => 'select',
    'default'   => 'website',
    'options'   => ['hofff_st_twitter_summary', 'hofff_st_twitter_summary_large_image'],
    'reference' => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_twitter_types'],
    'eval'      => [
        'mandatory'      => true,
        'chosen'         => true,
        'submitOnChange' => true,
        'tl_class'       => 'w50',
    ],
    'sql' => 'varchar(255) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_twitter_title'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_twitter_title'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'clr w50',
    ],
    'sql' => 'varchar(255) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_twitter_description'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_twitter_description'],
    'exclude'   => true,
    'inputType' => 'textarea',
    'eval'      => [
        'style'    => 'height: 60px;',
        'tl_class' => 'clr',
    ],
    'sql' => 'varchar(1022) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_twitter_site'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_twitter_site'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'clr w50',
    ],
    'sql' => 'varchar(255) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_twitter_creator'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_twitter_creator'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'w50',
    ],
    'sql' => 'varchar(255) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_twitter_image'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['hofff_st_twitter_image'],
    'exclude'   => true,
    'inputType' => 'fileTree',
    'eval'      => [
        'mandatory'  => true,
        'files'      => true,
        'fieldType'  => 'radio',
        'extensions' => 'gif,jpg,jpeg,png',
        'filesOnly'  => true,
        'tl_class'   => 'clr',
    ],
    'sql' => 'binary(16) NULL',
];
