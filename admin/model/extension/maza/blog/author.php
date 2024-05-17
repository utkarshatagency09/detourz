<?php
class ModelExtensionMazaBlogAuthor extends Model {
	public function addAuthor($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_author SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");

		$author_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_author SET image = '" . $this->db->escape($data['image']) . "' WHERE author_id = '" . (int)$author_id . "'");
		}

		foreach ($data['author_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_author_description SET author_id = '" . (int)$author_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			if($data['keyword']){
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'mz_blog_author_id=" . (int)$author_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}
		} else if (isset($data['author_seo_url'])) {
			foreach ($data['author_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mz_blog_author_id=" . (int)$author_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		// Set which layout to use with this author
		if (isset($data['author_layout'])) {
			foreach ($data['author_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_author_to_layout SET author_id = '" . (int)$author_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('author');

		return $author_id;
	}

	public function editAuthor($author_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_author SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE author_id = '" . (int)$author_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_author SET image = '" . $this->db->escape($data['image']) . "' WHERE author_id = '" . (int)$author_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_author_description WHERE author_id = '" . (int)$author_id . "'");

		foreach ($data['author_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_author_description SET author_id = '" . (int)$author_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		
		// SEO URL
		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'mz_blog_author_id=" . (int)$author_id . "'");

			if ($data['keyword']) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'mz_blog_author_id=" . (int)$author_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}
		} else {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'mz_blog_author_id=" . (int)$author_id . "'");

			if (isset($data['author_seo_url'])) {
				foreach ($data['author_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mz_blog_author_id=" . (int)$author_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_author_to_layout WHERE author_id = '" . (int)$author_id . "'");

		if (isset($data['author_layout'])) {
			foreach ($data['author_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_author_to_layout SET author_id = '" . (int)$author_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('author');
	}

	public function deleteAuthor($author_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_author WHERE author_id = '" . (int)$author_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_author_description WHERE author_id = '" . (int)$author_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_author_to_layout WHERE author_id = '" . (int)$author_id . "'");

		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			$this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE query = 'mz_blog_author_id=" . (int)$author_id . "'");
		} else {
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mz_blog_author_id=" . (int)$author_id . "'");
		}

		$this->cache->delete('author');
	}

	public function getAuthor($author_id) {
		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			$query = $this->db->query("SELECT DISTINCT *, (SELECT DISTINCT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'mz_blog_author_id=" . (int)$author_id . "') AS keyword FROM " . DB_PREFIX . "mz_blog_author a LEFT JOIN " . DB_PREFIX . "mz_blog_author_description ad ON (a.author_id = ad.author_id) WHERE a.author_id = '" . (int)$author_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");
		} else {
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_blog_author a LEFT JOIN " . DB_PREFIX . "mz_blog_author_description ad ON (a.author_id = ad.author_id) WHERE a.author_id = '" . (int)$author_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");
		}

		return $query->row;
	}

	public function getAuthors($data = array()) {
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "mz_blog_author a LEFT JOIN " . DB_PREFIX . "mz_blog_author_description ad ON (a.author_id = ad.author_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY a.author_id";

		$sort_data = array(
			'name',
			'sort_order',
                        'date_added',
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

	public function getAuthorDescriptions($author_id) {
		$author_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_author_description WHERE author_id = '" . (int)$author_id . "'");

		foreach ($query->rows as $result) {
			$author_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $author_description_data;
	}
	
	public function getAuthorSeoUrls($author_id) {
		$author_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'mz_blog_author_id=" . (int)$author_id . "'");

		foreach ($query->rows as $result) {
			$author_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $author_seo_url_data;
	}
	
	public function getAuthorLayouts($author_id) {
		$author_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_author_to_layout WHERE author_id = '" . (int)$author_id . "'");

		foreach ($query->rows as $result) {
			$author_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $author_layout_data;
	}

	public function getTotalAuthors() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_blog_author");

		return $query->row['total'];
	}
	
//	public function getTotalAuthorsByLayoutId($layout_id) {
//		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_blog_author_to_layout WHERE layout_id = '" . (int)$layout_id . "'");
//
//		return $query->row['total'];
//	}	
}