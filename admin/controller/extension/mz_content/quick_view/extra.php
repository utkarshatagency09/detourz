<?php
class ControllerExtensionMzContentQuickViewExtra extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/quick_view/extra');

                $data = array();
                

                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_brand'])){
                    $data['content_brand'] = $this->request->post['content_brand'];
                } else {
                    $data['content_brand'] =  1;
                }
                
                if(isset($this->request->post['content_model'])){
                    $data['content_model'] = $this->request->post['content_model'];
                } else {
                    $data['content_model'] =  1;
                }
                
                if(isset($this->request->post['content_reward'])){
                    $data['content_reward'] = $this->request->post['content_reward'];
                } else {
                    $data['content_reward'] =  1;
                }
                
                if(isset($this->request->post['content_stock'])){
                    $data['content_stock'] = $this->request->post['content_stock'];
                } else {
                    $data['content_stock'] =  1;
                }
                
                $this->response->setOutput($this->load->view('extension/mz_content/quick_view/extra', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/quick_view/extra')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
