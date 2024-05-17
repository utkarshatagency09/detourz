<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaCode extends model {
        /**
         * Duplicate custom code file of CSS, JS and Header, Footer code
         */
        public function duplicateCode($from_skin_id, $to_skin_id){
                $css_skin_file = DIR_CATALOG . 'view/javascript/maza/custom_code/css/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $from_skin_id . '.css';
                $css_new_skin_file = DIR_CATALOG . 'view/javascript/maza/custom_code/css/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $to_skin_id . '.css';
                if(is_file($css_skin_file)){
                    copy($css_skin_file, $css_new_skin_file);
                }

                $javascript_skin_file =   DIR_CATALOG . 'view/javascript/maza/custom_code/js/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $from_skin_id . '.js';
                $javascript_new_skin_file =   DIR_CATALOG . 'view/javascript/maza/custom_code/js/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $to_skin_id . '.js';
                if(is_file($javascript_skin_file)){
                    copy($javascript_skin_file, $javascript_new_skin_file);
                }

                $header_skin_file =   DIR_CATALOG . 'view/javascript/maza/custom_code/header/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $from_skin_id . '.html';
                $header_new_skin_file =   DIR_CATALOG . 'view/javascript/maza/custom_code/header/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $to_skin_id . '.html';
                if(is_file($header_skin_file)){
                    copy($header_skin_file, $header_new_skin_file);
                }

                $footer_skin_file =   DIR_CATALOG . 'view/javascript/maza/custom_code/footer/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $from_skin_id . '.html';
                $footer_new_skin_file =   DIR_CATALOG . 'view/javascript/maza/custom_code/footer/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $to_skin_id . '.html';
                if(is_file($footer_skin_file)){
                    copy($footer_skin_file, $footer_new_skin_file);
                }
        }
}
