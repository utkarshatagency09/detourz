<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2023, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventModelCache extends Controller
{
	private static $data = array();

	public function before(string $route, array $args)
	{
		if (isset(self::$data[$this->key($route, $args)])) {
			return self::$data[$this->key($route, $args)];
		}
	}

	public function after(string $route, array $args, $output)
	{
		if($output){
			self::$data[$this->key($route, $args)] = $output;
		}
	}

	private function key(string $route, array $args = []): string {
		if ($args) {
			return $route . md5(serialize($args));
		}

		return $route;
	}
}
