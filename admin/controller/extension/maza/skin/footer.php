<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaSkinFooter extends Controller {
        protected $error = array();
        
        /**
         * Drag and drop footer editor
         */
        public function index() {
                $this->load->model('extension/maza/footer');
                $this->load->model('tool/image');
                
                
                $this->load->language('extension/maza/skin/footer');
                
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
                
                $data['cancel'] = $this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token'] . '&footer_id=' . $this->request->get['footer_id'] . $url, true);
                $data['skin_target'] = $this->url->link('extension/maza/skin/footer', 'user_token=' . $this->session->data['user_token'] . '&footer_id=' . $this->request->get['footer_id'] . '&mz_theme_code=' . $this->request->get['mz_theme_code']);
                $data['export'] = $this->url->link('extension/maza/skin/footer/export', 'user_token=' . $this->session->data['user_token'] . '&footer_id=' . $this->request->get['footer_id'] . $url, true);
                
                // Get skins
                $data['mz_skins'] = $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
                $data['mz_skin_info'] = $this->model_extension_maza_skin->getSkin($this->mz_skin_config->get('skin_id'));
                
                // Get footer info
                $footer_info = $this->model_extension_maza_footer->getFooter($this->request->get['footer_id']);
                
                if($footer_info){
                    $data['footer_name'] = $footer_info['name'];
                    $this->document->setTitle($footer_info['name'] . ' | ' . $this->language->get('heading_title'));
                } else {
                    $data['warning'] = $this->language->get('error_footer_deleted');
                    $this->document->setTitle($this->language->get('heading_title'));
                }
                
                // Layout entries
                $data['footer'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'footer', 'group_owner' => $footer_info['footer_id']]);
                
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
                            'edit'      => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id']. $url, true)
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
                $data['footer_id'] = $this->request->get['footer_id'];
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
                
		$this->response->setOutput($this->load->view('extension/maza/skin/footer', $data));
        }
        
        /**
         * Submit layout
         */
        public function submitForm() {
                $json = array();
                
                $this->load->language('extension/maza/skin/footer');
                $this->load->model('extension/maza/layout_builder');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->get['footer_id'] && $this->validateLayout()){
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
                if(isset($data['footer'])){
                    $footer = $data['footer'];
                } else {
                    $footer = array();
                }
                $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'footer', $this->request->get['footer_id'], $footer);
        }
        
        /**
         * Duplicate skin layout from current skin to selected skin
         */
        public function duplicateLayout() {
                $json = array();
                
                $this->load->language('extension/maza/skin/footer');
                $this->load->model('extension/maza/layout_builder');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->get['footer_id']) && isset($this->request->post['duplicate_to_skin_id']) && $this->validateDuplicate()){
                    // footer
                    $footer = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'footer', $this->request->get['footer_id']);
                    $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'footer', $this->request->get['footer_id'], $footer);

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
		if (!$this->user->hasPermission('modify', 'extension/maza/skin/footer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        /**
         * Add new footer
         */
        public function add() {
                $json = array();
                
                $this->load->language('extension/maza/skin');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateAdd()){
                    $this->load->model('extension/maza/theme');
                    $this->load->model('extension/maza/footer');
                    $this->load->model('extension/maza/layout_builder');
                    
                    $footer_data = array();
                    $footer_data['name'] = $this->request->post['footer_name'];
                    
                    $theme_info = $this->model_extension_maza_theme->getTheme($this->request->get['theme_id']);
                    $parent_footer_info = $this->model_extension_maza_footer->getFooter($this->request->post['footer_parent_id']);
                    
                    // Check all parameter is valid
                    if($theme_info && $parent_footer_info && ($theme_info['theme_id'] == $parent_footer_info['theme_id']) && ($parent_footer_info['parent_footer_id'] == '0')){
                        $footer_data['parent_footer_id'] = $parent_footer_info['footer_id'];
                        $footer_data['theme_id']       = $theme_info['theme_id'];
                        
                        // Add Footer
                        $new_footer_id = $this->model_extension_maza_footer->addFooter($footer_data);
                        
                        // Duplicate Layout
                        $skins = $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
                        
                        foreach ($skins as $skin) {
                            $footer = $this->model_extension_maza_layout_builder->getLayout($skin['skin_id'], 'footer', $parent_footer_info['footer_id']);
                            $this->model_extension_maza_layout_builder->editLayout($skin['skin_id'], 'footer', $new_footer_id, $footer);
                        }
                        
                        // Duplicate setting
//                        $this->model_extension_maza_footer->duplicateSetting($parent_footer_info['footer_id'], $new_footer_id);
                        
                        $json['success'] = $this->language->get('text_success_footer_add');
                    }
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * Edit footer
         */
        public function edit() {
                $json = array();
                
                $this->load->language('extension/maza/skin');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateEdit()){
                    $this->load->model('extension/maza/footer');
                    
                    $footer_info = $this->model_extension_maza_footer->getFooter($this->request->get['footer_id']);
                    
                    // Check all parameter is valid
                    if($footer_info && ($footer_info['parent_footer_id'] != '0')){
                        $footer_data = array();
                        $footer_data['name'] = $this->request->post['footer_name'];
                        
                        // modify Footer
                        $this->model_extension_maza_footer->alterFooter($this->request->get['footer_id'], $footer_data);
                        
                        $json['success'] = $this->language->get('text_success_footer_edit');
                    }
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * Delete footer by id
         */
        public function delete() {
                $json = array();
                 
                $this->load->language('extension/maza/skin');
                
                if($this->validateDelete() && isset($this->request->get['footer_id'])){
                    $this->load->model('extension/maza/footer');
                    $this->load->model('extension/maza/layout_builder');
                    
                    // Delete Footer
                    $this->model_extension_maza_footer->deleteFooter($this->request->get['footer_id']);

                    $json['success'] = $this->language->get('text_success_footer_delete');
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * create new duplicate footer of existing footer
         */
        public function duplicate() {
                $json = array();
                 
                $this->load->language('extension/maza/skin');
                
                if($this->validateDuplicate() && isset($this->request->get['footer_id'])){
                    $this->load->model('extension/maza/footer');
                    $this->load->model('extension/maza/layout_builder');
                    
                    $footer_info = $this->model_extension_maza_footer->getFooter($this->request->get['footer_id']);
                    
                    // Footer mush be child footer to make duplicate
                    if($footer_info && $footer_info['parent_footer_id'] != 0){
                        unset($footer_info['code']);
                        $footer_info['name'] .= $this->language->get('text_duplicate');
                        
                        // Add new Footer
                        $duplicate_footer_id = $this->model_extension_maza_footer->addFooter($footer_info);
                        
                        // Duplicate Layout
                        $skins = $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
                        
                        foreach ($skins as $skin) {
                            $footer = $this->model_extension_maza_layout_builder->getLayout($skin['skin_id'], 'footer', $this->request->get['footer_id']);
                            $this->model_extension_maza_layout_builder->editLayout($skin['skin_id'], 'footer', $duplicate_footer_id, $footer);
                        }
                    }
                    
                    $json['success'] = $this->language->get('text_success_footer_duplicate');
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
		} elseif(empty ($this->request->post['footer_name']) || (utf8_strlen($this->request->post['footer_name']) < 3) || (utf8_strlen($this->request->post['footer_name']) > 64)) {
                        $this->error['warning'] = $this->language->get('error_footer_name');
                } elseif(empty ($this->request->post['footer_parent_id'])) {
                        $this->error['warning'] = $this->language->get('error_footer_parent');
                }

		return !$this->error;
	}
        
        protected function validateEdit() {
		if (!$this->user->hasPermission('modify', 'extension/maza/skin')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} elseif(empty ($this->request->post['footer_name']) || (utf8_strlen($this->request->post['footer_name']) < 3) || (utf8_strlen($this->request->post['footer_name']) > 64)) {
                        $this->error['warning'] = $this->language->get('error_footer_name');
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
                
                $this->load->model('extension/maza/footer');
                
                $footer_info = $this->model_extension_maza_footer->getFooter($this->request->get['footer_id']);
                
                if($footer_info){
                    $data['footer'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'footer', 'group_owner' => $footer_info['footer_id']]);
                    
                    header('Content-Type: application/json; charset=utf-8');
                    header('Content-disposition: attachment; filename="layout.footer.' . $this->mz_skin_config->get('skin_code') . '(' . $footer_info['name'] . ').json"');
                    
                    echo json_encode(['type' => 'footer', 'data' => $data]);
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
                $this->load->language('extension/maza/skin/footer');

                $json = array();

                // Check user has permission
                if (!$this->user->hasPermission('modify', 'extension/maza/skin/footer')) {
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
                                
                                if($data && $data['type'] == 'footer'){
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
