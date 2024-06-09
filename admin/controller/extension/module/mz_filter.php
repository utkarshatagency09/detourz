<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionModuleMzFilter extends Controller {
    private $error = array();
    
    public function index(): void {
            $this->load->language('extension/module/mz_filter');

            $this->document->setTitle($this->language->get('heading_title'));
            
            $this->load->model('extension/maza/module');
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
                array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
                array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
                array('name' => $this->language->get('tab_layout'), 'id' => 'tab-mz-layout', 'href' => false),
            );
            
            $header_data['menu_active'] = 'tab-mz-general';
            
            // Buttons
            $header_data['buttons'][] = array( // Button save
                'id' => 'button-save',
                'name' => false,
                'tooltip' => $this->language->get('button_save'),
                'icon' => 'fa-save',
                'class' => 'btn-primary',
                'href' => FALSE,
                'target' => FALSE,
                'form_target_id' => 'form-mz-filter',
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
                    'href' => $this->url->link('extension/module/mz_filter/export', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'] . $url, true),
                    'target' => '_self',
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array( // Button delete
                    'id' => 'button-delete',
                    'name' => false,
                    'tooltip' => $this->language->get('button_delete'),
                    'icon' => 'fa-trash',
                    'class' => 'btn-danger',
                    'href' => $this->url->link('extension/module/mz_filter/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true),
                    'target' => '_self',
                    'form_target_id' => false,
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
                'href' => 'https://docs.pocotheme.com/#module-maza-filter',
                'target' => '_blank'
            );
            
            // Form submit id
            $header_data['form_target_id'] = 'form-mz-filter';
            
            $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
            
            // Submit form and save module in case of no error
            if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()){
                
                if (!isset($this->request->get['module_id'])) {
                        $module_id = $this->model_extension_maza_module->addModule('mz_filter', $this->mz_skin_config->get('skin_id'), $this->request->post);
                } else {
                        $this->model_extension_maza_module->editModule($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'), $this->request->post);
                }
                
                $this->session->data['success'] = $this->language->get('text_success');
                
                // Add module id in url and redirect to it after newly added module
                if(isset($module_id)){
                    $this->response->redirect($this->url->link('extension/module/mz_filter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true)); 
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
                    $data['action'] = $this->url->link('extension/module/mz_filter', 'user_token=' . $this->session->data['user_token'] . $url, true);
                    $data['import'] = $this->url->link('extension/module/mz_filter/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
            } else {
                    $data['action'] = $this->url->link('extension/module/mz_filter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
                    $data['import'] = $this->url->link('extension/module/mz_filter/import', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'] . $url, true);
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
            $setting['name']              =   '';
            $setting['status']            =   false;
            $setting['title']             =   array();
            $setting['count_product']     =   1;
            $setting['cache']             =   0;
            $setting['ajax']              =   0;
            $setting['delay']             =   2;
            $setting['reset_all']         =   1;
            $setting['reset_group']       =   1;
            $setting['overflow']          =   'scroll';
            $setting['sort_by']           =   'product';
            $setting['hide_zero_filter']  =   false;
            
            // Filter type
            $setting['filter']            =   array();
            $setting['filter']['price']   =   array(
                'status' => 1,
                'title' => array(),
                'sort_order' => 1,
                'collapse' => 0
            );
            $setting['filter']['sub_category'] =   array(
                'status' => 1,
                'title' => array(),
                'sort_order' => 2,
                'collapse' => 0,
                'input_type' => 'checkbox',
                'list_type' => 'text',
                'image_width' => 30,
                'image_height' => 30,
                'search' => 0
            );
            $setting['filter']['manufacturer'] =   array(
                'status' => 1,
                'title' => array(),
                'sort_order' => 2,
                'collapse' => 0,
                'input_type' => 'checkbox',
                'list_type' => 'image',
                'image_width' => 40,
                'image_height' => 40,
                'search' => -1
            );
            $setting['filter']['search'] =   array(
                'status' => 0,
                'title' => array(),
                'placeholder' => array(),
                'collapse' => 0,
                'description' => 1,
                'sort_order' => 3
            );
            $setting['filter']['availability'] =   array(
                'status' => 1,
                'title' => array(),
                'sort_order' => 4,
                'collapse' => 0,
                'stock_status' => 1,
                'input_type' => 'checkbox',
            );
            $setting['filter']['discount']   =   array(
                'status' => 1,
                'title' => array(),
                'sort_order' => 5,
                'collapse' => 0
            );
            $setting['filter']['rating']   =   array(
                'status' => 1,
                'title' => array(),
                'sort_order' => 99,
                'collapse' => 0
            );
            $setting['filter']['custom']   =   array(
                'status' => 1,
                'search' => -1,
                'require_category' => 1
            );
            $setting['filter']['filter']   =   array(
                'status' => 1,
                'collapse' => 0,
                'search' => -1,
                'require_category' => 1
            );
            
            // layout
            $setting['collapsed']       =   'sm';
            
            // Get global name of module
            if($global_setting){
                $setting['name'] = $global_setting['name'];
            }
            
            if($this->request->server['REQUEST_METHOD'] == 'POST'){
                $setting = array_merge($setting, $this->request->post);
            } else {
                $setting = array_merge($setting, $module_setting); 
            }
            
            
            $data = array_merge($data, $setting);
            
            // Text
            $data['help_custom'] = sprintf($this->language->get('help_custom'), $this->url->link('extension/maza/filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
            $data['help_filter'] = sprintf($this->language->get('help_filter'), $this->url->link('catalog/filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
            
            // Data
            $data['list_search_status'] = array(
                array('code' => 1, 'text' => $this->language->get('text_always')),
                array('code' => 0, 'text' => $this->language->get('text_disabled')),
                array('code' => -1, 'text' => $this->language->get('text_on_demand'))
            );
            $data['input_types'] = array(
                array('code' => 'radio', 'text' => $this->language->get('text_radio')),
                array('code' => 'checkbox', 'text' => $this->language->get('text_checkbox'))
            );
            $data['list_types'] = array(
                array('code' => 'image', 'text' => $this->language->get('text_image')),
                array('code' => 'text', 'text' => $this->language->get('text_text')),
                array('code' => 'both', 'text' => $this->language->get('text_both'))
            );
            $data['list_sort'] = array(
                array('code' => 'product', 'text' => $this->language->get('text_product')),
                array('code' => 'sort_order', 'text' => $this->language->get('text_sort_order')),
            );
            $data['overflow_types'] = array(
                array('code' => 'scroll', 'text' => $this->language->get('text_scroll')),
                array('code' => 'more', 'text' => $this->language->get('text_more')),
            );
            $data['list_collapsed'] = array(
                array('code' => '1', 'text' => $this->language->get('text_enabled')),
                array('code' => '0', 'text' => $this->language->get('text_disabled')),
                array('code' => 'sm', 'text' => $this->language->get('text_sm')),
                array('code' => 'md', 'text' => $this->language->get('text_md')),
                array('code' => 'lg', 'text' => $this->language->get('text_lg')),
                array('code' => 'xl', 'text' => $this->language->get('text_xl'))
            );
            
            $data['user_token'] = $this->session->data['user_token'];
            
            $data['header'] = $this->load->controller('extension/maza/common/header/main');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
            $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left/module', 'mz_filter');
            
            $this->response->setOutput($this->load->view('extension/module/mz_filter', $data));
    }
    
    protected function validateForm(): bool {
        if (!$this->user->hasPermission('modify', 'extension/module/mz_filter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
                
        // Module name
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_module_name');
        }
                
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }
    
    public function delete(): void {
        $this->load->language('extension/module/mz_filter');
        
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
            
            $this->response->redirect($this->url->link('extension/module/mz_filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
        } else {
            $this->response->redirect($this->url->link('extension/module/mz_filter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true));
        }
    }
    
    protected function validateDelete(): bool {
        if (!$this->user->hasPermission('modify', 'extension/module/mz_filter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
    
    public function export(): void {
        $this->load->model('extension/maza/module');
        $this->load->language('extension/module/mz_filter');
        
        $module_setting = $this->model_extension_maza_module->getSetting($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'));
        
        if($module_setting){
            header('Content-Type: application/json; charset=utf-8');
            header('Content-disposition: attachment; filename="module.mz_filter.' . $this->mz_skin_config->get('skin_code') . '(' . $module_setting['name'] . ').json"');
            
            echo json_encode(['type' => 'module', 'code' => 'mz_filter', 'setting' => $module_setting]);
        } else {
            $this->session->data['warning'] = $this->language->get('error_module_empty');
            
            $url = '';
        
            if(isset($this->request->get['mz_theme_code'])){
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
            }

            if(isset($this->request->get['mz_skin_id'])){
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
            }
            
            $this->response->redirect($this->url->link('extension/module/mz_filter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true)); 
        }
    }
    
    public function import(): void {
        $this->load->language('extension/module/mz_filter');
        
        if(isset($this->request->get['module_id'])){
            $module_id = $this->request->get['module_id'];
        } else {
            $module_id = 0;
        }
        
        $warning = '';

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/module/mz_filter')) {
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
                        
                        if($data && $data['type'] == 'module' && $data['code'] == 'mz_filter'){
                            $this->load->model('extension/maza/module');
                            
                            if (!$module_id) {
                                    $module_id = $this->model_extension_maza_module->addModule('mz_filter', $this->mz_skin_config->get('skin_id'), $data['setting']);
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
            $this->response->redirect($this->url->link('extension/module/mz_filter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true)); 
        } else {
            $this->response->redirect($this->url->link('extension/module/mz_filter', 'user_token=' . $this->session->data['user_token'] . $url, true)); 
        }
    }
}
