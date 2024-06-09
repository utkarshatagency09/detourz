<?php
class ControllerExtensionMzWidgetLogin extends maza\layout\Widget {
    private $error = array();
    
    public function index(): void {
        $this->load->language('extension/mz_widget/login');
        
        $this->load->model('extension/maza/asset');

        $data = array();
        
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        if(isset($this->request->post['widget_status'])){
            $data['widget_status'] = $this->request->post['widget_status'];
        } else {
            $data['widget_status'] =  0;
        }
        
        if(isset($this->request->post['widget_title'])){
            $data['widget_title'] = $this->request->post['widget_title'];
        } else {
            $data['widget_title'] =  '';
        }
        
        if(isset($this->request->post['widget_size'])){
            $data['widget_size'] = $this->request->post['widget_size'];
        } else {
            $data['widget_size'] =  'md';
        }
        
        $data['sizes'] = array(
            array('code' => 'sm', 'text' => $this->language->get('text_small')),
            array('code' => 'md', 'text' => $this->language->get('text_medium')),
            array('code' => 'lg', 'text' => $this->language->get('text_large'))
        );
        
        // widget
        if(isset($this->request->post['widget_color'])){
            $data['widget_color'] = $this->request->post['widget_color'];
        } else {
            $data['widget_color'] =  'secondary';
        }
        
        $data['colors'] = array();
        foreach($this->model_extension_maza_asset->getColorTypes() as $color){
            $data['colors'][] = array('code' => $color, 'text' => ucfirst($color));
        }
        
        $this->response->setOutput($this->load->view('extension/mz_widget/login', $data));
    }
    
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_widget/login')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
    /**
     * Change default setting
     */
    public function getSettings(): array {
        $setting = parent::getSettings();
        
        $setting['widget_status_customer'] = 'guest';
        $setting['widget_cache'] = 'hard';
        
        return $setting;
    }
}
