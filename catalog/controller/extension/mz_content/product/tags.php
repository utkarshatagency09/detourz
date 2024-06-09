<?php
class ControllerExtensionMzContentProductTags extends maza\layout\Content {
        public function index($setting) {
                $data['color'] = $setting['content_color'];
                
                return $this->load->view('product/product/tags', $data);
        }
}
