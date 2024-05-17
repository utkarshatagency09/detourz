<?php
class ControllerExtensionMazaInstallEngine extends Controller {
    public function index() {
        $this->load->language('extension/maza/install/engine');

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
            'href' => $this->url->link('extension/maza/install/engine', $url, true)
        );
                
        if(empty($this->session->data['mz_database'])){
            $data['database']  = $this->url->link('extension/maza/install/engine/database', $url, true);
        }
        if(empty($this->session->data['mz_hook'])){
            $data['hook']  = $this->url->link('extension/maza/install/engine/hook', $url, true);
        }
        if(empty($this->session->data['mz_modification'])){
            $data['modification']  = $this->url->link('extension/maza/install/engine/modification', $url, true);
        }
        if(empty($this->session->data['mz_setting'])){
            $data['setting']  = $this->url->link('extension/maza/install/engine/setting', $url, true);
        }
        if($this->config->get('mz_version') && empty($this->session->data['mz_patch'])){
            $data['patch']  = $this->url->link('extension/maza/install/patch', $url, true);
        }
        $data['complete']  = $this->url->link('extension/maza/install/engine/complete', $url, true);
        
        if(isset($this->session->data['success'])){
            unset($this->session->data['success']);
        }
            
        if($this->config->get('mz_version')){
            $data['text_install'] = $this->language->get('text_upgrade');
            $data['button_install'] = $this->language->get('button_upgrade');
        }
                
        $data['continue']  = $this->url->link('extension/maza/skin', $url, true);
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('extension/maza/install/engine', $data));
    }
    
    public function database(){
        $this->load->language('extension/maza/install/engine');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/maza/install/engine')) {
            $json['error'] = $this->language->get('error_permission');
        }
        
        if(isset($this->session->data['mz_database'])){
            $json['next'] = 1;
        }
        
        if(!$json){
            $this->load->model('setting/setting');
            $this->load->model('extension/maza/install');
            
            $maintenance = $this->config->get('config_maintenance');
            $this->model_setting_setting->editSettingValue('config', 'config_maintenance', true);
            
            $this->model_extension_maza_install->updateDatabase();
            
            // Success
            $json['next'] = $this->session->data['mz_database'] = 1;
            
            $this->model_setting_setting->editSettingValue('config', 'config_maintenance', $maintenance);
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function modification(){
        $this->load->language('extension/maza/install/engine');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/maza/install/engine')) {
            $json['error'] = $this->language->get('error_permission');
        }
        
        if(isset($this->session->data['mz_modification'])){
            $json['next'] = 1;
        }
        
        if(isset($this->session->data['success'])){
            unset($this->session->data['success']);
            $json['next'] = 1;
        }
        
        if(!$json){
            $this->load->model('extension/maza/install');
            
            $this->model_extension_maza_install->intallOCModXml();
            
            // Success
            $json['next'] = $this->session->data['mz_modification'] = 1;
            
            // Refresh modification
            if($this->user->hasPermission('modify', 'marketplace/modification') || $this->user->hasPermission('modify', 'extension/modification')){
                if(version_compare(VERSION, '3.0.0.0') < 0){
                    $this->response->redirect($this->url->link('extension/maza/install/engine/refresh', 'token=' . $this->session->data['token'], true));
                } else {
                    $this->response->redirect($this->url->link('extension/maza/install/engine/refresh', 'user_token=' . $this->session->data['user_token'], true));
                }
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function hook(){
        $this->load->language('extension/maza/install/engine');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/maza/install/engine')) {
            $json['error'] = $this->language->get('error_permission');
        }
        
        if(isset($this->session->data['mz_hook'])){
            $json['next'] = 1;
        }
        
        if(!$json){
            $this->load->model('extension/maza/install');
            
            $this->model_extension_maza_install->addHooks();
            
            // Success
            $json['next'] = $this->session->data['mz_hook'] = 1;
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function refresh(){
        if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
            $this->load->controller('extension/modification/refresh', ['redirect' => 'extension/maza/install/engine/modification']);
            $this->response->redirect($this->url->link('extension/maza/install/engine/modification', 'token=' . $this->session->data['token'], true));
        } else {
            $this->load->controller('marketplace/modification/refresh', ['redirect' => 'extension/maza/install/engine/modification']);
        }
    }
    
    public function setting(){
        $this->load->language('extension/maza/install/engine');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/maza/install/engine')) {
            $json['error'] = $this->language->get('error_permission');
        }
        
        if(isset($this->session->data['mz_setting'])){
            $json['next'] = 1;
        }
        
        if(!$json){
            $this->load->model('setting/setting');
            $this->load->model('extension/maza/skin');
            
            $setting = array();
            $setting['maza_developer_mode'] = 0;
            $setting['maza_cache_status']   = 1;
            $setting['maza_cache_partial']  = 1;
            $setting['maza_cache_page']     = 0;
            $setting['maza_api_google_map_key'] = '';
            $setting['maza_api_exchangerate_key'] = '';
            $setting['maza_notification_status'] = 1;
            $setting['maza_notification_manufacturer'] = 1;
            $setting['maza_minify_css']     = 1;
            $setting['maza_minify_js']      = 1;
            $setting['maza_minify_html']    = 1;
            $setting['maza_combine_css']    = 1;
            $setting['maza_combine_js']     = 1;
            $setting['maza_js_position']    = 'footer'; // Header, footer, default
            $setting['maza_css_autoprefix'] = 1;
            $setting['maza_cdn']            = 1;
            $setting['maza_webp']           = 1;
            $setting['maza_schema']         = 1;
            $setting['maza_ogp']            = 1;
            $setting['maza_query_keyword']  = 1;
            $setting['maza_cron_status']    = 0;

            $this->model_setting_setting->editSetting('maza', array_merge($setting, $this->model_setting_setting->getSetting('maza')));

            // Fixed
            if(!$this->config->get('mz_version')){
                $this->model_setting_setting->editSetting('module_mz_newsletter', ['module_mz_newsletter_status' => 1]);
            }
            
            // Success
            $json['next'] = $this->session->data['mz_setting'] = 1;
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function complete(){
        $this->load->language('extension/maza/install/engine');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/maza/install/engine')) {
            $json['error'] = $this->language->get('error_permission');
        }
        
        if(!$json){
            if(!empty($this->session->data['mz_database']) && !empty($this->session->data['mz_hook']) && !empty($this->session->data['mz_modification']) && !empty($this->session->data['mz_setting'])){
                
                $this->load->model('setting/setting');
                $this->load->model('extension/maza/common');
                
                $this->model_setting_setting->editSetting('mz', ['mz_version' => MZ_CONST::VERSION]);
                
                // Add Permission
                $this->load->model('user/user_group');
                
                $routes = array('extension/maza', 'extension/mz_content', 'extension/mz_design', 'extension/mz_widget');
                
                $glob = array(
                    'controller/extension/maza/*.php',
                    'controller/extension/maza/*/*.php',
                    'controller/extension/mz_content/*/*.php',
                    'controller/extension/{mz_design,mz_widget}/*.php',
                    'controller/extension/module/mz_*.php',
                );
                
                foreach($glob as $pattern){
                    foreach(glob($pattern, GLOB_BRACE) as $file){
                        $routes[] = substr($file, strlen('controller/'), -4);
                    }
                }

                $user_group = $this->model_user_user_group->getUserGroup($this->user->getGroupId());
                
                foreach($routes as $route){
                    if(!in_array($route, $user_group['permission']['access'])){
                        $user_group['permission']['access'][] = $route;
                    }
                    if(!in_array($route, $user_group['permission']['modify'])){
                        $user_group['permission']['modify'][] = $route;
                    }
                }

                $this->model_user_user_group->editUserGroup($this->user->getGroupId(), $user_group);

                // Clear cache
                $this->model_extension_maza_common->clearCache();
                
                // unset($this->session->data['mz_startup']);
                unset($this->session->data['mz_database']);
                // unset($this->session->data['mz_event']);
                unset($this->session->data['mz_hook']);
                unset($this->session->data['mz_modification']);
                unset($this->session->data['mz_patch']);
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
