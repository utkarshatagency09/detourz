<?php
class ControllerExtensionMzWidgetVideo extends maza\layout\Widget {
    public function index(): void {
        $this->load->language('extension/mz_widget/video');

        $setting = array(
            'widget_status' => 1,
            'widget_title' => array(),
            'widget_url' => '',
            'widget_image' => array(),
            'widget_image_width' => 500,
            'widget_image_height' => 400,
            'widget_image_caption' => array(),
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }

        $this->load->model('tool/image');

        $data['thumb_image'] = array();
        
        foreach ($data['widget_image'] as $language_id => $image) {
            if($image){
                $data['thumb_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
            } else {
                $data['thumb_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_widget/video', $data));
    }
}
