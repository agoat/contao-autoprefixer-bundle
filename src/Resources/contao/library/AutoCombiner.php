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

namespace Agoat\AutoPrefixerBundle\Contao;

use Contao\Config;
use Contao\File;
use Contao\LayoutModel;
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
     *
     * @var AutoPrefixer|null
     */
    protected $autoprefixer = null;


    /**
     * Public constructor
     *
     * Prepares the autoprefixer class
     */
    public function __construct(LayoutModel $layout)
    {
        // Prepare browser list
        $browsers = explode(',', $layout->browsers ?: 'defaults');
        array_walk($browsers,
            function (&$value) {
                $value = trim(html_entity_decode($value));
            }
        );

        // Prepare flex/grid options
        $flex = $layout->flex == 'true' || $layout->flex == 'false' ? (bool)$layout->flex : $flex = $layout->flex;
        $grid = $layout->grid == 'false' ? $grid = false : $grid = $layout->grid;

        // Prepare other options
        $remove = (bool)$layout->remove;
        $supports = (bool)$layout->supports;

        $this->autoprefixer = new AutoPrefixer($browsers, $flex, $grid, $remove, $supports);

        parent::__construct();
    }

    /**
     * Adds vendor prefixes on css declarations
     */
    public function addPrefixes()
    {
        $strTarget = substr($this->strMode, 1);

        foreach ($this->arrFiles as &$file) {
            $strPath = 'assets/' . $strTarget . '/' . str_replace('/',
                    '_',
                    $file['name']
                ) . '.prefixed' . $this->strMode;

            if (Config::get('debugMode') || !file_exists($this->strRootDir . '/' . $strPath)) {
                $newFile = new File($strPath);

                $content = file_get_contents($this->strRootDir . '/' . $file['name']);

                // Compile SCSS/LESS
                if ($file['extension'] == self::SCSS || $file['extension'] == self::LESS) {
                    $content = $this->handleScssLess($content, $file);
                }

                // Add Prefixes
                $content = $this->autoprefixer->rewrite($content);

                $newFile->write($content);
                $newFile->close();
            }

            $file['name'] = $strPath;
            $file['extension'] = self::CSS;
        }
    }

}
