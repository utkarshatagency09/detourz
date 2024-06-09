<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaCommonHeader extends Controller {
    public function index($data = array()) {
        $this->load->language('extension/maza/common/header');
        
        $this->load->model('extension/maza/theme');
        $this->load->model('extension/maza/skin');
        $this->load->model('extension/maza/asset');
        $this->load->model('extension/maza/extension');
        
        $this->document->addStyle('view/stylesheet/maza/mz_stylesheet.css');
        $this->document->addScript('view/javascript/maza/mz_common.js');
        
        // Verify engine installation
        if(!$this->config->get('mz_version') || version_compare(MZ_CONST::VERSION, $this->config->get('mz_version')) > 0){
            return $this->response->redirect($this->url->link('extension/maza/install/engine', 'user_token=' . $this->session->data['user_token'], true));
        }
        
        // Get available themes of maza and get selected theme
        $data['mz_themes'] = $this->model_extension_maza_theme->getThemes();
        
        if(isset($this->request->get['mz_theme_code'])){
            $data['mz_theme_code'] =  $this->request->get['mz_theme_code'];
        } elseif(strpos($this->config->get('theme_default_directory'), 'mz_') === 0) {
            $data['mz_theme_code'] =  $this->config->get('theme_default_directory');
        } elseif(!empty($data['mz_themes'])){ // select default theme in case maza theme is not selected
            $data['mz_theme_code'] =  $data['mz_themes'][0]['code'];
        } else{
            throw new Exception($this->language->get('error_theme_not_exist'));
        }
        
        $mz_theme_info = $this->model_extension_maza_theme->getThemeByCode($data['mz_theme_code']);
        $mz_theme_config = $this->model_extension_maza_theme->getThemeConfig($data['mz_theme_code']);
        
        // Check if theme is require to install or upgrade
        if(!$mz_theme_info || version_compare($mz_theme_config['version'], $mz_theme_info['version']) > 0){
            return $this->response->redirect($this->url->link('extension/maza/install/theme', 'mz_theme_code=' . $data['mz_theme_code'] . '&user_token=' . $this->session->data['user_token'], true));
        }
        
        // Get skins
        $data['mz_skins'] = $this->model_extension_maza_skin->getSkins($mz_theme_info['theme_id']);

        if(empty($data['mz_skins'])){
            throw new Exception($this->language->get('error_skin_not_exist'));
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $data['mz_skin_id'] = $this->request->get['mz_skin_id'];
        }  else {
            $theme_setting = $this->model_extension_maza_theme->getSetting($data['mz_theme_code'], $this->config->get('config_store_id'));

            if($theme_setting){
                $data['mz_skin_id'] = $theme_setting['skin_id'];
            } else {
                $data['mz_skin_id'] = $data['mz_skins'][0]['skin_id'];
            }
        }
        
        // Check skin id is valid or Select default skin of theme
        if(!in_array($data['mz_skin_id'], array_column($data['mz_skins'], 'skin_id'))){
            unset($this->request->get['mz_skin_id']);
            unset($_GET['mz_skin_id']);
            $data['mz_skin_id'] = $data['mz_skins'][0]['skin_id'];
        }
        
        // Add GET parameter in case of not exist
        $redirect = false;
        
        if(empty($this->request->get['mz_theme_code'])){
            $this->request->get['mz_theme_code'] = $data['mz_theme_code'];
            $redirect = true;
        }
        
        if(empty($this->request->get['mz_skin_id'])){
            $this->request->get['mz_skin_id'] = $data['mz_skin_id'];
            $redirect = true;
        }
        
        if($redirect){
            $route = $this->request->get['route'];
            unset($this->request->get['route']);
            $this->response->redirect($this->url->link($route, http_build_query($this->request->get)));
        }
        
        $data['route'] = $this->request->get['route'];
        $data['user_token'] = $this->session->data['user_token'];
        
        if(!isset($data['theme_select'])){
            $data['theme_select'] = true;
        }
        if(!isset($data['skin_select'])){
            $data['skin_select'] = true;
        }
        
        return $this->load->view('extension/maza/common/header', $data);
    }
    
    public function main() {
        return str_replace('view/javascript/bootstrap/js/bootstrap.js', 'view/javascript/maza/bootstrap/js/bootstrap.min.js', $this->load->controller('common/header'));
    }
}
