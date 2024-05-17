<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionModuleMzSlider extends Controller {
    private $error = array();
        
    public function index(): void {
        $this->load->language('extension/module/mz_slider');

        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('extension/maza/module');
        $this->load->model('extension/maza/content_builder');
        $this->load->model('tool/image');
        $this->load->model('extension/maza/opencart');
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
        
        // Header
        $header_data = array();
        $header_data['title'] = $this->language->get('heading_title');
        $header_data['menu'] = array(
            array('name' => $this->language->get('tab_general'), 'id' => 'tab-general', 'href' => false),
            array('name' => $this->language->get('tab_slides'), 'id' => 'tab-slides', 'href' => false),
            array('name' => $this->language->get('tab_image'), 'id' => 'tab-image', 'href' => false),
            array('name' => $this->language->get('tab_control'), 'id' => 'tab-control', 'href' => false),
            array('name' => $this->language->get('tab_layout'), 'id' => 'tab-layout', 'href' => false)
        );
        
        $header_data['menu_active'] = 'tab-general';
        
        // Buttons
        $header_data['buttons'][] = array( // Button save
            'id' => 'button-save',
            'name' => false,
            'tooltip' => $this->language->get('button_save'),
            'icon' => 'fa-save',
            'class' => 'btn-primary',
            'href' => FALSE,
            'target' => FALSE,
            'form_target_id' => 'form-mz-slider',
        );
        $header_data['buttons'][] = array( // Button import
            'id' => 'button-import',
            'name' => false,
            'tooltip' => $this->language->get('button_import'),
            'icon' => 'fa-upload',
            'class' => 'btn-warning',
            'href' => FALSE,
            'target' => FALSE,
            'form_target_id' => false,
        );
        if (isset($this->request->get['module_id'])) {
            $header_data['buttons'][] = array( // Button export
                'id' => 'button-export',
                'name' => false,
                'tooltip' => $this->language->get('button_export'),
                'icon' => 'fa-download',
                'class' => 'btn-warning',
                'href' => $this->url->link('extension/module/mz_slider/export', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'] . $url, true),
                'target' => '_self',
                'form_target_id' => false,
            );
            $header_data['buttons'][] = array( // Button delete
                'id' => 'button-delete',
                'name' => false,
                'tooltip' => $this->language->get('button_delete'),
                'icon' => 'fa-trash',
                'class' => 'btn-danger',
                'href' => $this->url->link('extension/module/mz_slider/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true),
                'target' => '_self',
                'form_target_id' => false
            );
        }
        $header_data['buttons'][] = array( // Button cancel
            'id' => 'button-cancel',
            'name' => false,
            'tooltip' => $this->language->get('button_cancel'),
            'icon' => 'fa-reply',
            'class' => 'btn-default',
            'href' => $this->url->link('extension/maza/module', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => '_self',
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#module-maza-slider',
            'target' => '_blank'
        );
        
        // Form submit id
        $header_data['form_target_id'] = 'form-mz-slider';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
        
        // Submit form and save module in case of no error
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()){
            
            if (!isset($this->request->get['module_id'])) {
                    $module_id = $this->model_extension_maza_module->addModule('mz_slider', $this->mz_skin_config->get('skin_id'), $this->request->post);
            } else {
                    $this->model_extension_maza_module->editModule($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'), $this->request->post);
            }
            
            $this->session->data['success'] = $this->language->get('text_success');
            
            // Add module id in url and redirect to it after newly added module
            if(isset($module_id)){
                $this->response->redirect($this->url->link('extension/module/mz_slider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true)); 
            }
        }
        
        if(isset($this->session->data['warning'])){
            $data['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        } elseif(isset($this->error['warning'])){
            $data['warning'] = $this->error['warning'];
        }
        
        if(isset($this->session->data['success'])){
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        
        foreach ($this->error as $label => $error) {
            $data['err_' . $label] = $error;
        }
        
        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/mz_slider', 'user_token=' . $this->session->data['user_token'] . $url, true);
            $data['import'] = $this->url->link('extension/module/mz_slider/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/mz_slider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
            $data['import'] = $this->url->link('extension/module/mz_slider/import', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'] . $url, true);
        }
        
        
        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $global_setting = $this->model_extension_maza_opencart->getModule($this->request->get['module_id']);
            $module_setting = $this->model_extension_maza_module->getSetting($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'));
        } else {
            $global_setting = $module_setting = array();
        }
        
        // Language
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        $data['language_id'] = $this->config->get('config_language_id');
        
        // Setting
        $setting = array();
        
        // General
        $setting['name']                      =   '';
        $setting['status']                    =   false;
        $setting['title']                     =   array();
        $setting['slide_effect']              =   'slide';
        $setting['loop']                      =   false;
        $setting['navigation']                =   true;
        $setting['pagination']                =   true;
        $setting['pagination_type']           =   'bullets';
        $setting['autoplay_status']           =   1;
        $setting['autoplay_delay']            =   3000;
        $setting['speed']                     =   300;
        $setting['lazy_loading']              =   false;
        $setting['slide_per_column']          =   1;
        $setting['space_between_slide']       =   0;
        $setting['keyboard_control']          =   true;
        $setting['mousewheel_control']        =   false;
        $setting['simulate_touch']            =   true;
        $setting['auto_height']               =   1;
        $setting['column_xs']                 =   1;
        $setting['column_sm']                 =   1;
        $setting['column_md']                 =   1;
        $setting['column_lg']                 =   1;
        $setting['column_xl']                 =   1;
        $setting['nav_icon_image']            =   array();
        $setting['nav_icon_svg']              =   array();
        $setting['nav_icon_font']             =   array();
        $setting['nav_icon_size']             =   null;
        $setting['nav_icon_width']            =   null;
        $setting['nav_icon_height']           =   null;
        $setting['slide_image_width']         =   '';
        $setting['slide_image_height']        =   '';
        $setting['slide_image_srcset']        =   array('lg' => null, 'md' => null, 'sm' => null, 'xs' => null);
        $setting['url_target']                =   '_self';
        $setting['slides']                    =   array();
        
        // Get global name of module
        if($global_setting){
            $setting['name'] = $global_setting['name'];
        }
        
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $setting = array_merge($setting, $this->request->post);
        } else {
            $setting = array_merge($setting, $module_setting); 
        }
        
        $setting['slides'] = array_values($setting['slides']);
        
        foreach($setting['slides'] as &$slide){
            
            // Image
            foreach ($slide['slide_image'] as $language_id => $image) {
                if($image){
                    $slide['thumb_slide_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
                } else {
                    $slide['thumb_slide_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
            
            // SVG
            foreach ($slide['slide_svg'] as $language_id => $svg) {
                if($svg){
                    $slide['thumb_slide_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $svg;
                } else {
                    $slide['thumb_slide_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
            
            if(!empty($slide['url_link_code'])){
                $slide['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $slide['url_link_code']);
            } else {
                $slide['link_info'] =  '';
            }
        }
        
        $data = array_merge($data, $setting);
        
        // Slide effects
        $data['slide_effects'] = array(
            array('id' => 'slide', 'name' => $this->language->get('text_slide')),
            array('id' => 'fade', 'name' => $this->language->get('text_fade')),
            array('id' => 'cube', 'name' => $this->language->get('text_cube')),
            array('id' => 'coverflow', 'name' => $this->language->get('text_coverflow')),
            array('id' => 'flip', 'name' => $this->language->get('text_flip')),
            array('id' => 'cards', 'name' => $this->language->get('text_cards')),
        );
        
        // pagination type
        $data['list_pagination_type'] = array(
            array('id' => 'bullets', 'name' => $this->language->get('text_bullets')),
            array('id' => 'fraction', 'name' => $this->language->get('text_fraction')),
            array('id' => 'progressbar', 'name' => $this->language->get('text_progressbar'))
        );
        
        // Types
        $data['types'] = array(
            array('code' => 'image', 'text' => $this->language->get('text_image')),
            array('code' => 'html', 'text' => $this->language->get('text_html')),
            array('code' => 'module', 'text' => $this->language->get('text_module')),
            array('code' => 'content', 'text' => $this->language->get('text_content'))
        );
        
        // Image
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        // carousel nav icon image
        $data['thumb_nav_icon_image'] = array();
        
        foreach ($data['nav_icon_image'] as $language_id => $image) {
            if($image){
                $data['thumb_nav_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
            } else {
                $data['thumb_nav_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }

        // carousel nav icon svg
        $data['thumb_nav_icon_svg'] = array();

        foreach ($data['nav_icon_svg'] as $language_id => $image_svg) {
            if($image_svg){
                $data['thumb_nav_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
            } else {
                $data['thumb_nav_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
            }
        }
        
        // Modules
        $data['extensions'] = array();

        // Get a list of installed modules
        $extensions = $this->model_extension_maza_opencart->getInstalled('module');

        // Add all the modules which have multiple settings for each module
        foreach ($extensions as $code) {
            $this->load->language('extension/module/' . $code, 'extension');

            $module_data = array();

            $modules = $this->model_extension_maza_opencart->getModulesByCode($code);

            foreach ($modules as $module) {
                $module_data[] = array(
                    'name' => strip_tags($module['name']),
                    'code' => $code . '.' .  $module['module_id']
                );
            }

            if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                $heading_title = $this->language->get('heading_title');
            } else {
                $heading_title = $this->language->get('extension')->get('heading_title');
            }

            if ($this->config->has('module_' . $code . '_status') || $this->config->has($code . '_status') || $module_data) {
                $data['extensions'][] = array(
                    'name'   => strip_tags($heading_title),
                    'code'   => $code,
                    'module' => $module_data
                );
            }
        }


        $data['user_token'] = $this->session->data['user_token'];
        
        $data['header'] = $this->load->controller('extension/maza/common/header/main');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
        $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left/module', 'mz_slider');
        
        $this->response->setOutput($this->load->view('extension/module/mz_slider', $data));
    }
    
    protected function validateForm(): bool {
        if (!$this->user->hasPermission('modify', 'extension/module/mz_slider')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        // Module name
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_module_name');
        }
        
        
        // Slides
        if(isset($this->request->post['slides'])){
            $slides = array_values($this->request->post['slides']);
        } else {
            $slides = array();
        }
        
        if(empty($slides)){
            $this->error['slides'] = $this->language->get('error_slides');
        }
        
        if(!isset($this->error['warning']) && $this->error){
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }
    
    public function delete(): void {
        $this->load->language('extension/module/mz_slider');
        
        $this->load->model('extension/maza/opencart');
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
        
        if(isset($this->request->get['module_id']) && $this->validateDelete()){
            $this->model_extension_maza_opencart->deleteModule($this->request->get['module_id']);
            
            $this->session->data['success'] = $this->language->get('text_success');
            
            $this->response->redirect($this->url->link('extension/module/mz_slider', 'user_token=' . $this->session->data['user_token'] . $url, true));
        } else {
            $this->response->redirect($this->url->link('extension/module/mz_slider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true));
        } 
    }
    
    protected function validateDelete(): bool {
        if (!$this->user->hasPermission('modify', 'extension/module/mz_slider')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
    
    /**
     * Export setting
     */
    public function export(): void {
        $this->load->model('extension/maza/module');
        $this->load->language('extension/module/mz_slider');
        
        $module_setting = $this->model_extension_maza_module->getSetting($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'));
        
        if($module_setting){
            header('Content-Type: application/json; charset=utf-8');
            header('Content-disposition: attachment; filename="module.mz_slider.' . $this->mz_skin_config->get('skin_code') . '(' . $module_setting['name'] . ').json"');
            
            echo json_encode(['type' => 'module', 'code' => 'mz_slider', 'setting' => $module_setting]);
        } else {
            $this->session->data['warning'] = $this->language->get('error_module_empty');
            
            $url = '';
        
            if(isset($this->request->get['mz_theme_code'])){
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
            }

            if(isset($this->request->get['mz_skin_id'])){
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
            }
            
            $this->response->redirect($this->url->link('extension/module/mz_slider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true)); 
        }
    }
    
    /**
     * Import setting
     */
    public function import(): void {
        $this->load->language('extension/module/mz_slider');
        
        if(isset($this->request->get['module_id'])){
            $module_id = $this->request->get['module_id'];
        } else {
            $module_id = 0;
        }
        
        $warning = '';

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/module/mz_slider')) {
            $warning = $this->language->get('error_permission');
        } else {
            if (isset($this->request->files['file']['name'])) {
                if (substr($this->request->files['file']['name'], -4) != 'json') {
                    $warning = $this->language->get('error_filetype');
                }

                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $warning = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            } else {
                $warning = $this->language->get('error_upload');
            }
        }

        if (!$warning) {
            $file = $this->request->files['file']['tmp_name'];

            if (is_file($file)) {
                $data = json_decode(file_get_contents($file), true);
                
                if($data && $data['type'] == 'module' && $data['code'] == 'mz_slider'){
                    $this->load->model('extension/maza/module');
                    
                    if (!$module_id) {
                        $module_id = $this->model_extension_maza_module->addModule('mz_slider', $this->mz_skin_config->get('skin_id'), $data['setting']);
                    } else {
                        $this->model_extension_maza_module->editModule($module_id, $this->mz_skin_config->get('skin_id'), $data['setting']);
                    }
                    
                    $this->session->data['success'] = $this->language->get('text_success_import');
                } else {
                    $warning = $this->language->get('error_import_file');
                }
            } else {
                $warning = $this->language->get('error_file');
            }
        }
        
        if($warning){
            $this->session->data['warning'] = $warning;
        }
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }

        if($module_id){
            $this->response->redirect($this->url->link('extension/module/mz_slider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true)); 
        } else {
            $this->response->redirect($this->url->link('extension/module/mz_slider', 'user_token=' . $this->session->data['user_token'] . $url, true)); 
        }
    }
}
