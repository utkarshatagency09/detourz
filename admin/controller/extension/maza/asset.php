<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaAsset extends Controller {
    
        private $error = array();


        public function google_font() {
                $json = array();
                
                $this->load->language('extension/maza/asset');
                $this->load->model('extension/maza/asset');
                
                if($this->request->get['action'] == 'add' && $this->request->server['REQUEST_METHOD'] == 'POST' && $this->hasPermission() && $this->validateGoogleFont()){
                        $this->request->post['type'] = 'google';
                        $json['font_id'] = $this->model_extension_maza_asset->addFont($this->request->post);
                        $json['success'] = sprintf($this->language->get('text_success'), $this->language->get('text_font'));
                } elseif($this->request->get['action'] == 'delete' && $this->hasPermission()){
                        $this->model_extension_maza_asset->deleteFont($this->request->get['font_id']);
                        $json['success'] = sprintf($this->language->get('text_success'), $this->language->get('text_font'));
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = sprintf($this->error['warning'], $this->language->get('text_font'));
                }
                
                $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
        }
        
        protected function validateGoogleFont(){
                if(empty($this->request->post['name']) || empty($this->request->post['font_family']) || empty($this->request->post['url'])){
                        $this->error['warning'] = $this->language->get('error_missing_require_field');
                }
                
                if(!isset($this->error['warning'])){
                    $font_info = $this->model_extension_maza_asset->getFontByFamily($this->request->post['font_family']);
                    
                    if($font_info){
                        $this->error['warning'] = sprintf($this->language->get('error_duplicate_font_family'), $font_info['name']);
                    }
                }
                
                return !$this->error;
        }

        protected function hasPermission() {
		if (!$this->user->hasPermission('modify', 'extension/maza/asset')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
