<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2022, Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza\sms;

use maza\Library;

class Gatewayapi extends Library {
	private $token = '';
	private $sender = '';
	private $recipients = array();
	private $message = '';

	public function __construct() {
		$this->sender = $this->config->get('maza_gatewayapi_sender');
		$this->token = $this->config->get('maza_gatewayapi_token');
	}
	
	public function setSender(string $sender): void {
		$this->sender = $sender;
	}

	public function setRecipients(array $recipients): void {
		$this->recipients = $recipients;
	}

	public function setMessage(string $message): void {
		$this->message = $message;
	}

	public function send(): void {
		if (!$this->token) {
			throw new \Exception('Error: Gatewayapi token to required!');
		}
		if (!$this->sender) {
			throw new \Exception('Error: Gatewayapi sender to required!');
		}
		if (!$this->recipients) {
			throw new \Exception('Error: Gatewayapi recipients to required!');
		}
		if (!$this->message) {
			throw new \Exception('Error: Gatewayapi message to required!');
		}

		$json = [
			'sender' => $this->sender,
			'message' => $this->message,
			'recipients' => [],
		];
		foreach ($this->recipients as $msisdn) {
			$json['recipients'][] = ['msisdn' => $msisdn];
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://gatewayapi.com/rest/mtsms');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_USERPWD, $this->token . ":");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_exec($ch);
		curl_close($ch);
	}
}