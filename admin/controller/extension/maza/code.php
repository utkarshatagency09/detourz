<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaCode extends Controller {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/maza/code');
                $this->load->model('setting/setting');

		        $this->document->setTitle($this->language->get('heading_title'));
                $this->document->addScript('view/javascript/maza/ace/src-min/ace.js');
                
                // Header
                $header_data = array();
                
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_header'), 'id' => 'menu-header', 'href' => false),
                    array('name' => $this->language->get('tab_footer'), 'id' => 'menu-footer', 'href' => false),
                    array('name' => $this->language->get('tab_css'), 'id' => 'menu-css', 'href' => false),
                    array('name' => $this->language->get('tab_javascript'), 'id' => 'menu-javascript', 'href' => false),
                    array('name' => $this->language->get('tab_asset'), 'id' => 'menu-asset', 'href' => false),
                );
                
                $header_data['menu_active'] = 'menu-header';
                $header_data['buttons'][] = array(
                    'id' => 'button-save',
                    'name' => $this->language->get('button_save'),
                    'tooltip' => false,
                    'icon' => 'fa-save',
                    'class' => 'btn-primary',
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-code',
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-code',
                    'target' => '_blank'
                );
                $header_data['form_target_id'] = 'form-code';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // File location
                
                // CSS
                $css_global_file        =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'css/global.css';
                $css_theme_file         =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'css/' . $this->mz_theme_config->get('theme_code') . '.css';
                $css_skin_file          =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'css/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_skin_config->get('skin_id') . '.css';
                
                // Javascript
                $javascript_global_file        =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'js/global.js';
                $javascript_theme_file         =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'js/' . $this->mz_theme_config->get('theme_code') . '.js';
                $javascript_skin_file          =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'js/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_skin_config->get('skin_id') . '.js';
                
                // Header
                $header_global_file        =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/global.html';
                $header_theme_file         =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/' . $this->mz_theme_config->get('theme_code') . '.html';
                $header_skin_file          =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_skin_config->get('skin_id') . '.html';
                
                // Footer
                $footer_global_file        =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/global.html';
                $footer_theme_file         =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/' . $this->mz_theme_config->get('theme_code') . '.html';
                $footer_skin_file          =   MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_skin_config->get('skin_id') . '.html';
                
                
                // Submit form
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()){
                    $this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'code', $this->request->post['skin']);
                    $this->model_extension_maza_theme->editSetting($this->mz_theme_config->get('theme_code'), 'code', $this->request->post['theme']);
                    $this->model_setting_setting->editSetting('mz_code', $this->request->post['global']);
                    
                    // CSS
                    file_put_contents($css_global_file, html_entity_decode($this->request->post['css_global_code']));
                    file_put_contents($css_theme_file, html_entity_decode($this->request->post['css_theme_code']));
                    file_put_contents($css_skin_file, html_entity_decode($this->request->post['css_skin_code']));
                    
                    // Javascript
                    file_put_contents($javascript_global_file, html_entity_decode($this->request->post['javascript_global_code']));
                    file_put_contents($javascript_theme_file, html_entity_decode($this->request->post['javascript_theme_code']));
                    file_put_contents($javascript_skin_file, html_entity_decode($this->request->post['javascript_skin_code']));
                    
                    // Header
                    file_put_contents($header_global_file, html_entity_decode($this->request->post['header_global_code']));
                    file_put_contents($header_theme_file, html_entity_decode($this->request->post['header_theme_code']));
                    file_put_contents($header_skin_file, html_entity_decode($this->request->post['header_skin_code']));
                    
                    // Footer
                    file_put_contents($footer_global_file, html_entity_decode($this->request->post['footer_global_code']));
                    file_put_contents($footer_theme_file, html_entity_decode($this->request->post['footer_theme_code']));
                    file_put_contents($footer_skin_file, html_entity_decode($this->request->post['footer_skin_code']));
                    
                    // clear asset files for new settings
                    $this->mz_document->clear();
                    
                    $data['success'] = $this->language->get('text_success');
                }
                
                if(isset($this->error['warning'])){
                    $data['warning'] = $this->error['warning'];
                }
                
                if (isset($this->session->data['success'])) {
                    $data['success'] = $this->session->data['success'];
                    unset($this->session->data['success']);
                }
                if (isset($this->session->data['warning'])) {
                    $data['warning'] = $this->session->data['warning'];
                    unset($this->session->data['warning']);
                }
                
                $url = '';
                
                if(isset($this->request->get['mz_theme_code'])){
                    $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                
                if(isset($this->request->get['mz_skin_id'])){
                    $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
                
                $data['action'] = $this->url->link('extension/maza/code', 'user_token=' . $this->session->data['user_token'] . $url, true);
                
                // Setting
                $setting = array();
                
                // CSS
                $setting['mz_code_css_global_status']  = false;
                $setting['code_css_theme_status']  = false;
                $setting['code_css_skin_status']  = false;
                $setting['css_global_code'] = '';
                $setting['css_theme_code'] = '';
                $setting['css_skin_code'] = '';
                
                // javascript
                $setting['mz_code_javascript_global_status']  = false;
                $setting['code_javascript_theme_status']  = false;
                $setting['code_javascript_skin_status']  = false;
                $setting['javascript_global_code'] = '';
                $setting['javascript_theme_code'] = '';
                $setting['javascript_skin_code'] = '';
                
                // Header
                $setting['mz_code_header_global_status']  = false;
                $setting['code_header_theme_status']  = false;
                $setting['code_header_skin_status']  = false;
                $setting['header_global_code'] = '';
                $setting['header_theme_code'] = '';
                $setting['header_skin_code'] = '';
                
                // Footer
                $setting['mz_code_footer_global_status']  = false;
                $setting['code_footer_theme_status']  = false;
                $setting['code_footer_skin_status']  = false;
                $setting['footer_global_code'] = '';
                $setting['footer_theme_code'] = '';
                $setting['footer_skin_code'] = '';
                
                // Asset
                $setting['code_asset_skin_stylesheet']  = array();
                $setting['code_asset_theme_stylesheet']  = array();
                $setting['mz_code_asset_global_stylesheet']  = array();
                $setting['code_asset_skin_javascript']  = array();
                $setting['code_asset_theme_javascript']  = array();
                $setting['mz_code_asset_global_javascript']  = array();
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                    $setting = array_merge($setting, $this->request->post['skin']);
                    $setting = array_merge($setting, $this->request->post['theme']);
                    $setting = array_merge($setting, $this->request->post['global']);
                } else {
                    $setting = array_merge($setting, $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'code')); 
                    $setting = array_merge($setting, $this->model_extension_maza_theme->getSetting($this->mz_theme_config->get('theme_code'), 'code')); 
                    $setting = array_merge($setting, $this->model_setting_setting->getSetting('mz_code')); 
                    
                    // CSS
                    $setting['css_global_code'] = htmlspecialchars(@file_get_contents($css_global_file));
                    $setting['css_theme_code'] = htmlspecialchars(@file_get_contents($css_theme_file));
                    $setting['css_skin_code'] = htmlspecialchars(@file_get_contents($css_skin_file));
                    
                    // Javascript
                    $setting['javascript_global_code'] = htmlspecialchars(@file_get_contents($javascript_global_file));
                    $setting['javascript_theme_code'] = htmlspecialchars(@file_get_contents($javascript_theme_file));
                    $setting['javascript_skin_code'] = htmlspecialchars(@file_get_contents($javascript_skin_file));
                    
                    // Header
                    $setting['header_global_code'] = htmlspecialchars(@file_get_contents($header_global_file));
                    $setting['header_theme_code'] = htmlspecialchars(@file_get_contents($header_theme_file));
                    $setting['header_skin_code'] = htmlspecialchars(@file_get_contents($header_skin_file));
                    
                    // Footer
                    $setting['footer_global_code'] = htmlspecialchars(@file_get_contents($footer_global_file));
                    $setting['footer_theme_code'] = htmlspecialchars(@file_get_contents($footer_theme_file));
                    $setting['footer_skin_code'] = htmlspecialchars(@file_get_contents($footer_skin_file));
                }
                
                // Data
                $data = array_merge($data, $setting);
                
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		        $this->response->setOutput($this->load->view('extension/maza/code', $data));
        }
        
        protected function validate() {
            if (!$this->user->hasPermission('modify', 'extension/maza/code')) {
                $this->error['warning'] = $this->language->get('error_permission');
            }

            return !$this->error;
        }
}
