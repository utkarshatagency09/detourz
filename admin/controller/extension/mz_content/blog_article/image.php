<?php
class ControllerExtensionMzContentBlogArticleImage extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/blog_article/image');

                $data = array();
                

                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_additional_image_position'])){
                    $data['content_additional_image_position'] = $this->request->post['content_additional_image_position'];
                } else {
                    $data['content_additional_image_position'] =  'bottom';
                }
                
                $data['additional_image_positions'] = array(
                        array('code' => 'top', 'text' => $this->language->get('text_top')),
                        array('code' => 'bottom', 'text' => $this->language->get('text_bottom')),
                );
                
                $this->response->setOutput($this->load->view('extension/mz_content/blog_article/image', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/blog_article/image')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
