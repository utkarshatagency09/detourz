<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		http://www.templatemaza.com
*/
class ModelExtensionMazaInstallMerger extends Controller {
    
    /**
     * Merge setting of content with default setting
     * @param string $code content code
     * @param array $data content default setting
     */
    public function content(string $code, $data): void {
        $query = $this->db->query("SELECT entry_id, setting FROM " . DB_PREFIX . "mz_layout_entry WHERE `type` = 'content' AND `code` = '" . $code . "'");
                
        foreach ($query->rows as $entry) {
            $setting = json_decode($entry['setting'], TRUE);
            
            $entry_data = array();
            parse_str(html_entity_decode($setting['content_data']), $entry_data);

            if(is_callable($data)){
                $result = $data($entry_data);

                if(!$result || $result == $entry_data) continue;
            } else {
                $result = \maza\array_merge_subsequence($data, $entry_data);
            }
            
            $setting['content_data'] = htmlentities(http_build_query($result));
            
            $this->db->query("UPDATE " . DB_PREFIX . "mz_layout_entry SET `setting` = '" . $this->db->escape(json_encode($setting)) . "' WHERE `entry_id` = '" . (int)$entry['entry_id'] . "'");
        }
    }
    
    /**
     * Merge setting of design with default setting
     * @param string $code design code
     * @param array|Function $data design default setting or return modified setting from function
     */
    public function design(string $code, $data): void {
        $query = $this->db->query("SELECT entry_id, setting FROM " . DB_PREFIX . "mz_layout_entry WHERE `type` = 'design' AND `code` = '" . $code . "'");
                
        foreach ($query->rows as $entry) {
            $setting = json_decode($entry['setting'], TRUE);
            
            $entry_data = array();
            parse_str(html_entity_decode($setting['design_data']), $entry_data);

            if(is_callable($data)){
                $result = $data($entry_data);

                if(!$result || $result == $entry_data) continue;
            } else {
                $result = \maza\array_merge_subsequence($data, $entry_data);
            }
            
            $setting['design_data'] = htmlentities(http_build_query($result));
            
            $this->db->query("UPDATE " . DB_PREFIX . "mz_layout_entry SET `setting` = '" . $this->db->escape(json_encode($setting)) . "' WHERE `entry_id` = '" . (int)$entry['entry_id'] . "'");
        }
    }

    /**
     * Merge setting of widget with default setting
     * @param string $code widget code
     * @param array|Function $data widget default setting or return modified setting from function
     */
    public function widget(string $code, $data): void {
        $query = $this->db->query("SELECT entry_id, setting FROM " . DB_PREFIX . "mz_layout_entry WHERE `type` = 'widget' AND `code` = '" . $code . "'");
                
        foreach ($query->rows as $entry) {
            $setting = json_decode($entry['setting'], TRUE);
            
            $entry_data = array();
            parse_str(html_entity_decode($setting['widget_data']), $entry_data);

            if(is_callable($data)){
                $result = $data($entry_data);

                if(!$result || $result == $entry_data) continue;
            } else {
                $result = \maza\array_merge_subsequence($data, $entry_data);
            }
            
            $setting['widget_data'] = htmlentities(http_build_query($result));
            
            $this->db->query("UPDATE " . DB_PREFIX . "mz_layout_entry SET `setting` = '" . $this->db->escape(json_encode($setting)) . "' WHERE `entry_id` = '" . (int)$entry['entry_id'] . "'");
        }
    }

    /**
     * Merge setting of entry with default setting
     * @param string $type entry type
     * @param array|Function $data entry default setting or return modified setting from function
     */
    public function entry(string $type, $data): void {
        $query = $this->db->query("SELECT entry_id, setting FROM " . DB_PREFIX . "mz_layout_entry WHERE `type` = '" . $type . "'");
                
        foreach ($query->rows as $entry) {
            $setting = json_decode($entry['setting'], TRUE);

            $entry_setting = array();
            parse_str(html_entity_decode($setting['setting']), $entry_setting);

            if($entry_setting){
                if(is_callable($data)){
                    $result = $data($entry_setting);
    
                    if(!$result || $result == $entry_setting) continue;
                } else {
                    $result = \maza\array_merge_subsequence($data, $entry_setting);
                }

                $setting['setting'] = htmlentities(http_build_query($result));

                $this->db->query("UPDATE " . DB_PREFIX . "mz_layout_entry SET `setting` = '" . $this->db->escape(json_encode($setting)) . "' WHERE `entry_id` = '" . (int)$entry['entry_id'] . "'");
            }
        }
    }

    /**
     * Merge setting of module with default setting
     * @param string $code module code
     * @param array|Function $data module default setting or return modified setting from function
     */
    public function module(string $code, $data): void {
        $query = $this->db->query("SELECT ms.setting_id, ms.setting FROM " . DB_PREFIX . "mz_module_setting ms LEFT JOIN " . DB_PREFIX . "module m ON (ms.module_id = m.module_id) WHERE m.code = '" . $code . "'");
                
        foreach ($query->rows as $module) {
            $setting = json_decode($module['setting'], TRUE);

            if($setting){
                if(is_callable($data)){
                    $result = $data($setting);
    
                    if(!$result || $result == $setting) continue;
                } else {
                    $result = array_merge($data, $setting);
                }

                $this->db->query("UPDATE " . DB_PREFIX . "mz_module_setting SET `setting` = '" . $this->db->escape(json_encode($result)) . "' WHERE setting_id = '" . (int)$module['setting_id'] . "'");
            }
        }
    }
    
}