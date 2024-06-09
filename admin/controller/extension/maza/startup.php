<?php
require_once(DIR_SYSTEM . 'library/maza/startup.php');

class ControllerExtensionMazaStartup extends Controller {
	public function index() {
        // Config
        $this->config->load('maza/default');
        $this->config->load('maza/admin');

        // Library
        $this->mz_theme_config  = new maza\config\Theme();
        $this->mz_skin_config   = new maza\config\Skin();
        $this->mz_document      = new maza\Document();
        $this->mz_cache         = new maza\Cache($this->config->get('mz_cache_engine'), $this->config->get('cache_expire'));
        $this->mz_load          = new maza\Loader($this->registry);
        $this->mz_hook          = new maza\Hook($this->registry);
        $this->mz_minifier      = new maza\Minifier();

        $this->cart->mz_skin_config = $this->mz_skin_config;

        if ($this->config->get('db_autostart')) {
            $this->mz_db = new maza\DB($this->config->get('db_engine'), $this->config->get('db_hostname'), $this->config->get('db_username'), $this->config->get('db_password'), $this->config->get('db_database'), $this->config->get('db_port'));
        }

        // Restrict the access before to verify purchase
        if(defined('ENVATO_TOKEN')){
            $this->event->register('controller/extension/maza/*/export/before', new Action('extension/maza/event/common/themeforest'));
        }

        // Event
        if ($this->config->has('mz_action_event')) {
            foreach ($this->config->get('mz_action_event') as $key => $value) {
                foreach ($value as $priority => $action) {
                    $this->event->register($key, new Action($action), $priority);
                }
            }
        }

        // default http header
        foreach ($this->config->get('mz_response_header') as $header) {
            $this->response->addHeader($header);
        }

        if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
            $this->event->register('model/extension/module/deleteModule/before', new Action('extension/maza/event/setting/module/deleteModule'));
        } else {
            $this->event->register('model/setting/module/deleteModule/before', new Action('extension/maza/event/setting/module/deleteModule'));
        }

        // Website domain
        if ($this->request->server['HTTPS']) {
            $this->config->set('mz_store_url', HTTPS_CATALOG);
            $this->session->data['mz_admin_url'] = HTTPS_SERVER;
        } else {
            $this->config->set('mz_store_url', HTTP_CATALOG);
            $this->session->data['mz_admin_url'] = HTTP_SERVER;
        }
    
        // Load theme setting
        if(!empty($this->request->get['mz_theme_code'])){
            $this->load->model('extension/maza/theme');
            $this->load->model('extension/maza/skin');
            $this->load->model('extension/maza/header');
            $this->load->model('extension/maza/footer');
            $this->load->language('extension/maza/common');
            
            $theme_setting = $this->model_extension_maza_theme->getSetting($this->request->get['mz_theme_code'], $this->config->get('config_store_id'));
            
            if($theme_setting){
                foreach ($theme_setting as $key => $value) {
                    $this->mz_theme_config->set($key, $value);
                }
            }
            
            $mz_theme_info = $this->model_extension_maza_theme->getThemeByCode($this->request->get['mz_theme_code']);
            
            if($mz_theme_info){
                $this->mz_theme_config->set('theme_id', $mz_theme_info['theme_id']);
                $this->mz_theme_config->set('theme_code', $mz_theme_info['theme_code']);
                $this->mz_theme_config->set('version', $mz_theme_info['version']);
            }  elseif(strpos($this->request->get['route'], 'extension/maza/install/theme') !== 0) {
                $this->response->redirect($this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token'], true));
            } else {
                return;
            }
        } else {
            return;
        }
        
        // Load skin setting
        if(!empty($this->request->get['mz_skin_id'])){
            $skin_info = $this->model_extension_maza_skin->getSkin($this->request->get['mz_skin_id']);
    
            if($skin_info){
                $skin_setting = $this->model_extension_maza_skin->getSetting($this->request->get['mz_skin_id']);

                foreach ($skin_setting as $key => $value) {
                    $this->mz_skin_config->set($key, $value);
                }

                if($skin_info['parent_skin_id']){
                    $parent_skin_info = $this->model_extension_maza_skin->getSkin($skin_info['parent_skin_id']);
                    $skin_info['skin_code'] = $parent_skin_info['skin_code'];
                }
                
                $this->mz_skin_config->set('skin_id', $skin_info['skin_id']);
                $this->mz_skin_config->set('skin_code', $skin_info['skin_code']);
            } else {
                $this->response->redirect($this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token'], true));
            }
        } else {
            return;
        }
        
        $this->config->set('maza_status', TRUE);
        
        // Load config file
        MZ_CONFIG::load();
	}
}
