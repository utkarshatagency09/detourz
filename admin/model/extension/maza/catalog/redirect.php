<?php
class ModelExtensionMazaCatalogRedirect extends Model {
	public function addRedirect(array $data): int {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_redirect_url SET store_id = '" . (int)$data['store_id'] . "', `from` = '" . $this->db->escape($data['from']) . "', `to` = '" . $this->db->escape($data['to']) . "', date_added = NOW()");

		return $this->db->getLastId();
	}

	public function editRedirect(int $redirect_url_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_redirect_url SET store_id = '" . (int)$data['store_id'] . "', `from` = '" . $this->db->escape($data['from']) . "', `to` = '" . $this->db->escape($data['to']) . "' WHERE redirect_url_id = '" . (int)$redirect_url_id . "'");
	}

	public function deleteRedirect(int $redirect_url_id): void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_redirect_url WHERE redirect_url_id = '" . (int)$redirect_url_id . "'");
	}

	public function getRedirect(int $redirect_url_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_redirect_url WHERE redirect_url_id = '" . (int)$redirect_url_id . "'");
               
		return $query->row;
	}

	public function getRedirects(array $data = array()): array {
		$sql = "SELECT * FROM " . DB_PREFIX . "mz_redirect_url WHERE 1";

		if(isset($data['filter_store_id']) && !is_null($data['filter_store_id'])){
			$sql .= " AND store_id = '" . (int)$data['filter_store_id'] . "'";
		}

		if (!empty($data['filter_from'])) {
			$sql .= " AND `from` = '" . $this->db->escape($data['filter_from']) . "'";
		}

		if (!empty($data['filter_to'])) {
			$sql .= " AND `to` = '" . $this->db->escape($data['filter_to']) . "'";
		}

		$sql .= " GROUP BY redirect_url_id";

		$sort_data = array(
			'from',
			'date_added',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
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

	public function getTotalRedirects(array $data = array()): int {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_redirect_url WHERE 1";
                
		if(isset($data['filter_store_id']) && !is_null($data['filter_store_id'])){
			$sql .= " AND store_id = '" . (int)$data['filter_store_id'] . "'";
		}

		if (!empty($data['filter_from'])) {
			$sql .= " AND `from` = '" . $this->db->escape($data['filter_from']) . "'";
		}

		if (!empty($data['filter_to'])) {
			$sql .= " AND `to` = '" . $this->db->escape($data['filter_to']) . "'";
		}
                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}