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


// Palettes
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace('combineScripts', 'combineScripts,autoprefix', $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_layout']['palettes']['__selector__'][] = 'autoprefix';
$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['autoprefix'] = 'browsers';

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
	'default'       => 'last 2 versions',
	'inputType'     => 'text',
	'eval'          => array('maxlength'=>255, 'tl_class'=>'w50'),
	'save_callback' => array(
        array('tl_layout_autoprefixer', 'checkBrowserList')
    ),
	'sql'           => "varchar(255) NOT NULL default ''"
);
	
	
/**
 * Provide methods that are used by the data configuration array.
 */
class tl_layout_autoprefixer extends tl_layout
{
	/**
	 * Set to the default value (last 2 versions) if value is empty
	 *
	 * @param string $value Browserslist query
	 *
	 * @return string $value Browserslist query
	 */
	public function checkBrowserList ($value)
	{
		return (empty($value)) ? $GLOBALS['TL_DCA']['tl_layout']['fields']['browsers']['default'] : $value;
	}
}

