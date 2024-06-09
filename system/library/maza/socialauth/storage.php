<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2022, Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza\socialAuth;

use Hybridauth\Storage\StorageInterface;

class Storage implements StorageInterface {
	protected $namespace = 'HYBRIDAUTH::STORAGE';
	private $session;

	public function __construct($registry) {
		$this->session = $registry->get('session');
	}
	
	public function get($key) {
		if (isset($this->session->data[$this->namespace][$key])) {
			return $this->session->data[$this->namespace][$key];
		}
		return null;
	}

	public function set($key, $value) {
		$this->session->data[$this->namespace][$key] = $value;
	}

	public function delete($key) {
		unset($this->session->data[$this->namespace][$key]);
	}

	public function deleteMatch($key){
		$key = strtolower($key);

        if (isset($this->session->data[$this->namespace]) && count($this->session->data[$this->namespace])) {
            $tmp = $this->session->data[$this->namespace];

            foreach ($tmp as $k => $v) {
                if (strstr($k, $key)) {
                    unset($tmp[$k]);
                }
            }

            $this->session->data[$this->namespace] = $tmp;
        }
	}

	public function clear() {
        $this->session->data[$this->namespace] = [];
    }
}