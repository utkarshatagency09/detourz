<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewCommonFooter extends Controller {
    public function before(string $route, array &$data): void {
        // custom code before body tag
        $data['code_before_body_tag'] = '';
        
        // Global level code
        if($this->config->get('mz_code_footer_global_status')){
            if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/global.html')){
                $data['code_before_body_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/global.html') . PHP_EOL;
            }
        }
        
        // theme level code
        if($this->mz_theme_config->get('code_footer_theme_status')){
            if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/' . $this->mz_theme_config->get('theme_code') . '.html')){
                $data['code_before_body_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/' . $this->mz_theme_config->get('theme_code') . '.html') . PHP_EOL;
            }
        }
        
        // skin level code
        if($this->mz_skin_config->get('code_footer_skin_status')){
            if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.html')){
                $data['code_before_body_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.html') . PHP_EOL;
            }
        }
        
        /** Footer layout content */
        $data['footer_content'] = $this->mz_load->view($this->load->controller('extension/maza/layout_builder', ['group' => 'footer', 'group_owner' => $this->mz_skin_config->get('skin_footer_id')]), $data);

        // CSS
        if(version_compare(VERSION, '3.0.3.7') >= 0){
            $data['styles'] = $this->mz_document->getStyles('footer', $this->config->get('maza_minify_css'), $this->config->get('maza_combine_css'));
        }
        unset($data['styles']['catalog/view/javascript/jquery/swiper/css/opencart.css']);

        // javascript
        if($this->config->get('maza_js_position') == 'footer'){
            $data['scripts'] = $this->mz_document->getScripts('all', $this->config->get('maza_minify_js'), $this->config->get('maza_combine_js'));
        } elseif($this->config->get('maza_js_position') == 'default'){
            $data['scripts'] = $this->mz_document->getScripts('footer', $this->config->get('maza_minify_js'), $this->config->get('maza_combine_js'));
        } else {
            $data['scripts'] = array();
        }
        unset($data['scripts']['catalog/view/javascript/jquery/swiper/js/swiper.jquery.js']);
        
        // SVG data
        $data['svg_data'] = $this->mz_document->getSVGData();

        // Schemas
        if ($this->config->get('maza_schema')) {
            $data['schemas'] = $this->mz_schema->output();
        }
        
        // Language
        $data['language'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);
        
        // Currency
        $data['currency'] = $this->url->link('common/currency/currency', '', $this->request->server['HTTPS']);
        
        // Back to Top
        $data['back_to_top'] = $this->mz_skin_config->get('style_back_to_top_status');

        // Push notification
        if ($this->config->get('maza_notification_push')) {
            $data['push'] = $this->load->controller('extension/maza/common/push');
        }
    }
}
