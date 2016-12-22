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

namespace Agoat\AutoPrefixer;

use Contao\Template;
use Agoat\AutoPrefixer\AutoCombiner;


class Controller extends \Contao\Controller
{

	/**
	 * Replace the TL_CSS script tag and use the autoprefixer for vendor prefixes
	 *
	 * @param string $strBuffer The string with the tags to be replaced
	 *
	 * @return string The string with the replaced tags
	 */	
	public function generatePrefixedCSS($strBuffer) 
	{ 
		// Return the combined css if exist
		if (is_string($GLOBALS['TL_TEMPLATE_CSS']))
		{
			return str_replace('[[TL_CSS]]', $GLOBALS['TL_TEMPLATE_CSS'], $strBuffer);
		}

		// PageModel needed
		if (!isset($GLOBALS['objPage']))
		{
			return $strBuffer;
		}
		
		$objLayout = \LayoutModel::findByPk($GLOBALS['objPage']->layoutId);
		$blnCombineScripts = ($objLayout === null) ? false : $objLayout->combineScripts;

		// Autoprefix in this layout activated??
		if (!$objLayout->autoprefix)
		{
			return $strBuffer;
		}

		// Use the AutoCombiner for CSS-Files
		$objCombiner = new AutoCombiner();

		
		// Add the CSS framework style sheets
		if (!empty($GLOBALS['TL_FRAMEWORK_CSS']) && is_array($GLOBALS['TL_FRAMEWORK_CSS']))
		{
			foreach (array_unique($GLOBALS['TL_FRAMEWORK_CSS']) as $stylesheet)
			{
				$objCombiner->add($stylesheet);
			}
		}

		// Add the internal style sheets
		if (!empty($GLOBALS['TL_CSS']) && is_array($GLOBALS['TL_CSS']))
		{
			foreach (array_unique($GLOBALS['TL_CSS']) as $stylesheet)
			{
				$options = \StringUtil::resolveFlaggedUrl($stylesheet);

				if ($options->static)
				{
					if ($options->mtime === null)
					{
						$options->mtime = filemtime(TL_ROOT . '/' . $stylesheet);
					}

					$objCombiner->add($stylesheet, $options->mtime, $options->media);
				}
				else
				{
					$strScripts .= Template::generateStyleTag(static::addStaticUrlTo($stylesheet), $options->media) . "\n";
				}
			}
		}

		// Add the user style sheets
		if (!empty($GLOBALS['TL_USER_CSS']) && is_array($GLOBALS['TL_USER_CSS']))
		{
			foreach (array_unique($GLOBALS['TL_USER_CSS']) as $stylesheet)
			{
				$options = \StringUtil::resolveFlaggedUrl($stylesheet);

				if ($options->static)
				{
					$objCombiner->add($stylesheet, $options->mtime, $options->media);
				}
				else
				{
					$strScripts .= Template::generateStyleTag(static::addStaticUrlTo($stylesheet), $options->media) . "\n";
				}
			}
		}

		// Create the aggregated style sheet
		if ($objCombiner->hasEntries())
		{
			if ($blnCombineScripts)
			{
				$strScripts .= Template::generateStyleTag($objCombiner->getCombinedFile(), 'all') . "\n";
			}
			else
			{
				foreach ($objCombiner->getFileUrls() as $strUrl)
				{
					$strScripts .= Template::generateStyleTag($strUrl, 'all') . "\n";
				}
			}
		}

		
		// save to a global
		$GLOBALS['TL_TEMPLATE_CSS'] = $strScripts;

		// empty other CSS globals
		$GLOBALS['TL_FRAMEWORK_CSS'] = false;
		$GLOBALS['TL_CSS'] = false;
		$GLOBALS['TL_USER_CSS'] = false;
		
		return str_replace('[[TL_CSS]]', $GLOBALS['TL_TEMPLATE_CSS'], $strBuffer);

	}


}

