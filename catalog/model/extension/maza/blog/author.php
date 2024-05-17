<?php
class ModelExtensionMazaBlogAuthor extends Model {
	public function getAuthor($author_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_author a LEFT JOIN " . DB_PREFIX . "mz_blog_author_description ad ON (a.author_id = ad.author_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND a.author_id = '" . (int)$author_id . "'");

		return $query->row;
	}

	public function getAuthors($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "mz_blog_author a LEFT JOIN " . DB_PREFIX . "mz_blog_author_description ad ON (a.author_id = ad.author_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sort_data = array(
				'name',
				'sort_order'
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
		} else {
			$author_data = $this->cache->get('author');

			if (!$author_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_author a LEFT JOIN " . DB_PREFIX . "mz_blog_author_description ad ON (a.author_id = ad.author_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ad.name");

				$author_data = $query->rows;

				$this->cache->set('author', $author_data);
			}

			return $author_data;
		}
	}
}