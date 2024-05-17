<?php
class ControllerExtensionMzDesignListGroup extends maza\layout\Design {
    public function index(): void {
        $this->load->language('extension/mz_design/list_group');
        
        $this->load->model('localisation/language');
        $this->load->model('tool/image');
        
        $data = array();
        
        // Status
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        
        // Title
        if(isset($this->request->post['design_title'])){
            $data['design_title'] = $this->request->post['design_title'];
        } else {
            $data['design_title'] = array();
        }
        
        // Icon size
        if(isset($this->request->post['design_icon_width'])){
            $data['design_icon_width'] = $this->request->post['design_icon_width'];
        } else {
            $data['design_icon_width'] = '';
        }
        if(isset($this->request->post['design_icon_height'])){
            $data['design_icon_height'] = $this->request->post['design_icon_height'];
        } else {
            $data['design_icon_height'] = '';
        }
        
        // List
        if(isset($this->request->post['design_list'])){
            $design_list = $this->request->post['design_list'];
        } else {
            $design_list = array();
        }
        
        $data['design_list'] = array();
        
        foreach ($design_list as $list_item) {
            // Icon thumb Image
            $list_item['thumb_icon_image'] = array();

            foreach ($list_item['icon_image'] as $language_id => $image) {
                if($image){
                    $list_item['thumb_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
                } else {
                    $list_item['thumb_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }

            // Icon thumb svg
            $list_item['thumb_icon_svg'] = array();

            foreach ($list_item['icon_svg'] as $language_id => $image_svg) {
                if($image_svg){
                    $list_item['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
                } else {
                    $list_item['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
            
            // Link
            if(!empty($list_item['url_link_code'])){
                $list_item['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $list_item['url_link_code']);
            } else {
                $list_item['link_info'] = '';
            }
            
            $data['design_list'][] = $list_item;
        }
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['language_id'] = $this->config->get('config_language_id');
        
        // Image
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        $data['user_token']  = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/list_group', $data));
    }
}
