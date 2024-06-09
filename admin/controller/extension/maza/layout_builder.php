<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazalayoutBuilder extends Controller {
    private $error = array();
        
    public function index(): void {
        $this->load->model('design/layout');
        $this->load->model('tool/image');
        $this->load->model('extension/maza/layout_builder');
        $this->load->model('extension/maza/opencart');
        
        
        $this->load->language('extension/maza/layout_builder');
        
        $this->document->addStyle('view/javascript/maza/colorpicker/css/colorpicker.css');
        $this->document->addScript('view/javascript/maza/colorpicker/js/colorpicker.js');
        $this->document->addStyle('view/javascript/maza/jquery-ui-1.12.1.Interactions/jquery-ui.min.css');
        $this->document->addStyle('view/stylesheet/maza/mz_stylesheet.css');
        $this->document->addScript('view/javascript/maza/jquery-ui-1.12.1.Interactions/jquery-ui.min.js');
        $this->document->addScript('view/javascript/maza/layout_builder.js');
        $this->document->addScript('view/javascript/maza/mz_common.js');
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
        
        $data['cancel'] = $this->url->link('extension/maza/layout', 'user_token=' . $this->session->data['user_token']  . $url, true);
        $data['skin_target'] = $this->url->link('extension/maza/layout_builder', 'user_token=' . $this->session->data['user_token'] . '&layout_id=' . $this->request->get['layout_id'] . '&mz_theme_code=' . $this->request->get['mz_theme_code'], true);
        $data['export'] = $this->url->link('extension/maza/layout_builder/export', 'user_token=' . $this->session->data['user_token'] . '&layout_id=' . $this->request->get['layout_id'] . $url, true);
        
        // Get skins
        $data['mz_skins'] = $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
        $data['mz_skin_info'] = $this->model_extension_maza_skin->getSkin($this->mz_skin_config->get('skin_id'));
        
        // Get layout info
        $layout_info = $this->model_design_layout->getLayout($this->request->get['layout_id']);
        
        if($layout_info){
            $data['layout_name'] = $layout_info['name'];
            $this->document->setTitle($layout_info['name'] . ' | ' . $this->language->get('heading_title'));
        } else {
            $data['warning'] = $this->language->get('error_layout_deleted');
            $this->document->setTitle($this->language->get('heading_title'));
        }
        
        // Layout entries
        if($layout_info['mz_layout_type'] == 'default'){
            // Content top
            $data['layout_content_top'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout_content_top', 'group_owner' => $layout_info['layout_id']]);

            // Content bottom
            $data['layout_content_bottom'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout_content_bottom', 'group_owner' => $layout_info['layout_id']]);

            // Content left
            $data['layout_column_left'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout_column_left', 'group_owner' => $layout_info['layout_id']]);

            // Content right
            $data['layout_column_right'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout_column_right', 'group_owner' => $layout_info['layout_id']]);
        } else {
            $data['layout_content'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout', 'group_owner' => $layout_info['layout_id']]);
        }
        
        $data['layout_component'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout_component', 'group_owner' => $layout_info['layout_id']]);
        
        $this->load->model('extension/maza/extension');
        
        // widgets
        $data['widgets'] = $this->model_extension_maza_extension->getWidgets();
        
        // Designs
        $data['designs'] = $this->model_extension_maza_extension->getDesigns();
        
        // Get a list of installed modules
        $data['extensions'] = array();
        
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
        
        // Contents of type
        $data['contents'] = $this->model_extension_maza_extension->getContentsOfType($layout_info['mz_layout_type']);
        
        
        // Data
        $data['user_token'] = $this->session->data['user_token'];
        $data['layout_id'] = $this->request->get['layout_id'];
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
                
		$this->response->setOutput($this->load->view('extension/maza/layout_builder', $data));
    }
        
    public function submitForm(): void {
        $json = array();
        
        $this->load->language('extension/maza/layout_builder');
        $this->load->model('extension/maza/layout_builder');
        
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->get['layout_id'] && $this->validateLayout()){
            $this->editLayout($this->request->post);
            
            $json['success'] = $this->language->get('text_layout_success');
        }
        
        if(isset($this->error['warning'])){
            $json['error'] = $this->error['warning'];
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
        
    private function editLayout(array $data): void {
        // layout
        if(isset($data['layout'])){
            $layout_content = $data['layout'];
        } else {
            $layout_content = array();
        }
        $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'layout', $this->request->get['layout_id'], $layout_content);

        // layout component
        if(isset($data['layout_component'])){
            $layout_component = $data['layout_component'];
        } else {
            $layout_component = array();
        }
        $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'layout_component', $this->request->get['layout_id'], $layout_component);

        // layout_content top
        if(isset($data['layout_content_top'])){
            $layout_content_top = $data['layout_content_top'];
        } else {
            $layout_content_top = array();
        }
        $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'layout_content_top', $this->request->get['layout_id'], $layout_content_top);

        // layout_content bottom
        if(isset($data['layout_content_bottom'])){
            $layout_content_bottom = $data['layout_content_bottom'];
        } else {
            $layout_content_bottom = array();
        }
        $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'layout_content_bottom', $this->request->get['layout_id'], $layout_content_bottom);

        // layout column left
        if(isset($data['layout_column_left'])){
            $layout_column_left = $data['layout_column_left'];
        } else {
            $layout_column_left = array();
        }
        $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'layout_column_left', $this->request->get['layout_id'], $layout_column_left);

        // layout column right
        if(isset($data['layout_column_right'])){
            $layout_column_right = $data['layout_column_right'];
        } else {
            $layout_column_right = array();
        }
        $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'layout_column_right', $this->request->get['layout_id'], $layout_column_right);
    }
        
    /**
     * Duplicate skin layout from current skin to selected skin
     */
    public function duplicateLayout(): void {
        $json = array();
        
        $this->load->language('extension/maza/layout_builder');
        $this->load->model('extension/maza/layout_builder');
        
        if($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->get['layout_id']) && isset($this->request->post['duplicate_to_skin_id']) && $this->validateLayout()){
            // layout
            $layout = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'layout', $this->request->get['layout_id']);
            $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'layout', $this->request->get['layout_id'], $layout);
            
            // layout component
            $layout_component = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'layout_component', $this->request->get['layout_id']);
            $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'layout_component', $this->request->get['layout_id'], $layout_component);
            
            // layout_content top
            $layout_content_top = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'layout_content_top', $this->request->get['layout_id']);
            $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'layout_content_top', $this->request->get['layout_id'], $layout_content_top);
            
            // layout_content bottom
            $layout_content_bottom = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'layout_content_bottom', $this->request->get['layout_id']);
            $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'layout_content_bottom', $this->request->get['layout_id'], $layout_content_bottom);
            
            // layout column left
            $layout_column_left = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'layout_column_left', $this->request->get['layout_id']);
            $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'layout_column_left', $this->request->get['layout_id'], $layout_column_left);
            
            // layout column right
            $layout_column_right = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'layout_column_right', $this->request->get['layout_id']);
            $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'layout_column_right', $this->request->get['layout_id'], $layout_column_right);

            $json['success'] = $this->language->get('text_duplicate_layout_success');
        }
        
        if(isset($this->error['warning'])){
            $json['error'] = $this->error['warning'];
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
        
    
    protected function validateLayout(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/layout_builder')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
    public function getLayout($layout, array $entries = array()): array {
        $data = array();
    
        if($layout){
            $this->load->model('extension/maza/layout_builder');
            
            $entries = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), $layout['group'], $layout['group_owner']);
        }
        
        $url = '';
        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        
        foreach($entries as $entry){
                
            // Module entry
            if($entry['type'] === 'module'){
                $part = explode('.', $entry['code']);

                $this->load->language('extension/module/' . $part[0], 'module');

                if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                    $heading_title = $this->language->get('heading_title');
                } else {
                    $heading_title = $this->language->get('module')->get('heading_title');
                }

                if (!isset($part[1])) {
                    $entry['name'] = strip_tags($heading_title);
                    $entry['path'] = strip_tags($heading_title);
                    $entry['edit'] = $this->url->link('extension/module/' . $part[0], 'user_token=' . $this->session->data['user_token'] . $url, true);
                } else {
                    $this->load->model('extension/maza/opencart');
                    
                    $module_info = $this->model_extension_maza_opencart->getModule($part[1]);

                    if ($module_info) {
                        $entry['name'] = $module_info['name'];
                        $entry['path'] = strip_tags($heading_title) . ' > ' . $module_info['name'];
                        $entry['edit'] = $this->url->link('extension/module/' . $part[0], 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $part[1] . $url, true);
                    }				
                }
            }
            
            // Widget entry
            if($entry['type'] === 'widget'){
                $this->load->language('extension/mz_widget/' . $entry['code'], 'widget');

                if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                    $entry['name'] = $this->language->get('heading_title');
                } else {
                    $entry['name'] = strip_tags($this->language->get('widget')->get('heading_title'));
                }
            }
            
            // Design entry
            if($entry['type'] === 'design'){
                $this->load->language('extension/mz_design/' . $entry['code'], 'design');

                if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                    $entry['name'] = $this->language->get('heading_title');
                } else {
                    $entry['name'] = strip_tags($this->language->get('design')->get('heading_title'));
                }
            }
            
            // Content entry
            if($entry['type'] === 'content'){
                $part = explode('.', $entry['code']);

                if (isset($part[1])) {
                    $this->load->language('extension/mz_content/' . $part[0] . '/' . $part[1], 'content');

                    if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                        $entry['name'] = $this->language->get('heading_title');
                    } else {
                        $entry['name'] = strip_tags($this->language->get('content')->get('heading_title'));
                    }
                }
                
            }
            
            $entry['child_entry'] = $this->getLayout(null, $entry['child_entry']);
            
            $data[] = $entry;
        }
        
        return $data;
    }
        
    /**
     * Export layout
     */
    public function export(): void {
        $data = array();
        
        $this->load->model('design/layout');
        
        $layout_info = $this->model_design_layout->getLayout($this->request->get['layout_id']);
        
        if($layout_info){
            $data['layout_content_top'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout_content_top', 'group_owner' => $layout_info['layout_id']]);
            $data['layout_content_bottom'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout_content_bottom', 'group_owner' => $layout_info['layout_id']]);
            $data['layout_column_left'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout_column_left', 'group_owner' => $layout_info['layout_id']]);
            $data['layout_column_right'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout_column_right', 'group_owner' => $layout_info['layout_id']]);
            $data['layout'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout', 'group_owner' => $layout_info['layout_id']]);
            $data['layout_component'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'layout_component', 'group_owner' => $layout_info['layout_id']]);
            
            header('Content-Type: application/json; charset=utf-8');
            header('Content-disposition: attachment; filename="layout.layout_builder.' . $this->mz_skin_config->get('skin_code') . '(' . $layout_info['name'] . ').json"');
            
            echo json_encode(['type' => 'layout_builder', 'page_type' => $layout_info['mz_layout_type'], 'data' => $data]);
        } else {
            $url = '';
        
            if(isset($this->request->get['mz_theme_code'])){
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
            }
            if(isset($this->request->get['mz_skin_id'])){
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
            }
            
            $this->response->redirect($this->url->link('extension/maza/layout_builder', 'user_token=' . $this->session->data['user_token']  . $url, true));
        }
	}
        
    /**
     * Import layout
     */
    public function import(): void {
        $this->load->language('extension/maza/layout_builder');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/maza/layout_builder')) {
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
                
                if($data && $data['type'] == 'layout_builder'){
                    $this->load->model('extension/maza/layout_builder');
                    $this->load->model('design/layout');
                    
                    $layout_info = $this->model_design_layout->getLayout($this->request->get['layout_id']);
                    
                    $layout = array();
                    
                    if($data['page_type'] == $layout_info['mz_layout_type']){
                        $layout = $data['data'];
                    } else {
                        $layout['layout_content_top']   = $this->filterImport($data['data']['layout_content_top']);
                        $layout['layout_content_bottom']= $this->filterImport($data['data']['layout_content_bottom']);
                        $layout['layout_column_left']   = $this->filterImport($data['data']['layout_column_left']);
                        $layout['layout_column_right']  = $this->filterImport($data['data']['layout_column_right']);
                        $layout['layout']               = $this->filterImport($data['data']['layout']);
                        $layout['layout_component']     = $this->filterImport($data['data']['layout_component']);
                    }
                    $this->editLayout($layout);
                    
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
        
    private function filterImport(array $entries): array {
        foreach($entries as $key => $entry){
            if($entry['type'] == 'content'){
                unset($entries[$key]);
                continue;
            }
            
            if(isset($entry['child_entry'])){
                $entries[$key]['child_entry'] = $this->filterImport($entry['child_entry']);
            }
        }
        
        return $entries;
    }
}
