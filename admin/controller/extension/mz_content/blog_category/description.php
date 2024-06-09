<?php
class ControllerExtensionMzContentBlogCategoryDescription extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/blog_category/description');

                $data = array();
                
                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_limit'])){
                    $data['content_limit'] = $this->request->post['content_limit'];
                } else {
                    $data['content_limit'] =  0;
                }
                
                $this->response->setOutput($this->load->view('extension/mz_content/blog_category/description', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/blog_category/description')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
