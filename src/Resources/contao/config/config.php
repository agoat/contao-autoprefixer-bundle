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


/**
 * HOOKS
 */
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags'][] = array('Agoat\\AutoPrefixer\\Controller', 'generatePrefixedCSS'); 

