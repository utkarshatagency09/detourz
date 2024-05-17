<?php
class ControllerExtensionMzContentQuickViewButton extends maza\layout\Content {
        
        public function index($setting) {
                $data = array();
                
                $data['cart']             = $setting['content_cart'];
                $data['buynow']           = $setting['content_buynow'];
                $data['wishlist']         = $setting['content_wishlist'];
                $data['compare']          = $setting['content_compare'];
                $data['block']            = $setting['content_block'];
                $data['show']             = $setting['content_show'];
                $data['color']            = $setting['content_color'];
                $data['outline']          = $setting['content_outline'];
                $data['size']             = $setting['content_size'];
                $data['total_btn']        = count(array_filter([$setting['content_cart'], $setting['content_buynow'], $setting['content_wishlist'], $setting['content_compare']]));
                
                return $this->load->view('product/product/button', $data);
	}
        
}
