<?php
class ControllerExtensionMzWidgetNavbar extends maza\layout\Widget {
    public function index() {
        $this->load->language('extension/mz_widget/navbar');
        
        $this->load->model('localisation/language');
        $this->load->model('tool/image');
        $this->load->model('extension/maza/menu');
        $this->load->model('extension/maza/asset');

        $data = array();
        
        // Status
        if(isset($this->request->post['widget_status'])){
            $data['widget_status'] = $this->request->post['widget_status'];
        } else {
            $data['widget_status'] =  0;
        }
        
        // Orientation
        if(isset($this->request->post['widget_orientation'])){
            $data['widget_orientation'] = $this->request->post['widget_orientation'];
        } else {
            $data['widget_orientation'] = 'horizontal';
        }
        
        // Expand
        if(isset($this->request->post['widget_expand'])){
            $data['widget_expand'] = $this->request->post['widget_expand'];
        } else {
            $data['widget_expand'] = 'sm';
        }
        
        // collapsible
        if(isset($this->request->post['widget_collapsible'])){
            $data['widget_collapsible'] = $this->request->post['widget_collapsible'];
        } else {
            $data['widget_collapsible'] = 1;
        }
        
        // collapse by default
        if(isset($this->request->post['widget_collapse'])){
            $data['widget_collapse'] = $this->request->post['widget_collapse'];
        } else {
            $data['widget_collapse'] = -1;
        }
        
        // dropdown hoverable
        if(isset($this->request->post['widget_hoverable'])){
            $data['widget_hoverable'] = $this->request->post['widget_hoverable'];
        } else {
            $data['widget_hoverable'] = 1;
        }
        
        // Menu
        if(isset($this->request->post['widget_menu_id'])){
            $data['widget_menu_id'] = $this->request->post['widget_menu_id'];
        } else {
            $data['widget_menu_id'] = 0;
        }
        $menu_info =  $this->model_extension_maza_menu->getMenu($data['widget_menu_id']);
        if($menu_info){
            $data['widget_menu_name'] =  $menu_info['name'];
        } else {
            $data['widget_menu_name'] =  '';
        }
        
        // Brand
        if(isset($this->request->post['widget_brand'])){
            $data['widget_brand'] = $this->request->post['widget_brand'];
        } else {
            $data['widget_brand'] = array();
        }
        if(isset($this->request->post['widget_brand_icon_image'])){
            $data['widget_brand_icon_image'] = $this->request->post['widget_brand_icon_image'];
        } else {
            $data['widget_brand_icon_image'] = array();
        }
        if(isset($this->request->post['widget_brand_icon_svg'])){
            $data['widget_brand_icon_svg'] = $this->request->post['widget_brand_icon_svg'];
        } else {
            $data['widget_brand_icon_svg'] = array();
        }
        if(isset($this->request->post['widget_brand_icon_font'])){
            $data['widget_brand_icon_font'] = $this->request->post['widget_brand_icon_font'];
        } else {
            $data['widget_brand_icon_font'] = array();
        }
        if(isset($this->request->post['widget_brand_icon_width'])){
            $data['widget_brand_icon_width'] = $this->request->post['widget_brand_icon_width'];
        } else {
            $data['widget_brand_icon_width'] = NULL;
        }
        if(isset($this->request->post['widget_brand_icon_height'])){
            $data['widget_brand_icon_height'] = $this->request->post['widget_brand_icon_height'];
        } else {
            $data['widget_brand_icon_height'] = NULL;
        }
        if(isset($this->request->post['widget_brand_icon_size'])){
            $data['widget_brand_icon_size'] = $this->request->post['widget_brand_icon_size'];
        } else {
            $data['widget_brand_icon_size'] = NULL;
        }
        if(isset($this->request->post['widget_brand_icon_position'])){
            $data['widget_brand_icon_position'] = $this->request->post['widget_brand_icon_position'];
        } else {
            $data['widget_brand_icon_position'] = 'top';
        }
        
        // Image thumb Image
        $data['thumb_brand_icon_image'] = array();
        
        foreach ($data['widget_brand_icon_image'] as $language_id => $image) {
            if($image){
                $data['thumb_brand_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
            } else {
                $data['thumb_brand_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }
        
        // Image thumb svg
        $data['thumb_brand_icon_svg'] = array();
        
        foreach ($data['widget_brand_icon_svg'] as $language_id => $image_svg) {
            if($image_svg){
                $data['thumb_brand_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
            } else {
                $data['thumb_brand_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
            }
        }
        
        // URL
        if(!empty($this->request->post['widget_brand_url_link_code'])){
            $data['widget_brand_url_link_code'] = $this->request->post['widget_brand_url_link_code'];
            $data['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $this->request->post['widget_brand_url_link_code']);
        } else {
            $data['widget_brand_url_link_code'] =  '';
            $data['link_info'] = '';
        }
        
        // Color
        if(isset($this->request->post['widget_bg_color'])){
            $data['widget_bg_color'] = $this->request->post['widget_bg_color'];
        } else {
            $data['widget_bg_color'] = 'default';
        }
        
        if(isset($this->request->post['widget_text_color'])){
            $data['widget_text_color'] = $this->request->post['widget_text_color'];
        } else {
            $data['widget_text_color'] = 'default';
        }
        
        // Expand
        $data['list_expand'] = array(
            array('id' => 0, 'name' => $this->language->get('text_disabled')),
            array('id' => 'xs', 'name' => 'XS'),
            array('id' => 'sm', 'name' => 'SM'),
            array('id' => 'md', 'name' => 'MD'),
            array('id' => 'lg', 'name' => 'LG'),
            array('id' => 'xl', 'name' => 'XL'),
        );
        
        // Bg color
        $data['bg_colors'] = array();
        $data['bg_colors'][] = array('code' => 'default', 'text' => $this->language->get('text_default'));
        
        $color_types = $this->model_extension_maza_asset->getColorTypes();
        foreach($color_types as $color_type){
            $data['bg_colors'][] = array('code' => $color_type, 'text' => ucfirst($color_type));
        }
        
        // nav color
        $data['text_colors'] = array(
            array('code' => 'default', 'text' => $this->language->get('text_default')),
            array('code' => 'dark', 'text' => $this->language->get('text_light')),
            array('code' => 'light', 'text' => $this->language->get('text_dark'))
        );
        
        // Collaped by default
        $data['list_collapse'] = array(
            array('code' => 1, 'text' => $this->language->get('text_enabled')),
            array('code' => 0, 'text' => $this->language->get('text_disabled')),
            array('code' => -1, 'text' => $this->language->get('text_except_home')),
        );
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        // Image
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        $data['user_token']  = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_widget/navbar', $data));
    }
}
