<?php
class ModelExtensionMazaFilter extends Model {
	public function addFilter(array $data): int {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_filter SET sort_order = '" . (int)$data['sort_order'] . "', filter_language_id = '" . (int)$data['filter_language_id'] . "', status = '" . (int)$data['status'] . "', setting = '" . $this->db->escape(json_encode($data['setting'])) . "', date_added = NOW(), date_modified = NOW()");

		$filter_id = $this->db->getLastId();

		foreach ($data['filter_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_filter_description SET filter_id = '" . (int)$filter_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
                
		// Filter category
		if (isset($data['filter_category'])) {
			foreach ($data['filter_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_filter_to_category SET filter_id = '" . (int)$filter_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		return $filter_id;
	}

	public function editFilter(int $filter_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_filter SET sort_order = '" . (int)$data['sort_order'] . "', filter_language_id = '" . (int)$data['filter_language_id'] . "', status = '" . (int)$data['status'] . "', setting = '" . $this->db->escape(json_encode($data['setting'])) . "', date_modified = NOW() WHERE filter_id = '" . (int)$filter_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_description WHERE filter_id = '" . (int)$filter_id . "'");

		foreach ($data['filter_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_filter_description SET filter_id = '" . (int)$filter_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
                
		// Filter category
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_to_category WHERE filter_id = '" . (int)$filter_id . "'");
		
		if (isset($data['filter_category'])) {
			foreach ($data['filter_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_filter_to_category SET filter_id = '" . (int)$filter_id . "', category_id = '" . (int)$category_id . "'");
			}
		}
	}
        
	public function copyFilter(int $filter_id): int {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_filter WHERE filter_id = '" . (int)$filter_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['status'] = '0';
			$data['setting'] = json_decode($data['setting'], true);
			$data['filter_description'] = $this->getFilterDescriptions($filter_id);
			$data['filter_category'] = $this->getFilterCategories($filter_id);

			return $this->addFilter($data);
		}

		return 0;
	}

	public function deleteFilter(int $filter_id): void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter WHERE filter_id = '" . (int)$filter_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_description WHERE filter_id = '" . (int)$filter_id . "'");
		$this->db->query("DELETE v, vd, v2p FROM " . DB_PREFIX . "mz_filter_value v LEFT JOIN " . DB_PREFIX . "mz_filter_value_description vd ON (v.value_id = vd.value_id) LEFT JOIN " . DB_PREFIX . "mz_filter_value_to_product v2p ON (v2p.value_id = v.value_id) WHERE v.filter_id = '" . (int)$filter_id . "'");
	}

	public function getFilter(int $filter_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_filter f LEFT JOIN " . DB_PREFIX . "mz_filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id = '" . (int)$filter_id . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");
                
		if($query->row){
			$query->row['setting'] = json_decode($query->row['setting'], true);
		}

		return $query->row;
	}

	public function getFilters(array $data = array()): array {
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "mz_filter f LEFT JOIN " . DB_PREFIX . "mz_filter_description fd ON (f.filter_id = fd.filter_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND fd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
                
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND f.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY f.filter_id";

		$sort_data = array(
			'name',
			'sort_order',
			'date_added',
			'status',
			'date_sync'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
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

	public function getFilterDescriptions(int $filter_id): array {
		$filter_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_filter_description WHERE filter_id = '" . (int)$filter_id . "'");

		foreach ($query->rows as $result) {
			$filter_description_data[$result['language_id']] = array(
				'name' => $result['name']
			);
		}

		return $filter_description_data;
	}
        
	public function getFilterCategories(int $filter_id): array {
		$filter_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_filter_to_category WHERE filter_id = '" . (int)$filter_id . "'");

		foreach ($query->rows as $result) {
			$filter_category_data[] = $result['category_id'];
		}

		return $filter_category_data;
	}

	public function getTotalFilters(array $data = array()): int {
		$sql = "SELECT COUNT(DISTINCT f.filter_id) AS total FROM " . DB_PREFIX . "mz_filter f LEFT JOIN " . DB_PREFIX . "mz_filter_description fd ON (f.filter_id = fd.filter_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
                
		if (!empty($data['filter_name'])) {
			$sql .= " AND fd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
                
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND f.status = '" . (int)$data['filter_status'] . "'";
		}
                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}