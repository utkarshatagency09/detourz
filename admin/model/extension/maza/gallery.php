<?php
class ModelExtensionMazaGallery extends Model {
	public function addGallery(array $data): int {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_gallery SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

		$gallery_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_gallery SET image = '" . $this->db->escape(json_encode($data['image'])) . "' WHERE gallery_id = '" . (int)$gallery_id . "'");
		}

		if (isset($data['video'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_gallery SET video = '" . $this->db->escape(json_encode($data['video'])) . "' WHERE gallery_id = '" . (int)$gallery_id . "'");
		}

		return $gallery_id;
	}

	public function editGallery(int $gallery_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_gallery SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', image = NULL, video = NULL WHERE gallery_id = '" . (int)$gallery_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_gallery SET image = '" . $this->db->escape(json_encode($data['image'])) . "' WHERE gallery_id = '" . (int)$gallery_id . "'");
		}

		if (isset($data['video'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_gallery SET video = '" . $this->db->escape(json_encode($data['video'])) . "' WHERE gallery_id = '" . (int)$gallery_id . "'");
		}
	}
        
	public function copyGallery(int $gallery_id): int {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_gallery WHERE gallery_id = '" . (int)$gallery_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['status'] = '0';
			$data['image'] = json_decode($data['image'], true);
			$data['video'] = json_decode($data['video'], true);

			return $this->addGallery($data);
		}

		return 0;
	}

	public function deleteGallery(int $gallery_id): void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_gallery WHERE gallery_id = '" . (int)$gallery_id . "'");
	}

	public function getGallery(int $gallery_id): array {
		$data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_gallery WHERE gallery_id = '" . (int)$gallery_id . "'");
                
		if($query->row){
			$data = $query->row;
			$data['image'] = json_decode($query->row['image'], true);
			$data['video'] = json_decode($query->row['video'], true);
		}

		return $data;
	}

	public function getGalleries(array $data = array()): array {
		$sql = "SELECT * FROM " . DB_PREFIX . "mz_gallery WHERE 1";

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
                
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}

		$sort_data = array(
			'name',
			'date_added',
			'status'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added";
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

	public function getTotalGalleries(array $data = array()): int {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_gallery WHERE 1";
                
		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
                
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}
                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}