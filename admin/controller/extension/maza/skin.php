<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaSkin extends Controller {
    protected $error = array();

    public function index(): void {
        $this->load->language('extension/maza/skin');

        $this->document->setTitle($this->language->get('heading_title'));
        
        // Header
        $header_data = array();
        
        $header_data['menu'] = array(
            array('name' => $this->language->get('tab_skin'), 'id' => 'menu-skin', 'href' => false),
            array('name' => $this->language->get('tab_header'), 'id' => 'menu-header', 'href' => false),
            array('name' => $this->language->get('tab_footer'), 'id' => 'menu-footer', 'href' => false)
        );
        
        $header_data['menu_active'] = 'menu-skin';
        $header_data['buttons'][] = array(
            'id' => 'button-save',
            'name' => false,
            'tooltip' => $this->language->get('button_save'),
            'icon' => 'fa-save',
            'class' => 'btn-primary',
            'href' => FALSE,
            'target' => FALSE,
            'form_target_id' => 'form-skin',
        );
        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#page-skin',
            'target' => '_blank'
        );
        $header_data['form_target_id'] = 'form-skin';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
        
        // Submit form
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()){
            // Skin setting
            $this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'skin', $this->request->post);
            
            // clear asset files for new settings
            $this->mz_document->clear();
            
            // Theme setting
            foreach ($this->request->post['theme'] as $store_id => $setting) {
                $this->model_extension_maza_theme->editSetting($this->mz_theme_config->get('theme_code'), 'skin', $setting, $store_id);
            }
            
            $data['success'] = $this->language->get('text_success');
        }
        
        if(isset($this->error['warning'])){
            $data['warning'] = $this->error['warning'];
        }
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . htmlspecialchars($this->request->get['mz_theme_code']);
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['action'] = $this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['add_skin'] = $this->url->link('extension/maza/skin/addSkin', 'user_token=' . $this->session->data['user_token'] . '&theme_id=' .  $this->mz_theme_config->get('theme_id') . $url);
        $data['add_header'] = $this->url->link('extension/maza/skin/header/add', 'user_token=' . $this->session->data['user_token'] . '&theme_id=' .  $this->mz_theme_config->get('theme_id') . $url);
        $data['add_footer'] = $this->url->link('extension/maza/skin/footer/add', 'user_token=' . $this->session->data['user_token'] . '&theme_id=' .  $this->mz_theme_config->get('theme_id') . $url);
        
        // Setting
        $setting = array();
        
        // Header setting
        $setting['skin_header_id'] = 0;
        $setting['skin_footer_id'] = 0;
        
        
        if($this->request->server['REQUEST_METHOD'] == 'POST' && !empty($this->request->post)){
            $setting = array_merge($setting, $this->request->post);
        } else {
            $setting = array_merge($setting, $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'skin')); 
        }
        
        $data = array_merge($data, $setting);
        
        // Data
        
        // Skin list
        $data['skins'] = array();
        $skins      =   $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
        
        foreach ($skins as $skin) {
            // Get parent of skin for child skin
            if($skin['parent_skin_id']){
                $parent_skin_info  = $this->model_extension_maza_skin->getSkin($skin['parent_skin_id']);
            } else {
                $parent_skin_info  = array();
            }
            
            // Get skin config
            if($parent_skin_info){
                $skin_config  = $this->model_extension_maza_skin->getSkinConfig($this->mz_theme_config->get('theme_code'), $parent_skin_info['skin_code']);
            }else{
                $skin_config  = $this->model_extension_maza_skin->getSkinConfig($this->mz_theme_config->get('theme_code'), $skin['skin_code']);
            }
            
            if($skin_config){
                // Skin image thumb
                if ($this->request->server['HTTPS']) {
                    $image_end_point = HTTPS_CATALOG . 'image/';
                } else {
                    $image_end_point = HTTP_CATALOG . 'image/';
                }

                if(file_exists(DIR_IMAGE . $skin_config['image'])){
                    $image = $image_end_point . $skin_config['image'];
                } else {
                    $image = $image_end_point . 'no_image.png';
                }

                if ($this->request->server['HTTPS']){
                    $preview  = HTTPS_CATALOG . '?mz_theme_code=' . $this->mz_theme_config->get('theme_code') . '&mz_skin_id=' . $skin['skin_id'];
                } else {
                    $preview  = HTTP_CATALOG . '?mz_theme_code=' . $this->mz_theme_config->get('theme_code') . '&mz_skin_id=' . $skin['skin_id'];
                }

                $data['skins'][] = array(
                        'skin_id' => $skin['skin_id'],
                        'name'  =>  $skin['name'],
                        'image'  =>  $image,
                        'parent_skin' => $parent_skin_info,
                        'preview' => $preview
                );
            }
        }
        
        // Multistore skin
        $this->load->model('setting/store');
        
        $data['stores'] = array();
        
        if(isset($this->request->post['theme'][0])){
            $skin_id = $this->request->post['theme'][0]['skin_id'];
        } else {
            $skin_id = $this->model_extension_maza_theme->getSettingValue($this->mz_theme_config->get('theme_code'), 'skin_id', 0);
        }

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->config->get('config_name') . $this->language->get('text_default'),
            'skin_id'  => $skin_id
		);
                
        $results = $this->model_setting_store->getStores();

		foreach ($results as $result) {
            if(isset($this->request->post['theme'][$result['store_id']])){
                $skin_id = $this->request->post['theme'][$result['store_id']]['skin_id'];
            } else {
                $skin_id = $this->model_extension_maza_theme->getSettingValue($this->mz_theme_config->get('theme_code'), 'skin_id', $result['store_id']);
            }
                
			$data['stores'][] = array(
				'store_id' => $result['store_id'],
				'name'     => $result['name'],
                'skin_id'  => $skin_id
			);
		}
                
        // Header
        $data['headers'] = array();
        $headers      =   $this->model_extension_maza_header->getHeaders($this->mz_theme_config->get('theme_id'));
        
        foreach ($headers as $header) {
            
            // Get parent of header for child header
            if($header['parent_header_id']){
                $parent_header_info  = $this->model_extension_maza_header->getHeader($header['parent_header_id']);
            } else {
                $parent_header_info  = array();
            }
            
            // Get header config
            if($parent_header_info){
                $header_config  = $this->model_extension_maza_header->getHeaderConfig($this->mz_theme_config->get('theme_code'), $parent_header_info['code']);
            }else{
                $header_config  = $this->model_extension_maza_header->getHeaderConfig($this->mz_theme_config->get('theme_code'), $header['code']);
            }
            
            if($header_config){
                // Header image thumb
                if ($this->request->server['HTTPS']) {
                    $image_end_point = HTTPS_CATALOG . 'image/';
                } else {
                    $image_end_point = HTTP_CATALOG . 'image/';
                }

                if(file_exists(DIR_IMAGE . $header_config['image'])){
                    $image = $image_end_point . $header_config['image'];
                } else {
                    $image = $image_end_point . 'no_image.png';
                }

                $data['headers'][] = array(
                    'header_id' => $header['header_id'],
                    'name'  =>  $header['name'],
                    'image'  =>  $image,
                    'parent_header' => $parent_header_info,
                    'edit'  => $this->url->link('extension/maza/skin/header', 'user_token=' . $this->session->data['user_token'] . '&header_id=' . $header['header_id'] . $url, true)
                );
            }
        }
        
        // Footer
        $data['footers'] = array();
        $footers      =   $this->model_extension_maza_footer->getFooters($this->mz_theme_config->get('theme_id'));
        
        foreach ($footers as $footer) {
            // Get parent of footer for child footer
            if($footer['parent_footer_id']){
                $parent_footer_info  = $this->model_extension_maza_footer->getFooter($footer['parent_footer_id']);
            } else {
                $parent_footer_info  = array();
            }
            
            // Get footer config
            if($parent_footer_info){
                $footer_config  = $this->model_extension_maza_footer->getFooterConfig($this->mz_theme_config->get('theme_code'), $parent_footer_info['code']);
            }else{
                $footer_config  = $this->model_extension_maza_footer->getFooterConfig($this->mz_theme_config->get('theme_code'), $footer['code']);
            }
            
            if($footer_config){
                // Footer image thumb
                if ($this->request->server['HTTPS']) {
                    $image_end_point = HTTPS_CATALOG . 'image/';
                } else {
                    $image_end_point = HTTP_CATALOG . 'image/';
                }

                if(file_exists(DIR_IMAGE . $footer_config['image'])){
                    $image = $image_end_point . $footer_config['image'];
                } else {
                    $image = $image_end_point . 'no_image.png';
                }

                $data['footers'][] = array(
                    'footer_id' => $footer['footer_id'],
                    'name'  =>  $footer['name'],
                    'image'  =>  $image,
                    'parent_footer' => $parent_footer_info,
                    'edit'  => $this->url->link('extension/maza/skin/footer', 'user_token=' . $this->session->data['user_token'] . '&footer_id=' . $footer['footer_id'] . $url, true)
                );
            }
        }
        
        if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
        if (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}
                
        $data['mz_theme_code'] = $this->request->get['mz_theme_code'];
        $data['mz_skin_id'] = $this->request->get['mz_skin_id'];
        
        $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
        $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		$this->response->setOutput($this->load->view('extension/maza/skin', $data));
    }
        
    /**
     * Add new skin
     */
    public function addSkin(): void {
        $json = array();
        
        $this->load->language('extension/maza/skin');
        
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateAddSkin()){
            $this->load->model('extension/maza/theme');
            $this->load->model('extension/maza/skin');
            $this->load->model('extension/maza/module');
            $this->load->model('extension/maza/code');
            $this->load->model('extension/maza/layout_builder');
            
            $skin_data = array();
            $skin_data['name'] = $this->request->post['skin_name'];
            
            $theme_info = $this->model_extension_maza_theme->getTheme($this->request->get['theme_id']);
            $parent_skin_info = $this->model_extension_maza_skin->getSkin($this->request->post['skin_parent_id']);
            
            // Check all parameter is valid
            if($theme_info && $parent_skin_info && ($theme_info['theme_id'] == $parent_skin_info['theme_id']) && ($parent_skin_info['parent_skin_id'] == '0')){
                $skin_data['parent_skin_id'] = $parent_skin_info['skin_id'];
                $skin_data['theme_id']       = $theme_info['theme_id'];
                
                // Add Skin
                $new_skin_id = $this->model_extension_maza_skin->addSkin($skin_data);
                
                // Duplicate setting
                $this->model_extension_maza_skin->duplicateSetting($parent_skin_info['skin_id'], $new_skin_id);
                
                // Duplicate module setting
                $this->model_extension_maza_module->duplicateSetting($parent_skin_info['skin_id'], $new_skin_id);
                
                // Duplicate layout builder setting
                $this->model_extension_maza_layout_builder->duplicateSkin($parent_skin_info['skin_id'], $new_skin_id);
                
                // Copy code
                $this->model_extension_maza_code->duplicateCode($parent_skin_info['skin_id'], $new_skin_id);
                
                $json['success'] = $this->language->get('text_success_skin_add');
            }
        }
        
        if(isset($this->error['warning'])){
            $json['error'] = $this->error['warning'];
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
        
    /**
     * Edit skin
     */
    public function editSkin(): void {
        $json = array();
        
        $this->load->language('extension/maza/skin');
        
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateEditSkin()){
            $this->load->model('extension/maza/skin');
            
            $skin_info = $this->model_extension_maza_skin->getSkin($this->request->get['skin_id']);
            
            // Check all parameter is valid
            if($skin_info && ($skin_info['parent_skin_id'] != '0')){
                $skin_data = array();
                $skin_data['name'] = $this->request->post['skin_name'];
                
                // modify Skin
                $this->model_extension_maza_skin->alterSkin($this->request->get['skin_id'], $skin_data);
                
                $json['success'] = $this->language->get('text_success_skin_edit');
            }
        }
        
        if(isset($this->error['warning'])){
            $json['error'] = $this->error['warning'];
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
        
    /**
     * Delete skin by id
     */
    public function deleteSkin(): void {
        $json = array();
            
        $this->load->language('extension/maza/skin');
        
        if (!$this->user->hasPermission('modify', 'extension/maza/skin')) {
			$json['error'] = $this->language->get('error_permission');
		}
                
        if(!$json && isset($this->request->post['skin_id'])){
            $this->load->model('extension/maza/skin');
            
            // Delete Skin
            $this->model_extension_maza_skin->deleteSkin($this->request->post['skin_id']);

            $json['success'] = $this->language->get('text_success_skin_delete');
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
        
    /**
     * create new duplicate skin of existing skin
     */
    public function duplicateSkin(): void {
        $json = array();
            
        $this->load->language('extension/maza/skin');
        
        if (!$this->user->hasPermission('modify', 'extension/maza/skin')) {
			$json['error'] = $this->language->get('error_permission');
		}
                
        if(!$json && isset($this->request->post['skin_id'])){
            $this->load->model('extension/maza/skin');
            $this->load->model('extension/maza/module');
            $this->load->model('extension/maza/layout_builder');
            $this->load->model('extension/maza/code');
            
            $skin_info = $this->model_extension_maza_skin->getSkin($this->request->post['skin_id']);
            
            // Skin mush be child skin to make duplicate
            if($skin_info && $skin_info['parent_skin_id'] != 0){
                unset($skin_info['skin_code']);
                $skin_info['name'] .= $this->language->get('text_duplicate');
                
                // Add new Skin
                $duplicate_skin_id = $this->model_extension_maza_skin->addSkin($skin_info);
                
                // Duplicate skin setting
                if($duplicate_skin_id){
                    $this->model_extension_maza_skin->duplicateSetting($this->request->post['skin_id'], $duplicate_skin_id);
                    $this->model_extension_maza_module->duplicateSetting($this->request->post['skin_id'], $duplicate_skin_id);
                    $this->model_extension_maza_layout_builder->duplicateSkin($this->request->post['skin_id'], $duplicate_skin_id);
                    $this->model_extension_maza_code->duplicateCode($this->request->post['skin_id'], $duplicate_skin_id);
                }
            }
            
            $json['success'] = $this->language->get('text_success_skin_duplicate');
        }
        
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
        
    protected function validateAddSkin(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/skin')) {
            $this->error['warning'] = $this->language->get('error_permission');
		} elseif(empty ($this->request->post['skin_name']) || (utf8_strlen($this->request->post['skin_name']) < 3) || (utf8_strlen($this->request->post['skin_name']) > 64)) {
            $this->error['warning'] = $this->language->get('error_skin_name');
        } elseif(empty ($this->request->post['skin_parent_id'])) {
            $this->error['warning'] = $this->language->get('error_skin_parent');
        }

		return !$this->error;
	}
        
    protected function validateEditSkin(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/skin')) {
            $this->error['warning'] = $this->language->get('error_permission');
		} elseif(empty ($this->request->post['skin_name']) || (utf8_strlen($this->request->post['skin_name']) < 3) || (utf8_strlen($this->request->post['skin_name']) > 64)) {
            $this->error['warning'] = $this->language->get('error_skin_name');
        }

		return !$this->error;
	}
        
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/skin')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error && !empty($this->request->post);
	}
}
