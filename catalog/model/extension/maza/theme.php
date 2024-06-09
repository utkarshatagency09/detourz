<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazatheme extends model {
        /**
         * Get configuration of theme
         * @param string $theme_code unique theme code
         */
        public function getThemeConfig($theme_code) {
                if(file_exists(DIR_APPLICATION . 'view/theme/' . $theme_code . '/config.json')){
                    return json_decode(file_get_contents(DIR_APPLICATION . 'view/theme/' . $theme_code . '/config.json'), true);
                } else {
                    return array();
                }
        }
        
        public function getSetting($theme_code, $store_id = 0) {
                $setting_data = array();
                
                $query = $this->db->query("SELECT s.* FROM " . DB_PREFIX . "mz_theme th LEFT JOIN " . DB_PREFIX . "mz_theme_setting s ON th.theme_id = s.theme_id WHERE th.theme_code = '" . $this->db->escape($theme_code) . "' AND s.store_id = '" . (int)$store_id . "'");
                
		foreach ($query->rows as $result) {
			if (!$result['is_serialized']) {
				$setting_data[$result['key']] = $result['value'];
			} else {
				$setting_data[$result['key']] = json_decode($result['value'], true);
			}
		}

		return $setting_data;
        }
}
