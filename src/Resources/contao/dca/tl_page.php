<?php

declare(strict_types=1);

use Hofff\Contao\SocialTags\Action\FacebookLintAction;

$this->loadLanguageFile('hofff_st');

$GLOBALS['TL_DCA']['tl_page']['list']['operations']['hofff_st_facebookLint'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_page']['hofff_st_facebookLint'],
    'icon'       => 'bundles/hofffcontaosocialtags/html/images/og.png',
    'href'       => 'key=hofff_st_facebookLint',
    'attributes' => ' onclick="window.open(this.href); return false;"',
];

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'hofff_st';

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
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['hofff_st_hofff_st_tree'] = 'hofff_st_type'
    . ',hofff_st_title,hofff_st_site'
    . ',hofff_st_url'
    . ',hofff_st_image,hofff_st_imageSize'
    . ',hofff_st_description';// . ',hofff_st_curies,hofff_st_custom'

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
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_type'] = [
    'label'     => &$GLOBALS['TL_LANG']['hofff_st']['type'],
    'exclude'   => true,
    'inputType' => 'select',
    'default'   => 'website',
    'options'   => FacebookLintAction::getInstance()->getTypeOptions(),
    'reference' => &$GLOBALS['TL_LANG']['hofff_st']['types'],
    'eval'      => [
        'mandatory'      => true,
        'chosen'         => true,
        'submitOnChange' => true,
        'tl_class'       => 'w50',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_title'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['hofff_st_title'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'clr w50',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_site'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['hofff_st_site'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'w50',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_url'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['hofff_st_url'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 1022,
        'decodeEntities' => true,
        'rgxp'           => 'url',
        'tl_class'       => 'clr long',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_image'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['hofff_st_image'],
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
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_imageSize'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['hofff_st_imageSize'],
    'exclude'   => true,
    'inputType' => 'imageSize',
    'options'   => $GLOBALS['TL_CROP'],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval'      => [
        'rgxp'       => 'digit',
        'nospace'    => true,
        'helpwizard' => true,
        'tl_class'   => 'clr w50',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_st_description'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['hofff_st_description'],
    'exclude'   => true,
    'inputType' => 'textarea',
    'eval'      => [
        'style'    => 'height: 60px;',
        'tl_class' => 'clr',
    ],
];
