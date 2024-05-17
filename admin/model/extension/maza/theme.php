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
         * Get list of all available maza themes
         */
        public function getThemes() {
                $themes = array();
                
                $themes_config_file = glob(DIR_CATALOG . 'view/theme/mz_*/config.json');
                
                foreach ($themes_config_file as $theme_config_file) {
                    $theme_config = json_decode(file_get_contents($theme_config_file), true);
                    
                    if($theme_config){
                        $themes[] = $theme_config;
                    }
                }
                
                return $themes;
        }
        
        /**
         * Get list of all available maza skin
         */
        public function getSkins($theme_code) {
                $skins = array();
                        
                $skins_config_file = glob(DIR_CATALOG . 'view/theme/' . $theme_code . '/skins/content/*/config.json');
                
                foreach ($skins_config_file as $skin_config_file) {
                    $skin_config = json_decode(file_get_contents($skin_config_file), true);
                    
                    if($skin_config){
                        $skins[] = $skin_config;
                    }
                }
                
                return $skins;
        }
        
        /**
         * Get list of all available maza header
         */
        public function getHeaders($theme_code) {
                $headers = array();
                        
                $headers_config_file = glob(DIR_CATALOG . 'view/theme/' . $theme_code . '/skins/header/*/config.json');
                
                foreach ($headers_config_file as $header_config_file) {
                    $header_config = json_decode(file_get_contents($header_config_file), true);
                    
                    if($header_config){
                        $headers[] = $header_config;
                    }
                }
                
                return $headers;
        }
        
        /**
         * Get list of all available maza footer
         */
        public function getFooters($theme_code) {
                $footers = array();
                        
                $footers_config_file = glob(DIR_CATALOG . 'view/theme/' . $theme_code . '/skins/footer/*/config.json');
                
                foreach ($footers_config_file as $footer_config_file) {
                    $footer_config = json_decode(file_get_contents($footer_config_file), true);
                    
                    if($footer_config){
                        $footers[] = $footer_config;
                    }
                }
                
                return $footers;
        }
        
        /**
         * Get configuration of theme
         * @param string $theme_code unique theme code
         */
        public function getThemeConfig($theme_code) {
                if(file_exists(DIR_CATALOG . 'view/theme/' . $theme_code . '/config.json')){
                    return json_decode(file_get_contents(DIR_CATALOG . 'view/theme/' . $theme_code . '/config.json'), true);
                } else {
                    return array();
                }
        }
        
        /**
         * Get installed theme detail by code
         * @param string $theme_code unique theme code
         */
        public function getThemeByCode($theme_code) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_theme WHERE status = 1 AND theme_code = '" . $this->db->escape($theme_code) . "'");
                return $query->row;
        }
        
        /**
         * Get installed theme detail by id
         * @param int $theme_id unique theme id
         */
        public function getTheme($theme_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_theme WHERE theme_id = '" . (int)$theme_id . "' LIMIT 1");
                return $query->row;
        }
        
        /**
         * add theme
         * @param array $data theme data
         * @return int theme id
         */
        public function addTheme($data) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "mz_theme SET theme_code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', version = '" . $this->db->escape($data['version']) . "', status = 1");
                return $this->db->getLastId();
        }
        
        /**
         * edit theme
         * @param int $theme_id theme id
         * @param array $data theme data
         * @return void
         */
        public function editTheme($theme_id, $data) {
                $this->db->query("UPDATE " . DB_PREFIX . "mz_theme SET theme_code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', version = '" . $this->db->escape($data['version']) . "', status = 1 WHERE theme_id = '" . (int)$theme_id . "'");
        }
        
        /**
         * Install theme
         * @param string $theme_code unique theme code
         */
        public function install($theme_code) {
                $theme_config = $this->getThemeConfig($theme_code);
                
                // Install theme
                $theme_id = $this->addTheme($theme_config);
                
                // Install skin
                $skins = $this->getSkins($theme_code);
                
                foreach ($skins as $skin) {
                    $skin['theme_id'] = $theme_id;
                    $this->model_extension_maza_skin->addSkin($skin);
                }
                
                // Install header
                $headers = $this->getHeaders($theme_code);
                
                foreach ($headers as $header) {
                    $header['theme_id'] = $theme_id;
                    $this->model_extension_maza_header->addHeader($header);
                }
                
                // Install footer
                $footers = $this->getFooters($theme_code);
                
                foreach ($footers as $footer) {
                    $footer['theme_id'] = $theme_id;
                    $this->model_extension_maza_footer->addFooter($footer);
                }
        }
        
        /**
         * upgrade theme
         * @param string $theme_code unique theme code
         */
        public function upgrade($theme_code) {
                $theme_info = $this->getThemeByCode($theme_code);
                
                // upgrade theme
                $theme_config = $this->getThemeConfig($theme_code);
                $this->editTheme($theme_info['theme_id'], $theme_config);
                
                // pause all skin of this theme before to upgrade
                $this->model_extension_maza_skin->pauseSkins($theme_info['theme_id']);
                
                // Install skin
                $skins = $this->getSkins($theme_code);
                
                foreach ($skins as $skin) {
                    $skin['theme_id'] = $theme_info['theme_id'];
                    $skin_info = $this->model_extension_maza_skin->getSkinByCode($theme_code, $skin['code']);
                    
                    // Add or edit and enable skin
                    if($skin_info){
                        $this->model_extension_maza_skin->editSkin($skin_info['skin_id'], $skin);
                    } else {
                        $this->model_extension_maza_skin->addSkin($skin);
                    }
                }
                
                // delete all pause skin of this theme
                $this->model_extension_maza_skin->deletePauseSkins($theme_info['theme_id']);
                
                // pause all header of this theme before to upgrade
                $this->model_extension_maza_header->pauseHeaders($theme_info['theme_id']);
                
                // Install header
                $headers = $this->getHeaders($theme_code);
                
                foreach ($headers as $header) {
                    $header['theme_id'] = $theme_info['theme_id'];
                    $header_info = $this->model_extension_maza_header->getHeaderByCode($theme_code, $header['code']);
                    
                    if($header_info){
                        $this->model_extension_maza_header->editHeader($header_info['header_id'], $header);
                    } else {
                        $this->model_extension_maza_header->addHeader($header);
                    }
                }
                
                // delete all pause header of this theme
                $this->model_extension_maza_header->deletePauseHeaders($theme_info['theme_id']);
                
                // pause all footer of this theme before to upgrade
                $this->model_extension_maza_footer->pauseFooters($theme_info['theme_id']);
                
                // Install footer
                $footers = $this->getFooters($theme_code);
                
                foreach ($footers as $footer) {
                    $footer['theme_id'] = $theme_info['theme_id'];
                    $footer_info = $this->model_extension_maza_footer->getFooterByCode($theme_code, $footer['code']);
                    
                    if($footer_info){
                        $this->model_extension_maza_footer->editFooter($footer_info['footer_id'], $footer);
                    } else {
                        $this->model_extension_maza_footer->addFooter($footer);
                    }
                }
                
                // delete all pause footer of this theme
                $this->model_extension_maza_footer->deletePauseFooters($theme_info['theme_id']);
        }
        
        /**
         * Get settings of theme by code in store
         * @param string $theme_code unique theme code
         * @param string $code setting code(prefix of key)
         * @param INT $store_id store id
         * @return array
         */
        public function getSetting($theme_code, $code = null, $store_id = 0) {
		$setting_data = array();
                
                $sql = "SELECT s.* FROM " . DB_PREFIX . "mz_theme th LEFT JOIN " . DB_PREFIX . "mz_theme_setting s ON th.theme_id = s.theme_id WHERE th.theme_code = '" . $this->db->escape($theme_code) . "' AND s.store_id = '" . (int)$store_id . "'";
                
                if($code){
                    $sql .= " AND s.code = '" . $this->db->escape($code) . "'";
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
         * edit settings of theme by code in store
         * @param string $theme_code unique theme code
         * @param string $code setting code(prefix of key)
         * @param string $data setting data
         * @param INT $store_id store id
         * @return void
         */
	public function editSetting($theme_code, $code, $data, $store_id = 0) {
                $theme_info = $this->getThemeByCode($theme_code);
                
		$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_theme_setting` WHERE theme_id = '" . (int)$theme_info['theme_id'] . "' AND store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

		foreach ($data as $key => $value) {
			if (substr($key, 0, strlen($code)) == $code) {
				if (!is_array($value)) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "mz_theme_setting SET theme_id = '" . (int)$theme_info['theme_id'] . "', store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "mz_theme_setting SET theme_id = '" . (int)$theme_info['theme_id'] . "', store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value, true)) . "', is_serialized = '1'");
				}
			}
		}
	}
        
        /**
         * delete settings of theme by code in store
         * @param string $theme_code unique theme code
         * @param string $code setting code(prefix of key)
         * @param INT $store_id store id
         * @return void
         */
	public function deleteSetting($theme_code, $code, $store_id = 0) {
		$this->db->query("DELETE s FROM " . DB_PREFIX . "mz_theme th LEFT JOIN " . DB_PREFIX . "mz_theme_setting s ON th.theme_id = s.theme_id WHERE th.theme_code = '" . $this->db->escape($theme_code) . "' AND s.store_id = '" . (int)$store_id . "' AND `s.code` = '" . $this->db->escape($code) . "'");
	}
	
        /**
         * Get specific value of settings by key
         * @param string $theme_code unique theme code
         * @param string $key setting key
         * @param INT $store_id store id
         * @return mixed
         */
	public function getSettingValue($theme_code, $key, $store_id = 0) {
		$query = $this->db->query("SELECT s.value FROM " . DB_PREFIX . "mz_theme th LEFT JOIN " . DB_PREFIX . "mz_theme_setting s ON th.theme_id = s.theme_id WHERE th.theme_code = '" . $this->db->escape($theme_code) . "' AND s.store_id = '" . (int)$store_id . "' AND s.key = '" . $this->db->escape($key) . "'");

		if ($query->num_rows) {
			return $query->row['value'];
		} else {
			return null;	
		}
	}
	
        /**
         * Edit specific value of settings by key
         * @param string $theme_code unique theme code
         * @param string $key setting key
         * @param mixed $value value of key
         * @param INT $store_id store id
         * @return void
         */
	public function editSettingValue($theme_code, $key = '', $value = '', $store_id = 0) {
                $theme_info = $this->getThemeByCode($theme_code);
                
		if (!is_array($value)) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_theme_setting SET `value` = '" . $this->db->escape($value) . "', is_serialized = '0'  WHERE theme_id = '" . (int)$theme_info['theme_id'] . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_theme_setting SET `value` = '" . $this->db->escape(json_encode($value)) . "', is_serialized = '1' WHERE  theme_id = '" . (int)$theme_info['theme_id'] . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		}
	}
}
