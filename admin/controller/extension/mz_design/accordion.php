<?php
class ControllerExtensionMzDesignAccordion extends maza\layout\Design {
    public function index(): void {
        $this->load->language('extension/mz_design/accordion');
        
        $this->load->model('localisation/language');
        $this->load->model('tool/image');
        $this->load->model('extension/maza/extension');
        $this->load->model('extension/maza/content_builder');
        $this->load->model('design/layout');
        $this->load->model('extension/maza/opencart');
        
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
        
        // auto close
        if(isset($this->request->post['design_auto_close'])){
            $data['design_auto_close'] = $this->request->post['design_auto_close'];
        } else {
            $data['design_auto_close'] = 1;
        }

        // Collapsed by default 
        if(isset($this->request->post['design_collapsed'])){
            $data['design_collapsed'] = $this->request->post['design_collapsed'];
        } else {
            $data['design_collapsed'] = 1;
        }

        // Hook
        if(isset($this->request->post['design_hook'])){
            $data['design_hook'] = $this->request->post['design_hook'];
        } else {
            $data['design_hook'] = 1;
        }
        
        // Accordion
        if(isset($this->request->post['design_accordion'])){
            $design_accordion = $this->request->post['design_accordion'];
        } else {
            $design_accordion = array();
        }
        
        $data['design_accordion'] = array();
        
        foreach ($design_accordion as $design_panel) {
            // Icon thumb Image
            $design_panel['thumb_icon_image'] = array();

            foreach ($design_panel['icon_image'] as $language_id => $image) {
                if($image){
                    $design_panel['thumb_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
                } else {
                    $design_panel['thumb_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }

            // Icon thumb svg
            $design_panel['thumb_icon_svg'] = array();

            foreach ($design_panel['icon_svg'] as $language_id => $image_svg) {
                if($image_svg){
                    $design_panel['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
                } else {
                    $design_panel['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
            
            $content_builder_info =  $this->model_extension_maza_content_builder->getContent($design_panel['content_builder_id']);
        
            if($content_builder_info){
                $design_panel['content_builder_name'] =  $content_builder_info['name'];
            } else {
                $design_panel['content_builder_name'] =  '';
            }

            $data['design_accordion'][] = $design_panel;
        }
        
        // Types
        $data['types'] = array(
            array('code' => 'html', 'text' => $this->language->get('text_html')),
            array('code' => 'content_builder', 'text' => $this->language->get('text_content_builder')),
            array('code' => 'module', 'text' => $this->language->get('text_module')),
            array('code' => 'widget', 'text' => $this->language->get('text_widget'))
        );
        
        // widgets
        $data['widgets'] = $this->model_extension_maza_extension->getWidgets();
        
        // Designs
        $data['designs'] = $this->model_extension_maza_extension->getDesigns();
        
        if(isset($this->request->get['layout_id'])){
            $layout_info = $this->model_design_layout->getLayout($this->request->get['layout_id']);
        } else {
            $layout_info = array();
        }
        
        // Contents of type
        if($layout_info){
            $data['contents'] = $this->model_extension_maza_extension->getContentsOfType($layout_info['mz_layout_type']);
            $data['types'][] = array('code' => 'content', 'text' => $this->language->get('text_content'));
        } else {
            $data['contents'] = array();
        }
        
        // Modules
        $data['extensions'] = array();

        // Get a list of installed modules
        $extensions = $this->model_extension_maza_opencart->getInstalled('module');

        // Add all the modules which have multiple settings for each module
        foreach ($extensions as $code) {
            $this->load->language('extension/module/' . $code, 'extension');

            if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                $heading_title = $this->language->get('heading_title');
            } else {
                $heading_title = $this->language->get('extension')->get('heading_title');
            }

            $module_data = array();

            $modules = $this->model_extension_maza_opencart->getModulesByCode($code);

            foreach ($modules as $module) {
                $module_data[] = array(
                    'name' => strip_tags($module['name']),
                    'code' => $code . '.' .  $module['module_id']
                );
            }

            if ($this->config->has('module_' . $code . '_status') || $this->config->has($code . '_status') || $module_data) {
                $data['extensions'][] = array(
                    'name'   => strip_tags($heading_title),
                    'code'   => $code,
                    'module' => $module_data
                );
            }
        }
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['language_id'] = $this->config->get('config_language_id');
        
        // Image
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        if(isset($this->request->get['skip_content_builder_id'])){
            $data['skip_content_builder_id']  = (int)$this->request->get['skip_content_builder_id'];
        }
        
        $data['user_token']  = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/accordion', $data));
    }

    /**
     * Merger Event
     */
    public function merger(String $route, array $param): void {
        list($code, $data) = $param;

        $type = array_slice(explode('/', $route), -1)[0]; // get last element from array

        $this->model_extension_maza_install_merger->design('accordion', function($setting) use($type, $code, $data) {
            $isEdited = false;

            foreach($setting['design_accordion']??[] as $key => $accordion){
                if($type == 'content' && $accordion['content'] === $code){
                    $content_data = array();
                    parse_str(html_entity_decode($accordion['content_data']), $content_data);

                    if(is_callable($data)){
                        $result = $data($content_data);
        
                        if(!$result) continue;
                    } else {
                        $result = \maza\array_merge_subsequence($data, $content_data);
                    }

                    $setting['design_accordion'][$key]['content_data'] = htmlentities(http_build_query($result));

                    $isEdited = true;
                }
            }

            if($isEdited){
                return $setting;
            }
            return false;
        });
    }
}
