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

/**
 * Adds prefixes to css
 *
 * Usage:
 *
 *     $combiner = new Autoprefixer();
 *
 *     $combiner->add('css/style.css');
 *     $combiner->add('css/fonts.scss');
 *     $combiner->add('css/print.less');
 *
 *     echo $combiner->getCombinedFile();
 *
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
			'node ' . __DIR__ . '/../../autoprefixer/controller.js',
            array(array('pipe', 'r'), array('pipe', 'w')),
            $pipes
        );
		
        if ($nodejs === false) 
		{
            throw new RuntimeException('Could not start node runtime');
        }

		// send to node.js
		$stdin = array
		(
			'css' => $css,
			'browsers' => $this->browsers
		);
		
		$stdin = json_encode($stdin);
		
		fwrite($pipes[0], $stdin);
        fclose($pipes[0]);

		// get from node.js
        $stdout = stream_get_contents($pipes[1]);
        $stdout = json_decode($stdout, true);
        fclose($pipes[1]);
       
        proc_close($nodejs);
		             
        return $stdout;
    }
    
   
};
