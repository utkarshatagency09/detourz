<?php
class ControllerExtensionMzWidgetTotal extends maza\layout\Widget {
    public function index(): void {
        $this->load->language('extension/mz_widget/total');

        $setting = array(
            'widget_status' => 1,
            'widget_title' => array(),
            'widget_product' => 1,
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_widget/total', $data));
    }
}
