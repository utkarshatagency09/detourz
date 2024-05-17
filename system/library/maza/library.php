<?php
/**
* Library class
*/
namespace MAZA;

abstract class Library {
	public function __get($key) {
		return Registry::get($key);
	}

	public function __set($key, $value) {
		Registry::set($key, $value);
	}
}