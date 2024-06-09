<?php
class ControllerExtensionMzContentSearchProductLimit extends maza\layout\Content {
        public function index($setting) {
                $data['mz_suffix'] = $setting['mz_suffix'];
                $data['label']     = $setting['content_label'];

                return $this->load->view('product/common/product_limit', $data);
        }
}
