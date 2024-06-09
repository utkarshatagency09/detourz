<?php
class ControllerExtensionMzDesignHorizontalLine extends maza\layout\Design {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_design/horizontal_line');
        $this->load->model('localisation/language');
        
        $data = array();
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        
        // Title
        if(isset($this->request->post['design_title'])){
            $data['design_title'] = $this->request->post['design_title'];
        } else {
            $data['design_title'] =  array();
        }
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/horizontal_line', $data));
    }
    
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_design/horizontal_line')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
