<?php
class ControllerExtensionMzContentCategoryListGrid extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/category/list_grid');
                $this->load->model('extension/maza/asset');

                $data = array();
                

                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_color'])){
                    $data['content_color'] = $this->request->post['content_color'];
                } else {
                    $data['content_color'] =  'primary';
                }
                
                $data['colors'] = array();
                foreach($this->model_extension_maza_asset->getColorTypes() as $color){
                    $data['colors'][] = array('code' => $color, 'text' => ucfirst($color));
                }
                
                
                $this->response->setOutput($this->load->view('extension/mz_content/category/list_grid', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/category/list_grid')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
