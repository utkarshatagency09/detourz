<?php
class ControllerExtensionMzContentProductReviewForm extends maza\layout\Content {
        public function index($setting) {
                $data['mz_suffix']             = $setting['mz_suffix'];
                $data['size']             = $setting['content_size'];
                $data['color']            = $setting['content_color'];
                
                return $this->load->view('product/product/review_form', $data);
        }
}
