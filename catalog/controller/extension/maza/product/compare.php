<?php
class ControllerExtensionMazaProductCompare extends Controller {
	public function remove() {
		$this->load->language('product/compare');

		$json = array();

		if (isset($this->session->data['compare']) && isset($this->request->post['product_id'])) {
			$key = array_search($this->request->post['product_id'], $this->session->data['compare']);

			if ($key !== false) {
				unset($this->session->data['compare'][$key]);

				$this->session->data['success'] = $this->language->get('text_remove');
                                
				$json['total'] = count($this->session->data['compare']);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
