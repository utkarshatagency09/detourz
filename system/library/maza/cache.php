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
* Cache class
*/
class Cache extends Library {
	private $adaptor;
    private $var = array(); // Cache variable
	
	/**
	 * Constructor
	 *
	 * @param string $adaptor The type of storage for the cache.
	 * @param int $expire Optional parameters
	 *
 	*/
	public function __construct($adaptor = 'file', $expire = 3600) {
        $class = 'Maza\\Cache\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class($expire);
		} else {
			throw new \Exception('Error: Could not load cache adaptor ' . $adaptor . ' cache!');
		}
	}
        
	
    /**
     * Gets a cache by key name.
     * @param string $key The cache key name
     */
	public function get(string $key) {
        if($this->config->get('maza_cache_status')){
            return $this->adaptor->get($key);
        }
	}
	
    /**
     * Set a cache by key
     * @param string $key The cache key
     * @param mixed $value The cache value
     * @param mixed $expire expire timestamp
     */
	public function set(string $key, $value, $expire = true) {
        if($this->config->get('maza_cache_status') && $value){
		    return $this->adaptor->set($key, $value, $expire);
        }
	}
   
    /**
     * delete cache by key name
     * @param string $key The cache key
     */
	public function delete(string $key) {
		return $this->adaptor->delete($key);
	}
        
    /**
     * Clear cache
     * @param string $key The cache key
     */
	public function clear() {
		return $this->adaptor->clear();
	}
    
    /**
     * get variable
     * @param string $name The variable name
     */
    public function getVar(string $name){
        if(isset($this->var[$name])){
            return $this->var[$name];
        }
    }
    
    /**
     * set variable
     * @param string $name The variable name
     * @param mixed $value value
     */
    public function setVar(string $name, $value): void {
        $this->var[$name] = $value;
    }
    
    /**
     * delete variable
     * @param string $name The variable name
     */
    public function deleteVar(string $name): void {
        unset($this->var[$name]);
    }
    
    /**
     * check variable
     * @param string $name The variable name
     */
    public function isVar(string $name): bool{
        if(isset($this->var[$name])){
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Flush the expire cache
     */
    public function flush(){
        return $this->adaptor->flush();
    }
}
