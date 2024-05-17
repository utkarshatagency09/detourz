<?php
class ControllerExtensionMzContentQuickViewDescription extends maza\layout\Content {
        public function index($setting) {
                $data['collapsed'] = $setting['content_collapsed'];
                
                return $this->load->view('product/product/description', $data);
        }
}
