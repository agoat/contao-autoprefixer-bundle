<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2016 Leo Feyer
 *
 * @package  	 AutoPrefixer
 * @author   	 Arne Stappen
 * @license  	 LGPL-3.0+ 
 * @copyright	 Arne Stappen 2016
 */
 
 

// palettes
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace('loadingOrder', 'loadingOrder,autoprefix', $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_layout']['palettes']['__selector__'][] = 'autoprefix';
$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['autoprefix'] = 'browsers';

// fields
$GLOBALS['TL_DCA']['tl_layout']['fields']['autoprefix'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['autoprefix'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['browsers'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['browsers'],
	'exclude'                 => true,
	'default'                 => 'last 2 versions',
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'save_callback'   	  => array(array('tl_layout_autoprefixer', 'checkBrowserList')),
	'sql'                     => "varchar(255) NOT NULL default ''"
);


	
	
class tl_layout_autoprefixer extends tl_layout
{
	public function checkBrowserList ($varValue)
	{
		// if nothing entered set to default value
		return (empty($varValue)) ? 'last 2 versions' : $varValue;
	}
}

