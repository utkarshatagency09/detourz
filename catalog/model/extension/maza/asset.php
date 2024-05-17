<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaAsset extends model {
        
        /**
         * Get font detail
         * @param int $font_id font id
         * @return array font detail
         */
        public function getFont($font_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_fonts WHERE font_id = '" . (int)$font_id . "'");
                
                return $query->row;
        }
        
        
        /**
         * Get list of background overlay pattern
         * @param string $code pattern code
         * @return array pattern list
         */
        public function overlayPatterns($code = null) {
                if(file_exists(MZ_CONFIG::$DIR_THEME_CONFIG . 'overlay_pattern.php')){
                    $_ = array();

                    require(MZ_CONFIG::$DIR_THEME_CONFIG . 'overlay_pattern.php');
                    
                    if($code && isset($_[$code])){
                        return $_[$code];
                    } elseif($code) {
                        return array();
                    } else {
                        return $_;
                    }
                } else {
                    return array();
                }
        }
        
        /**
         * Get list of color palette
         * @param string $code color palette code
         * @return array color palette list
         */
        public function getColorPalettes($code = null) {
                $palettes = array();
                
                if(file_exists(MZ_CONFIG::$DIR_THEME_CONFIG . 'color_schemes.php')){
                    $_ = array();
                    require(MZ_CONFIG::$DIR_THEME_CONFIG . 'color_schemes.php');
                    
                    if(isset($_['palette'])){
                        $palettes = $_['palette'];
                    }
                }
                
                if($code && isset($palettes[$code])){
                    return $palettes[$code]['color'];
                } elseif($code) {
                    return $palettes['default']['color'];
                } else {
                    return $palettes;
                }
        }
        
        /**
         * Get list of color types
         * @return array color types list
         */
        public function getColorTypes() {
                $types = array();
                
                if(file_exists(MZ_CONFIG::$DIR_THEME_CONFIG . 'color_schemes.php')){
                    $_ = array();
                    require(MZ_CONFIG::$DIR_THEME_CONFIG . 'color_schemes.php');
                    
                    if(isset($_['type'])){
                        $types = $_['type'];
                    }
                }
                
                return $types;
        }
        
        public function getCustomCSS() {
                $css = '';
                
                // Global level css
                $css .= '/* Custom global lavel css*/';
                if($this->config->get('mz_code_css_global_status')){
                    if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'css/global.css')){
                        $css .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'css/global.css') . PHP_EOL;
                    }
                }
                
                // theme level css
                $css .= '/* Custom theme lavel css*/';
                if($this->mz_theme_config->get('code_css_theme_status')){
                    if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'css/' . $this->mz_theme_config->get('theme_code') . '.css')){
                        $css .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'css/' . $this->mz_theme_config->get('theme_code') . '.css') . PHP_EOL;
                    }
                }
                
                // skin level css
                $css .= '/* Custom skin lavel css*/';
                if($this->mz_skin_config->get('code_css_skin_status')){
                    if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'css/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.css')){
                        $css .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'css/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.css') . PHP_EOL;
                    }
                }
                
                return $css;
        }
        
        public function getCustomJavacript() {
                $javascript = '';
                
                // Global level javascript
                $javascript .= '/* Custom global lavel javascript*/';
                if($this->config->get('mz_code_javascript_global_status')){
                    if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'js/global.js')){
                        $javascript .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'js/global.js') . PHP_EOL;
                    }
                }
                
                // theme level javascript
                $javascript .= '/* Custom theme lavel javascript*/';
                if($this->mz_theme_config->get('code_javascript_theme_status')){
                    if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'js/' . $this->mz_theme_config->get('theme_code') . '.js')){
                        $javascript .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'js/' . $this->mz_theme_config->get('theme_code') . '.js') . PHP_EOL;
                    }
                }
                
                // skin level javascript
                $javascript .= '/* Custom skin lavel javascript*/';
                if($this->mz_skin_config->get('code_javascript_skin_status')){
                    if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'js/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.js')){
                        $javascript .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'js/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.js') . PHP_EOL;
                    }
                }
                
                return $javascript;
        }
        
         /**
         * Get list of available font icon packages
         * @return array package detail list
         */
        public function getFontIconPackages() {
                $data = array();
                
                $package_files = glob(MZ_CONFIG::$DIR_THEME_ASSET . 'font_icon/*.php');
                    
                foreach ($package_files as $file) {
                    $_ = array();

                    require_once $file;

                    $data[] = array(
                        'name' => $_['name'],
                        'status' => $_['status'],
                        'code' => basename($file, '.php'),
                        'type' => 'package',
                        'css_file' => $_['css_file']
                    );
                }
                
                return $data;
        }
}
