<?php
class ControllerExtensionMzDesignImage extends maza\layout\Design {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_design/image');
        
        $this->load->model('tool/image');
        $this->load->model('extension/maza/common');
        
        $data = array();
        
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        
        if(isset($this->request->post['design_alt'])){
            $data['design_alt'] = $this->request->post['design_alt'];
        } else {
            $data['design_alt'] =  array();
        }
        
        if(isset($this->request->post['design_caption'])){
            $data['design_caption'] = $this->request->post['design_caption'];
        } else {
            $data['design_caption'] =  array();
        }
        
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
        // -- URL
        
        if(isset($this->request->post['design_style'])){
            $data['design_style'] = $this->request->post['design_style'];
        } else {
            $data['design_style'] =  'default';
        }

        if(isset($this->request->post['design_lazy_loading'])){
            $data['design_lazy_loading'] = $this->request->post['design_lazy_loading'];
        } else {
            $data['design_lazy_loading'] =  0;
        }
        
        // Data
        $data['styles'] = array(
            array('code' => 'default', 'text' => $this->language->get('text_default')),
            array('code' => 'rounded', 'text' => $this->language->get('text_rounded')),
            array('code' => 'rounded-circle', 'text' => $this->language->get('text_circle')),
            array('code' => 'img-thumbnail', 'text' => $this->language->get('text_thumbnail')),
        );
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/image', $data));
    }
}
