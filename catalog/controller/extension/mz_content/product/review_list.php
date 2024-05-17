<?php
class ControllerExtensionMzContentProductReviewList extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('product/product/review_list');
        }
}
