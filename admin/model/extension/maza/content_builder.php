<?php
class ModelExtensionMazaContentBuilder extends Model {
	public function addContent($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_content SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', date_added = NOW(), date_modified = NOW()");

		return $this->db->getLastId();
	}

	public function editContent($content_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_content SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', date_modified = NOW() WHERE content_id = '" . (int)$content_id . "'");
	}
        
        public function copyContent($content_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_content WHERE content_id = '" . (int)$content_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['status'] = '0';

			return $this->addContent($data);
		}
	}

	public function deleteContent($content_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_content` WHERE content_id = '" . (int)$content_id . "'");
                
                $this->model_extension_maza_layout_builder->deleteLayout('content_builder', $content_id);
	}

	public function getContent($content_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_content WHERE content_id = '" . (int)$content_id . "'");

		return $query->row;
	}

	public function getContents($data = array()) {
                $sql = "SELECT * FROM " . DB_PREFIX . "mz_content WHERE 1";
                
                if(!empty($data['filter_name'])){
                    $sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
                }
                
                if(isset($data['filter_status'])){
                    $sql .= " AND status = '" . (int)$data['filter_status'] . "'";
                }

                $sort_data = array(
                        'name',
                        'status'
                );

                if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                        $sql .= " ORDER BY " . $data['sort'];
                } else {
                        $sql .= " ORDER BY name";
                }

                if (isset($data['order']) && ($data['order'] == 'DESC')) {
                        $sql .= " DESC";
                } else {
                        $sql .= " ASC";
                }

                if (isset($data['start']) || isset($data['limit'])) {
                        if ($data['start'] < 0) {
                                $data['start'] = 0;
                        }

                        if ($data['limit'] < 1) {
                                $data['limit'] = 20;
                        }

                        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
                }

                $query = $this->db->query($sql);

                return $query->rows;
	}

	public function getTotalContents($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_content WHERE 1"; 
                
                if(!empty($data['filter_name'])){
                    $sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
                }
                
                if(isset($data['filter_status'])){
                    $sql .= " AND status = '" . (int)$data['filter_status'] . "'";
                }

                
                $query = $this->db->query($sql);

		return $query->row['total'];
	}
}