<?php

/*
 * Autoprefixer plugin for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2021
 * @package    contao-autoprefixer
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */


// Palettes
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace('combineScripts',
    'combineScripts,autoprefix',
    $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']
);
$GLOBALS['TL_DCA']['tl_layout']['palettes']['__selector__'][] = 'autoprefix';
$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['autoprefix'] = 'browsers,flex,grid,remove,supports';

// Fields
$GLOBALS['TL_DCA']['tl_layout']['fields']['autoprefix'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['autoprefix'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_layout']['fields']['browsers'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['browsers'],
    'default' => 'defaults',
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_layout']['fields']['flex'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['flex'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['true', 'no-2009', 'false'],
    'reference' => &$GLOBALS['TL_LANG']['tl_layout']['flexOptions'],
    'eval' => ['tl_class' => 'clr w50'],
    'sql' => "varchar(8) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_layout']['fields']['grid'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['grid'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['false', 'autoplace', 'no-autoplace'],
    'reference' => &$GLOBALS['TL_LANG']['tl_layout']['gridOptions'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => "varchar(16) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_layout']['fields']['remove'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['remove'],
    'default' => true,
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_layout']['fields']['supports'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['supports'],
    'default' => true,
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];
