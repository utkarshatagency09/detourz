<?php
class ControllerExtensionMzWidgetLanguage extends maza\layout\Widget {
	public function index(array $setting): string {
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

			$data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
		}
		
		$data['position'] = $setting['widget_position'];

		return $this->load->view('common/language', $data);
	}
	
	/**
	 * Change default setting
	 */
	public function getSettings(): array {
		$setting['xl'] = $setting['lg'] = $setting['md'] = 
		$setting['sm'] = $setting['xs'] = array(
			'widget_flex_grow' => 0,
			'widget_flex_shrink' => 0,
		);

		return \maza\array_merge_subsequence(parent::getSettings(), $setting);
	}
}
