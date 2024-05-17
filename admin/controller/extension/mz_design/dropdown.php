<?php
class ControllerExtensionMzDesignDropdown extends maza\layout\Design {
    public function index() {
        $this->load->language('extension/mz_design/dropdown');
        
        $this->load->model('localisation/language');
        $this->load->model('tool/image');

        $data = array();
        
        // -- General -------------------------------------------
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        if(isset($this->request->post['design_type'])){
            $data['design_type'] = $this->request->post['design_type'];
        } else {
            $data['design_type'] = 'link';
        }
        if(isset($this->request->post['design_direction'])){
            $data['design_direction'] = $this->request->post['design_direction'];
        } else {
            $data['design_direction'] = 'dropdown';
        }
        if(isset($this->request->post['design_alignment'])){
            $data['design_alignment'] = $this->request->post['design_alignment'];
        } else {
            $data['design_alignment'] = 'left';
        }

        // -- Button ------------------------------
        if(isset($this->request->post['design_btn_color'])){
            $data['design_btn_color'] = $this->request->post['design_btn_color'];
        } else {
            $data['design_btn_color'] =  'primary';
        }

        $this->load->model('extension/maza/asset');
        
        $color_types = $this->model_extension_maza_asset->getColorTypes();
        
        $data['colors'] = array();
        foreach($color_types as $color_type){
            $data['colors'][] = array('code' => $color_type, 'text' => ucfirst($color_type));
        }

        if(isset($this->request->post['design_btn_size'])){
            $data['design_btn_size'] = $this->request->post['design_btn_size'];
        } else {
            $data['design_btn_size'] =  'md';
        }

        $data['list_btn_size'] = array(
            array('id' => 'sm', 'name' => $this->language->get('text_small')),
            array('id' => 'md', 'name' => $this->language->get('text_medium')),
            array('id' => 'lg', 'name' => $this->language->get('text_large')),
        );

        if(isset($this->request->post['design_btn_outline'])){
            $data['design_btn_outline'] = $this->request->post['design_btn_outline'];
        } else {
            $data['design_btn_outline'] =  '1';
        }

        // -- Title ---------------------------------------------
        if(isset($this->request->post['design_title'])){
            $data['design_title'] = $this->request->post['design_title'];
        } else {
            $data['design_title'] = array();
        }

        if(isset($this->request->post['design_title_show'])){
            $data['design_title_show'] = $this->request->post['design_title_show'];
        } else {
            $data['design_title_show'] = 'both';
        }

        if(isset($this->request->post['design_title_icon_image'])){
            $data['design_title_icon_image'] = $this->request->post['design_title_icon_image'];
        } else {
            $data['design_title_icon_image'] = array();
        }
        if(isset($this->request->post['design_title_icon_svg'])){
            $data['design_title_icon_svg'] = $this->request->post['design_title_icon_svg'];
        } else {
            $data['design_title_icon_svg'] = array();
        }
        if(isset($this->request->post['design_title_icon_font'])){
            $data['design_title_icon_font'] = $this->request->post['design_title_icon_font'];
        } else {
            $data['design_title_icon_font'] = array();
        }
        if(isset($this->request->post['design_title_icon_width'])){
            $data['design_title_icon_width'] = $this->request->post['design_title_icon_width'];
        } else {
            $data['design_title_icon_width'] = NULL;
        }
        if(isset($this->request->post['design_title_icon_height'])){
            $data['design_title_icon_height'] = $this->request->post['design_title_icon_height'];
        } else {
            $data['design_title_icon_height'] = NULL;
        }
        if(isset($this->request->post['design_title_icon_size'])){
            $data['design_title_icon_size'] = $this->request->post['design_title_icon_size'];
        } else {
            $data['design_title_icon_size'] = NULL;
        }
        if(isset($this->request->post['design_title_icon_position'])){
            $data['design_title_icon_position'] = $this->request->post['design_title_icon_position'];
        } else {
            $data['design_title_icon_position'] = 'left';
        }
        
        // Image thumb Image
        $data['thumb_title_icon_image'] = array();
        
        foreach ($data['design_title_icon_image'] as $language_id => $image) {
            if($image){
                $data['thumb_title_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
            } else {
                $data['thumb_title_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }
        
        // Image thumb svg
        $data['thumb_title_icon_svg'] = array();
        
        foreach ($data['design_title_icon_svg'] as $language_id => $image_svg) {
            if($image_svg){
                $data['thumb_title_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
            } else {
                $data['thumb_title_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
            }
        }
        
        // -- Items --------------------------------------------

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
        
        // -- Item -----------------------------------

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
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['language_id'] = $this->config->get('config_language_id');
        
        // Image
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';

        $data['user_token']  = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/dropdown', $data));
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
