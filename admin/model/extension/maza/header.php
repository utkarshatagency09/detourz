<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaHeader extends model {
        /**
         * add header
         * @param array $data header data
         * @return int header id
         */
        public function addHeader($data) {
                $sql = "INSERT INTO " . DB_PREFIX . "mz_header SET name = '" . $this->db->escape($data['name']) . "', theme_id = '" . (int)$data['theme_id'] . "', status = 1";
                
                if(!empty($data['code'])){
                    $sql .= ", code = '" . $this->db->escape($data['code']) . "'" ;
                }
                
                if(isset($data['parent_header_id'])){
                    $sql .= ", parent_header_id = '" . (int)$data['parent_header_id'] . "'";
                } else {
                    $sql .= ", parent_header_id = '0'";
                }
                
                $this->db->query($sql);
                
                return $this->db->getLastId();
        }
        
        /**
         * edit header
         * @param int $header_id header id
         * @param array $data header data
         * @return void
         */
        public function editHeader($header_id, $data) {
                $this->db->query("UPDATE " . DB_PREFIX . "mz_header SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', theme_id = '" . (int)$data['theme_id'] . "', parent_header_id = '" . (isset($data['parent_header_id'])?(int)$data['parent_header_id']:0) . "', status = 1 WHERE header_id = '" . (int)$header_id . "'");
        }
        
        /**
         * alter partial data of header
         * @param int $header_id header id
         * @param array $data header data
         * @return void
         */
        public function alterHeader($header_id, $data) {
                $set = array();
                
                if(isset($data['name'])){
                    $set[] = "name = '" . $this->db->escape($data['name']) . "'";
                }
                
                $this->db->query("UPDATE " . DB_PREFIX . "mz_header SET " . implode(', ', $set) . " WHERE header_id = '" . (int)$header_id . "'");
        }
        
        /**
         * delete header
         * @param int $header_id header id
         * @return void
         */
        public function deleteHeader($header_id) {
                $this->load->model('extension/maza/layout_builder');
            
                // Delete child header
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_header WHERE parent_header_id = '" . (int)$header_id . "'");
                
                foreach ($query->rows as $header_info) {
                    $args[] = $header_info['header_id'];
                    $this->event->trigger('model/extension/maza/header/deleteHeader/before', array('extension/maza/header', &$args));
                    $this->deleteHeader($header_info['header_id']);
                    $this->event->trigger('model/extension/maza/header/deleteHeader/after', array('extension/maza/header', &$args));
                }
                
                // Delete header and header data
                $this->model_extension_maza_layout_builder->deleteLayout('top_header', $header_id);
                $this->model_extension_maza_layout_builder->deleteLayout('main_header', $header_id);
                $this->model_extension_maza_layout_builder->deleteLayout('main_navigation', $header_id);
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_header WHERE header_id = '" . (int)$header_id . "'");
                
        }
        
        /**
         * Get list of installed header
         * @param int $theme_id theme id
         * @param int $parent_header_id $parent_header_id
         * @param int $status status of header
         * @return array List of header
         */
        public function getHeaders($theme_id, $parent_header_id = null) {
                $sql = "SELECT * FROM " . DB_PREFIX . "mz_header WHERE theme_id = '" . (int)$theme_id . "'";
                
                if($parent_header_id){
                    $sql .= " AND parent_header_id = '" . (int)$parent_header_id . "'";
                }
                
                $sql .= " ORDER BY code, name";
                
                $query = $this->db->query($sql);
                
                return $query->rows;
        }
        
        /**
         * Get installed header detail by code
         * @param string $theme_code unique theme code
         * @param string $code unique header code
         * @return array header detail
         */
        public function getHeaderByCode($theme_code, $code) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_header h LEFT JOIN " . DB_PREFIX . "mz_theme t ON h.theme_id = t.theme_id WHERE t.theme_code = '" . $this->db->escape($theme_code) . "' AND h.code = '" . $this->db->escape($code) . "' AND h.parent_header_id = '0' LIMIT 1");
                return $query->row;
        }
        
        /**
         * Get config detail pf header by code
         * @param string $theme_code unique theme code
         * @param string $code unique header code
         * @return array header config detail
         */
        public function getHeaderConfig($theme_code, $code) {
                if(file_exists(DIR_CATALOG . 'view/theme/' . $theme_code . '/skins/header/' . $code . '/config.json')){
                    return json_decode(file_get_contents(DIR_CATALOG . 'view/theme/' . $theme_code . '/skins/header/' . $code . '/config.json'), true);
                } else {
                    return array();
                }
        }
        
        /**
         * Get installed header detail by id
         * @param int $header_id unique header id
         * @return array Header detail
         */
        public function getHeader($header_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_header WHERE header_id = '" . (int)$header_id . "' LIMIT 1");
                return $query->row;
        }
        
}
