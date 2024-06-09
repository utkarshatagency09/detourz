<?php
class ControllerExtensionMzDesignCKEditor extends maza\layout\design {
	private $error = array();
        
        public function index(): void {
                $this->load->language('extension/mz_design/ckeditor');

                $data = array();
                
                // Status
                if(isset($this->request->post['design_status'])){
                    $data['design_status'] = $this->request->post['design_status'];
                } else {
                    $data['design_status'] =  0;
                }
                
                // HTML
                if (isset($this->request->post['design_html'])) {
			$data['design_html'] = $this->request->post['design_html'];
		} else {
			$data['design_html'] = array();
		}
                
                $this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
                
                $this->response->setOutput($this->load->view('extension/mz_design/ckeditor', $data));
        }
        
        protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_design/ckeditor')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        /**
         * Change default setting
         */
        public function getSettings(): array {
            $setting = parent::getSettings();
            
            $setting['design_cache'] = 'hard';
            
            return $setting;
        }
}
