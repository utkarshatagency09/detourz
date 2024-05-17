<?php
class ModelExtensionMazaFormRecord extends Model {
	public function deleteRecord(int $form_record_id): void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_record_value WHERE form_record_id = '" . (int)$form_record_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_record WHERE form_record_id = '" . (int)$form_record_id . "'");
	}

	public function getRecord(int $form_record_id): array {
		$query = $this->db->query("SELECT *, (SELECT name FROM `" . DB_PREFIX . "mz_form_description` WHERE form_id = r.form_id AND language_id = '" . (int)$this->config->get('config_language_id') . "') form, (SELECT name FROM `" . DB_PREFIX . "language` WHERE language_id = r.language_id) language, (SELECT title FROM `" . DB_PREFIX . "currency` WHERE currency_id = r.currency_id) currency, (SELECT name FROM `" . DB_PREFIX . "store` WHERE store_id = r.store_id) store, (SELECT name FROM `" . DB_PREFIX . "product_description` WHERE product_id = r.product_id AND language_id = '" . (int)$this->config->get('config_language_id') . "') product, (SELECT name FROM `" . DB_PREFIX . "category_description` WHERE category_id = r.category_id AND language_id = '" . (int)$this->config->get('config_language_id') . "') category, (SELECT name FROM `" . DB_PREFIX . "manufacturer` WHERE manufacturer_id = r.manufacturer_id AND language_id = '" . (int)$this->config->get('config_language_id') . "') manufacturer FROM " . DB_PREFIX . "mz_form_record r WHERE r.form_record_id = '" . (int)$form_record_id . "'");
                
		return $query->row;
	}

	public function getRecords(array $data = array()): array {
		$sql = "SELECT *, (SELECT name FROM `" . DB_PREFIX . "mz_form_description` WHERE form_id = r.form_id AND language_id = '" . (int)$this->config->get('config_language_id') . "') form, (SELECT CONCAT(firstname, ' ', lastname) FROM `" . DB_PREFIX . "customer` WHERE customer_id = r.customer_id) customer, (SELECT name FROM `" . DB_PREFIX . "language` WHERE language_id = r.language_id) language FROM " . DB_PREFIX . "mz_form_record r";

		if (!empty($data['filter_field'])){
			$sql .= " LEFT JOIN " . DB_PREFIX . "mz_form_record_value rv ON (r.form_record_id = rv.form_record_id)";
		}

		$where = array();

		if (!empty($data['filter_form_id'])) {
			$where[] = "r.form_id = '" . (int)$data['filter_form_id'] . "'";
		}

		if (!empty($data['filter_language_id'])) {
			$where[] = "r.language_id = '" . (int)$data['filter_language_id'] . "'";
		}
                
		if (!empty($data['filter_customer_id'])) {
			$where[] = "r.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_product_id'])) {
			$where[] = "r.product_id = '" . (int)$data['filter_product_id'] . "'";
		}

		if (!empty($data['filter_category_id'])) {
			$where[] = "r.category_id = '" . (int)$data['filter_category_id'] . "'";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$where[] = "r.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		if (!empty($data['filter_date_min'])) {
			$where[] = "r.date_added >= '" . $this->db->escape($data['filter_date_min']) . "'";
		}

		if (!empty($data['filter_date_max'])) {
			$where[] = "r.date_added <= '" . $this->db->escape($data['filter_date_max']) . "'";
		}

		if (!empty($data['filter_field'])){
			if ($data['filter_field']['match'] == 'exact'){ // Exact match
				$where[] = "rv.name = '" . $this->db->escape($data['filter_field']['name']) . "' AND rv.value = '" . $this->db->escape($data['filter_field']['value']) . "'";
			}
			else if ($data['filter_field']['match'] == 'contain'){ // contain match
				$where[] = "rv.name = '" . $this->db->escape($data['filter_field']['name']) . "' AND rv.value LIKE '%" . $this->db->escape($data['filter_field']['value']) . "%'";
			}
			else if ($data['filter_field']['match'] == 'regexp'){ // regexp match
				$where[] = "rv.name = '" . $this->db->escape($data['filter_field']['name']) . "' AND rv.value RLIKE '" . $this->db->escape($data['filter_field']['value']) . "'";
			}
		}

		if($where){
			$sql .= " WHERE " . implode(' AND ', $where);
		}

		$sql .= " GROUP BY r.form_record_id";

		$sort_data = array(
			'form',
			'customer',
			'r.language_id',
			'r.ip_address',
			'r.date_added',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_added";
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

	public function getTotalRecords(array $data = array()): int {
		$sql = "SELECT COUNT(DISTINCT r.form_record_id) AS total FROM " . DB_PREFIX . "mz_form_record r";

		if (!empty($data['filter_field'])){
			$sql .= " LEFT JOIN " . DB_PREFIX . "mz_form_record_value rv ON (r.form_record_id = rv.form_record_id)";
		}

		$where = array();

		if (!empty($data['filter_form_id'])) {
			$where[] = "r.form_id = '" . (int)$data['filter_form_id'] . "'";
		}

		if (!empty($data['filter_language_id'])) {
			$where[] = "r.language_id = '" . (int)$data['filter_language_id'] . "'";
		}
                
		if (!empty($data['filter_customer_id'])) {
			$where[] = "r.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_product_id'])) {
			$where[] = "r.product_id = '" . (int)$data['filter_product_id'] . "'";
		}

		if (!empty($data['filter_category_id'])) {
			$where[] = "r.category_id = '" . (int)$data['filter_category_id'] . "'";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$where[] = "r.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		if (!empty($data['filter_date_min'])) {
			$where[] = "DATE(r.date_added) >= '" . $this->db->escape($data['filter_date_min']) . "'";
		}

		if (!empty($data['filter_date_max'])) {
			$where[] = "DATE(r.date_added) <= '" . $this->db->escape($data['filter_date_max']) . "'";
		}

		if (!empty($data['filter_field'])){
			if ($data['filter_field']['match'] == 'exact'){ // Exact match
				$where[] = "rv.name = '" . $this->db->escape($data['filter_field']['name']) . "' AND rv.value = '" . $this->db->escape($data['filter_field']['value']) . "'";
			}
			else if ($data['filter_field']['match'] == 'contain'){ // contain match
				$where[] = "rv.name = '" . $this->db->escape($data['filter_field']['name']) . "' AND rv.value LIKE '%" . $this->db->escape($data['filter_field']['value']) . "%'";
			}
			else if ($data['filter_field']['match'] == 'regexp'){ // regexp match
				$where[] = "rv.name = '" . $this->db->escape($data['filter_field']['name']) . "' AND rv.value RLIKE '" . $this->db->escape($data['filter_field']['value']) . "'";
			}
		}

		if($where){
			$sql .= " WHERE " . implode(' AND ', $where);
		}
                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getRecordValues(int $form_record_id): array {
		$value_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_record_value WHERE form_record_id = '" . (int)$form_record_id . "'");

		foreach ($query->rows as $result) {
			$value_data[$result['name']] = $result['value'];
		}

		return $value_data;
	}
}