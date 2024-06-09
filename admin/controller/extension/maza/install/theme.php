<?php
class ControllerExtensionMazaInstallTheme extends Controller {
	public function index() {
		$this->load->language('extension/maza/install/theme');

		$this->document->setTitle($this->language->get('heading_title'));
                
                if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                    $url = 'token=' . $this->session->data['token'];
                    
                    $data = $this->language->all();
                } else {
                    $url = 'user_token=' . $this->session->data['user_token'];
                }
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/maza/install/theme', $url, true)
		);
                
                $this->load->model('extension/maza/theme');
                
                if(empty($this->session->data['mz_config'])){
                    $data['config']  = $this->url->link('extension/maza/install/theme/config', 'mz_theme_code=' . $this->request->get['mz_theme_code'] . '&' . $url, true);
                }
                if(empty($this->session->data['mz_skin'])){
                    $data['skin']  = $this->url->link('extension/maza/install/theme/skin', 'mz_theme_code=' . $this->request->get['mz_theme_code'] . '&' . $url, true);
                }
                if(empty($this->session->data['mz_header'])){
                    $data['mz_header']  = $this->url->link('extension/maza/install/theme/header', 'mz_theme_code=' . $this->request->get['mz_theme_code'] . '&' . $url, true);
                }
                if(empty($this->session->data['mz_footer'])){
                    $data['mz_footer']  = $this->url->link('extension/maza/install/theme/footer', 'mz_theme_code=' . $this->request->get['mz_theme_code'] . '&' . $url, true);
                }
                if(empty($this->session->data['mz_setting'])){
                    $data['setting']  = $this->url->link('extension/maza/install/theme/setting', 'mz_theme_code=' . $this->request->get['mz_theme_code'] . '&' . $url, true);
                }
                $data['complete']  = $this->url->link('extension/maza/install/theme/complete', 'mz_theme_code=' . $this->request->get['mz_theme_code'] . '&' . $url, true);
                
                $theme_info = $this->model_extension_maza_theme->getThemeByCode($this->request->get['mz_theme_code']);
                
                if($theme_info){
                    $data['text_install'] = $this->language->get('text_upgrade');
                    $data['button_install'] = $this->language->get('button_upgrade');
                }
                
                $data['continue']  = $this->url->link('extension/maza/skin', 'mz_theme_code=' . $this->request->get['mz_theme_code'] . '&' . $url, true);
                
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/maza/install/theme', $data));
	}
        
        public function config(){
                $this->load->language('extension/maza/install/theme');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/maza/install/theme')) {
			$json['error'] = $this->language->get('error_permission');
                }
                
                if(isset($this->session->data['mz_config'])){
                    $json['next'] = 1;
                }
                
                if(!$json){
                    $this->load->model('setting/setting');
                    $this->load->model('extension/maza/theme');
                    
                    $maintenance = $this->config->get('config_maintenance');
                    $this->model_setting_setting->editSettingValue('config', 'config_maintenance', true);
                        
                    // Install theme
                    $theme_info = $this->model_extension_maza_theme->getThemeByCode($this->request->get['mz_theme_code']);
                    $theme_config = $this->model_extension_maza_theme->getThemeConfig($this->request->get['mz_theme_code']);
                    
                    if($theme_info){
                        $this->model_extension_maza_theme->editTheme($theme_info['theme_id'], $theme_config);
                    } else {
                        $this->model_extension_maza_theme->addTheme($theme_config);
                    }
                    
                    // Success
                    $json['next'] = $this->session->data['mz_config'] = 1;
                    
                    $this->model_setting_setting->editSettingValue('config', 'config_maintenance', $maintenance);
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        public function skin(){
                $this->load->language('extension/maza/install/theme');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/maza/install/theme')) {
			$json['error'] = $this->language->get('error_permission');
                }
                
                if(isset($this->session->data['mz_skin'])){
                    $json['next'] = 1;
                }
                
                if(!$json){
                    $this->load->model('setting/setting');
                    $this->load->model('extension/maza/theme');
                    $this->load->model('extension/maza/skin');
                    
                    $maintenance = $this->config->get('config_maintenance');
                    $this->model_setting_setting->editSettingValue('config', 'config_maintenance', true);
                        
                    // Install skins
                    $theme_info = $this->model_extension_maza_theme->getThemeByCode($this->request->get['mz_theme_code']);
                    $skins = $this->model_extension_maza_theme->getSkins($this->request->get['mz_theme_code']);
                    
                    foreach ($skins as $skin_config) {
                        $skin_config['theme_id'] = $theme_info['theme_id'];
                        $skin_info = $this->model_extension_maza_skin->getSkinByCode($theme_info['theme_code'], $skin_config['code']);

                        if($skin_info){
                            $this->model_extension_maza_skin->editSkin($skin_info['skin_id'], $skin_config);
                        } else {
                            $this->model_extension_maza_skin->addSkin($skin_config);
                        }
                    }
                    
                    // Success
                    $json['next'] = $this->session->data['mz_skin'] = 1;
                    
                    $this->model_setting_setting->editSettingValue('config', 'config_maintenance', $maintenance);
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        public function header(){
                $this->load->language('extension/maza/install/theme');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/maza/install/theme')) {
			$json['error'] = $this->language->get('error_permission');
                }
                
                if(isset($this->session->data['mz_header'])){
                    $json['next'] = 1;
                }
                
                if(!$json){
                    $this->load->model('setting/setting');
                    $this->load->model('extension/maza/theme');
                    $this->load->model('extension/maza/header');
                    
                    $maintenance = $this->config->get('config_maintenance');
                    $this->model_setting_setting->editSettingValue('config', 'config_maintenance', true);
                        
                    // Install headers
                    $theme_info = $this->model_extension_maza_theme->getThemeByCode($this->request->get['mz_theme_code']);
                    $headers = $this->model_extension_maza_theme->getHeaders($this->request->get['mz_theme_code']);
                    
                    foreach ($headers as $header) {
                        $header['theme_id'] = $theme_info['theme_id'];
                        $header_info = $this->model_extension_maza_header->getHeaderByCode($theme_info['theme_code'], $header['code']);

                        if($header_info){
                            $this->model_extension_maza_header->editHeader($header_info['header_id'], $header);
                        } else {
                            $this->model_extension_maza_header->addHeader($header);
                        }
                    }
                    
                    // Success
                    $json['next'] = $this->session->data['mz_header'] = 1;
                    
                    $this->model_setting_setting->editSettingValue('config', 'config_maintenance', $maintenance);
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        public function footer(){
                $this->load->language('extension/maza/install/theme');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/maza/install/theme')) {
			$json['error'] = $this->language->get('error_permission');
                }
                
                if(isset($this->session->data['mz_footer'])){
                    $json['next'] = 1;
                }
                
                if(!$json){
                    $this->load->model('setting/setting');
                    $this->load->model('extension/maza/theme');
                    $this->load->model('extension/maza/footer');
                    
                    $maintenance = $this->config->get('config_maintenance');
                    $this->model_setting_setting->editSettingValue('config', 'config_maintenance', true);
                        
                    // Install footers
                    $theme_info = $this->model_extension_maza_theme->getThemeByCode($this->request->get['mz_theme_code']);
                    $footers = $this->model_extension_maza_theme->getFooters($this->request->get['mz_theme_code']);
                    
                    foreach ($footers as $footer) {
                        $footer['theme_id'] = $theme_info['theme_id'];
                        $footer_info = $this->model_extension_maza_footer->getFooterByCode($theme_info['theme_code'], $footer['code']);

                        if($footer_info){
                            $this->model_extension_maza_footer->editFooter($footer_info['footer_id'], $footer);
                        } else {
                            $this->model_extension_maza_footer->addFooter($footer);
                        }
                    }
                    
                    // Success
                    $json['next'] = $this->session->data['mz_footer'] = 1;
                    
                    $this->model_setting_setting->editSettingValue('config', 'config_maintenance', $maintenance);
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        public function setting(){
                $this->load->language('extension/maza/install/theme');

                $json = array();

                // Check user has permission
                if (!$this->user->hasPermission('modify', 'extension/maza/install/theme')) {
                    $json['error'] = $this->language->get('error_permission');
                }
                
                if(isset($this->session->data['mz_setting'])){
                    $json['next'] = 1;
                }
                
                if(!$json){
                    $this->load->model('setting/setting');
                    $this->load->model('setting/store');
                    $this->load->model('extension/maza/install');
                    $this->load->model('extension/maza/theme');
                    $this->load->model('extension/maza/skin');
                    $this->load->model('extension/maza/header');
                    $this->load->model('extension/maza/footer');
                    
                    $maintenance = $this->config->get('setting_maintenance');
                    $this->model_setting_setting->editSettingValue('setting', 'setting_maintenance', true);
                    
                    // OC Stores
                    $stores = array('0');
                    foreach($this->model_setting_store->getStores() as $store){
                        $stores[] = $store['store_id'];
                    }
                    
                    $theme_info = $this->model_extension_maza_theme->getThemeByCode($this->request->get['mz_theme_code']);
                    $theme_config = $this->model_extension_maza_theme->getThemeConfig($this->request->get['mz_theme_code']);
                    
                    $skins = $this->model_extension_maza_skin->getSkins($theme_info['theme_id'], 0);
                    $headers = $this->model_extension_maza_header->getHeaders($theme_info['theme_id'], 0);
                    $footers = $this->model_extension_maza_footer->getFooters($theme_info['theme_id'], 0);
                    
                    
                    // Add theme setting
                    foreach($stores as $store_id){
                        $theme_setting = $this->model_extension_maza_theme->getSetting($this->request->get['mz_theme_code'], 'skin', $store_id);
                        
                        if(isset($theme_config['default_skin'])){
                            $skin_setting = $this->model_extension_maza_skin->getSkinByCode($this->request->get['mz_theme_code'], $theme_config['default_skin']);
                        } else {
                            $skin_setting = array();
                        }
                        
                        if($skin_setting){
                            $skin_id = $skin_setting['skin_id'];
                        } else {
                            $skin_id = $skins[0]['skin_id'];
                        }
                        
                        if(!$theme_setting){
                            // Default setting
                            foreach($this->model_extension_maza_install->getThemeSetting($this->request->get['mz_theme_code']) as $code => $default_setting){
                                $this->model_extension_maza_theme->editSetting($this->request->get['mz_theme_code'], $code, $default_setting, $store_id);
                            }
                            
                            // Skin setting
                            $this->model_extension_maza_theme->editSetting($this->request->get['mz_theme_code'], 'skin', ['skin_id' => $skin_id], $store_id);
                        }
                    }
                    
                    // Add skin setting
                    foreach($skins as $skin){
                        $skin_setting = $this->model_extension_maza_skin->getSetting($skin['skin_id'], 'skin');
                        $skin_config = $this->model_extension_maza_skin->getSkinConfig($this->request->get['mz_theme_code'], $skin['skin_code']);
                        
                        if(isset($skin_config['default_header'])){
                            $header_setting = $this->model_extension_maza_header->getHeaderByCode($this->request->get['mz_theme_code'], $skin_config['default_header']);
                        } else {
                            $header_setting = array();
                        }
                        
                        if(isset($skin_config['default_footer'])){
                            $footer_setting = $this->model_extension_maza_footer->getFooterByCode($this->request->get['mz_theme_code'], $skin_config['default_footer']);
                        } else {
                            $footer_setting = array();
                        }
                        
                        if($header_setting){
                            $header_id = $header_setting['header_id'];
                        } else {
                            $header_id = $headers[0]['header_id'];
                        }
                        
                        if($footer_setting){
                            $footer_id = $footer_setting['footer_id'];
                        } else {
                            $footer_id = $footers[0]['footer_id'];
                        }
                        
                        if(!$skin_setting){
                            // Default setting
                            foreach($this->model_extension_maza_install->getSkinSetting($this->request->get['mz_theme_code'], $skin['skin_code']) as $code => $default_setting){
                                $this->model_extension_maza_skin->editSetting($skin['skin_id'], $code, $default_setting);
                            }
                            
                            // Skin setting
                            $this->model_extension_maza_skin->editSetting($skin['skin_id'], 'skin', ['skin_header_id' => $header_id, 'skin_footer_id' => $footer_id]);
                        }
                    }
                        
                    // Success
                    $json['next'] = $this->session->data['mz_setting'] = 1;
                    
                    $this->model_setting_setting->editSettingValue('setting', 'setting_maintenance', $maintenance);
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        public function complete(){
                $this->load->language('extension/maza/install/theme');

                $json = array();

                // Check user has permission
                if (!$this->user->hasPermission('modify', 'extension/maza/install/theme')) {
                    $json['error'] = $this->language->get('error_permission');
                }
                
                if(!$json){
                    if(!empty($this->session->data['mz_config']) && !empty($this->session->data['mz_skin']) && !empty($this->session->data['mz_header']) && !empty($this->session->data['mz_footer']) && !empty($this->session->data['mz_setting'])){
                        
                        unset($this->session->data['mz_config']);
                        unset($this->session->data['mz_skin']);
                        unset($this->session->data['mz_header']);
                        unset($this->session->data['mz_footer']);
                        unset($this->session->data['mz_setting']);
                        
                        $json['success'] = $this->language->get('text_success');
                    } else {
                        $json['error'] = $this->language->get('error_complete');
                    }
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
}
