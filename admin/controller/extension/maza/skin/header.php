<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaSkinHeader extends Controller {
        protected $error = array();
        
        /**
         * Drag and drop header editor
         */
        public function index() {
                $this->load->model('extension/maza/header');
                $this->load->model('tool/image');
                
                
                $this->load->language('extension/maza/skin/header');
                
                $this->document->addStyle('view/javascript/maza/colorpicker/css/colorpicker.css');
                $this->document->addScript('view/javascript/maza/colorpicker/js/colorpicker.js');
                $this->document->addStyle('view/javascript/maza/jquery-ui-1.12.1.Interactions/jquery-ui.min.css');
                $this->document->addStyle('view/stylesheet/maza/mz_stylesheet.css');
                $this->document->addScript('view/javascript/maza/jquery-ui-1.12.1.Interactions/jquery-ui.min.js');
                $this->document->addScript('view/javascript/maza/layout_builder.js');
                $this->document->addScript('view/javascript/maza/mz_common.js');
                
                // Redirect to homepage in case of inactive maza engine
                if(!$this->config->get('maza_status')){
                    $this->response->redirect($this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token']));
                }
                
                $url = '';
                
                if(isset($this->request->get['mz_theme_code'])){
                    $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                
                if(isset($this->request->get['mz_skin_id'])){
                    $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
                
                $data['cancel'] = $this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token'] . '&header_id=' . $this->request->get['header_id'] . $url, true);
                $data['skin_target'] = $this->url->link('extension/maza/skin/header', 'user_token=' . $this->session->data['user_token'] . '&header_id=' . $this->request->get['header_id'] . '&mz_theme_code=' . $this->request->get['mz_theme_code']);
                $data['export'] = $this->url->link('extension/maza/skin/header/export', 'user_token=' . $this->session->data['user_token'] . '&header_id=' . $this->request->get['header_id'] . $url, true);
                
                // Get skins
                $data['mz_skins'] = $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
                $data['mz_skin_info'] = $this->model_extension_maza_skin->getSkin($this->mz_skin_config->get('skin_id'));
                
                // Get header info
                $header_info = $this->model_extension_maza_header->getHeader($this->request->get['header_id']);
                
                if($header_info){
                    $data['header_name'] = $header_info['name'];
                    $this->document->setTitle($header_info['name'] . ' | ' . $this->language->get('heading_title'));
                } else {
                    $data['warning'] = $this->language->get('error_header_deleted');
                    $this->document->setTitle($this->language->get('heading_title'));
                }
                
                // Layout entries
                // Top header
                $data['top_header'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'top_header', 'group_owner' => $header_info['header_id']]);
                
                // Main header
                $data['main_header'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'main_header', 'group_owner' => $header_info['header_id']]);
                
                // Main navigation
                $data['main_navigation'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'main_navigation', 'group_owner' => $header_info['header_id']]);
                
                // header componen
                $data['header_component'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'header_component', 'group_owner' => $header_info['header_id']]);
                
                $this->load->model('extension/maza/extension');
                
                // widgets
                $data['widgets'] = $this->model_extension_maza_extension->getWidgets();
                
                // Designs
                $data['designs'] = $this->model_extension_maza_extension->getDesigns();
                
                // Get a list of installed modules
                $data['extensions'] = array();
                
                $this->load->model('extension/maza/opencart');
                
                $extensions = $this->model_extension_maza_opencart->getInstalled('module');

                // Add all the modules which have multiple settings for each module
                foreach ($extensions as $code) {
                    $this->load->language('extension/module/' . $code, 'extension');

                    $module_data = array();

                    $modules = $this->model_extension_maza_opencart->getModulesByCode($code);

                    if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                        $heading_title = $this->language->get('heading_title');
                    } else {
                        $heading_title = $this->language->get('extension')->get('heading_title');
                    }

                    foreach ($modules as $module) {
                        $module_data[] = array(
                            'module_id' => $module['module_id'],
                            'name'      => strip_tags($module['name']),
                            'path'      => strip_tags($heading_title) . ' > ' . $module['name'],
                            'code'      => $code . '.' .  $module['module_id'],
                            'edit'      => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'] . $url, true)
                        );
                    }

                    if ($this->config->has('module_' . $code . '_status') || $this->config->has($code . '_status') || $module_data) {
                        $data['extensions'][] = array(
                            'name'   => strip_tags($heading_title),
                            'path'   => strip_tags($heading_title),
                            'code'   => $code,
                            'module' => $module_data,
                            'edit'   => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . $url, true)
                        );
                    }
                }
                
                // Data
                $data['user_token'] = $this->session->data['user_token'];
                $data['header_id'] = $this->request->get['header_id'];
                $data['url'] = $url;
                
                $data['device_views'] = array(
                    array('icon' => 'fa-desktop','code' => 'xl'),
                    array('icon' => 'fa-laptop', 'code' => 'lg'),
                    array('icon' => 'fa-tablet fa-rotate-270','code' => 'md'),
                    array('icon' => 'fa-tablet', 'code' => 'sm'),
                    array('icon' => 'fa-mobile', 'code' => 'xs')
                );
                
                
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
                $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                
		        $this->response->setOutput($this->load->view('extension/maza/skin/header', $data));
        }
        
        /**
         * Submit layout
         */
        public function submitForm() {
                $json = array();
                
                $this->load->language('extension/maza/skin/header');
                $this->load->model('extension/maza/layout_builder');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->get['header_id'] && $this->validateLayout()){
                    $this->editLayout($this->request->post);
                    
                    $json['success'] = $this->language->get('text_layout_success');
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * Edit layout
         * @param array $data
         */
        private function editLayout($data){
                // Top header
                if(isset($data['top_header'])){
                    $top_header = $data['top_header'];
                } else {
                    $top_header = array();
                }
                $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'top_header', $this->request->get['header_id'], $top_header);

                // main header
                if(isset($data['main_header'])){
                    $main_header = $data['main_header'];
                } else {
                    $main_header = array();
                }
                $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'main_header', $this->request->get['header_id'], $main_header);

                // main navigation
                if(isset($data['main_navigation'])){
                    $main_navigation = $data['main_navigation'];
                } else {
                    $main_navigation = array();
                }
                $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'main_navigation', $this->request->get['header_id'], $main_navigation);

                // header component
                if(isset($data['header_component'])){
                    $header_component = $data['header_component'];
                } else {
                    $header_component = array();
                }
                $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'header_component', $this->request->get['header_id'], $header_component);
        }
        
        /**
         * Duplicate skin layout from current skin to selected skin
         */
        public function duplicateLayout() {
                $json = array();
                
                $this->load->language('extension/maza/skin/header');
                $this->load->model('extension/maza/layout_builder');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->get['header_id']) && isset($this->request->post['duplicate_to_skin_id']) && $this->validateDuplicate()){
                    // Top header
                    $top_header = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'top_header', $this->request->get['header_id']);
                    $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'top_header', $this->request->get['header_id'], $top_header);
                    
                    // main header
                    $main_header = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'main_header', $this->request->get['header_id']);
                    $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'main_header', $this->request->get['header_id'], $main_header);
                    
                    // main navigation
                    $main_navigation = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'main_navigation', $this->request->get['header_id']);
                    $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'main_navigation', $this->request->get['header_id'], $main_navigation);
                    
                    // header component
                    $header_component = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'header_component', $this->request->get['header_id']);
                    $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'header_component', $this->request->get['header_id'], $header_component);
                    
                    $json['success'] = $this->language->get('text_duplicate_layout_success');
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * Validate layout
         */
        protected function validateLayout() {
		if (!$this->user->hasPermission('modify', 'extension/maza/skin/header')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        /**
         * Add new header
         */
        public function add() {
                $json = array();
                
                $this->load->language('extension/maza/skin');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateAdd()){
                    $this->load->model('extension/maza/theme');
                    $this->load->model('extension/maza/header');
                    $this->load->model('extension/maza/layout_builder');
                    
                    $header_data = array();
                    $header_data['name'] = $this->request->post['header_name'];
                    
                    $theme_info = $this->model_extension_maza_theme->getTheme($this->request->get['theme_id']);
                    $parent_header_info = $this->model_extension_maza_header->getHeader($this->request->post['header_parent_id']);
                    
                    // Check all parameter is valid
                    if($theme_info && $parent_header_info && ($theme_info['theme_id'] == $parent_header_info['theme_id']) && ($parent_header_info['parent_header_id'] == '0')){
                        $header_data['parent_header_id'] = $parent_header_info['header_id'];
                        $header_data['theme_id']       = $theme_info['theme_id'];
                        
                        // Add Header
                        $new_header_id = $this->model_extension_maza_header->addHeader($header_data);
                        
                        // Duplicate Layout
                        $skins = $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
                        
                        foreach ($skins as $skin) {
                            // Top header
                            $top_header = $this->model_extension_maza_layout_builder->getLayout($skin['skin_id'], 'top_header', $parent_header_info['header_id']);
                            $this->model_extension_maza_layout_builder->editLayout($skin['skin_id'], 'top_header', $new_header_id, $top_header);

                            // main header
                            $main_header = $this->model_extension_maza_layout_builder->getLayout($skin['skin_id'], 'main_header', $parent_header_info['header_id']);
                            $this->model_extension_maza_layout_builder->editLayout($skin['skin_id'], 'main_header', $new_header_id, $main_header);

                            // main navigation
                            $main_navigation = $this->model_extension_maza_layout_builder->getLayout($skin['skin_id'], 'main_navigation', $parent_header_info['header_id']);
                            $this->model_extension_maza_layout_builder->editLayout($skin['skin_id'], 'main_navigation', $new_header_id, $main_navigation);
                            
                            // header component
                            $header_component = $this->model_extension_maza_layout_builder->getLayout($skin['skin_id'], 'header_component', $parent_header_info['header_id']);
                            $this->model_extension_maza_layout_builder->editLayout($skin['skin_id'], 'header_component', $new_header_id, $header_component);
                        }
                        
                        // Duplicate setting
//                        $this->model_extension_maza_header->duplicateSetting($parent_header_info['header_id'], $new_header_id);
                        
                        $json['success'] = $this->language->get('text_success_header_add');
                    }
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * Edit header
         */
        public function edit() {
                $json = array();
                
                $this->load->language('extension/maza/skin');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateEdit()){
                    $this->load->model('extension/maza/header');
                    
                    $header_info = $this->model_extension_maza_header->getHeader($this->request->get['header_id']);
                    
                    // Check all parameter is valid
                    if($header_info && ($header_info['parent_header_id'] != '0')){
                        $header_data = array();
                        $header_data['name'] = $this->request->post['header_name'];
                        
                        // modify Header
                        $this->model_extension_maza_header->alterHeader($this->request->get['header_id'], $header_data);
                        
                        $json['success'] = $this->language->get('text_success_header_edit');
                    }
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * Delete header by id
         */
        public function delete() {
                $json = array();
                 
                $this->load->language('extension/maza/skin');
                
                if($this->validateDelete() && isset($this->request->get['header_id'])){
                    $this->load->model('extension/maza/header');
                    $this->load->model('extension/maza/layout_builder');
                    
                    // Delete Header
                    $this->model_extension_maza_header->deleteHeader($this->request->get['header_id']);

                    $json['success'] = $this->language->get('text_success_header_delete');
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * create new duplicate header of existing header
         */
        public function duplicate() {
                $json = array();
                 
                $this->load->language('extension/maza/skin');
                
                if($this->validateDuplicate() && isset($this->request->get['header_id'])){
                    $this->load->model('extension/maza/header');
                    $this->load->model('extension/maza/skin');
                    $this->load->model('extension/maza/layout_builder');
                    
                    $header_info = $this->model_extension_maza_header->getHeader($this->request->get['header_id']);
                    
                    // Header mush be child header to make duplicate
                    if($header_info && $header_info['parent_header_id'] != 0){
                        unset($header_info['code']);
                        $header_info['name'] .= $this->language->get('text_duplicate');
                        
                        // Add new Header
                        $duplicate_header_id = $this->model_extension_maza_header->addHeader($header_info);
                        
                        // Duplicate Layout
                        $skins = $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
                        
                        foreach ($skins as $skin) {
                            // Top header
                            $top_header = $this->model_extension_maza_layout_builder->getLayout($skin['skin_id'], 'top_header', $this->request->get['header_id']);
                            $this->model_extension_maza_layout_builder->editLayout($skin['skin_id'], 'top_header', $duplicate_header_id, $top_header);

                            // main header
                            $main_header = $this->model_extension_maza_layout_builder->getLayout($skin['skin_id'], 'main_header', $this->request->get['header_id']);
                            $this->model_extension_maza_layout_builder->editLayout($skin['skin_id'], 'main_header', $duplicate_header_id, $main_header);

                            // main navigation
                            $main_navigation = $this->model_extension_maza_layout_builder->getLayout($skin['skin_id'], 'main_navigation', $this->request->get['header_id']);
                            $this->model_extension_maza_layout_builder->editLayout($skin['skin_id'], 'main_navigation', $duplicate_header_id, $main_navigation);
                            
                            // header component
                            $header_component = $this->model_extension_maza_layout_builder->getLayout($skin['skin_id'], 'header_component', $this->request->get['header_id']);
                            $this->model_extension_maza_layout_builder->editLayout($skin['skin_id'], 'header_component', $duplicate_header_id, $header_component);
                        }
                        
                    }
                    
                    $json['success'] = $this->language->get('text_success_header_duplicate');
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        protected function validateAdd() {
		if (!$this->user->hasPermission('modify', 'extension/maza/skin')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} elseif(empty ($this->request->post['header_name']) || (utf8_strlen($this->request->post['header_name']) < 3) || (utf8_strlen($this->request->post['header_name']) > 64)) {
                        $this->error['warning'] = $this->language->get('error_header_name');
                } elseif(empty ($this->request->post['header_parent_id'])) {
                        $this->error['warning'] = $this->language->get('error_header_parent');
                }

		return !$this->error;
	}
        
        protected function validateEdit() {
		if (!$this->user->hasPermission('modify', 'extension/maza/skin')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} elseif(empty ($this->request->post['header_name']) || (utf8_strlen($this->request->post['header_name']) < 3) || (utf8_strlen($this->request->post['header_name']) > 64)) {
                        $this->error['warning'] = $this->language->get('error_header_name');
                }

		return !$this->error;
	}
        
        protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/maza/skin')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        protected function validateDuplicate() {
		if (!$this->user->hasPermission('modify', 'extension/maza/skin')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        /**
         * Export layout
         */
        public function export() {
                $data = array();
                
                $this->load->model('extension/maza/header');
                
                $header_info = $this->model_extension_maza_header->getHeader($this->request->get['header_id']);
                
                if($header_info){
                    $data['top_header'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'top_header', 'group_owner' => $header_info['header_id']]);
                    $data['main_header'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'main_header', 'group_owner' => $header_info['header_id']]);
                    $data['main_navigation'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'main_navigation', 'group_owner' => $header_info['header_id']]);
                    $data['header_component'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'header_component', 'group_owner' => $header_info['header_id']]);
                    
                    header('Content-Type: application/json; charset=utf-8');
                    header('Content-disposition: attachment; filename="layout.header.' . $this->mz_skin_config->get('skin_code') . '(' . $header_info['name'] . ').json"');
                    
                    echo json_encode(['type' => 'header', 'data' => $data]);
                } else {
                    $url = '';
                
                    if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                    }
                    if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                    }
                    
                    $this->response->redirect($this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token']  . $url, true));
                }
	}
        
        /**
         * Import layout
         */
        public function import(){
                $this->load->language('extension/maza/skin/header');

                $json = array();

                // Check user has permission
                if (!$this->user->hasPermission('modify', 'extension/maza/skin/header')) {
                        $json['error'] = $this->language->get('error_permission');
                } else {
                    if (isset($this->request->files['file']['name'])) {
                            if (substr($this->request->files['file']['name'], -4) != 'json') {
                                    $json['error'] = $this->language->get('error_filetype');
                            }

                            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                            }
                    } else {
                            $json['error'] = $this->language->get('error_upload');
                    }
                }

                if (!$json) {
                        $file = $this->request->files['file']['tmp_name'];

                        if (is_file($file)) {
                                $data = json_decode(file_get_contents($file), true);
                                
                                if($data && $data['type'] == 'header'){
                                    $this->load->model('extension/maza/layout_builder');
                                    
                                    $this->editLayout($data['data']);
                                    
                                    $json['success'] = $this->language->get('text_success_import');
                                } else {
                                    $json['error'] = $this->language->get('error_layout_support');
                                }
                        } else {
                                $json['error'] = $this->language->get('error_file');
                        }
                }

                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
}
