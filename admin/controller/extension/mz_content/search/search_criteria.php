<?php
class ControllerExtensionMzContentSearchSearchCriteria extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/search/search_criteria');

                $data = array();
                

                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                // Input placeholder
                if(isset($this->request->post['content_placeholder'])){
                    $data['content_placeholder'] = $this->request->post['content_placeholder'];
                } else {
                    $data['content_placeholder'] = array();
                }
                
                // Category status
                if(isset($this->request->post['widget_category'])){
                    $data['widget_category'] = $this->request->post['widget_category'];
                } else {
                    $data['widget_category'] =  0;
                }
                
                // Search in description
                if(isset($this->request->post['widget_description'])){
                    $data['widget_description'] = $this->request->post['widget_description'];
                } else {
                    $data['widget_description'] =  1;
                }
                
                // Search in subcategory
                if(isset($this->request->post['widget_sub_category'])){
                    $data['widget_sub_category'] = $this->request->post['widget_sub_category'];
                } else {
                    $data['widget_sub_category'] =  1;
                }
                
                $this->load->model('localisation/language');
                $data['languages'] = $this->model_localisation_language->getLanguages();
                
                $this->response->setOutput($this->load->view('extension/mz_content/search/search_criteria', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/search/search_criteria')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
