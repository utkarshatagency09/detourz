<?php
class ModelExtensionMazaTestimonial extends Model {
	public function addTestimonial($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_testimonial SET rating = '" . (int)$data['rating'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', email = '" . $this->db->escape($data['email']) . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW()");

		$testimonial_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_testimonial SET image = '" . $this->db->escape($data['image']) . "' WHERE testimonial_id = '" . (int)$testimonial_id . "'");
		}

		foreach ($data['testimonial_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_testimonial_description SET testimonial_id = '" . (int)$testimonial_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', `extra` = '" . $this->db->escape($value['extra']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
                
		if (isset($data['testimonial_store'])) {
			foreach ($data['testimonial_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_testimonial_to_store SET testimonial_id = '" . (int)$testimonial_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->cache->delete('testimonial');

		return $testimonial_id;
	}

	public function editTestimonial($testimonial_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_testimonial SET rating = '" . (int)$data['rating'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', email = '" . $this->db->escape($data['email']) . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_testimonial SET image = '" . $this->db->escape($data['image']) . "' WHERE testimonial_id = '" . (int)$testimonial_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_testimonial_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		foreach ($data['testimonial_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_testimonial_description SET testimonial_id = '" . (int)$testimonial_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', `extra` = '" . $this->db->escape($value['extra']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
                
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_testimonial_to_store WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		if (isset($data['testimonial_store'])) {
			foreach ($data['testimonial_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_testimonial_to_store SET testimonial_id = '" . (int)$testimonial_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->cache->delete('testimonial');
	}

	public function deleteTestimonial($testimonial_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_testimonial WHERE testimonial_id = '" . (int)$testimonial_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_testimonial_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_testimonial_to_store WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		$this->cache->delete('testimonial');
	}

	public function getTestimonial($testimonial_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_testimonial t LEFT JOIN " . DB_PREFIX . "mz_testimonial_description td ON (t.testimonial_id = td.testimonial_id) WHERE t.testimonial_id = '" . (int)$testimonial_id . "' AND td.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");

		return $query->row;
	}

	public function getTestimonials($data = array()) {
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "mz_testimonial t LEFT JOIN " . DB_PREFIX . "mz_testimonial_description td ON (t.testimonial_id = td.testimonial_id) WHERE td.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND td.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
                
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND t.status = '" . (int)$data['filter_status'] . "'";
		}
                
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND t.date_added = '" . $this->db->escape($data['filter_date_added']) . "'";
		}
                
		if (!empty($data['filter_rating'])) {
			$sql .= " AND t.rating = '" . $this->db->escape($data['filter_rating']) . "'";
		}

		$sql .= " GROUP BY t.testimonial_id";

		$sort_data = array(
			'name',
			'sort_order',
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

	public function getTestimonialDescriptions($testimonial_id) {
		$testimonial_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_testimonial_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		foreach ($query->rows as $result) {
			$testimonial_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'extra'            => $result['extra'],
				'description'      => $result['description']
			);
		}

		return $testimonial_description_data;
	}
	
	public function getTestimonialStores($testimonial_id) {
		$testimonial_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_testimonial_to_store WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		foreach ($query->rows as $result) {
			$testimonial_store_data[] = $result['store_id'];
		}

		return $testimonial_store_data;
	}

	public function getTotalTestimonials($data = array()) {
		$sql = "SELECT COUNT(DISTINCT t.testimonial_id) AS total FROM " . DB_PREFIX . "mz_testimonial t LEFT JOIN " . DB_PREFIX . "mz_testimonial_description td ON (t.testimonial_id = td.testimonial_id) WHERE td.language_id = '" . (int)$this->config->get('config_language_id') . "'";
                
		if (!empty($data['filter_name'])) {
			$sql .= " AND td.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
                
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND t.status = '" . (int)$data['filter_status'] . "'";
		}
                
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND t.date_added = '" . $this->db->escape($data['filter_date_added']) . "'";
		}
                
		if (!empty($data['filter_rating'])) {
			$sql .= " AND t.rating = '" . $this->db->escape($data['filter_rating']) . "'";
		}
                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}