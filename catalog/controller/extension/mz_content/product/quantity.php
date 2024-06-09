<?php
class ControllerExtensionMzContentProductQuantity extends maza\layout\Content {
        public function index($setting) {
                $data['size']             = $setting['content_size'];
                $data['color']            = $setting['content_color'];
                $data['outline']          = $setting['content_outline'];
                
                return $this->load->view('product/product/quantity', $data);
        }
}
