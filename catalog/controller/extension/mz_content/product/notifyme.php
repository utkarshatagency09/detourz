<?php
class ControllerExtensionMzContentProductNotifyme extends maza\layout\Content {
	public function index(array $setting): string {
		$data['show']             = $setting['content_show'];
		$data['icon_position']    = $setting['content_icon_position'];
		$data['color']            = $setting['content_color'];
		$data['outline']          = $setting['content_outline'];
		$data['size']             = $setting['content_size'];
		$data['block']            = $setting['content_block'];
		$data['dropdown_align']   = $setting['content_dropdown_align'];

		if ($this->config->get('maza_notification_status')) {
			return $this->load->view('product/product/notifyme', $data);
		}
		
		return '';
	}
}
