<?php
class ModelExtensionMazaPageBuilder extends Model {
	public function addPage(array $data): int {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_page SET status = '" . (int)$data['status'] . "', override_skin_id = '" . (int)$data['override_skin_id'] . "', date_added = NOW(), date_modified = NOW()");

		$page_id = $this->db->getLastId();

		foreach ($data['page_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_page_description SET page_id = '" . (int)$page_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['page_store'])) {
			foreach ($data['page_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_page_to_store SET page_id = '" . (int)$page_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// SEO URL
		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			if($data['keyword']){
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'mz_page_id=" . (int)$page_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}
		} else if (isset($data['page_seo_url'])) {
			foreach ($data['page_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mz_page_id=" . (int)$page_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		return $page_id;
	}

	public function editPage(int $page_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_page SET status = '" . (int)$data['status'] . "', override_skin_id = '" . (int)$data['override_skin_id'] . "', date_modified = NOW() WHERE page_id = '" . (int)$page_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_page_description WHERE page_id = '" . (int)$page_id . "'");

		foreach ($data['page_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_page_description SET page_id = '" . (int)$page_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_page_to_store WHERE page_id = '" . (int)$page_id . "'");

		if (isset($data['page_store'])) {
			foreach ($data['page_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_page_to_store SET page_id = '" . (int)$page_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'mz_page_id=" . (int)$page_id . "'");

			if ($data['keyword']) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'mz_page_id=" . (int)$page_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}
		} else {
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mz_page_id=" . (int)$page_id . "'");

			if (isset($data['page_seo_url'])) {
				foreach ($data['page_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (trim($keyword)) {
							$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mz_page_id=" . (int)$page_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}
		}
	}
        
	public function copyPage(int $page_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_page p WHERE p.page_id = '" . (int)$page_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['status'] = '0';
			$data['page_description'] = $this->getPageDescriptions($page_id);
			$data['page_store'] = $this->getPageStores($page_id);

			return $this->addPage($data);
		}
	}

	public function deletePage(int $page_id): void {
		$this->model_extension_maza_layout_builder->deleteLayout('page', $page_id);
		$this->model_extension_maza_layout_builder->deleteLayout('page_component', $page_id);
                
		$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_page_to_store` WHERE page_id = '" . (int)$page_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_page_description` WHERE page_id = '" . (int)$page_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_page` WHERE page_id = '" . (int)$page_id . "'");

		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			$this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE query = 'mz_page_id=" . (int)$page_id . "'");
		} else {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'mz_page_id=" . (int)$page_id . "'");
		}
	}

	public function getPage(int $page_id): array {
		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			$query = $this->db->query("SELECT DISTINCT *, (SELECT DISTINCT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'mz_page_id=" . (int)$page_id . "') AS keyword FROM " . DB_PREFIX . "mz_page p LEFT JOIN " . DB_PREFIX . "mz_page_description pd ON (p.page_id = pd.page_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.page_id = '" . (int)$page_id . "'");
		} else {
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_page p LEFT JOIN " . DB_PREFIX . "mz_page_description pd ON (p.page_id = pd.page_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.page_id = '" . (int)$page_id . "'");
		}
		
		return $query->row;
	}

	public function getPages(array $data = array()): array {
		$sql = "SELECT *, (SELECT name FROM " . DB_PREFIX . "mz_skin WHERE skin_id = p.override_skin_id) skin FROM " . DB_PREFIX . "mz_page p LEFT JOIN " . DB_PREFIX . "mz_page_description pd ON (p.page_id = pd.page_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if(!empty($data['filter_name'])){
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		
		if(isset($data['filter_status'])){
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$sort_data = array(
			'pd.name',
			'p.sort_order',
			'p.status',
			'skin',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
		} else {
				$sql .= " ORDER BY pd.name";
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

	public function getPageDescriptions(int $page_id): array {
		$page_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_page_description WHERE page_id = '" . (int)$page_id . "'");

		foreach ($query->rows as $result) {
			$page_description_data[$result['language_id']] = array(
				'name'            => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword']
			);
		}

		return $page_description_data;
	}

	public function getPageStores(int $page_id): array {
		$page_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_page_to_store WHERE page_id = '" . (int)$page_id . "'");

		foreach ($query->rows as $result) {
			$page_store_data[] = $result['store_id'];
		}

		return $page_store_data;
	}

	public function getPageSeoUrls(int $page_id): array {
		$page_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'mz_page_id=" . (int)$page_id . "'");

		foreach ($query->rows as $result) {
			$page_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $page_seo_url_data;
	}

	public function getTotalPages(array $data = array()): int {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_page p LEFT JOIN " . DB_PREFIX . "mz_page_description pd ON (p.page_id = pd.page_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'"; 
                
		if(!empty($data['filter_name'])){
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		
		if(isset($data['filter_status'])){
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}