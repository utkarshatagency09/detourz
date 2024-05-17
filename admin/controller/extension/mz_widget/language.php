<?php
class ControllerExtensionMzWidgetLanguage extends maza\layout\Widget {
    private $error = array();
    
    public function index(): void {
        $this->load->language('extension/mz_widget/language');

        $data = array();
        

        if(isset($this->request->post['widget_status'])){
            $data['widget_status'] = $this->request->post['widget_status'];
        } else {
            $data['widget_status'] =  0;
        }
        
        if(isset($this->request->post['widget_position'])){
            $data['widget_position'] = $this->request->post['widget_position'];
        } else {
            $data['widget_position'] =  'left';
        }
        
        
        $this->response->setOutput($this->load->view('extension/mz_widget/language', $data));
    }
    
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_widget/language')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
    /**
     * Change default setting
     */
    public function getSettings(): array {
        $setting['xl'] = $setting['lg'] = $setting['md'] = 
        $setting['sm'] = $setting['xs'] = array(
            'widget_flex_grow' => 0,
            'widget_flex_shrink' => 0,
        );
        
        return \maza\array_merge_subsequence(parent::getSettings(), $setting);
    }
}
