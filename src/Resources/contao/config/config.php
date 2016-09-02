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
 

/**
 * HOOKS
 *
 */
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags'][] = array('Agoat\\AutoPrefixer\\Controller', 'generatePrefixedCSS'); 
 


