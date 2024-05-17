<?php
class ControllerExtensionMzContentQuickViewQuantity extends maza\layout\Content {
        public function index($setting) {
                $data['size']             = $setting['content_size'];
                
                return $this->load->view('product/product/quantity', $data);
        }
}
