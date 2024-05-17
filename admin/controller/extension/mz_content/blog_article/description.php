<?php
class ControllerExtensionMzContentBlogArticleDescription extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/blog_article/description');

                $data = array();
                
                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_collapsed'])){
                    $data['content_collapsed'] = $this->request->post['content_collapsed'];
                } else {
                    $data['content_collapsed'] =  0;
                }
                
                $this->response->setOutput($this->load->view('extension/mz_content/blog_article/description', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/blog_article/description')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
