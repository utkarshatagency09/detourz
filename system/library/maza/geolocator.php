<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2022, Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;

class GeoLocator {

	/**
	 * Constructor
	 *
	 * @param	string	$adaptor
	 *
 	*/
	public function __construct(string $adaptor = 'geoplugin') {
		$class = 'maza\\geolocator\\' . $adaptor;
		
		if (class_exists($class)) {
			$this->adaptor = new $class();

			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$this->adaptor->load($_SERVER['HTTP_CLIENT_IP']);
			} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$this->adaptor->load($_SERVER['HTTP_X_FORWARDED_FOR']);
			} else {
				$this->adaptor->load($_SERVER['REMOTE_ADDR']);
			}
		} else {
			trigger_error('Error: Could not load geolocator adaptor ' . $adaptor . '!');
			exit();
		}	
	}

	public function getCity(): string {
		return $this->adaptor->getCity();
	}

	public function getRegion(): string {
		return $this->adaptor->getRegion();
	}

	public function getRegionCode(): string {
		return $this->adaptor->getRegionCode();
	}

	public function getCountry(): string {
		return $this->adaptor->getCountry();
	}

	public function getCountryCode(): string {
		return $this->adaptor->getCountryCode();
	}

	public function getTimezone(): string {
		return $this->adaptor->getTimezone();
	}

	public function getCurrencyCode(): string {
		return $this->adaptor->getCurrencyCode();
	}
}