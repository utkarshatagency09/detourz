<?php
class ControllerExtensionMzDesignAlert extends maza\layout\Design {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_design/alert');
        
        $this->load->model('tool/image');
        $this->load->model('extension/maza/common');
        $this->load->model('extension/maza/content_builder');
        $this->load->model('extension/maza/asset');
        $this->load->model('localisation/language');
        
        $data = array();
        
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

        // Title Size
        if(isset($this->request->post['design_title_size'])){
            $data['design_title_size'] = $this->request->post['design_title_size'];
        } else {
            $data['design_title_size'] =  '';
        }
        
        // closable
        if(isset($this->request->post['design_dismissible'])){
            $data['design_dismissible'] = $this->request->post['design_dismissible'];
        } else {
            $data['design_dismissible'] =  0;
        }
        
        // design
        if(isset($this->request->post['design_color'])){
            $data['design_color'] = $this->request->post['design_color'];
        } else {
            $data['design_color'] =  'primary';
        }
        
        $color_types = $this->model_extension_maza_asset->getColorTypes();
        
        $data['colors'] = array();
        foreach($color_types as $color_type){
            $data['colors'][] = array('code' => $color_type, 'text' => ucfirst($color_type));
        }
        
        // HTML
        if(isset($this->request->post['design_html'])){
            $data['design_html'] = $this->request->post['design_html'];
        } else {
            $data['design_html'] =  array();
        }
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/alert', $data));
    }
}
