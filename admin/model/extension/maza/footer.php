<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaFooter extends model {
        /**
         * add footer
         * @param array $data footer data
         * @return int footer id
         */
        public function addFooter($data) {
                $sql = "INSERT INTO " . DB_PREFIX . "mz_footer SET name = '" . $this->db->escape($data['name']) . "', theme_id = '" . (int)$data['theme_id'] . "', status = 1";
                
                if(!empty($data['code'])){
                    $sql .= ", code = '" . $this->db->escape($data['code']) . "'" ;
                }
                
                if(isset($data['parent_footer_id'])){
                    $sql .= ", parent_footer_id = '" . (int)$data['parent_footer_id'] . "'";
                } else {
                    $sql .= ", parent_footer_id = '0'";
                }
                
                $this->db->query($sql);
                
                return $this->db->getLastId();
        }
        
        /**
         * edit footer
         * @param int $footer_id footer id
         * @param array $data footer data
         * @return void
         */
        public function editFooter($footer_id, $data) {
                $this->db->query("UPDATE " . DB_PREFIX . "mz_footer SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', theme_id = '" . (int)$data['theme_id'] . "', parent_footer_id = '" . (isset($data['parent_footer_id'])?(int)$data['parent_footer_id']:0) . "', status = 1 WHERE footer_id = '" . (int)$footer_id . "'");
        }
        
        /**
         * alter partial data of footer
         * @param int $footer_id footer id
         * @param array $data footer data
         * @return void
         */
        public function alterFooter($footer_id, $data) {
                $set = array();
                
                if(isset($data['name'])){
                    $set[] = "name = '" . $this->db->escape($data['name']) . "'";
                }
                
                $this->db->query("UPDATE " . DB_PREFIX . "mz_footer SET " . implode(', ', $set) . " WHERE footer_id = '" . (int)$footer_id . "'");
        }
        
        /**
         * delete footer
         * @param int $footer_id footer id
         * @return void
         */
        public function deleteFooter($footer_id) {
                $this->load->model('extension/maza/layout_builder');
                
                // Delete child footer
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_footer WHERE parent_footer_id = '" . (int)$footer_id . "'");
                
                foreach ($query->rows as $footer_info) {
                    $args[] = $footer_info['footer_id'];
                    $this->event->trigger('model/extension/maza/footer/deleteFooter/before', array('extension/maza/footer', &$args));
                    $this->deleteFooter($footer_info['footer_id']);
                    $this->event->trigger('model/extension/maza/footer/deleteFooter/after', array('extension/maza/footer', &$args));
                }
                
                // Delete footer and footer data
                $this->model_extension_maza_layout_builder->deleteLayout('footer', $footer_id);
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_footer WHERE parent_footer_id = '" . (int)$footer_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_footer WHERE footer_id = '" . (int)$footer_id . "'");
        }
        
        /**
         * Get list of installed footer
         * @param int $theme_id theme id
         * @param int $parent_footer_id $parent_footer_id
         * @param int $status status of header
         * @return array footer list
         */
        public function getFooters($theme_id, $parent_footer_id = null) {
                $sql = "SELECT * FROM " . DB_PREFIX . "mz_footer WHERE theme_id = '" . (int)$theme_id . "'";
                
                if($parent_footer_id){
                    $sql .= " AND parent_footer_id = '" . (int)$parent_footer_id . "'";
                }
                
                $sql .= " ORDER BY code, name";
                
                $query = $this->db->query($sql);
                
                return $query->rows;
        }
        
        /**
         * Get installed footer detail by code
         * @param string $theme_code unique theme code
         * @param string $code unique footer code
         * @return array footer detail
         */
        public function getFooterByCode($theme_code, $code) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_footer f LEFT JOIN " . DB_PREFIX . "mz_theme t ON f.theme_id = t.theme_id WHERE t.theme_code = '" . $this->db->escape($theme_code) . "' AND f.code = '" . $this->db->escape($code) . "' AND f.parent_footer_id = '0' LIMIT 1");
                return $query->row;
        }
        
        /**
         * Get config detail of footer by code
         * @param string $theme_code unique theme code
         * @param string $code unique footer code
         * @return array config of footer
         */
        public function getFooterConfig($theme_code, $code) {
                if(file_exists(DIR_CATALOG . 'view/theme/' . $theme_code . '/skins/footer/' . $code . '/config.json')){
                    return json_decode(file_get_contents(DIR_CATALOG . 'view/theme/' . $theme_code . '/skins/footer/' . $code . '/config.json'), true);
                } else {
                    return array();
                }
        }
        
        /**
         * Get installed footer detail by id
         * @param int $footer_id unique footer id
         * @return array footer detail
         */
        public function getFooter($footer_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_footer WHERE footer_id = '" . (int)$footer_id . "' LIMIT 1");
                return $query->row;
        }
}
