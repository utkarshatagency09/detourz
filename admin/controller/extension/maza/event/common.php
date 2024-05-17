<?php
class ControllerExtensionMazaEventCommon extends Controller {
	/**
	 * Add menu
	 * @param string $route 
	 * @param array $data 
	 * 
	 */
	public function menu($route, &$data) {
		foreach($data['menus'] as &$menu){
			if(isset($menu['id']) && $menu['id'] == 'menu-extension'){
				if ($this->user->hasPermission('access', 'extension/maza/skin')) {
					$menu['children'][] = array(
						'name'	   => 'MazaEngine',
						'href'     => $this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token'], true),
						'children' => array()		
					);
				}
			}
		}
	}
	
	/**
	 * Verify themeforest purchase before to give access
	 */
	public function themeforest(&$route, &$output){
		if(!$this->isValidBuyer()){
			header('WWW-Authenticate: Basic realm="Enter your themeforest username and purchase code in password", charset="UTF-8"');
			http_response_code(401);

			die('<h1>Purchase verification failed!</h1><strong>Please verify your purchase to access this page.</strong><p>Enter your themeforest username and purchase code in password. Click on <a target="_blank" href="https://themeforest.net/downloads">download</a> button on item to get your purchase code</p>');
		}
	}

	private function isValidBuyer(){
		if(empty($this->request->server['PHP_AUTH_PW']) || empty($this->request->server['PHP_AUTH_USER'])){
			return false;
		}

		if(!empty($this->session->data['isValidBuyer'])){
			return true;
		}

		$curl_h = curl_init('https://api.envato.com/v3/market/author/sale?code=' . urlencode($this->request->server['PHP_AUTH_PW']));

		curl_setopt($curl_h, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . ENVATO_TOKEN]);
		curl_setopt($curl_h, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl_h);

		if($response){
			$data = json_decode($response, true);

			if(!empty($data['buyer']) && $data['buyer'] == $this->request->server['PHP_AUTH_USER']){
				$this->session->data['isValidBuyer'] = true;
				return true;
			}
		}

		return false;
	}
}
