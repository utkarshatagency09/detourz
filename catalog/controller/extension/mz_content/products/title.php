<?php
class ControllerExtensionMzContentProductsTitle extends maza\layout\Content {
        public function index($setting) {
                $data['size']       =   $setting['content_size'];
                
                return $this->load->view('product/common/title', $data);
        }
}
