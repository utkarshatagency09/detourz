<?php
class ControllerExtensionMzDesignMenu extends maza\layout\Design {
    public function index() {
        $this->load->language('extension/mz_design/menu');
        
        $this->load->model('localisation/language');
        $this->load->model('tool/image');

        $data = array();
        
        // Status
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        
        // Orientation
        if(isset($this->request->post['design_orientation'])){
            $data['design_orientation'] = $this->request->post['design_orientation'];
        } else {
            $data['design_orientation'] = 'vertical';
        }
        
        // show
        if(isset($this->request->post['design_show'])){
            $data['design_show'] = $this->request->post['design_show'];
        } else {
            $data['design_show'] = 'both';
        }
        
        $data['list_show'] = array(
            array('id' => 'text', 'name' => $this->language->get('text_text')),
            array('id' => 'icon', 'name' => $this->language->get('text_icon')),
            array('id' => 'both', 'name' => $this->language->get('text_both')),
        );
        
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

        // Title
        if(isset($this->request->post['design_title'])){
            $data['design_title'] = $this->request->post['design_title'];
        } else {
            $data['design_title'] = array();
        }

        // Title URL
        if(!empty($this->request->post['design_title_url_link_code'])){
            $data['design_title_url_link_code'] = $this->request->post['design_title_url_link_code'];
            $data['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $this->request->post['design_title_url_link_code']);
        } else {
            $data['design_title_url_link_code'] =  '';
            $data['link_info'] = '';
        }
        
        // Items
        if(isset($this->request->post['design_items'])){
            $design_items = $this->request->post['design_items'];
        } else {
            $design_items = array();
        }
        
        $data['design_items'] = array();
        
        foreach ($design_items as $design_item) {
            // Icon thumb Image
            $design_item['thumb_icon_image'] = array();

            foreach ($design_item['icon_image'] as $language_id => $image) {
                if($image){
                    $design_item['thumb_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
                } else {
                    $design_item['thumb_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }

            // Icon thumb svg
            $design_item['thumb_icon_svg'] = array();

            foreach ($design_item['icon_svg'] as $language_id => $image_svg) {
                if($image_svg){
                    $design_item['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
                } else {
                    $design_item['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
            
            // Link
            if(!empty($design_item['url_link_code'])){
                $design_item['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $design_item['url_link_code']);
            } else {
                $design_item['link_info'] = '';
            }
            
            $data['design_items'][] = $design_item;
        }
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['language_id'] = $this->config->get('config_language_id');
        
        // Image
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        if(isset($this->request->post['design_image_image'])){
            $data['design_image_image'] = $this->request->post['design_image_image'];
        } else {
            $data['design_image_image'] = array();
        }
        if(isset($this->request->post['design_image_svg'])){
            $data['design_image_svg'] = $this->request->post['design_image_svg'];
        } else {
            $data['design_image_svg'] = array();
        }
        if(isset($this->request->post['design_image_font'])){
            $data['design_image_font'] = $this->request->post['design_image_font'];
        } else {
            $data['design_image_font'] = array();
        }
        if(isset($this->request->post['design_image_width'])){
            $data['design_image_width'] = $this->request->post['design_image_width'];
        } else {
            $data['design_image_width'] = NULL;
        }
        if(isset($this->request->post['design_image_height'])){
            $data['design_image_height'] = $this->request->post['design_image_height'];
        } else {
            $data['design_image_height'] = NULL;
        }
        if(isset($this->request->post['design_image_size'])){
            $data['design_image_size'] = $this->request->post['design_image_size'];
        } else {
            $data['design_image_size'] = NULL;
        }
        if(isset($this->request->post['design_image_position'])){
            $data['design_image_position'] = $this->request->post['design_image_position'];
        } else {
            $data['design_image_position'] = 'top';
        }
        
        // Image thumb Image
        $data['thumb_image_image'] = array();
        
        foreach ($data['design_image_image'] as $language_id => $image) {
            if($image){
                $data['thumb_image_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
            } else {
                $data['thumb_image_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }
        
        // Image thumb svg
        $data['thumb_image_svg'] = array();
        
        foreach ($data['design_image_svg'] as $language_id => $image_svg) {
            if($image_svg){
                $data['thumb_image_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
            } else {
                $data['thumb_image_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
            }
        }
        
        // Image position
        $data['list_image_position'] = array(
            array('id' => 'top', 'name' => $this->language->get('text_top')),
            array('id' => 'bottom', 'name' => $this->language->get('text_bottom')),
            array('id' => 'left', 'name' => $this->language->get('text_left')),
            array('id' => 'right', 'name' => $this->language->get('text_right')),
        );

        $data['user_token']  = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/menu', $data));
    }
}
