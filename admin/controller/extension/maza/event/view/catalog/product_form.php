<?php
class ControllerExtensionMazaEventViewCatalogProductForm extends Controller {
	public function before($route, &$data) {
		if (isset($this->request->get['product_id'])) {
			$data['mz_edit'] = $this->url->link('extension/maza/catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'], true);
			$data['mz_notification'] = $this->url->link('extension/maza/notification/send', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'], true);

			$data['mz_preview'] = $this->config->get('mz_store_url') . 'index.php?route=product/product&product_id=' . $this->request->get['product_id'];
		}
	}
}
