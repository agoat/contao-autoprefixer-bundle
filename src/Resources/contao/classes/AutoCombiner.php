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

use Contao\Combiner;
use Agoat\AutoPrefixer\AutoPrefixer;


/**
 * Combines .css files into one single file and add vendor prefixes
 *
 * Usage:
 *
 *     $combiner = new AutoCombiner();
 *
 *     $combiner->add('css/style.css');
 *     $combiner->add('css/fonts.scss');
 *     $combiner->add('css/print.less');
 *
 *     echo $combiner->getCombinedFile();
 *
 */
class AutoCombiner extends Combiner
{

	protected $autoprefixer = null;
	
	/**
	 * Generate the combined file, add vendor prefixes with autoprefixer and return its path
	 *
	 * @param string $strUrl An optional URL to prepend
	 *
	 * @return string The path to the combined file
	 */
	public function getCombinedFile($strUrl=null)
	{
		// Prepare autoprefixer
		if ($GLOBALS['objPage'])
		{
			$objLayout = \LayoutModel::findByPk($GLOBALS['objPage']->layoutId);

			// prepare browser list
			$browsers = explode(',', $objLayout->browsers);
			array_walk($browsers, function (&$value) { $value = trim(html_entity_decode($value)); });

			$this->autoprefixer = new AutoPrefixer($browsers);
		}

		if (\Config::get('debugMode'))
		{
			return $this->getDebugMarkup();
		}

		return $this->getCombinedFileUrl($strUrl);
	}


	/**
	 * Generates the debug markup.
	 *
	 * @return string The debug markup
	 */
	protected function getDebugMarkup()
	{
		$return = $this->getFileUrls();

		if ($this->strMode == self::JS)
		{
			return implode('"></script><script src="', $return);
		}
		else
		{
			return implode('"><link rel="stylesheet" href="', $return);
		}
	}

	/**
	 * Generates the files, add vendor prefixes and returns the URLs.
	 *
	 * @return array The file URLs
	 */
	public function getFileUrls()
	{
		$return = array();
		$strTarget = substr($this->strMode, 1);

		foreach ($this->arrFiles as $arrFile)
		{
			$content = file_get_contents(TL_ROOT . '/' . $arrFile['name']);

			// Compile SCSS/LESS files into temporary files
			if ($arrFile['extension'] == self::SCSS || $arrFile['extension'] == self::LESS)
			{
				$strPath = 'assets/' . $strTarget . '/' . str_replace('/', '_', $arrFile['name']) . $this->strMode;

				$objFile = new \File($strPath);
				if ($this->autoprefixer !== null)
				{
					$objFile->write($this->autoprefixer->rewrite($this->handleScssLess($content, $arrFile)));
				}
				else
				{
					$objFile->write($this->handleScssLess($content, $arrFile));
				}
				$objFile->close();

				$return[] = $strPath;
			}
			// Compile CSS files into temporary files
			else if ($arrFile['extension'] == self::CSS)
			{
				$strPath = 'assets/' . $strTarget . '/' . str_replace('/', '_', $arrFile['name']) . $this->strMode;

				$objFile = new \File($strPath);
				if ($this->autoprefixer !== null && strpos($arrFile['name'], 'assets/contao') === false) // exclude framework css
				{
					$objFile->write($this->autoprefixer->rewrite($this->handleCss($content, $arrFile)));
				}
				else
				{
					$objFile->write($this->handleCss($content, $arrFile));
				}
				$objFile->close();

				$return[] = $strPath;
			}
			else
			{
				$name = $arrFile['name'];

				// Strip the web/ prefix (see #328)
				if (strncmp($name, 'web/', 4) === 0)
				{
					$name = substr($name, 4);
				}

				// Add the media query (see #7070)
				if ($arrFile['media'] != '' && $arrFile['media'] != 'all' && strpos($content, '@media') === false)
				{
					$name .= '" media="' . $arrFile['media'];
				}

				$return[] = $name;
			}
		}

		return $return;
	}

	/**
	 * Generate the combined file, add vendor prefixes and return its path
	 *
	 * @param string $strUrl An optional URL to prepend
	 *
	 * @return string The path to the combined file
	 */
	protected function getCombinedFileUrl($strUrl=null)
	{
		if ($strUrl === null)
		{
			$strUrl = TL_ASSETS_URL;
		}

		$strTarget = substr($this->strMode, 1);
		$strKey = substr(md5($this->strKey), 0, 12);

		// Load the existing file
		if (file_exists(TL_ROOT . '/assets/' . $strTarget . '/' . $strKey . $this->strMode))
		{
			return $strUrl . 'assets/' . $strTarget . '/' . $strKey . $this->strMode;
		}

		// Create the file
		$objFile = new \File('assets/' . $strTarget . '/' . $strKey . $this->strMode);
		$objFile->truncate();
		$strFile = '';

		foreach ($this->arrFiles as $arrFile)
		{
			$content = file_get_contents(TL_ROOT . '/' . $arrFile['name']);

			// HOOK: modify the file content
			if (isset($GLOBALS['TL_HOOKS']['getCombinedFile']) && is_array($GLOBALS['TL_HOOKS']['getCombinedFile']))
			{
				foreach ($GLOBALS['TL_HOOKS']['getCombinedFile'] as $callback)
				{
					$this->import($callback[0]);
					$content = $this->{$callback[0]}->{$callback[1]}($content, $strKey, $this->strMode, $arrFile);
				}
			}

			if ($arrFile['extension'] == self::CSS)
			{
				$content = $this->handleCss($content, $arrFile);
			}
			elseif ($arrFile['extension'] == self::SCSS || $arrFile['extension'] == self::LESS)
			{
				$content = $this->handleScssLess($content, $arrFile);
			}

			$strFile .= $content;
		}

		// add vendor prefixes in the combined file
		if ($this->autoprefixer !== null)
		{
			$objFile->write($this->autoprefixer->rewrite($strFile));
		}
		else
		{
			$objFile->write($strFile);
		}
		$objFile->close();

		unset($content);

		// Create a gzipped version
		if (\Config::get('gzipScripts') && function_exists('gzencode'))
		{
			\File::putContent('assets/' . $strTarget . '/' . $strKey . $this->strMode . '.gz', gzencode(file_get_contents(TL_ROOT . '/assets/' . $strTarget . '/' . $strKey . $this->strMode), 9));
		}

		return $strUrl . 'assets/' . $strTarget . '/' . $strKey . $this->strMode;
	}

}
