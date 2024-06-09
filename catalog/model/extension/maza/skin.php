<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaSkin extends model {
        /**
         * Get installed skin detail by id
         * @param int $skin_id unique skin id
         */
        public function getSkin($skin_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_skin WHERE skin_id = '" . (int)$skin_id . "'");
                return $query->row;
        }
        
        /**
         * Get setting of skin
         * @param int $skin_id skin id
         * @param string $code setting code(prefix of key)
         * @return array settings
         */
        public function getSetting($skin_id, $code = null) {
		$setting_data = array();
                
                $sql = "SELECT * FROM " . DB_PREFIX . "mz_skin_setting WHERE skin_id = '" . (int)$skin_id . "'";
                
                if($code){
                    $sql .= " AND `code` = '" . $this->db->escape($code) . "'";
                }
                
		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			if (!$result['is_serialized']) {
				$setting_data[$result['key']] = $result['value'];
			} else {
				$setting_data[$result['key']] = json_decode($result['value'], true);
			}
		}

		return $setting_data;
	}
        
        
        /**
         * Get installed header detail by id
         * @param int $header_id unique header id
         */
        public function getHeader($header_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_header WHERE header_id = '" . (int)$header_id . "' LIMIT 1");
                return $query->row;
        }
        
        /**
         * Get installed footer detail by id
         * @param int $footer_id unique footer id
         */
        public function getFooter($footer_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_footer WHERE footer_id = '" . (int)$footer_id . "' LIMIT 1");
                return $query->row;
        }
}
