<?php
class ControllerExtensionMzContentQuickViewBrand extends maza\layout\Content {
        public function index($setting) {
                $data['show_caption'] = $setting['content_caption'];
                
                return $this->load->view('product/product/brand', $data);
        }
}
