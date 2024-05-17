<?php
class ControllerExtensionMzWidgetNewsletter extends maza\layout\Widget {
	public function index(array $setting) {
        if(!$this->config->get('module_mz_newsletter_status')) return;
            
        $this->load->language('extension/mz_widget/newsletter');
    
        $data = array();
        
        // Action
        $data['action_subscribe'] = $this->url->link('extension/maza/newsletter/subscribe');
        $data['action_unsubscribe'] = $this->url->link('extension/maza/newsletter/unsubscribe');
        
        
        // Newsletter text
        $description = maza\getOfLanguage($setting['widget_description']);

        $data['title']              = !empty($description['title'])?$description['title']:false;
        $data['description']        = !empty($description['description'])?$description['description']:false;
        $data['input_placeholder']  = !empty($description['input_placeholder'])?$description['input_placeholder']:$this->language->get('text_email');
        $data['text_subscribe']     = !empty($description['subscribe'])?$description['subscribe']:$this->language->get('text_subscribe');
        $data['text_unsubscribe']   = !empty($description['unsubscribe'])?$description['unsubscribe']:$this->language->get('text_unsubscribe');
        
        // Setting
        $data['unsubscribe_status']     = $setting['widget_unsubscribe_status'];
        $data['subscribe_button_type']  = $setting['widget_subscribe_button_type'];
        $data['unsubscribe_button_type']= $setting['widget_unsubscribe_button_type'];
        $data['button_icon_size']       = $setting['widget_button_icon_size'];
        
        // Icon
        // Subscribe button icon font
        $data['subscribe_button_icon_font'] = maza\getOfLanguage($setting['widget_subscribe_button_icon_font']);
        
        // Subscribe button icon svg
        $widget_subscribe_button_icon_svg = maza\getOfLanguage($setting['widget_subscribe_button_icon_svg']);
        if($widget_subscribe_button_icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_subscribe_button_icon_svg)){
            $data['subscribe_button_icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_subscribe_button_icon_svg);
        } else {
            $data['subscribe_button_icon_svg'] = false;
        }
        
        // Subscribe button icon image
        $widget_subscribe_button_icon_image = maza\getOfLanguage($setting['widget_subscribe_button_icon_image']);
        if($widget_subscribe_button_icon_image && is_file(DIR_IMAGE . $widget_subscribe_button_icon_image)){
            list($width, $height) = $this->model_extension_maza_image->getEstimatedSize($widget_subscribe_button_icon_image, $setting['widget_button_icon_size'], $setting['widget_button_icon_size']);
            
            $data['subscribe_button_image_width'] = $width;
            $data['subscribe_button_image_height'] = $height;
            
            $data['subscribe_button_icon_image'] = $this->model_tool_image->resize($widget_subscribe_button_icon_image, $width, $height);
        } else {
            $data['subscribe_button_icon_image'] = false;
        }
        
        // unsubscribe button icon font
        $data['unsubscribe_button_icon_font'] = maza\getOfLanguage($setting['widget_unsubscribe_button_icon_font']);
        
        // Subscribe button icon svg
        $widget_unsubscribe_button_icon_svg = maza\getOfLanguage($setting['widget_unsubscribe_button_icon_svg']);
        if($widget_unsubscribe_button_icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_unsubscribe_button_icon_svg)){
            $data['unsubscribe_button_icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_unsubscribe_button_icon_svg);
        } else {
            $data['unsubscribe_button_icon_svg'] = false;
        }
        
        // unsubscribe button icon image
        $widget_unsubscribe_button_icon_image = maza\getOfLanguage($setting['widget_unsubscribe_button_icon_image']);
        if($widget_unsubscribe_button_icon_image && is_file(DIR_IMAGE . $widget_unsubscribe_button_icon_image)){
            list($width, $height) = $this->model_extension_maza_image->getEstimatedSize($widget_unsubscribe_button_icon_image, $setting['widget_button_icon_size'], $setting['widget_button_icon_size']);
            
            $data['unsubscribe_button_image_width'] = $width;
            $data['unsubscribe_button_image_height'] = $height;
            
            $data['unsubscribe_button_icon_image'] = $this->model_tool_image->resize($widget_unsubscribe_button_icon_image, $width, $height);
        } else {
            $data['unsubscribe_button_icon_image'] = false;
        }
        
        // Notification
        $data['notification_channel'] = $setting['widget_notification_channel']??[];

        return $this->load->view('extension/mz_widget/newsletter', $data);
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
