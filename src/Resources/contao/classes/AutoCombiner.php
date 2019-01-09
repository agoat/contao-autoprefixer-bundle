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

namespace Agoat\AutoPrefixerBundle\Contao;

use Contao\Combiner;


/**
 * Combines .css files into one single file and add vendor prefixes
 *
 * Usage:
 *
 *     $combiner = new AutoCombiner();
 *
 *     $combiner->add('css/style.css');
 *     $combiner->add('scss/layout.scss');
 *     ...
 *
 *     $prefixedfile = $combiner->getCombinedFile();
 *     $prefixedfiles = $combiner->getFileUrls();
 *
 */
class AutoCombiner extends Combiner
{
    /**
     * Autoprefixer
     * @var null|class
     */
	protected $autoprefixer = null;
	

	/**
	 * Public constructor
	 *
	 * Prepares the autoprefixer class
	 */
	public function __construct()
	{
		// Prepare autoprefixer
		if ($GLOBALS['objPage'])
		{
			$objLayout = \LayoutModel::findByPk($GLOBALS['objPage']->layoutId);

			// Prepare browser list
			$browsers = explode(',', $objLayout->browsers);
			array_walk($browsers, function (&$value) {
			    $value = trim(html_entity_decode($value));
			});

            // Prepare flex/grid options
            $flex = $objLayout->flex == 'true' || $objLayout->flex == 'false' ?
                (bool) $objLayout->flex : $flex = $objLayout->flex;

            $grid = $objLayout->grid == 'false' ? $grid = false : $grid = $objLayout->grid;

            // Prepare other options
            $remove = (bool) $objLayout->remove;
            $supports = (bool) $objLayout->supports;

			$this->autoprefixer = new AutoPrefixer($browsers, $flex, $grid, $remove, $supports);
		}
		
		parent::__construct();
	}

	
	/**
	 * Generates the files, add vendor prefixes and returns the URLs.
	 *
	 * @return array The file Urls
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

				if (\Config::get('debugMode') || !file_exists(TL_ROOT . '/' . $strPath))
				{
					$objFile = new \File($strPath);

					if ($this->autoprefixer)
					{
						$objFile->write($this->autoprefixer->rewrite($this->handleScssLess(file_get_contents(TL_ROOT . '/' . $arrFile['name']), $arrFile)));
					}
					else
					{
						$objFile->write($this->handleScssLess(file_get_contents(TL_ROOT . '/' . $arrFile['name']), $arrFile));
					}
					
					$objFile->close();
				}

				$return[] = $strPath;
			}
			
			// Compile CSS files into temporary files
			else if ($arrFile['extension'] == self::CSS)
			{
				$strPath = 'assets/' . $strTarget . '/' . str_replace('/', '_', $arrFile['name']) . $this->strMode;

				if (\Config::get('debugMode') || !file_exists(TL_ROOT . '/' . $strPath))
				{
					$objFile = new \File($strPath);
					
					if ($this->autoprefixer)
					{
						$objFile->write($this->autoprefixer->rewrite($this->handleCss(file_get_contents(TL_ROOT . '/' . $arrFile['name']), $arrFile)));
					}
					else
					{
						$objFile->write($this->handleCss(file_get_contents(TL_ROOT . '/' . $arrFile['name']), $arrFile));
					}
					
					$objFile->close();
				}

				$return[] = $strPath;
			}

			else
			{
				$name = $arrFile['name'];

				// Strip the web/ prefix (see #328)
				if (strncmp($name, $this->strWebDir . '/', strlen($this->strWebDir) + 1) === 0)
				{
					$name = substr($name, strlen($this->strWebDir) + 1);
				}

				// Add the media query (see #7070)
				if ($arrFile['media'] != '' && $arrFile['media'] != 'all' && !$this->hasMediaTag($arrFile['name']))
				{
					$name .= '|' . $arrFile['media'];
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
