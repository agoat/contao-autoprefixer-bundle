<?php

/*
 * Autoprefixer plugin for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-autoprefixer
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

//$GLOBALS['TL_DCA']['tl_layout']['config']['onsubmit_callback'] = array(
//    function ($dca) {
//        dump($dca);
//    }
//);


// Palettes
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace('combineScripts', 'combineScripts,autoprefix', $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_layout']['palettes']['__selector__'][] = 'autoprefix';
$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['autoprefix'] = 'browsers,flex,grid,remove,supports';

// Fields
$GLOBALS['TL_DCA']['tl_layout']['fields']['autoprefix'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_layout']['autoprefix'],
	'exclude'       => true,
	'inputType'     => 'checkbox',
	'eval'          => array('submitOnChange'=>true, 'tl_class'=>'clr w50 m12'),
	'sql'           => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['browsers'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_layout']['browsers'],
    'default'       => 'last 2 versions',
    'exclude'       => true,
	'inputType'     => 'text',
	'eval'          => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'           => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['flex'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_layout']['flex'],
    'exclude'       => true,
    'inputType'     => 'select',
    'options'       => array('true', 'no-2009', 'false'),
    'reference'     => &$GLOBALS['TL_LANG']['tl_layout']['flexOptions'],
    'eval'          => array('tl_class'=>'clr w50'),
    'sql'           => "varchar(8) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['grid'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_layout']['grid'],
    'exclude'       => true,
    'inputType'     => 'select',
    'options'       => array('false', 'autoplace', 'no-autoplace'),
    'reference'     => &$GLOBALS['TL_LANG']['tl_layout']['gridOptions'],
    'eval'          => array('tl_class'=>'w50'),
    'sql'           => "varchar(16) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['remove'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_layout']['remove'],
    'default'       => true,
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => array('tl_class'=>'w50 m12'),
    'sql'           => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['supports'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_layout']['supports'],
    'default'       => true,
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => array('tl_class'=>'w50 m12'),
    'sql'           => "char(1) NOT NULL default ''"
);
