<?php
class ControllerExtensionMzContentProductsListGrid extends maza\layout\Content {
        public function index($setting) {
                $data['color'] = $setting['content_color'];
                
                return $this->load->view('product/common/list_grid', $data);
        }
}
