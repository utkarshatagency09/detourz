<?php
class ControllerExtensionMzDesignButton extends maza\layout\Design {
    public function index(): void {
        $this->load->language('extension/mz_design/button');
        
        $this->load->model('tool/image');
        $this->load->model('localisation/language');
        $this->load->model('extension/maza/asset');
        
        $data = array();
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        // Status
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        
        // URL
        if(isset($this->request->post['design_url_target'])){
            $data['design_url_target'] = $this->request->post['design_url_target'];
        } else {
            $data['design_url_target'] =  '_self';
        }

        if(!empty($this->request->post['design_url_link_code'])){
            $data['design_url_link_code'] = $this->request->post['design_url_link_code'];
            $data['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $this->request->post['design_url_link_code']);
        } else {
            $data['design_url_link_code'] =  '';
            $data['link_info'] = '';
        }
        
        // Button
        // Name
        if(isset($this->request->post['design_name'])){
            $data['design_name'] = $this->request->post['design_name'];
        } else {
            $data['design_name'] =  array();
        }
        
        // Icon
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        // Image
        if(isset($this->request->post['design_icon_image'])){
            $data['design_icon_image'] = $this->request->post['design_icon_image'];
        } else {
            $data['design_icon_image'] =  array();
        }
        
        // image svg
        if(isset($this->request->post['design_icon_svg'])){
            $data['design_icon_svg'] = $this->request->post['design_icon_svg'];
        } else {
            $data['design_icon_svg'] =  array();
        }
        
        // image font
        if(isset($this->request->post['design_icon_font'])){
            $data['design_icon_font'] = $this->request->post['design_icon_font'];
        } else {
            $data['design_icon_font'] =  array();
        }
        
        // Image
        $data['thumb_icon_image'] = array();
        
        if (isset($this->request->post['design_icon_image'])){
            foreach ($this->request->post['design_icon_image'] as $language_id => $icon_image) {
                if($icon_image){
                    $data['thumb_icon_image'][$language_id] = $this->model_tool_image->resize($icon_image, 100, 100);
                } else {
                    $data['thumb_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        
        // image svg
        $data['thumb_icon_svg'] = array();
        
        if (isset($this->request->post['design_icon_svg'])){
            foreach ($this->request->post['design_icon_svg'] as $language_id => $icon_svg) {
                if($icon_svg){
                    $data['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $icon_svg;
                } else {
                    $data['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        if(isset($this->request->post['design_icon_width'])){
            $data['design_icon_width'] = $this->request->post['design_icon_width'];
        } else {
            $data['design_icon_width'] =  '';
        }
        
        if(isset($this->request->post['design_icon_height'])){
            $data['design_icon_height'] = $this->request->post['design_icon_height'];
        } else {
            $data['design_icon_height'] =  '';
        }
        
        if(isset($this->request->post['design_icon_size'])){
            $data['design_icon_size'] = $this->request->post['design_icon_size'];
        } else {
            $data['design_icon_size'] =  '';
        }

        if(isset($this->request->post['design_icon_position'])){
            $data['design_icon_position'] = $this->request->post['design_icon_position'];
        } else {
            $data['design_icon_position'] = 'left';
        }
        
        $data['list_icon_position'] = array(
            array('id' => 'left', 'name' => $this->language->get('text_left')),
            array('id' => 'right', 'name' => $this->language->get('text_right')),
        );

        // design
        if(isset($this->request->post['design_color'])){
            $data['design_color'] = $this->request->post['design_color'];
        } else {
            $data['design_color'] =  'primary';
        }
        
        $color_types = $this->model_extension_maza_asset->getColorTypes();
        
        $data['colors'] = array();
        foreach($color_types as $color_type){
            $data['colors'][] = array('code' => $color_type, 'text' => ucfirst($color_type));
        }
        
        if(isset($this->request->post['design_outline'])){
            $data['design_outline'] = $this->request->post['design_outline'];
        } else {
            $data['design_outline'] =  0;
        }
        
        if(isset($this->request->post['design_size'])){
            $data['design_size'] = $this->request->post['design_size'];
        } else {
            $data['design_size'] =  'md';
        }
        
        $data['button_sizes'] = array(
            array('code' => 'sm', 'text' => $this->language->get('text_small')),
            array('code' => 'md', 'text' => $this->language->get('text_medium')),
            array('code' => 'lg', 'text' => $this->language->get('text_large'))
        );
        
        if(isset($this->request->post['design_width'])){
            $data['design_width'] = $this->request->post['design_width'];
        } else {
            $data['design_width'] =  'inline';
        }
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/button', $data));
    }
    
    /**
     * Change default setting
     */
    public function getSettings(): array {
        $setting['xl'] = $setting['lg'] = $setting['md'] = 
        $setting['sm'] = $setting['xs'] = array(
            'design_flex_grow' => 0,
            'design_flex_shrink' => 0,
        );
        
        return \maza\array_merge_subsequence(parent::getSettings(), $setting);
    }
}
