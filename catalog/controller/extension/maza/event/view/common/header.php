<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewCommonHeader extends Controller {
    public function before($route, &$data) {
        // Load header content
        $data['top_header'] = $this->mz_load->view($this->mz_cache->getVar('top_header'), $data);
        $data['main_header'] = $this->mz_load->view($this->mz_cache->getVar('main_header'), $data);
        $data['main_navigation'] = $this->mz_load->view($this->mz_cache->getVar('main_navigation'), $data);
        $data['header_component'] = $this->mz_load->view($this->mz_cache->getVar('header_component'), $data);
        
        // Page component
        $data['page_component'] = $this->mz_cache->getVar('page_component');
        
        // Preloader
        $data['preloader_status'] = $this->mz_skin_config->get('style_page_loader_status');
        
        $style_loader_spinner_image = maza\getOfLanguage($this->mz_skin_config->get('style_loader_spinner_image'));
        if($style_loader_spinner_image && is_file(DIR_IMAGE . $style_loader_spinner_image)){
            $data['spinner_url'] = $this->config->get('mz_store_url') . 'image/' . $style_loader_spinner_image;
        } else {
            $data['spinner_url'] = $this->config->get('mz_store_url') . 'image/no_image.png';
        }
        $data['spinner_type'] = pathinfo($data['spinner_url'], PATHINFO_EXTENSION);
        
        # custom code between head tag #
        $data['code_in_head_tag'] = '';
        
        // Global level code
        if($this->config->get('mz_code_header_global_status')){
            if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/global.html')){
                $data['code_in_head_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/global.html') . PHP_EOL;
            }
        }
        
        // theme level code
        if($this->mz_theme_config->get('code_header_theme_status')){
            if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/' . $this->mz_theme_config->get('theme_code') . '.html')){
                $data['code_in_head_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/' . $this->mz_theme_config->get('theme_code') . '.html') . PHP_EOL;
            }
        }
        
        // skin level code
        if($this->mz_skin_config->get('code_header_skin_status')){
            if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.html')){
                $data['code_in_head_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.html') . PHP_EOL;
            }
        }
        
        // Stylesheet file
        $data['styles'] = $this->mz_document->getStyles('header', $this->config->get('maza_minify_css'), $this->config->get('maza_combine_css'));
        
        // javascript file
        if($this->config->get('maza_js_position') == 'header'){
            $data['scripts'] = $this->mz_document->getScripts('all', $this->config->get('maza_minify_js'), $this->config->get('maza_combine_js'));
        } elseif($this->config->get('maza_js_position') == 'default'){
            $data['scripts'] = $this->mz_document->getScripts('header', $this->config->get('maza_minify_js'), $this->config->get('maza_combine_js'));
        } else {
            $data['scripts'] = array();
        }
        
        // Remove or replace conflicted stylesheet and js
        unset($data['styles']['catalog/view/javascript/jquery/swiper/css/opencart.css']);
        unset($data['scripts']['catalog/view/javascript/jquery/swiper/js/swiper.jquery.js']);
        
        $data['mz_cdn'] = $this->config->get('maza_cdn');

        // Meta data
        $data['metadata'] = $this->mz_document->getMetadata();

        $data['page_class'] = str_replace('/', '-', $this->mz_document->getRoute());

        // Admin info
        if (isset($this->session->data['user_id'])) {
            $data['admin_info'] = $this->load->controller('extension/maza/tool/admin');
        }
    }
}
