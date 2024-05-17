<?php
class ControllerExtensionMzContentCategoryTitle extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/category/title');

                $data = array();
                

                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_size'])){
                    $data['content_size'] = $this->request->post['content_size'];
                } else {
                    $data['content_size'] =  'h1';
                }
                
                $this->response->setOutput($this->load->view('extension/mz_content/category/title', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/category/title')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
