<?php
class ControllerExtensionMzContentSearchSearchCriteria extends maza\layout\Content {
        public function index($setting) {
                $data = array();
                
                if(isset($setting['content_placeholder'][$this->config->get('config_language_id')])){
                    $data['placeholder'] = $setting['content_placeholder'][$this->config->get('config_language_id')];
                } else {
                    $data['placeholder'] = '';
                }
                
                $data['category']       = $setting['content_category'];
                $data['description']    = $setting['content_description'];
                $data['sub_category']   = $setting['content_sub_category'];
                
                return $this->load->view('product/search/search_criteria', $data);
        }
}
