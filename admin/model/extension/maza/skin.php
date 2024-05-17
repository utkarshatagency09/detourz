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
         * add skin
         * @param array $data skin data
         * @return int skin id
         */
        public function addSkin($data) {
                $sql = "INSERT INTO " . DB_PREFIX . "mz_skin SET name = '" . $this->db->escape($data['name']) . "', theme_id = '" . (int)$data['theme_id'] . "', status = 1";
                
                if(!empty($data['code'])){
                    $sql .= ", skin_code = '" . $this->db->escape($data['code']) . "'" ;
                }
                
                if(isset($data['parent_skin_id'])){
                    $sql .= ", parent_skin_id = '" . (int)$data['parent_skin_id'] . "'";
                } else {
                    $sql .= ", parent_skin_id = '0'";
                }
                
                $this->db->query($sql);
                
                return $this->db->getLastId();
        }
        
        /**
         * edit skin
         * @param int $skin_id skin id
         * @param array $data skin data
         * @return void
         */
        public function editSkin($skin_id, $data) {
                $this->db->query("UPDATE " . DB_PREFIX . "mz_skin SET skin_code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', theme_id = '" . (int)$data['theme_id'] . "', parent_skin_id = '" . (isset($data['parent_skin_id'])?(int)$data['parent_skin_id']:0) . "', status = 1 WHERE skin_id = '" . (int)$skin_id . "'");
        }
        
        /**
         * alter partial data of skin
         * @param int $skin_id skin id
         * @param array $data skin data
         * @return void
         */
        public function alterSkin($skin_id, $data) {
                $set = array();
                
                if(isset($data['name'])){
                    $set[] = "name = '" . $this->db->escape($data['name']) . "'";
                }
                
                $this->db->query("UPDATE " . DB_PREFIX . "mz_skin SET " . implode(', ', $set) . " WHERE skin_id = '" . (int)$skin_id . "'");
        }
        
        /**
         * delete skin
         * @param int $skin_id skin id
         * @return void
         */
        public function deleteSkin($skin_id) {
                // Delete child skin
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_skin WHERE parent_skin_id = '" . (int)$skin_id . "'");
                
                foreach ($query->rows as $skin_info) {
                    $args[] = $skin_info['skin_id'];
                    $this->event->trigger('model/extension/maza/skin/deleteSkin/before', array('extension/maza/skin', &$args));
                    $this->deleteSkin($skin_info['skin_id']);
                    $this->event->trigger('model/extension/maza/skin/deleteSkin/after', array('extension/maza/skin', &$args));
                }
            
                // Delete skin setting
                $this->deleteAllSetting($skin_id);
                
                // Delete layout builder data of skin
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_layout_entry WHERE skin_id = '" . (int)$skin_id . "'");
                
                // Delete module setting
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_module_setting WHERE skin_id = '" . (int)$skin_id . "'");
                
                // Delete skin
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_skin WHERE skin_id = '" . (int)$skin_id . "' AND parent_skin_id <> 0");
                
                // Reset layout overwritten skin
                $this->db->query("UPDATE `" . DB_PREFIX . "layout` SET mz_override_skin_id = 0 WHERE mz_override_skin_id = '" . (int)$skin_id . "'");

                // Page override reset
                $this->db->query("UPDATE `" . DB_PREFIX . "mz_page` SET override_skin_id = 0 WHERE override_skin_id = '" . (int)$skin_id . "'");
        }
        
        /**
         * Get list of installed skin
         * @param int $theme_id theme id
         * @param int $parent_skin_id $parent_skin_id
         * @param int $status skin status
         * @return array Skin list
         */
        public function getSkins($theme_id = null, $parent_skin_id = null) {
                $sql = "SELECT * FROM " . DB_PREFIX . "mz_skin WHERE 1";
                
                if($theme_id){
                    $sql .= " AND theme_id = '" . (int)$theme_id . "'";
                }
                
                if($parent_skin_id){
                    $sql .= " AND parent_skin_id = '" . (int)$parent_skin_id . "'";
                }
                
                $sql .= " ORDER BY skin_code, name";
                
                $query = $this->db->query($sql);
                
                return $query->rows;
        }
        
        /**
         * Get installed skin detail by code
         * @param string $theme_code unique theme code
         * @param string $skin_code unique skin code
         * @return array skin detail
         */
        public function getSkinByCode($theme_code, $skin_code) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_skin s LEFT JOIN " . DB_PREFIX . "mz_theme t ON s.theme_id = t.theme_id WHERE t.theme_code = '" . $this->db->escape($theme_code) . "' AND s.skin_code = '" . $this->db->escape($skin_code) . "' AND s.parent_skin_id = 0 LIMIT 1");
                return $query->row;
        }
        
        /**
         * Get config detail pf skin by code
         * @param string $theme_code unique theme code
         * @param string $skin_code unique skin code
         * @return array skin config detail
         */
        public function getSkinConfig($theme_code, $skin_code) {
                if(file_exists(DIR_CATALOG . 'view/theme/' . $theme_code . '/skins/content/' . $skin_code . '/config.json')){
                    return json_decode(file_get_contents(DIR_CATALOG . 'view/theme/' . $theme_code . '/skins/content/' . $skin_code . '/config.json'), true);
                } else {
                    return array();
                }
        }
        
        /**
         * Get installed skin detail by id
         * @param int $skin_id unique skin id
         * @return array Skin detail
         */
        public function getSkin($skin_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_skin WHERE skin_id = '" . (int)$skin_id . "' LIMIT 1");
                return $query->row;
        }
        
        
        /**
         * Get setting of skin by code
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
         * edit setting of skin by code
         * @param int $skin_id skin id
         * @param string $code setting code(prefix of key)
         * @param array $data setting data(key=>value) pair
         * @return void
         */
	public function editSetting($skin_id, $code, $data) {
                $this->deleteSetting($skin_id, $code);

		foreach ($data as $key => $value) {
			if (substr($key, 0, strlen($code)) == $code) {
				if (!is_array($value)) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "mz_skin_setting SET skin_id = '" . (int)$skin_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "mz_skin_setting SET skin_id = '" . (int)$skin_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value, true)) . "', is_serialized = '1'");
				}
			}
		}
	}
        
        /**
         * Delete skin setting
         * @param int $skin_id skin id
         * @param string $code setting code(prefix of key)
         * @return void
         */
	public function deleteSetting($skin_id, $code) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_skin_setting WHERE skin_id = '" . (int)$skin_id . "' AND `code` = '" . $this->db->escape($code) . "'");
	}
        
        /**
         * Delete skin all setting
         * @param int $skin_id skin id
         * @return void
         */
	public function deleteAllSetting($skin_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_skin_setting WHERE skin_id = '" . (int)$skin_id . "'");
	}
	
        /**
         * Get specific key value in skin setting
         * @param int $skin_id skin id
         * @param string $key setting key
         * @return mixed
         */
	public function getSettingValue($skin_id, $key) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "mz_skin_setting WHERE skin_id = '" . (int)$skin_id . "' AND `key` = '" . $this->db->escape($key) . "'");

		if ($query->num_rows) {
			return $query->row['value'];
		} else {
			return null;	
		}
	}
	
        /**
         * edit value of specific key in skin setting
         * @param int $skin_id skin id
         * @param string $key setting key
         * @param mixed $value
         * @return mixed
         */
	public function editSettingValue($skin_id, $key, $value = '') {
		if (!is_array($value)) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_skin_setting SET `value` = '" . $this->db->escape($value) . "', is_serialized = '0'  WHERE `key` = '" . $this->db->escape($key) . "' AND skin_id = '" . (int)$skin_id . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_skin_setting SET `value` = '" . $this->db->escape(json_encode($value)) . "', is_serialized = '1' WHERE `key` = '" . $this->db->escape($key) . "' AND skin_id = '" . (int)$skin_id . "'");
		}
	}
        
        /**
         * Duplicate settings from one skin to another
         * @param int $from_skin_id duplicate from
         * @param int $to_skin_id duplicate to
         * @return void
         */
        public function duplicateSetting($from_skin_id, $to_skin_id) {
                // Skin setting
                $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_skin_setting` (`skin_id`, `code`, `key`, `value`, `is_serialized`) SELECT " . (int)$to_skin_id . ", `code`, `key`, `value`, `is_serialized` FROM `" . DB_PREFIX . "mz_skin_setting` WHERE skin_id = '" . (int)$from_skin_id . "'");
        }
        
}
