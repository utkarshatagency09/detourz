<?php
class ControllerExtensionMzContentProductExtra extends maza\layout\Content {
	public function index(array $setting): string {
		$data['show_brand']         = $setting['content_brand'];
		$data['show_model']         = $setting['content_model'];
		$data['show_reward']        = $setting['content_reward'];
		$data['show_stock']         = $setting['content_stock'];
		$data['show_viewed']        = $setting['content_viewed'];
		$data['show_sold']          = $setting['content_sold'];
		
		$data['show_sku']           = $setting['content_sku'];
		$data['show_upc']           = $setting['content_upc'];
		$data['show_ean']           = $setting['content_ean'];
		$data['show_jan']           = $setting['content_jan'];
		$data['show_isbn']          = $setting['content_isbn'];
		$data['show_mpn']           = $setting['content_mpn'];
		
		$data['show_date_modified'] = $setting['content_date_modified'];
		$data['show_date_available']= $setting['content_date_available'];
		
		$data['show_weight']        = $setting['content_weight'];
		$data['show_length']        = $setting['content_length'];
		$data['show_width']         = $setting['content_width'];
		$data['show_height']        = $setting['content_height'];
		
		return $this->load->view('product/product/extra', $data);
	}
}
