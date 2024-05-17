<?php
class ControllerExtensionMzDesignTable extends maza\layout\Design {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_design/table');
        
        
        $data = array();
        
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        # general
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        
        if(isset($this->request->post['design_title'])){
            $data['design_title'] = $this->request->post['design_title'];
        } else {
            $data['design_title'] =  array();
        }
        
        if(isset($this->request->post['design_caption'])){
            $data['design_caption'] = $this->request->post['design_caption'];
        } else {
            $data['design_caption'] =  array();
        }
        
        if(isset($this->request->post['design_size'])){
            $data['design_size'] = $this->request->post['design_size'];
        } else {
            $data['design_size'] =  'md';
        }
        
        # Head
        if(isset($this->request->post['design_head_status'])){
            $data['design_head_status'] = $this->request->post['design_head_status'];
        } else {
            $data['design_head_status'] =  1;
        }
        
        if(isset($this->request->post['design_head_style'])){
            $data['design_head_style'] = $this->request->post['design_head_style'];
        } else {
            $data['design_head_style'] =  'default';
        }
        
        # styles
        if(isset($this->request->post['design_style_dark'])){
            $data['design_style_dark'] = $this->request->post['design_style_dark'];
        } else {
            $data['design_style_dark'] =  0;
        }
        
        if(isset($this->request->post['design_style_striped'])){
            $data['design_style_striped'] = $this->request->post['design_style_striped'];
        } else {
            $data['design_style_striped'] =  0;
        }
        
        if(isset($this->request->post['design_style_bordered'])){
            $data['design_style_bordered'] = $this->request->post['design_style_bordered'];
        } else {
            $data['design_style_bordered'] =  0;
        }
        
        if(isset($this->request->post['design_style_borderless'])){
            $data['design_style_borderless'] = $this->request->post['design_style_borderless'];
        } else {
            $data['design_style_borderless'] =  0;
        }
        
        if(isset($this->request->post['design_style_hover'])){
            $data['design_style_hover'] = $this->request->post['design_style_hover'];
        } else {
            $data['design_style_hover'] =  0;
        }
        
        #data
        if(isset($this->request->post['design_data'])){
            $data['design_data'] = $this->request->post['design_data'];
        } else {
            $data['design_data'] =  array();
        }
        
        // Options data
        $data['sizes'] = array(
            array('code' => 'sm', 'text' => $this->language->get('text_small')),
            array('code' => 'md', 'text' => $this->language->get('text_regular'))
        );
        
        $data['head_styles'] = array(
            array('code' => 'default', 'text' => $this->language->get('text_default')),
            array('code' => 'dark', 'text' => $this->language->get('text_dark')),
            array('code' => 'light', 'text' => $this->language->get('text_light'))
        );
        
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/table', $data));
    }
    
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_design/table')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
