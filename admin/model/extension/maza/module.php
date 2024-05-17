<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaModule extends model {
        /**
         * Add module and setting by skin id
         * @param int $code module id
         * @param int $skin_id skin id
         * @param array $data setting of module
         * @return int $module_id module id
         */
        public function addModule($code, $skin_id, $data) {
                // add global name of module
                $this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data['name']) . "', `code` = '" . $this->db->escape($code) . "'");
                
                $module_id = $this->db->getLastId();
                
                $this->db->query("UPDATE `" . DB_PREFIX . "module` SET `setting` = '" . $this->db->escape(json_encode(['status' => true, 'name' => $data['name'], 'module_id' => (int)$module_id])) . "' WHERE `module_id` = '" . (int)$module_id . "'");
                
                // add setting of module by skin id
		$this->db->query("INSERT INTO `" . DB_PREFIX . "mz_module_setting` SET `module_id` = '" . (int)$module_id . "', `skin_id` = '" . (int)$skin_id . "', `setting` = '" . $this->db->escape(json_encode($data)) . "'");
                
                return $module_id;
	}
	
        /**
         * edit setting of module by skin id
         * @param int $module_id module id
         * @param int $skin_id skin id
         * @param array $data setting of module
         * @return void
         */
	public function editModule($module_id, $skin_id, $data) {
                // Update global name of module
                $this->db->query("UPDATE `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data['name']) . "', `setting` = '" . $this->db->escape(json_encode(['status' => true, 'name' => $data['name'], 'module_id' => $module_id])) . "' WHERE `module_id` = '" . (int)$module_id . "'");
                
                // check module setting is avaialbe in skin or not
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_module_setting` WHERE `module_id` = '" . (int)$module_id . "' AND skin_id = '" . (int)$skin_id . "'");
                
                if($query->num_rows){
                    $this->db->query("UPDATE `" . DB_PREFIX . "mz_module_setting` SET `setting` = '" . $this->db->escape(json_encode($data)) . "' WHERE `module_id` = '" . (int)$module_id . "' AND `skin_id` = '" . (int)$skin_id . "'");
                } else {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_module_setting` SET `module_id` = '" . (int)$module_id . "', `skin_id` = '" . (int)$skin_id . "', `setting` = '" . $this->db->escape(json_encode($data)) . "'");
                }
                
                $this->mz_cache->clear();
	}
        
        /**
         * Get module setting
         * @param int $module_id module id
         * @param int $skin_id skin id
         * @return array
         */
        public function getSetting($module_id, $skin_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_module_setting` WHERE `module_id` = '" . (int)$module_id . "' AND skin_id = '" . (int)$skin_id . "'");

		if ($query->row) {
			return json_decode($query->row['setting'], true);
		} else {
			return array();
		}
	}
        
        /**
         * Duplicate settings from one skin to another
         * @param int $from_skin_id duplicate from
         * @param int $to_skin_id duplicate to
         * @return void
         */
        public function duplicateSetting($from_skin_id, $to_skin_id) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_module_setting` (`skin_id`, `module_id`, `setting`) SELECT " . (int)$to_skin_id . ", `module_id`, `setting` FROM `" . DB_PREFIX . "mz_module_setting` WHERE skin_id = '" . (int)$from_skin_id . "'");
        }
}
