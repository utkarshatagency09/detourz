<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2022, Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
 */
namespace maza;

class Browser {
	const NAME_MSIE = 'Internet Explorer';
	const NAME_FIREFOX = 'Mozilla Firefox';
	const NAME_OPERA = 'Opera';
	const NAME_CHROME = 'Google Chrome';
	const NAME_SAFARI = 'Apple Safari';
	const NAME_NETSCAPE = 'Netscape';
	const NAME_EDGE = 'Edge';
	const CODE_MSIE = 'MSIE';
	const CODE_FIREFOX = 'Firefox';
	const CODE_OPERA = 'OPR';
	const CODE_CHROME = 'Chrome';
	const CODE_SAFARI = 'Safari';
	const CODE_NETSCAPE = 'Netscape';
	const CODE_EDGE = 'Edge';

	public $user_agent;
	public $name;
	public $code;
	public $version;

	public function __construct(string $user_agent) {
		$this->user_agent = $user_agent;

		// Next get the name of the useragent yes seperately and for good reason
		if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
			$this->name = self::NAME_MSIE;
			$this->code = self::CODE_MSIE;
		} elseif (preg_match('/Firefox/i', $user_agent)) {
			$this->name = self::NAME_FIREFOX;
			$this->code = self::CODE_FIREFOX;
		} elseif (preg_match('/OPR/i', $user_agent)) {
			$this->name = self::NAME_OPERA;
			$this->code = self::CODE_OPERA;
		} elseif (preg_match('/Chrome/i', $user_agent) && !preg_match('/Edge/i', $user_agent)) {
			$this->name = self::NAME_CHROME;
			$this->code = self::CODE_CHROME;
		} elseif (preg_match('/Safari/i', $user_agent) && !preg_match('/Edge/i', $user_agent)) {
			$this->name = self::NAME_SAFARI;
			$this->code = self::CODE_SAFARI;
		} elseif (preg_match('/Netscape/i', $user_agent)) {
			$this->name = self::NAME_NETSCAPE;
			$this->code = self::CODE_NETSCAPE;
		} elseif (preg_match('/Edge/i', $user_agent)) {
			$this->name = self::NAME_EDGE;
			$this->code = self::CODE_EDGE;
		} elseif (preg_match('/Trident/i', $user_agent)) {
			$this->name = self::NAME_MSIE;
			$this->code = self::CODE_MSIE;
		}

		// finally get the correct version number
		$known   = array('Version', $this->code, 'other');
		$pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (preg_match_all($pattern, $user_agent, $matches)) {
			// see how many we have
			$i = count($matches['browser']);
			if ($i != 1) {
				//we will have two since we are not using 'other' argument yet
				//see if version is before or after the name
				if (strripos($user_agent, "Version") < strripos($user_agent, $this->code)) {
					$this->version = $matches['version'][0];
				} else {
					$this->version = $matches['version'][1];
				}
			} else {
				$this->version = $matches['version'][0];
			}
		}
	}

	public function isSupportedWebp(): bool {
		if (!$this->name || !$this->version) {
			return false;
		}

		if ($this->name == self::NAME_CHROME && version_compare($this->version, '32', '>=')) {
			return true;
		}
		if ($this->name == self::NAME_EDGE && version_compare($this->version, '18', '>=')) {
			return true;
		}
		if ($this->name == self::NAME_SAFARI && version_compare($this->version, '16', '>=')) {
			return true;
		}
		if ($this->name == self::NAME_FIREFOX && version_compare($this->version, '65', '>=')) {
			return true;
		}
		if ($this->name == self::NAME_OPERA && version_compare($this->version, '19', '>=')) {
			return true;
		}

		return false;
	}
}