<?php
class ControllerExtensionMzContentProductCountdown extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('product/product/countdown');
        }
}
