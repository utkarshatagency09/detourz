<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2022, Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza\geolocator;

use maza\Library;

class GeoPlugin extends Library {
	private $city = '';
	private $region = '';
	private $regionCode = '';
	private $countryCode = '';
	private $countryName = '';
	private $latitude = '';
	private $longitude = '';
	private $timezone = '';
	private $currencyCode = '';
	
	public function load(string $ip): void {
		if (!empty($this->session->data['mz_geoplugin'])) {
			$data = $this->session->data['mz_geoplugin'];
		} else {
			$data = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ip));

			if ($data && $data['geoplugin_status'] == '200') {
				$this->session->data['mz_geoplugin'] = $data;
			}
		}

		if($data){
			$this->city = $data['geoplugin_city'];
			$this->region = $data['geoplugin_region'];
			$this->regionCode = $data['geoplugin_regionCode'];
			$this->countryCode = $data['geoplugin_countryCode'];
			$this->countryName = $data['geoplugin_countryName'];
			$this->latitude = $data['geoplugin_latitude'];
			$this->longitude = $data['geoplugin_longitude'];
			$this->timezone = $data['geoplugin_timezone'];
			$this->currencyCode = $data['geoplugin_currencyCode'];
		}
	}

	public function getCity(): string {
		return $this->city;
	}

	public function getRegion(): string {
		return $this->region;
	}

	public function getRegionCode(): string {
		return $this->regionCode;
	}

	public function getCountry(): string {
		return $this->countryName;
	}

	public function getCountryCode(): string {
		return $this->countryCode;
	}

	public function getTimezone(): string {
		return $this->timezone;
	}

	public function getCurrencyCode(): string {
		return $this->currencyCode;
	}
}