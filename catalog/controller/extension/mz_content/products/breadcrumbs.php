<?php
class ControllerExtensionMzContentProductsBreadcrumbs extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('product/common/breadcrumbs');
        }
}
