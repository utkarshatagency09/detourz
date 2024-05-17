<?php
class ControllerExtensionMzWidgetGallery extends maza\layout\Widget {
    public function index(): void {
        $this->load->language('extension/mz_widget/gallery');

        $setting = array(
            'widget_status' => 1,
            'widget_title' => array(),
            'widget_image_width' => 100,
            'widget_image_height' => 100,
            'widget_gallery_id' => 0,
            'widget_gutter' => 1,
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }

        $this->load->model('extension/maza/gallery');

        $gallery_info = $this->model_extension_maza_gallery->getGallery($data['widget_gallery_id']);

        if($gallery_info){
            $data['widget_gallery'] = $gallery_info['name'];
        } else {
            $data['widget_gallery'] = '';
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_widget/gallery', $data));
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
