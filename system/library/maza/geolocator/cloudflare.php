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

class Cloudflare extends Library {
	private $city = '';
	private $region = '';
	private $regionCode = '';
	private $countryCode = '';
	private $countryName = '';
	private $timezone = '';
	private $currencyCode = '';
	
	public function load(string $ip): void {
		if (isset($this->request->server['HTTP_CF_IPCOUNTRY'])) {
			$this->countryCode = $this->request->server['HTTP_CF_IPCOUNTRY'];
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