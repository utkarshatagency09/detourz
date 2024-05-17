<?php
class ControllerExtensionMzContentQuickViewBrand extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/quick_view/brand');

                $data = array();
                

                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_caption'])){
                    $data['content_caption'] = $this->request->post['content_caption'];
                } else {
                    $data['content_caption'] =  1;
                }
                
                
                $this->response->setOutput($this->load->view('extension/mz_content/quick_view/brand', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/quick_view/brand')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
