<?php
class ControllerExtensionMzContentProductTotal extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('product/product/total');
        }
}