<?php
class ModelExtensionMazaPage extends Model {
	public function getPage(int $page_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_page p LEFT JOIN " . DB_PREFIX . "mz_page_description pd ON (p.page_id = pd.page_id) LEFT JOIN " . DB_PREFIX . "mz_page_to_store p2s ON (p.page_id = p2s.page_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.page_id = '" . (int)$page_id . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.status = '1'");

		return $query->row;
	}

	public function getPages(): array {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_page p LEFT JOIN " . DB_PREFIX . "mz_page_description pd ON (p.page_id = pd.page_id) LEFT JOIN " . DB_PREFIX . "mz_page_to_store p2s ON (p.page_id = p2s.page_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.status = '1'");

		return $query->rows;
	}

	public function getTotalPages(): int {
		$query = $this->db->query("SELECT COUNT(DISTINCT p.page_id) AS total FROM " . DB_PREFIX . "mz_page p LEFT JOIN " . DB_PREFIX . "mz_page_to_store p2s ON (p.page_id = p2s.page_id) WHERE p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		return $query->row['total'];
	}

	public function getPageSkinId(int $page_id): int {
		$query = $this->db->query("SELECT override_skin_id FROM " . DB_PREFIX . "mz_page WHERE page_id = '" . (int)$page_id . "'");

		if ($query->num_rows) {
			return $query->row['override_skin_id'];
		} else {
			return 0;
		}
	}
}