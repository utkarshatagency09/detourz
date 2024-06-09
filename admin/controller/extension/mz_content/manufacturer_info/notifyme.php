<?php
class ControllerExtensionMzContentManufacturerInfoNotifyme extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/manufacturer_info/notifyme');
        
        $setting = array(
            'content_status' => 0,
            'content_color' => 'primary',
            'content_outline' => 1,
            'content_size' => 'md',
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }
        
        $this->load->model('extension/maza/asset');
        
        $data['colors'] = array();

        foreach($this->model_extension_maza_asset->getColorTypes() as $color){
            $data['colors'][] = array('code' => $color, 'text' => ucfirst($color));
        }

        $data['button_sizes'] = array(
            array('code' => 'sm', 'text' => $this->language->get('text_small')),
            array('code' => 'md', 'text' => $this->language->get('text_medium')),
            array('code' => 'lg', 'text' => $this->language->get('text_large'))
        );

        if(!$this->config->get('maza_notification_status')){
            $data['warning'] = sprintf($this->language->get('error_system'), $this->url->link('extension/maza/system', 'user_token=' . $this->session->data['user_token'], true));
        }
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_content/manufacturer_info/notifyme', $data));
    }
}
