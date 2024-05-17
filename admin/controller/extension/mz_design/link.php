<?php
class ControllerExtensionMzDesignLink extends maza\layout\Design {
    public function index(): void {
        $this->load->language('extension/mz_design/link');
        
        $this->load->model('tool/image');
        $this->load->model('localisation/language');

        $data = array();
        
        # Status
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        
        # URL
        if(isset($this->request->post['design_url_target'])){
            $data['design_url_target'] = $this->request->post['design_url_target'];
        } else {
            $data['design_url_target'] =  '_self';
        }
        
        if(isset($this->request->post['design_url_nofollow'])){
            $data['design_url_nofollow'] = $this->request->post['design_url_nofollow'];
        } else {
            $data['design_url_nofollow'] =  0;
        }
        
        if(!empty($this->request->post['design_url_link_code'])){
            $data['design_url_link_code'] = $this->request->post['design_url_link_code'];
            $data['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $this->request->post['design_url_link_code']);
        } else {
            $data['design_url_link_code'] =  '';
            $data['link_info'] = '';
        }
        
        // Link
        # Name
        if(isset($this->request->post['design_name'])){
            $data['design_name'] = $this->request->post['design_name'];
        } else {
            $data['design_name'] =  array();
        }
        
        # Icon
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
        
        // Icon position
        if(isset($this->request->post['design_icon_position'])){
            $data['design_icon_position'] = $this->request->post['design_icon_position'];
        } else {
            $data['design_icon_position'] = 'left';
        }
        
        $data['list_icon_position'] = array(
            array('id' => 'top', 'name' => $this->language->get('text_top')),
            array('id' => 'left', 'name' => $this->language->get('text_left')),
            array('id' => 'right', 'name' => $this->language->get('text_right')),
        );
        
        # show
        if(isset($this->request->post['design_show'])){
            $data['design_show'] = $this->request->post['design_show'];
        } else {
            $data['design_show'] =  'both';
        }
        
        $data['list_show'] = array(
            array('id' => 'icon', 'name' => $this->language->get('text_icon')),
            array('id' => 'text', 'name' => $this->language->get('text_name')),
            array('id' => 'both', 'name' => $this->language->get('text_both')),
        );
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/link', $data));
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
