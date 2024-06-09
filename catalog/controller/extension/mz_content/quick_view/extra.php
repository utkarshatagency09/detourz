<?php
class ControllerExtensionMzContentQuickViewExtra extends maza\layout\Content {
        public function index($setting) {
                $data['show_brand'] = $setting['content_brand'];
                $data['show_model'] = $setting['content_model'];
                $data['show_reward'] = $setting['content_reward'];
                $data['show_stock'] = $setting['content_stock'];
                
                return $this->load->view('product/product/extra', $data);
        }
}
