<?php
class ControllerCommonLanguage extends Controller {
	public function index() {
		$this->load->language('common/language');

		$data['action'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);

		$data['code'] = $this->session->data['language'];

		$this->load->model('localisation/language');

		$data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$data['languages'][] = array(
					'name' => $result['name'],
					'code' => $result['code']
				);
			}
		}

		if (!isset($this->request->get['route'])) {
			$data['redirect'] = $this->url->link('common/home');
		} else {
			$url_data = $this->request->get;

			unset($url_data['_route_']);
			
			$route = $url_data['route'];
			
			unset($url_data['route']);
			
			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			// $data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
			if($route=='product/product'){
				$product_id = $url_data['product_id'];
				if($this->session->data['language']=='en-gb'){//load russia url
					$language_id=3;
				}else{//load english url
					$language_id=1;
				}
				// Check if HTTPS is enabled
				if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
					$home_url = HTTPS_SERVER;
				} else {
					$home_url = HTTP_SERVER;
				}
				$query = $this->db->query("SELECT su.keyword
                                       FROM " . DB_PREFIX . "product p
                                       LEFT JOIN " . DB_PREFIX . "seo_url su ON su.query = CONCAT('product_id=', p.product_id)
                                       WHERE su.language_id = '" . (int)$language_id . "'
                                       AND p.product_id = '" . (int)$product_id . "'");
				$data['redirect'] = $home_url.$query->row['keyword'];
			}else{
				$data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
			}
		}

		return $this->load->view('common/language', $data);
	}

	public function language() {
		if (isset($this->request->post['code'])) {
			$this->session->data['language'] = $this->request->post['code'];
		}

		if (isset($this->request->post['redirect'])) {
			$this->response->redirect($this->request->post['redirect']);
		} else {
			$this->response->redirect($this->url->link('common/home'));
		}
	}
}