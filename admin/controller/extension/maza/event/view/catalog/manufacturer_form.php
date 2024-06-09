<?php
class ControllerExtensionMazaEventViewCatalogManufacturerForm extends Controller {
	public function before($route, &$data) {
		if (isset($this->request->get['manufacturer_id'])) {
			$data['mz_edit'] = $this->url->link('extension/maza/catalog/manufacturer/edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $this->request->get['manufacturer_id'], true);
		}
	}
}
