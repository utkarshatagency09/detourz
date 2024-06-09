<?php
namespace maza;

class Wrapper {
	protected $object;

	public function __construct(object $object) {
		$this->object = $object;
	}

	public function __get($key) {
		return $this->object->{$key};
	}

	public function __set($key, $value) {
		$this->object->{$key} = $value;
	}

	public function __call($method, $args) {
		return $this->object->{$method}(...$args);
	}

	public function restore() {
		return $this->object;
	}
}