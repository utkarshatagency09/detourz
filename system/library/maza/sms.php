<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2022, Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;

class SMS {

	/**
	 * Constructor
	 *
	 * @param	string	$adaptor
	 *
 	*/
	public function __construct(string $adaptor = 'gatewayapi') {
		$class = 'maza\\sms\\' . $adaptor;
		
		if (class_exists($class)) {
			$this->adaptor = new $class();
		} else {
			trigger_error('Error: Could not load SMS adaptor ' . $adaptor . '!');
			exit();
		}	
	}
	
	public function setSender(string $sender): void {
		$this->adaptor->setSender($sender);
	}

	public function setRecipients(array $recipients): void {
		$this->adaptor->setRecipients($recipients);
	}

	public function setMessage(string $message): void {
		$this->adaptor->setMessage($message);
	}

	public function send(): void {
		$this->adaptor->send();
	}
}