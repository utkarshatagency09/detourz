<?php
class ControllerExtensionMzWidgetBanner extends maza\layout\Widget {
    public function index() {
        $this->load->language('extension/mz_widget/banner');
        
        $this->load->model('tool/image');
        
        $data = array();
        
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        
        if(isset($this->request->post['widget_status'])){
            $data['widget_status'] = $this->request->post['widget_status'];
        } else {
            $data['widget_status'] =  0;
        }
        
        if(isset($this->request->post['widget_title'])){
            $data['widget_title'] = $this->request->post['widget_title'];
        } else {
            $data['widget_title'] =  array();
        }
        
        if(isset($this->request->post['widget_alt'])){
            $data['widget_alt'] = $this->request->post['widget_alt'];
        } else {
            $data['widget_alt'] =  array();
        }
        
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        
        // Banner
        if(isset($this->request->post['widget_banner_image'])){
            $data['widget_banner_image'] = $this->request->post['widget_banner_image'];
        } else {
            $data['widget_banner_image'] =  array();
        }
        
        $data['thumb_banner_image'] = array();
        
        if (isset($this->request->post['widget_banner_image'])){
            foreach ($this->request->post['widget_banner_image'] as $language_id => $banner_image) {
                if($banner_image){
                    $data['thumb_banner_image'][$language_id] = $this->model_tool_image->resize($banner_image, 100, 100);
                } else {
                    $data['thumb_banner_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        
        if(isset($this->request->post['widget_banner_width'])){
            $data['widget_banner_width'] = $this->request->post['widget_banner_width'];
        } else {
            $data['widget_banner_width'] =  '';
        }
        
        if(isset($this->request->post['widget_banner_height'])){
            $data['widget_banner_height'] = $this->request->post['widget_banner_height'];
        } else {
            $data['widget_banner_height'] =  '';
        }
        
        if(isset($this->request->post['widget_banner_srcset'])){
            $data['widget_banner_srcset'] =  $this->request->post['widget_banner_srcset'];
        } else {
            $data['widget_banner_srcset'] =  array('lg' => null, 'md' => null, 'sm' => null, 'xs' => null);
        }
        
        if(isset($this->request->post['widget_lazy_loading'])){
            $data['widget_lazy_loading'] = $this->request->post['widget_lazy_loading'];
        } else {
            $data['widget_lazy_loading'] =  0;
        }
        
        if(isset($this->request->post['widget_banner_caption'])){
            $data['widget_banner_caption'] = $this->request->post['widget_banner_caption'];
        } else {
            $data['widget_banner_caption'] =  '';
        }
        
        // URL
        if(isset($this->request->post['widget_url_target'])){
            $data['widget_url_target'] = $this->request->post['widget_url_target'];
        } else {
            $data['widget_url_target'] =  '_self';
        }
        
        if(!empty($this->request->post['widget_url_link_code'])){
            $data['widget_url_link_code'] = $this->request->post['widget_url_link_code'];
            $data['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $this->request->post['widget_url_link_code']);
        } else {
            $data['widget_url_link_code'] =  '';
            $data['link_info'] = '';
        }
        
        // Banner Hover Effects
        if(isset($this->request->post['widget_banner_effect'])){
            $data['widget_banner_effect'] = $this->request->post['widget_banner_effect'];
        } else {
            $data['widget_banner_effect'] =  'no-effect';
        }
        
        $data['image_hover_effects'] = array(
            array('name' => 'No effect', 'code' => 'no-effect'),
            array('name' => $this->language->get('text_effect') . ' 1', 'code' => 'hover-effect-1'),
            array('name' => $this->language->get('text_effect') . ' 2', 'code' => 'hover-effect-2'),
            array('name' => $this->language->get('text_effect') . ' 3', 'code' => 'hover-effect-3'),
            array('name' => $this->language->get('text_effect') . ' 4', 'code' => 'hover-effect-4'),
            array('name' => $this->language->get('text_effect') . ' 5', 'code' => 'hover-effect-5'),
            array('name' => $this->language->get('text_effect') . ' 6', 'code' => 'hover-effect-6'),
            array('name' => $this->language->get('text_effect') . ' 7', 'code' => 'hover-effect-7'),
            array('name' => $this->language->get('text_effect') . ' 8', 'code' => 'hover-effect-8'),
            array('name' => $this->language->get('text_effect') . ' 9', 'code' => 'hover-effect-9'),
            array('name' => $this->language->get('text_effect') . ' 10', 'code' => 'hover-effect-10'),
            array('name' => $this->language->get('text_effect') . ' 11', 'code' => 'hover-effect-11'),
            array('name' => $this->language->get('text_effect') . ' 12', 'code' => 'hover-effect-12'),
        );
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_widget/banner', $data));
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
