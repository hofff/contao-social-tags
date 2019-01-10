<?php

$this->loadLanguageFile('bbit_og');

$GLOBALS['TL_DCA']['tl_page']['list']['operations']['bbit_og_facebookLint'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_page']['bbit_og_facebookLint'],
    'icon'       => 'system/modules/backboneit_opengraph/html/images/og.png',
    'href'       => 'key=bbit_og_facebookLint',
    'attributes' => ' onclick="window.open(this.href); return false;"',
];

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'bbit_og';

foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $strKey => &$strPalette) {
    if ($strKey != '__selector__') {
        $strPalette = preg_replace(
            '@(\{meta_legend\}[^;]*;)@',
            '$1{bbit_og_legend},bbit_og;',
            $strPalette
        );
    }
}

$GLOBALS['TL_DCA']['tl_page']['subpalettes']['bbit_og_bbit_og_page'] =
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['bbit_og_bbit_og_tree']
    = 'bbit_og_type'
    . ',bbit_og_title,bbit_og_site'
    . ',bbit_og_url'
    . ',bbit_og_image,bbit_og_imageSize'
    . ',bbit_og_description'//	. ',bbit_og_curies,bbit_og_custom'
;


$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_og'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_og'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => [
        'bbit_og_page',
        'bbit_og_tree',
        'bbit_og_parent',
        'bbit_og_root',
        'bbit_og_disablePage',
        'bbit_og_disableTree',
    ],
    'reference' => &$GLOBALS['TL_LANG']['tl_page']['bbit_ogOptions'],
    'eval'      => [
        'chosen'             => true,
        'submitOnChange'     => true,
        'includeBlankOption' => true,
        'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_page']['bbit_ogOptions'][''],
        'tl_class'           => '',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_og_type'] = [
    'label'     => &$GLOBALS['TL_LANG']['bbit_og']['type'],
    'exclude'   => true,
    'inputType' => 'select',
    'default'   => 'website',
    'options'   => ContaoOpenGraphBackend::getInstance()->getTypeOptions(),
    'reference' => &$GLOBALS['TL_LANG']['bbit_og']['types'],
    'eval'      => [
        'mandatory'      => true,
        'chosen'         => true,
        'submitOnChange' => true,
        'tl_class'       => 'w50',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_og_title'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_og_title'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'clr w50',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_og_site'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_og_site'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'w50',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_og_url'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_og_url'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 1022,
        'decodeEntities' => true,
        'rgxp'           => 'url',
        'tl_class'       => 'clr long',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_og_image'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_og_image'],
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

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_og_imageSize'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_og_imageSize'],
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

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_og_description'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_og_description'],
    'exclude'   => true,
    'inputType' => 'textarea',
    'eval'      => [
        'style'    => 'height: 60px;',
        'tl_class' => 'clr',
    ],
];
