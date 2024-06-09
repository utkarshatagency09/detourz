<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventExtensionModuleMzTags extends Controller {
        
        /**
         * Run code before product search view
         * @param string $route route
         * @param array $param parameter of method
         * @return void
         */
        public function productSearchView($route, $data) {
            if($this->config->get('maza_status')){
                $this->load->model('extension/module/mz_tags');
                
                if (isset($this->request->get['tag'])) {
                    $this->model_extension_module_mz_tags->updateProductTagViewed($this->request->get['tag']);
                }
            }
        }
        
        /**
         * Run code before product search view
         * @param string $route route
         * @param array $param parameter of method
         * @return void
         */
        public function blogSearchView($route, $data) {
            if($this->config->get('maza_status')){
                $this->load->model('extension/module/mz_tags');
                
                if (isset($this->request->get['tag'])) {
                    $this->model_extension_module_mz_tags->updateBlogTagViewed($this->request->get['tag']);
                }
            }
        }
        
}
