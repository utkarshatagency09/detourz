<?php
class ControllerExtensionMzContentManufacturerInfoNotifyme extends maza\layout\Content {
	public function index(array $setting): string {
		$data['color']            = $setting['content_color'];
		$data['outline']          = $setting['content_outline'];
		$data['size']             = $setting['content_size'];

		if ($this->config->get('maza_notification_status') && $this->config->get('maza_notification_manufacturer')) {
			return $this->load->view('product/manufacturer_info/notifyme', $data);
		}

		return '';
	}
}
