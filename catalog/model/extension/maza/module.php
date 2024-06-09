<?php
class ModelExtensionMazaModule extends Model {
	public function getSetting($module_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_module_setting WHERE skin_id = '" . (int)$this->mz_skin_config->get('skin_id') . "' AND module_id = '" . (int)$module_id . "'");
		
		if ($query->row) {
			return json_decode($query->row['setting'], true);
		} else {
			return array();	
		}
	}		
}