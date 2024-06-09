<?php
class ControllerExtensionMazaEventSettingModule extends Controller {
        
        /**
         * Delete setting when module deleted
         * @param string $route controller route
         * @param array $param parameter of method
         * @return void
         */
        public function deleteModule($route, $param) {
                $module_id = $param[0];
                
                // Delete module setting
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_module_setting WHERE module_id = '" . (int)$module_id . "'");
                
                // Delete module entry
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_layout_entry WHERE `type` = 'module' AND `code` LIKE '%." . (int)$module_id . "'");
        }
}
