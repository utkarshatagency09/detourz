<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
*/
namespace maza\config;

/**
 * Skin specific setting
 */

class Skin {
        private $data = array();
    
        /**
         * 
         *
         * @param	string	$key
         * 
         * @return	mixed
         */
        public function get($key) {
                if(!is_string($key)){
                    var_dump($key);
                }
                return (isset($this->data[$key]) ? $this->data[$key] : null);
        }
	
        /**
         * 
         *
         * @param string $key
         * @param string $value
         */
	public function set($key, $value) {
            $this->data[$key] = $value;
	}

        /**
         * 
         *
         * @param	string	$key
         *
         * @return	mixed
         */
	public function has($key) {
		return isset($this->data[$key]);
	}
	
        /**
         * 
         * @param	string	$filename
         */
	public function load($filename) {
		$file = \MZ_CONFIG::$DIR_THEME_CONFIG . $filename . '.php';
                
		if (is_file($file)) {
			$_ = array();

			require($file);

			$this->data = array_merge($this->data, $_);
		}
	}
}
