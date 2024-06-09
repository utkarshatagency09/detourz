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

class Theme {
        private $data = array();
    
        /**
         * 
         *
         * @param	string	$key
         * 
         * @return	mixed
         */
        public function get($key) {
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
		$file = DIR_CONFIG . $filename . '.php';

		if (file_exists($file)) {
			$_ = array();

			require($file);

			$this->data = array_merge($this->data, $_);
		} else {
			trigger_error('Error: Could not load config ' . $filename . '!');
			exit();
		}
	}
}
