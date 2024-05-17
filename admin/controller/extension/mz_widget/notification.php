<?php
class ControllerExtensionMzWidgetNotification extends maza\layout\Widget {
    public function index(): void {
        $this->load->language('extension/mz_widget/notification');
        
        $this->load->model('tool/image');
        $this->load->model('localisation/language');

        $setting = array(
            'widget_status'         => 1,
            'widget_title'          => array(),
            'widget_icon_image'     => array(),
            'widget_icon_svg'       => array(),
            'widget_icon_font'      => array(),
            'widget_icon_width'     => '',
            'widget_icon_height'    => '',
            'widget_icon_size'      => '',
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }
        
        
        // Image
        $data['thumb_icon_image'] = array();
        
        if (isset($this->request->post['widget_icon_image'])){
            foreach ($this->request->post['widget_icon_image'] as $language_id => $icon_image) {
                if($icon_image){
                    $data['thumb_icon_image'][$language_id] = $this->model_tool_image->resize($icon_image, 100, 100);
                } else {
                    $data['thumb_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        
        // image svg
        $data['thumb_icon_svg'] = array();
        
        if (isset($this->request->post['widget_icon_svg'])){
            foreach ($this->request->post['widget_icon_svg'] as $language_id => $icon_svg) {
                if($icon_svg){
                    $data['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $icon_svg;
                } else {
                    $data['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }

        // Icon
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';

        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        $this->response->setOutput($this->load->view('extension/mz_widget/notification', $data));
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
