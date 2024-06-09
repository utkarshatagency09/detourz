<?php
class ControllerExtensionMzContentQuickViewQuantity extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/quick_view/quantity');

                $data = array();
                

                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_size'])){
                    $data['content_size'] = $this->request->post['content_size'];
                } else {
                    $data['content_size'] =  'md';
                }
                
                $data['button_sizes'] = array(
                    array('code' => 'sm', 'text' => $this->language->get('text_small')),
                    array('code' => 'md', 'text' => $this->language->get('text_medium')),
                    array('code' => 'lg', 'text' => $this->language->get('text_large'))
                );
                
                $this->response->setOutput($this->load->view('extension/mz_content/quick_view/quantity', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/quick_view/quantity')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
