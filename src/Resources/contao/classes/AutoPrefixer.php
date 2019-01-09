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


/**
 * Handles the node.js task
 *
 * Uses node.js to run autoprefixer.js to add prefixes to css
 */
class AutoPrefixer
{
    private $browsers = array();
    private $flex;
    private $grid;
    private $remove;
    private $supports;


    /**
     * Prepare the options
     *
     * @param array $browsers
     * @param bool $flex
     * @param bool $grid
     * @param bool $remove
     * @param bool $supports
     */
    public function __construct(array $browsers, bool $flex, bool $grid, bool $remove, bool $supports)
    {
        $this->browsers = $browsers;
        $this->flex = $flex;
        $this->grid = $grid;
        $this->remove = $remove;
        $this->supports = $supports;
    }

	
	/**
	 * Rewrite the css with the autoprefixer.js in node.js
	 *
	 * @param string CSS
	 *
	 * @return string Prefixed CSS
	 *
	 * @throws \RuntimeException If node.js could not be startet
	 */
    public function rewrite($css)
    {
        // Open autoprefix controller in node.js
        $nodejs = proc_open(
            'node ' . \System::getContainer()->getParameter('kernel.project_dir') . '/vendor/agoat/contao-autoprefixer/src/Resources/autoprefixer/controller.js',
            array(array('pipe', 'r'), array('pipe', 'w')),
            $pipes
        );

        if ($nodejs === false) {
            throw new \RuntimeException('Could not start node runtime');
        }

        $stdin = array(
            'css' => $css,
            'browsers' => $this->browsers,
            'flex' => $this->flex,
            'grid' => $this->grid,
            'remove' => $this->remove,
            'supports' => $this->supports
        );

        $stdin = json_encode($stdin);
        
        // Send to node.js
        fwrite($pipes[0], $stdin);
        fclose($pipes[0]);

        // Get from node.js
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
       
        // Close controller in node.js
        proc_close($nodejs);

        return json_decode($stdout, true);
    }
};
