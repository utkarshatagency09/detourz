<?php
class ControllerExtensionMzContentCategoryDescription extends maza\layout\Content {
        public function index($setting) {
                $data['category_image'] = $setting['content_category_image'];
                $data['limit'] = $setting['content_limit'];
                
                return $this->load->view('product/category/description', $data);
        }
}
