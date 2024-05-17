<?php
class ControllerExtensionMzDesignBreadcrumb extends maza\layout\Design {
    public function index(): void {
        $this->load->language('extension/mz_design/breadcrumb');
        $this->load->model('localisation/language');

        $data = array();
        
        // Status
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        
        // Breadcrumb
        if(isset($this->request->post['design_breadcrumb'])){
            $design_breadcrumb = $this->request->post['design_breadcrumb'];
        } else {
            $design_breadcrumb = array();
        }
        
        $data['design_breadcrumb'] = array();
        
        foreach ($design_breadcrumb as $breadcrumb_item) {
            
            // Link
            if(!empty($breadcrumb_item['url_link_code'])){
                $breadcrumb_item['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $breadcrumb_item['url_link_code']);
            } else {
                $breadcrumb_item['link_info'] = '';
            }
            
            $data['design_breadcrumb'][] = $breadcrumb_item;
        }

        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['language_id'] = $this->config->get('config_language_id');
        
        $data['user_token']  = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/breadcrumb', $data));
    }
}
