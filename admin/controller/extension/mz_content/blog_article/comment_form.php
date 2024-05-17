<?php
class ControllerExtensionMzContentBlogArticleCommentForm extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/blog_article/comment_form');
                
                $this->load->model('extension/maza/asset');
                
                $data = array();
                
                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_field_email'])){
                    $data['content_field_email'] = $this->request->post['content_field_email'];
                } else {
                    $data['content_field_email'] =  0;
                }
                
                if(isset($this->request->post['content_field_website'])){
                    $data['content_field_website'] = $this->request->post['content_field_website'];
                } else {
                    $data['content_field_website'] =  0;
                }
                
                if(isset($this->request->post['content_size'])){
                    $data['content_size'] = $this->request->post['content_size'];
                } else {
                    $data['content_size'] =  'md';
                }
                
                $data['sizes'] = array(
                    array('code' => 'sm', 'text' => $this->language->get('text_small')),
                    array('code' => 'md', 'text' => $this->language->get('text_medium')),
                    array('code' => 'lg', 'text' => $this->language->get('text_large'))
                );
                
                // content
                if(isset($this->request->post['content_color'])){
                    $data['content_color'] = $this->request->post['content_color'];
                } else {
                    $data['content_color'] =  'secondary';
                }
                
                $data['colors'] = array();
                foreach($this->model_extension_maza_asset->getColorTypes() as $color){
                    $data['colors'][] = array('code' => $color, 'text' => ucfirst($color));
                }
                
                $this->response->setOutput($this->load->view('extension/mz_content/blog_article/comment_form', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/blog_article/comment_form')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
