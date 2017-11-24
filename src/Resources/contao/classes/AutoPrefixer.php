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
    /**
     * Browserslist queries
     * @var array
     */
    private $browsers = array();

	
	/**
	 * Prepare the Browserslist queries array
	 *
	 * @param array Browserslist queries
	 */
    public function __construct($browsers = array('last 2 version'))
    {
        $this->browsers = $browsers;
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
        // Open wrapper to autoprefixer.js in node.js
        $nodejs = proc_open
        (
            'node ' . \System::getContainer()->getParameter('kernel.project_dir') . '/vendor/agoat/contao-autoprefixer/src/Resources/autoprefixer/controller.js',
            array(array('pipe', 'r'), array('pipe', 'w')),
            $pipes
        );

        if ($nodejs === false) 
        {
            throw new \RuntimeException('Could not start node runtime');
        }

        $stdin = array
        (
            'css' => $css,
            'browsers' => $this->browsers,
            'env' => 'production'
        );
        
        $stdin = json_encode($stdin);
        
        // Send to node.js
        fwrite($pipes[0], $stdin);
        fclose($pipes[0]);

        // Get from node.js
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
       
        // Close wrapper in node.js
        proc_close($nodejs);
                     
        return json_decode($stdout, true);
    }
};
