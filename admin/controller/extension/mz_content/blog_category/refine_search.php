<?php
class ControllerExtensionMzContentBlogCategoryRefineSearch extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/blog_category/refine_search');

                $data = array();
                

                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_title'])){
                    $data['content_title'] = $this->request->post['content_title'];
                } else {
                    $data['content_title'] =  4;
                }
                
                if(isset($this->request->post['content_style'])){
                    $data['content_style'] = $this->request->post['content_style'];
                } else {
                    $data['content_style'] =  'text';
                }
                
                $data['styles'] = array(
                    array('code' => 'text', 'text' => $this->language->get('text_text')),
                    array('code' => 'image', 'text' => $this->language->get('text_image')),
                    array('code' => 'both', 'text' => $this->language->get('text_both')),
                );
                
                // Grid size
                if(isset($this->request->post['content_column_xs'])){
                    $data['content_column_xs'] = $this->request->post['content_column_xs'];
                } else {
                    $data['content_column_xs'] =  2;
                }
                
                if(isset($this->request->post['content_column_sm'])){
                    $data['content_column_sm'] = $this->request->post['content_column_sm'];
                } else {
                    $data['content_column_sm'] =  2;
                }
                
                if(isset($this->request->post['content_column_md'])){
                    $data['content_column_md'] = $this->request->post['content_column_md'];
                } else {
                    $data['content_column_md'] =  3;
                }
                
                if(isset($this->request->post['content_column_lg'])){
                    $data['content_column_lg'] = $this->request->post['content_column_lg'];
                } else {
                    $data['content_column_lg'] =  4;
                }
                
                if(isset($this->request->post['content_column_xl'])){
                    $data['content_column_xl'] = $this->request->post['content_column_xl'];
                } else {
                    $data['content_column_xl'] =  5;
                }
                
                
                $this->response->setOutput($this->load->view('extension/mz_content/blog_category/refine_search', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/blog_category/refine_search')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
