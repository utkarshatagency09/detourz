<?php
class ControllerExtensionMzWidgetContactForm extends maza\layout\Widget {
	private $error = array();
        
    public function index() {
        $this->load->language('extension/mz_widget/contact_form');
        
        $this->load->model('localisation/language');
        $this->load->model('extension/maza/asset');

        $data = array();
        
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
        
        // Fields
        if(isset($this->request->post['widget_field_name_text'])){
            $data['widget_field_name_text'] = $this->request->post['widget_field_name_text'];
        } else {
            $data['widget_field_name_text'] = array();
        }
        
        if(isset($this->request->post['widget_field_email_text'])){
            $data['widget_field_email_text'] = $this->request->post['widget_field_email_text'];
        } else {
            $data['widget_field_email_text'] = array();
        }
        
        if(isset($this->request->post['widget_field_subject_text'])){
            $data['widget_field_subject_text'] = $this->request->post['widget_field_subject_text'];
        } else {
            $data['widget_field_subject_text'] = array();
        }
        
        if(isset($this->request->post['widget_field_message_text'])){
            $data['widget_field_message_text'] = $this->request->post['widget_field_message_text'];
        } else {
            $data['widget_field_message_text'] = array();
        }
        
        if(isset($this->request->post['widget_field_submit_text'])){
            $data['widget_field_submit_text'] = $this->request->post['widget_field_submit_text'];
        } else {
            $data['widget_field_submit_text'] = array();
        }
        
        // Layout
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
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        $this->response->setOutput($this->load->view('extension/mz_widget/contact_form', $data));
    }
    
    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/mz_widget/contact_form')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
    
    /**
     * Change default setting
     */
    public function getSettings(): array {
        $setting = parent::getSettings();
        
        $setting['widget_cache'] = 'hard';
        
        return $setting;
    }
}
