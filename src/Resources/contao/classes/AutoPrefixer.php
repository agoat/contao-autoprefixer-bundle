<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2016 Leo Feyer
 *
 * @package     AutoPrefixer
 * @author      Arne Stappen
 * @license     LGPL-3.0+ 
 * @copyright   Arne Stappen 2016
 */

namespace Agoat\AutoPrefixer;


/**
 * Uses node.js to run autoprefixer.js to add prefixes to css
 */

class AutoPrefixer
{
    private $browsers = array();

    public function __construct($browsers = 'last 2 version')
    {
        $this->browsers = $browsers;
	}

    public function rewrite($css)
    {
        // open wrapper to autoprefixer.js in node.js
        $nodejs = proc_open
        (
            'node ' . \System::getContainer()->getParameter('kernel.root_dir') . '/../vendor/agoat/autoprefixer-bundle/src/Resources/autoprefixer/controller.js',
            array(array('pipe', 'r'), array('pipe', 'w')),
            $pipes
        );

        if ($nodejs === false) 
        {
            throw new RuntimeException('Could not start node runtime');
        }

        $stdin = array
        (
            'css' => $css,
            'browsers' => $this->browsers,
            'env' => 'production'
        );
        
        $stdin = json_encode($stdin);
        
        // send to node.js
        fwrite($pipes[0], $stdin);
        fclose($pipes[0]);

        // get from node.js
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
       
        // close wrapper in node.js
        proc_close($nodejs);
                     
        return json_decode($stdout, true);
    }
};
