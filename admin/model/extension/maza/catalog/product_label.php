<?php
class ModelExtensionMazaCatalogProductLabel extends Model {
	public function addLabel(array $data) : int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "mz_product_label` SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', `type` = '" . $this->db->escape($data['type']) . "', setting = '" . $this->db->escape(json_encode($data['setting'])) . "', customer = '" . (int)$data['customer'] . "', product_page_status = '" . (int)$data['product_page_status'] . "', date_added = NOW(), date_modified = NOW()");

		$product_label_id = $this->db->getLastId();

		// Description
		foreach ($data['label_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_label_description SET product_label_id = '" . (int)$product_label_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		// Label store
		if (isset($data['label_store'])) {
			foreach ($data['label_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_label_to_store SET product_label_id = '" . (int)$product_label_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		// Customer Group
		if (isset($data['label_customer_group'])) {
			foreach ($data['label_customer_group'] as $customer_group_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_label_customer_group SET product_label_id = '" . (int)$product_label_id . "', customer_group_id = '" . (int)$customer_group_id . "'");
			}
		}
		
		return $product_label_id;
	}

	public function editLabel(int $product_label_id, array $data) : void {
		$this->db->query("UPDATE `" . DB_PREFIX . "mz_product_label` SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', `type` = '" . $this->db->escape($data['type']) . "', setting = '" . $this->db->escape(json_encode($data['setting'])) . "', customer = '" . (int)$data['customer'] . "', product_page_status = '" . (int)$data['product_page_status'] . "', date_modified = NOW() WHERE product_label_id = '" . (int)$product_label_id . "'");

		// Description
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_label_description WHERE product_label_id = '" . (int)$product_label_id . "'");

		foreach ($data['label_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_label_description SET product_label_id = '" . (int)$product_label_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		// Label store
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_label_to_store WHERE product_label_id = '" . (int)$product_label_id . "'");

		if (isset($data['label_store'])) {
			foreach ($data['label_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_label_to_store SET product_label_id = '" . (int)$product_label_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		// Customer Group
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_label_customer_group WHERE product_label_id = '" . (int)$product_label_id . "'");

		if (isset($data['label_customer_group'])) {
			foreach ($data['label_customer_group'] as $customer_group_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_label_customer_group SET product_label_id = '" . (int)$product_label_id . "', customer_group_id = '" . (int)$customer_group_id . "'");
			}
		}
	}
        
	public function copyLabel(int $product_label_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_product_label WHERE product_label_id = '" . (int)$product_label_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['status'] = '0';
			$data['setting'] = json_decode($data['setting'], true);
			$data['label_store'] = $this->getLabelStores($product_label_id);
			$data['label_customer_group'] = $this->getLabelCustomerGroups($product_label_id);
			$data['label_description'] = $this->getLabelDescriptions($product_label_id);

			$label_id = $this->addLabel($data);

			$this->editStyle($label_id, $this->getStyle($product_label_id));

			return $label_id;
		}
	}

	public function deleteLabel(int $product_label_id) : void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_label_style WHERE product_label_id = '" . (int)$product_label_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_label_to_store WHERE product_label_id = '" . (int)$product_label_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_label_customer_group WHERE product_label_id = '" . (int)$product_label_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_label_description WHERE product_label_id = '" . (int)$product_label_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_label WHERE product_label_id = '" . (int)$product_label_id . "'");
	}

	public function getLabel(int $product_label_id) : array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_product_label WHERE product_label_id = '" . (int)$product_label_id . "'");
                
		if($query->row){
			$query->row['setting'] = json_decode($query->row['setting'], true);
		}

		return $query->row;
	}

	public function getLabels($data = array()) : array {
		$sql = "SELECT * FROM " . DB_PREFIX . "mz_product_label";

		$sort_data = array(
			'name',
			'sort_order',
			'date_modified',
			'status',
			'type'
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

	public function getTotalLabels() : int { 
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_product_label");

		return $query->row['total'];
	}

	public function getLabelStores(int $product_label_id) : array {
		$label_store_data = array();

		$query = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "mz_product_label_to_store WHERE product_label_id = '" . (int)$product_label_id . "'");

		foreach ($query->rows as $result) {
			$label_store_data[] = $result['store_id'];
		}

		return $label_store_data;
	}

	public function getLabelCustomerGroups(int $product_label_id) : array {
		$label_customer_group_data = array();

		$query = $this->db->query("SELECT customer_group_id FROM " . DB_PREFIX . "mz_product_label_customer_group WHERE product_label_id = '" . (int)$product_label_id . "'");

		foreach ($query->rows as $result) {
			$label_customer_group_data[] = $result['customer_group_id'];
		}

		return $label_customer_group_data;
	}

	public function getLabelDescriptions(int $product_label_id): array {
		$label_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_product_label_description WHERE product_label_id = '" . (int)$product_label_id . "'");

		foreach ($query->rows as $result) {
			$label_description_data[$result['language_id']] = array(
				'name'	=> $result['name'],
			);
		}

		return $label_description_data;
	}

	public function editStyle(int $product_label_id, array $data) : void {
		if ($data) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_product_label_style` WHERE product_label_id = '" . (int)$product_label_id . "' AND skin_id = '" . (int)$this->mz_skin_config->get('skin_id') . "'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "mz_product_label_style` SET product_label_id = '" . (int)$product_label_id . "', skin_id = '" . (int)$this->mz_skin_config->get('skin_id') . "', position = '" . $this->db->escape($data['position']) . "', shape = '" . $this->db->escape($data['shape']) . "', visibility = '" . $this->db->escape($data['visibility']) . "', color_text = '" . $this->db->escape($data['color_text']) . "', color_bg = '" . $this->db->escape($data['color_bg']) . "', custom_class = '" . $this->db->escape($data['custom_class']) . "', product_page_position = '" . $this->db->escape($data['product_page_position']) . "', product_page_visibility = '" . $this->db->escape($data['product_page_visibility']) . "'");
		}
	}

	public function getStyle(int $product_label_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_product_label_style` WHERE product_label_id = '" . (int)$product_label_id . "' AND skin_id = '" . (int)$this->mz_skin_config->get('skin_id') . "'");

		return $query->row;
	}
}