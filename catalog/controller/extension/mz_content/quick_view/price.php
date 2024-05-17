<?php
class ControllerExtensionMzContentQuickViewPrice extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('product/product/price');
        }
}
