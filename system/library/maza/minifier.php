<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright           Copyright (c) 2021 Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;

use MatthiasMullie\Minify;

/**
* Minifier class
*/
class Minifier extends Library{
	
        /**
         * Minified CSS code or file
         * @param mixed $source CSS code or file path or array of mixed code and file
         * @param string $destination_file optional destination file path
         * @return mixed Return Minified CSS code if $destination_file is not specified
         */
        public function css($source, $destination_file = null){
                $minifier = new Minify\CSS();
                
                // Add CSS code or file path to minifier
                if(is_array($source)){
                    foreach($source as $src){
                        $minifier->add($src);
                    }
                } else {
                    $minifier->add($source);
                }
                
                // Set destination file path or return minified code if destination file is not exist
                if($destination_file){
                    $minifier->minify($destination_file);
                } else {
                    return $minifier->minify();
                }
        }
        
        /**
         * Minified JS code or file
         * @param mixed $source JS code or file path or array of mixed code and file
         * @param string $destination_file optional destination file path
         * @return mixed Return Minified JS code if $destination_file is not specified
         */
        public function js($source, $destination_file = null){
                $minifier = new Minify\JS();
                
                // Add JS code or file path to minifier
                if(is_array($source)){
                    foreach($source as $src){
                        $minifier->add($src);
                    }
                } else {
                    $minifier->add($source);
                }
                
                // Set destination file path or return minified code if destination file is not exist
                if($destination_file){
                    $minifier->minify($destination_file);
                } else {
                    return $minifier->minify();
                }
        }
}
