<?php

$this->loadLanguageFile('bbit_st');

$GLOBALS['TL_DCA']['tl_page']['list']['operations']['bbit_st_facebookLint'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_page']['bbit_st_facebookLint'],
    'icon'       => 'bundles/hofffcontaosocialtags/html/images/og.png',
    'href'       => 'key=bbit_st_facebookLint',
    'attributes' => ' onclick="window.open(this.href); return false;"',
];

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'bbit_st';

foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $strKey => &$strPalette) {
    if ($strKey != '__selector__') {
        $strPalette = preg_replace(
            '@(\{meta_legend\}[^;]*;)@',
            '$1{bbit_st_legend},bbit_st;',
            $strPalette
        );
    }
}

$GLOBALS['TL_DCA']['tl_page']['subpalettes']['bbit_st_bbit_st_page'] =
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['bbit_st_bbit_st_tree']
    = 'bbit_st_type'
    . ',bbit_st_title,bbit_st_site'
    . ',bbit_st_url'
    . ',bbit_st_image,bbit_st_imageSize'
    . ',bbit_st_description'//	. ',bbit_st_curies,bbit_st_custom'
;


$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_st'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_st'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => [
        'bbit_st_page',
        'bbit_st_tree',
        'bbit_st_parent',
        'bbit_st_root',
        'bbit_st_disablePage',
        'bbit_st_disableTree',
    ],
    'reference' => &$GLOBALS['TL_LANG']['tl_page']['bbit_stOptions'],
    'eval'      => [
        'chosen'             => true,
        'submitOnChange'     => true,
        'includeBlankOption' => true,
        'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_page']['bbit_stOptions'][''],
        'tl_class'           => '',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_st_type'] = [
    'label'     => &$GLOBALS['TL_LANG']['bbit_st']['type'],
    'exclude'   => true,
    'inputType' => 'select',
    'default'   => 'website',
    'options'   => ContaoOpenGraphBackend::getInstance()->getTypeOptions(),
    'reference' => &$GLOBALS['TL_LANG']['bbit_st']['types'],
    'eval'      => [
        'mandatory'      => true,
        'chosen'         => true,
        'submitOnChange' => true,
        'tl_class'       => 'w50',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_st_title'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_st_title'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'clr w50',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_st_site'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_st_site'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 255,
        'decodeEntities' => true,
        'tl_class'       => 'w50',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_st_url'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_st_url'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'maxlength'      => 1022,
        'decodeEntities' => true,
        'rgxp'           => 'url',
        'tl_class'       => 'clr long',
    ],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_st_image'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_st_image'],
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

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_st_imageSize'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_st_imageSize'],
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

$GLOBALS['TL_DCA']['tl_page']['fields']['bbit_st_description'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['bbit_st_description'],
    'exclude'   => true,
    'inputType' => 'textarea',
    'eval'      => [
        'style'    => 'height: 60px;',
        'tl_class' => 'clr',
    ],
];
