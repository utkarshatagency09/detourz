<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaAsset extends model {
        /**
         * add google Font
         * @param array $data font data
         * @return int font id
         */
        public function addFont($data) {
                $sql = "INSERT INTO " . DB_PREFIX . "mz_fonts SET `type` = '" . $this->db->escape($data['type']) . "', name = '" . $this->db->escape($data['name']) . "', font_family = '" . $this->db->escape($data['font_family']) . "'";
                
                if(!empty($data['url'])){
                    $sql .= ", url = '" . $this->db->escape($data['url']) . "'";
                }
                
                if(!empty($data['parent_font_id'])){
                    $sql .= ", parent_font_id = '" . (int)$data['parent_font_id'] . "'";
                }
                
                $this->db->query($sql);
                
                return $this->db->getLastId();
        }
        
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
         * Get font detail by family
         * @param string $font_family font family
         * @return array font detail
         */
        public function getFontByFamily($font_family) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_fonts WHERE font_family = '" . $this->db->escape($font_family) . "'");
                
                return $query->row;
        }
        
        /**
         * Get list of fonts
         * @param string $type font type
         * @return array list of fonts
         */
        public function getFonts($type = null) {
                $sql = "SELECT * FROM " . DB_PREFIX . "mz_fonts WHERE 1";
                
                if($type){
                    $sql .= " AND `type` = '" . $this->db->escape($type) . "'";
                }
                
                $query = $this->db->query($sql);
                
                return $query->rows;
        }
        
        /**
         * Delete google font
         * @param int $font_id unique font id
         */
        public function deleteFont($font_id) {
                return $this->db->query("DELETE FROM " . DB_PREFIX . "mz_fonts WHERE font_id = '" . (int)$font_id . "'");
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
        
        /**
         * @deprecated
         *  Clear asset files
         */
//        public function clear() {
//                // Delete main css file
//                if(file_exists(MZ_CONFIG::$DIR_CSS_CACHE . 'main.css')){
//                    unlink(MZ_CONFIG::$DIR_CSS_CACHE . 'main.css');
//                }
//                // Delete main js file
//                if(file_exists(MZ_CONFIG::$DIR_JS_CACHE . 'main.js')){
//                    unlink(MZ_CONFIG::$DIR_JS_CACHE . 'main.js');
//                }
//                // Delete route css and js file
//                $files = glob(MZ_CONFIG::$DIR_CSS_CACHE . 'route/*.{css|js}');
//                
//                foreach($files as $file){
//                    unlink($file);
//                }
//        }
        
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
                        'code' => basename($file, '.php'),
                        'type' => 'package',
                        'file' => $_['font_manager_css'],
                    );
                }
                
                return $data;
        }
}
