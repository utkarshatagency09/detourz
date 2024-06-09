<?php
class ControllerExtensionMzWidgetCountdown extends maza\layout\Widget {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_widget/countdown');
        $this->load->model('localisation/language');
        
        $data = array();
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        // Status
        if(isset($this->request->post['widget_status'])){
            $data['widget_status'] = $this->request->post['widget_status'];
        } else {
            $data['widget_status'] =  0;
        }
        
        // Title
        if(isset($this->request->post['widget_title'])){
            $data['widget_title'] = $this->request->post['widget_title'];
        } else {
            $data['widget_title'] = array();
        }
        
        // Time start
        if(isset($this->request->post['widget_timestart'])){
            $data['widget_timestart'] = $this->request->post['widget_timestart'];
        } else {
            $data['widget_timestart'] =  '';
        }
        
        // Time end
        if(isset($this->request->post['widget_timeend'])){
            $data['widget_timeend'] = $this->request->post['widget_timeend'];
        } else {
            $data['widget_timeend'] =  '';
        }
        
        // Direction
        if(isset($this->request->post['widget_direction'])){
            $data['widget_direction'] = $this->request->post['widget_direction'];
        } else {
            $data['widget_direction'] =  'horizontal';
        }
        
        // Format
        if(isset($this->request->post['widget_format'])){
            $data['widget_format'] = $this->request->post['widget_format'];
        } else {
            $data['widget_format'] =  'dHMS';
        }
        
        // Compact
        if(isset($this->request->post['widget_compact'])){
            $data['widget_compact'] = $this->request->post['widget_compact'];
        } else {
            $data['widget_compact'] =  1;
        }
        
        $this->response->setOutput($this->load->view('extension/mz_widget/countdown', $data));
    }
    
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_widget/countdown')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
    /**
     * Change default setting
     */
    public function getSettings(): array{
        $setting = array();
        
        $setting['widget_cache'] = 'hard';
        
        $setting['xl'] = $setting['lg'] = $setting['md'] = 
        $setting['sm'] = $setting['xs'] = array(
            'widget_flex_grow' => 0,
        );
        
        return \maza\array_merge_subsequence(parent::getSettings(), $setting);
    }
}
