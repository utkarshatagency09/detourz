<?php
class ControllerExtensionMzContentBlogSearchArticles extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/blog_search/articles');

                $data = array();
                

                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_list_grid'])){
                    $data['content_list_grid'] = $this->request->post['content_list_grid'];
                } else {
                    $data['content_list_grid'] =  'grid';
                }
                
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
                
                // Articles article element
                if(isset($this->request->post['content_comment_count'])){
                    $data['content_comment_count'] = $this->request->post['content_comment_count'];
                } else {
                    $data['content_comment_count'] =  1;
                }
                
                if(isset($this->request->post['content_viewed_count'])){
                    $data['content_viewed_count'] = $this->request->post['content_viewed_count'];
                } else {
                    $data['content_viewed_count'] =  1;
                }
                
                if(isset($this->request->post['content_author'])){
                    $data['content_author'] = $this->request->post['content_author'];
                } else {
                    $data['content_author'] =  1;
                }

                if(isset($this->request->post['content_category'])){
                    $data['content_category'] = $this->request->post['content_category'];
                } else {
                    $data['content_category'] =  0;
                }
                
                if(isset($this->request->post['content_timestamp'])){
                    $data['content_timestamp'] = $this->request->post['content_timestamp'];
                } else {
                    $data['content_timestamp'] =  1;
                }
                
                if(isset($this->request->post['content_readmore'])){
                    $data['content_readmore'] = $this->request->post['content_readmore'];
                } else {
                    $data['content_readmore'] =  1;
                }
                
                if(isset($this->request->post['content_description'])){
                    $data['content_description'] = $this->request->post['content_description'];
                } else {
                    $data['content_description'] =  1;
                }
                
                
                $this->response->setOutput($this->load->view('extension/mz_content/blog_search/articles', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/blog_search/articles')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
