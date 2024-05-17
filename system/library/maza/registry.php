<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright           Copyright (c) 2021 Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;

/**
* Registry class
*/
final class Registry {
	public static $registry;
        
    /**
     * Get registry data
     */
    public static function get(string $key){
        return static::$registry->get($key);
    }
    
    /**
     * Get or set config data
     */
    public static function config(string $key, $value = null){
        if($value){
            static::$registry->get('config')->set($key, $value);
        } else {
            return static::$registry->get('config')->get($key);
        }
    }
    
    /**
     * Get or set Maza theme setting
     */
    public static function theme(string $key, $value = null){
        if($value){
            static::$registry->get('mz_theme_config')->set($key, $value);
        } else {
            return static::$registry->get('mz_theme_config')->get($key);
        }
    }
    
    /**
     * Get or set Maza skin setting
     */
    public static function skin(string $key, $value = null){
        if($value){
            static::$registry->get('mz_skin_config')->set($key, $value);
        } else {
            return static::$registry->get('mz_skin_config')->get($key);
        }
    }
}
