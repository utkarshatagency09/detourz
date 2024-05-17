<?php
class ModelExtensionMazaCatalogDocument extends Model {
	public function addDocument(array $data): int {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_document SET store_id = '" . (int)$data['store_id'] . "', route = '" . $this->db->escape($data['route']) . "', status = '" . (int)$data['status'] . "', og_image_width = '" . (int)$data['og_image_width'] . "', og_image_height = '" . (int)$data['og_image_height'] . "', og_video = '" . $this->db->escape($data['og_video']) . "', date_added = NOW(), date_modified = NOW()");

		$document_id = $this->db->getLastId();

		foreach ($data['document_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_document_description SET document_id = '" . (int)$document_id . "', language_id = '" . (int)$language_id . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', og_title = '" . $this->db->escape($value['og_title']) . "', og_description = '" . $this->db->escape($value['og_description']) . "', og_image = '" . $this->db->escape($value['og_image']) . "', og_image_alt = '" . $this->db->escape($value['og_image_alt']) . "'");
		}

		return $document_id;
	}

	public function editDocument(int $document_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_document SET store_id = '" . (int)$data['store_id'] . "', route = '" . $this->db->escape($data['route']) . "', status = '" . (int)$data['status'] . "', og_image_width = '" . (int)$data['og_image_width'] . "', og_image_height = '" . (int)$data['og_image_height'] . "', og_video = '" . $this->db->escape($data['og_video']) . "', date_modified = NOW() WHERE document_id = '" . (int)$document_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_document_description WHERE document_id = '" . (int)$document_id . "'");

		foreach ($data['document_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_document_description SET document_id = '" . (int)$document_id . "', language_id = '" . (int)$language_id . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', og_title = '" . $this->db->escape($value['og_title']) . "', og_description = '" . $this->db->escape($value['og_description']) . "', og_image = '" . $this->db->escape($value['og_image']) . "', og_image_alt = '" . $this->db->escape($value['og_image_alt']) . "'");
		}
	}

	public function deleteDocument(int $document_id): void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_document_description WHERE document_id = '" . (int)$document_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_document WHERE document_id = '" . (int)$document_id . "'");
	}

	public function getDocument(int $document_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_document d LEFT JOIN " . DB_PREFIX . "mz_document_description dd ON (d.document_id = dd.document_id) WHERE d.document_id = '" . (int)$document_id . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");
               
		return $query->row;
	}

	public function getDocuments(array $data = array()): array {
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "mz_document d LEFT JOIN " . DB_PREFIX . "mz_document_description dd ON (d.document_id = dd.document_id) WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if(isset($data['filter_status']) && !is_null($data['filter_status'])){
			$sql .= " AND d.status = '" . (int)$data['filter_status'] . "'";
		}

		if(isset($data['filter_store_id']) && !is_null($data['filter_store_id'])){
			$sql .= " AND d.store_id = '" . (int)$data['filter_store_id'] . "'";
		}

		if (!empty($data['filter_route'])) {
			$sql .= " AND d.route LIKE '" . $this->db->escape($data['filter_route']) . "%'";
		}

		$sql .= " GROUP BY d.document_id";

		$sort_data = array(
			'd.status',
			'd.route',
			'd.date_modified',
			'd.date_added',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY d.route";
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

	public function getTotalDocuments(array $data = array()): int {
		$sql = "SELECT COUNT(DISTINCT d.document_id) AS total FROM " . DB_PREFIX . "mz_document d LEFT JOIN " . DB_PREFIX . "mz_document_description dd ON (d.document_id = dd.document_id) WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
                
		if(isset($data['filter_status']) && !is_null($data['filter_status'])){
			$sql .= " AND d.status = '" . (int)$data['filter_status'] . "'";
		}

		if(isset($data['filter_store_id']) && !is_null($data['filter_store_id'])){
			$sql .= " AND d.store_id = '" . (int)$data['filter_store_id'] . "'";
		}

		if (!empty($data['filter_route'])) {
			$sql .= " AND d.route LIKE '" . $this->db->escape($data['filter_route']) . "%'";
		}
                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getDocumentDescriptions(int $document_id): array {
		$document_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_document_description WHERE document_id = '" . (int)$document_id . "'");

		foreach ($query->rows as $result) {
			$document_description_data[$result['language_id']] = array(
				'meta_title' 			=> $result['meta_title'],
				'meta_description' 		=> $result['meta_description'],
				'meta_keyword' 			=> $result['meta_keyword'],
				'og_title' 				=> $result['og_title'],
				'og_description' 		=> $result['og_description'],
				'og_image' 				=> $result['og_image'],
				'og_image_alt' 			=> $result['og_image_alt'],
			);
		}

		return $document_description_data;
	}
}