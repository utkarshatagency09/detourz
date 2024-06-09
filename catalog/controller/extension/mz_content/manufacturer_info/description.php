<?php
class ControllerExtensionMzContentManufacturerInfoDescription extends maza\layout\Content {
        public function index($setting) {
                $data['manufacturer_image'] = $setting['content_manufacturer_image'];
                $data['limit'] = $setting['content_limit'];
                
                return $this->load->view('product/manufacturer_info/description', $data);
        }
}
