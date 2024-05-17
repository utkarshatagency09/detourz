<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazalayoutBuilder extends model {
    /**
     * Delete and Add layout entries
     * @param string $group entry group
     * @param int $group_owner entry group owner
     * @param array $entries entry list
     * @return void
     */
    public function editLayout(int $skin_id, string $group, int $group_owner, array $entries): void {
        $this->db->query("DELETE FROM " . DB_PREFIX . "mz_layout_entry WHERE `group` = '" . $this->db->escape($group) . "' AND `group_owner` = '" . (int)$group_owner . "' AND `skin_id` = '" . (int)$skin_id . "'");
        
        $this->addEntry($skin_id, $group, $group_owner, $entries);
        
        $this->mz_cache->delete($this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code'));
        $this->mz_cache->delete('page');
        $this->mz_document->clear(); // clear asset files for new settings
    }
        
    /**
     * Add sub sequence layout entries
     * @param string $group entry group
     * @param int $group_owner entry group owner
     * @param array $entries entry list
     * @param int $parent_entry_id parent entry id
     * @return void
     */
    private function addEntry(int $skin_id, string $group, int $group_owner, array $entries, int $parent_entry_id = 0): void {
        foreach ($entries as $entry) {
            $setting = array(); // entry setting
            
            // device profile setting
            if(isset($entry['device_size'])){
                $setting['device_size'] = $entry['device_size'];
            }
            
            if(isset($entry['device_hidden'])){
                $setting['device_hidden'] = $entry['device_hidden'];
            }
            
            if(isset($entry['device_order'])){
                $setting['device_order'] = $entry['device_order'];
            }
            
            // custom setting
            $setting['setting'] = empty($entry['setting'])?NULL:$entry['setting'];
            
            // Section
            if($entry['type'] == 'section'){
                $this->db->query("INSERT INTO " . DB_PREFIX . "mz_layout_entry SET `group` = '" . $this->db->escape($group) . "', `group_owner` = '" . (int)$group_owner . "', `type` = 'section', setting = '" . $this->db->escape(json_encode($setting)) . "', parent_entry_id = '" . (int)$parent_entry_id . "', `skin_id` = '" . (int)$skin_id . "'");
            }
            
            // Row
            if($entry['type'] == 'row'){
                $this->db->query("INSERT INTO " . DB_PREFIX . "mz_layout_entry SET `group` = '" . $this->db->escape($group) . "', `group_owner` = '" . (int)$group_owner . "', `type` = 'row', setting = '" . $this->db->escape(json_encode($setting)) . "', parent_entry_id = '" . (int)$parent_entry_id . "', `skin_id` = '" . (int)$skin_id . "'");
            }
            
            // Column
            if($entry['type'] == 'col'){
                $this->db->query("INSERT INTO " . DB_PREFIX . "mz_layout_entry SET `group` = '" . $this->db->escape($group) . "', `group_owner` = '" . (int)$group_owner . "', `type` = 'col', `setting` = '" . $this->db->escape(json_encode($setting)) . "', parent_entry_id = '" . (int)$parent_entry_id . "', `skin_id` = '" . (int)$skin_id . "'");
            }
            
            // module
            if($entry['type'] == 'module'){
                $this->db->query("INSERT INTO " . DB_PREFIX . "mz_layout_entry SET `group` = '" . $this->db->escape($group) . "', `group_owner` = '" . (int)$group_owner . "', `type` = 'module', `code` = '" . $this->db->escape($entry['code']) . "', `setting` = '" . $this->db->escape(json_encode($setting)) . "', parent_entry_id = '" . (int)$parent_entry_id . "', `skin_id` = '" . (int)$skin_id . "'");
            }
            
            // widget
            if($entry['type'] == 'widget'){
                $setting['widget_data'] = empty($entry['widget_data'])?NULL:$entry['widget_data'];
                
                $this->db->query("INSERT INTO " . DB_PREFIX . "mz_layout_entry SET `group` = '" . $this->db->escape($group) . "', `group_owner` = '" . (int)$group_owner . "', `type` = 'widget', `code` = '" . $this->db->escape($entry['code']) . "', `setting` = '" . $this->db->escape(json_encode($setting)) . "', parent_entry_id = '" . (int)$parent_entry_id . "', `skin_id` = '" . (int)$skin_id . "'");
            }
            
            // Design
            if($entry['type'] == 'design'){
                $setting['design_data'] = empty($entry['design_data'])?NULL:$entry['design_data'];
                
                $this->db->query("INSERT INTO " . DB_PREFIX . "mz_layout_entry SET `group` = '" . $this->db->escape($group) . "', `group_owner` = '" . (int)$group_owner . "', `type` = 'design', `code` = '" . $this->db->escape($entry['code']) . "', `setting` = '" . $this->db->escape(json_encode($setting)) . "', parent_entry_id = '" . (int)$parent_entry_id . "', `skin_id` = '" . (int)$skin_id . "'");
            }
            
            // content
            if($entry['type'] == 'content'){
                $setting['content_data'] = empty($entry['content_data'])?NULL:$entry['content_data'];
                
                $this->db->query("INSERT INTO " . DB_PREFIX . "mz_layout_entry SET `group` = '" . $this->db->escape($group) . "', `group_owner` = '" . (int)$group_owner . "', `type` = 'content', `code` = '" . $this->db->escape($entry['code']) . "', `setting` = '" . $this->db->escape(json_encode($setting)) . "', parent_entry_id = '" . (int)$parent_entry_id . "', `skin_id` = '" . (int)$skin_id . "'");
            }
            
            // Component
            if($entry['type'] == 'component'){
                $this->db->query("INSERT INTO " . DB_PREFIX . "mz_layout_entry SET `group` = '" . $this->db->escape($group) . "', `group_owner` = '" . (int)$group_owner . "', `type` = 'component', setting = '" . $this->db->escape(json_encode($setting)) . "', parent_entry_id = '" . (int)$parent_entry_id . "', `skin_id` = '" . (int)$skin_id . "'");
            }
            
            $entry_id = $this->db->getLastId();
            
            // Add child entry
            if(isset($entry['child_entry'])){
                $this->addEntry($skin_id, $group, $group_owner, $entry['child_entry'], $entry_id);
            }
        }
    }
        
    /**
     * Get layout entries
     * @param string $group entry group
     * @param int $group_owner entry group owner
     * @param int $parent_entry_id parent entry id
     * @return array $entries
     */
    public function getLayout(int $skin_id, string $group, int $group_owner, int $parent_entry_id = 0): array {
        $entries = array();
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_layout_entry WHERE `group` = '" . $this->db->escape($group) . "' AND group_owner = '" . (int)$group_owner . "' AND parent_entry_id = '" . (int)$parent_entry_id . "' AND `skin_id` = '" . (int)$skin_id . "' ORDER BY entry_id ASC");
        
        foreach ($query->rows as $entry) {
            $setting = json_decode($entry['setting'], TRUE);
            
            $entry_info = array(
                'entry_id'      => $entry['entry_id'],
                'type'          => $entry['type'],
                'code'          => $entry['code'],
                'setting'       => empty($setting['setting'])?null:$setting['setting'],
                'device_hidden' => empty($setting['device_hidden'])?array():$setting['device_hidden'],
                'device_order'  => empty($setting['device_order'])?array():$setting['device_order'],
                'child_entry'   => $this->getLayout($skin_id, $group, $group_owner, $entry['entry_id'])
            );
            
            
            // Column
            if($entry['type'] == 'col'){
                $entry_info['device_size']      = isset($setting['device_size'])?$setting['device_size']:array();
            }
            
            // Widget
            if($entry['type'] == 'widget'){
                $entry_info['widget_data']  = empty($setting['widget_data'])?NULL:$setting['widget_data'];
            }
            
            // Design
            if($entry['type'] == 'design'){
                $entry_info['design_data']  = empty($setting['design_data'])?NULL:$setting['design_data'];
            }
            
            // Content
            if($entry['type'] == 'content'){
                $entry_info['content_data']  = empty($setting['content_data'])?NULL:$setting['content_data'];
            }
            
            $entries[] = $entry_info;
        }
        
        return $entries;
    }
        
    /**
     * Delete layout entries
     * @param string $group entry group
     * @param int $group_owner entry group owner (optional)
     * @param int $skin_id skin id (optional)
     * @return void
     */
    public function deleteLayout(string $group, int $group_owner = 0, int $skin_id = 0): void {
        $sql = "DELETE FROM " . DB_PREFIX . "mz_layout_entry WHERE `group` = '" . $this->db->escape($group) . "'";
        
        if($group_owner){
            $sql.= " AND `group_owner` = '" . (int)$group_owner . "'";
        }
        
        if($skin_id){
            $sql.= " AND `skin_id` = '" . (int)$skin_id . "'";
        }
        
        $this->db->query($sql);
    }

    /**
     * Duplicate skin data of layout to other skin
     * @param int $from_skin_id skin id to duplicate from
     * @param int $to_skin_id skin id to duplicate to
     */
    public function duplicateSkin(int $from_skin_id, int $to_skin_id): void {
        // Duplicate group 
        $query_group = $this->db->query("SELECT DISTINCT `group` FROM `" . DB_PREFIX . "mz_layout_entry` WHERE skin_id = '" . $from_skin_id . "' GROUP BY `group`");
        foreach(array_column($query_group->rows, 'group') as $group){
            
            // Duplicate group owner
            $query_group_owner = $this->db->query("SELECT DISTINCT `group_owner` FROM `" . DB_PREFIX . "mz_layout_entry` WHERE skin_id = '" . $from_skin_id . "' AND `group` = '" . $this->db->escape($group) . "' GROUP BY `group_owner`");
            foreach(array_column($query_group_owner->rows, 'group_owner') as $group_owner){
                
                // Duplicate layout of group owner
                $layout = $this->getLayout($from_skin_id, $group, $group_owner);
                $this->editLayout($to_skin_id, $group, $group_owner, $layout);
            }
        }
    }
}
