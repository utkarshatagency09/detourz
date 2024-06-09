<?php
class ControllerExtensionMzWidgetNewsletter extends maza\layout\Widget {
    public function index() {
        $this->load->language('extension/mz_widget/newsletter');
        
        $this->load->model('tool/image');
        $this->load->model('extension/maza/common');
        
        $data = array();
        
        // Setting
        $setting                                        =  array();
        $setting['widget_status']                       =  0;
        $setting['widget_unsubscribe_status']           =  0;
        $setting['widget_subscribe_button_type']        =  'text';
        $setting['widget_unsubscribe_button_type']      =  'text';
        $setting['widget_subscribe_button_icon_image']  =  array();
        $setting['widget_subscribe_button_icon_svg']    =  array();
        $setting['widget_subscribe_button_icon_font']   =  array();
        $setting['widget_unsubscribe_button_icon_image']=  array();
        $setting['widget_unsubscribe_button_icon_svg']  =  array();
        $setting['widget_unsubscribe_button_icon_font'] =  array();
        $setting['widget_button_icon_size']             =  null;
        $setting['widget_description']                  =  array();
        $setting['widget_notification_channel']         =  array();
        
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $setting = array_merge($setting, $this->request->post);
        }
        
        $data = array_merge($data, $setting);
        
        // Data
        
        $data['list_button_type'] = array(
            array('id' => 'icon', 'name' => $this->language->get('text_icon')),
            array('id' => 'text', 'name' => $this->language->get('text_text')),
            array('id' => 'both', 'name' => $this->language->get('text_both'))
        );
        
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        // Subscribe icon thumb
        $data['thumb_subscribe_button_icon_image'] = array();
        foreach ($data['widget_subscribe_button_icon_image'] as $language_id => $image) {
            if($image){
                $data['thumb_subscribe_button_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
            } else {
                $data['thumb_subscribe_button_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }
        $data['thumb_subscribe_button_icon_svg'] = array();
        foreach ($data['widget_subscribe_button_icon_svg'] as $language_id => $image_svg) {
            if($image_svg){
                $data['thumb_subscribe_button_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
            } else {
                $data['thumb_subscribe_button_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
            }
        }
        
        // Unsubscribe icon thumb
        $data['thumb_unsubscribe_button_icon_image'] = array();
        foreach ($data['widget_unsubscribe_button_icon_image'] as $language_id => $image) {
            if($image){
                $data['thumb_unsubscribe_button_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
            } else {
                $data['thumb_unsubscribe_button_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }
        $data['thumb_unsubscribe_button_icon_svg'] = array();
        foreach ($data['widget_unsubscribe_button_icon_svg'] as $language_id => $image_svg) {
            if($image_svg){
                $data['thumb_unsubscribe_button_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
            } else {
                $data['thumb_unsubscribe_button_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
            }
        }

        // Notification channels
        $this->load->model('extension/maza/notification/channel');

        $data['notification_channels'] = [];

        foreach ($data['widget_notification_channel'] as $channel_id) {
            $channel_info = $this->model_extension_maza_notification_channel->getChannel($channel_id);

            $data['notification_channels'][] = [
                'channel_id' => $channel_info['channel_id'],
                'name' => $channel_info['name'],
            ];
        }
        
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_widget/newsletter', $data));
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
