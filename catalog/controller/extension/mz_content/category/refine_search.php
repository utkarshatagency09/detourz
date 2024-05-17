<?php
class ControllerExtensionMzContentCategoryRefineSearch extends maza\layout\Content {
        public function index($setting) {
                $data = array();
                
                $data['title']              = $setting['content_title'];
                $data['show_content']       = $setting['content_style'];
                $data['design']             = $setting['content_design'];
                
                $data['column_xs']          = $setting['content_column_xs'];
                $data['column_sm']          = $setting['content_column_sm'];
                $data['column_md']          = $setting['content_column_md'];
                $data['column_lg']          = $setting['content_column_lg'];
                $data['column_xl']          = $setting['content_column_xl'];
                
                return $this->load->view('product/category/refine_search', $data);
        }
}
