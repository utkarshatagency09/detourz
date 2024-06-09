<?php
class ControllerExtensionMzContentCategoryImage extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('product/category/image');
        }
}
