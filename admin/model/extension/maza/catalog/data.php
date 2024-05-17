<?php
class ModelExtensionMazaCatalogData extends Model {
	public function addData(array $data) : int {
		$sql = "INSERT INTO `" . DB_PREFIX . "mz_catalog_data` SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', customer = '" . (int)$data['customer'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', page = '" . $this->db->escape($data['page']) . "', hook = '" . $this->db->escape($data['hook']) . "', is_filter = '" . (int)$data['is_filter'] . "', filter_special = '" . (int)$data['filter_special'] . "', sub_category = '" . (int)$data['sub_category'] . "', setting = '" . $this->db->escape(json_encode($data['setting'])) . "', date_added = NOW(), date_modified = NOW()";

		if(!is_null($data['filter_quantity_min']) && $data['filter_quantity_min'] !== ''){
			$sql .= ", filter_quantity_min = '" . (int)$data['filter_quantity_min'] . "'";
		}

		if(!is_null($data['filter_quantity_max']) && $data['filter_quantity_max'] !== ''){
			$sql .= ", filter_quantity_max = '" . (int)$data['filter_quantity_max'] . "'";
		}

		if(!is_null($data['filter_price_min']) && $data['filter_price_min'] !== ''){
			$sql .= ", filter_price_min = '" . (float)$data['filter_price_min'] . "'";
		}

		if(!is_null($data['filter_price_max']) && $data['filter_price_max'] !== ''){
			$sql .= ", filter_price_max = '" . (float)$data['filter_price_max'] . "'";
		}

		if(!is_null($data['date_start']) && $data['date_start'] !== '' && $data['date_start'] !== '0000-00-00 00:00'){
			$sql .= ", date_start = '" . $this->db->escape($data['date_start']) . "'";
		}

		if(!is_null($data['date_end']) && $data['date_end'] !== '' && $data['date_start'] !== '0000-00-00 00:00'){
			$sql .= ", date_end = '" . $this->db->escape($data['date_end']) . "'";
		}

		$this->db->query($sql);

		$data_id = $this->db->getLastId();

		// Data store
		if (isset($data['data_store'])) {
			foreach ($data['data_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_catalog_data_to_store SET data_id = '" . (int)$data_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		// Data product
		if (isset($data['data_product'])) {
			foreach ($data['data_product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_catalog_data_to_product SET data_id = '" . (int)$data_id . "', product_id = '" . (int)$product_id . "'");
			}
		}
		
		// Data category
		if (isset($data['data_category'])) {
			foreach ($data['data_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_catalog_data_to_category SET data_id = '" . (int)$data_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		// Data manufacturer
		if (isset($data['data_manufacturer'])) {
			foreach ($data['data_manufacturer'] as $manufacturer_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_catalog_data_to_manufacturer SET data_id = '" . (int)$data_id . "', manufacturer_id = '" . (int)$manufacturer_id . "'");
			}
		}

		// Data filter
		if (isset($data['data_filter'])) {
			foreach ($data['data_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_catalog_data_to_filter SET data_id = '" . (int)$data_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}
		
		return $data_id;
	}

	public function editData(int $data_id, array $data) : void {
		$sql = "UPDATE `" . DB_PREFIX . "mz_catalog_data` SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', customer = '" . (int)$data['customer'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', page = '" . $this->db->escape($data['page']) . "', hook = '" . $this->db->escape($data['hook']) . "', is_filter = '" . (int)$data['is_filter'] . "', filter_special = '" . (int)$data['filter_special'] . "', sub_category = '" . (int)$data['sub_category'] . "', setting = '" . $this->db->escape(json_encode($data['setting'])) . "', date_modified = NOW()";

		if(!is_null($data['filter_quantity_min']) && $data['filter_quantity_min'] !== ''){
			$sql .= ", filter_quantity_min = '" . (int)$data['filter_quantity_min'] . "'";
		} else {
			$sql .= ", filter_quantity_min = NULL";
		}

		if(!is_null($data['filter_quantity_max']) && $data['filter_quantity_max'] !== ''){
			$sql .= ", filter_quantity_max = '" . (int)$data['filter_quantity_max'] . "'";
		} else {
			$sql .= ", filter_quantity_max = NULL";
		}

		if(!is_null($data['filter_price_min']) && $data['filter_price_min'] !== ''){
			$sql .= ", filter_price_min = '" . (float)$data['filter_price_min'] . "'";
		} else {
			$sql .= ", filter_price_min = NULL";
		}

		if(!is_null($data['filter_price_max']) && $data['filter_price_max'] !== ''){
			$sql .= ", filter_price_max = '" . (float)$data['filter_price_max'] . "'";
		} else {
			$sql .= ", filter_price_max = NULL";
		}

		if(!is_null($data['date_start']) && $data['date_start'] !== '' && $data['date_start'] !== '0000-00-00 00:00'){
			$sql .= ", date_start = '" . $this->db->escape($data['date_start']) . "'";
		} else {
			$sql .= ", date_start = NULL";
		}

		if(!is_null($data['date_end']) && $data['date_end'] !== '' && $data['date_start'] !== '0000-00-00 00:00'){
			$sql .= ", date_end = '" . $this->db->escape($data['date_end']) . "'";
		} else {
			$sql .= ", date_end = NULL";
		}

		$this->db->query($sql . " WHERE data_id = '" . (int)$data_id . "'");

		// Data store
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_store WHERE data_id = '" . (int)$data_id . "'");

		if (isset($data['data_store'])) {
			foreach ($data['data_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_catalog_data_to_store SET data_id = '" . (int)$data_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		// Data product
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_product WHERE data_id = '" . (int)$data_id . "'");

		if (isset($data['data_product'])) {
			foreach ($data['data_product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_catalog_data_to_product SET data_id = '" . (int)$data_id . "', product_id = '" . (int)$product_id . "'");
			}
		}
		
		// Data category
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_category WHERE data_id = '" . (int)$data_id . "'");

		if (isset($data['data_category'])) {
			foreach ($data['data_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_catalog_data_to_category SET data_id = '" . (int)$data_id . "', category_id = '" . (int)$category_id . "'");
			}
		}
		
		// Data manufacturer
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_manufacturer WHERE data_id = '" . (int)$data_id . "'");

		if (isset($data['data_manufacturer'])) {
			foreach ($data['data_manufacturer'] as $manufacturer_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_catalog_data_to_manufacturer SET data_id = '" . (int)$data_id . "', manufacturer_id = '" . (int)$manufacturer_id . "'");
			}
		}
		
		// Data filter
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_filter WHERE data_id = '" . (int)$data_id . "'");

		if (isset($data['data_filter'])) {
			foreach ($data['data_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_catalog_data_to_filter SET data_id = '" . (int)$data_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}
	}
        
	public function copyData(int $data_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_catalog_data WHERE data_id = '" . (int)$data_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['status'] = '0';
			$data['setting'] = json_decode($data['setting'], true);
			$data['setting']['popup_unique_id'] = 'mz-popup-' . mt_rand(); // Change unique id to make it different than a data, which coping from
			$data['data_store'] = $this->getDataStores($data_id);
			$data['data_product'] = $this->getDataProducts($data_id);
			$data['data_category'] = $this->getDataCategories($data_id);
			$data['data_manufacturer'] = $this->getDataManufacturers($data_id);
			$data['data_filter'] = $this->getDataFilter($data_id);

			return $this->addData($data);
		}
	}

	public function deleteData(int $data_id) : void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_store WHERE data_id = '" . (int)$data_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_product WHERE data_id = '" . (int)$data_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_category WHERE data_id = '" . (int)$data_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_manufacturer WHERE data_id = '" . (int)$data_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_filter WHERE data_id = '" . (int)$data_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data WHERE data_id = '" . (int)$data_id . "'");
	}

	public function getData(int $data_id) : array {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_catalog_data WHERE data_id = '" . (int)$data_id . "' LIMIT 1");
                
		if($query->row){
			$query->row['setting'] = json_decode($query->row['setting'], true);
		}

		return $query->row;
	}

	public function getDatas($data = array()) : array {
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "mz_catalog_data d";

		if (!empty($data['filter_product_id'])) {
			$sql .= " RIGHT JOIN " . DB_PREFIX . "mz_catalog_data_to_product d2p ON (d.data_id = d2p.data_id AND d2p.product_id = '" . (int)$data['filter_product_id'] . "')";
		}

		if (!empty($data['filter_category_id'])) {
			$sql .= " RIGHT JOIN " . DB_PREFIX . "mz_catalog_data_to_category d2c ON (d.data_id = d2c.data_id AND d2c.category_id = '" . (int)$data['filter_category_id'] . "')";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " RIGHT JOIN " . DB_PREFIX . "mz_catalog_data_to_manufacturer d2m ON (d.data_id = d2m.data_id AND d2m.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "')";
		}

		$sql .= " WHERE d.data_id IS NOT NULL";

		if (!empty($data['filter_name'])) {
			$sql .= " AND d.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_page'])) {
			$sql .= " AND d.page = '" . $this->db->escape($data['filter_page']) . "'";
		}

		if (!empty($data['filter_hook'])) {
			$sql .= " AND d.hook = '" . $this->db->escape($data['filter_hook']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND d.date_end <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
                
		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND d.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY d.data_id";

		$sort_data = array(
			'd.name',
			'd.sort_order',
			'd.page',
			'd.hook',
			'd.date_modified',
			'd.status',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY d.sort_order";
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

	public function getTotalDatas($data = array()) : int {
		$sql = "SELECT COUNT(DISTINCT d.data_id) AS total FROM " . DB_PREFIX . "mz_catalog_data d";
                
		if (!empty($data['filter_product_id'])) {
			$sql .= " RIGHT JOIN " . DB_PREFIX . "mz_catalog_data_to_product d2p ON (d.data_id = d2p.data_id AND d2p.product_id = '" . (int)$data['filter_product_id'] . "')";
		}

		if (!empty($data['filter_category_id'])) {
			$sql .= " RIGHT JOIN " . DB_PREFIX . "mz_catalog_data_to_category d2c ON (d.data_id = d2c.data_id AND d2c.category_id = '" . (int)$data['filter_category_id'] . "')";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " RIGHT JOIN " . DB_PREFIX . "mz_catalog_data_to_manufacturer d2m ON (d.data_id = d2m.data_id AND d2m.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "')";
		}

		$sql .= " WHERE d.data_id IS NOT NULL";

		if (!empty($data['filter_name'])) {
			$sql .= " AND d.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_page'])) {
			$sql .= " AND d.page = '" . $this->db->escape($data['filter_page']) . "'";
		}

		if (!empty($data['filter_hook'])) {
			$sql .= " AND d.hook = '" . $this->db->escape($data['filter_hook']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND d.date_end <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
                
		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND d.status = '" . (int)$data['filter_status'] . "'";
		}
                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getDataStores(int $data_id) : array {
		$data_store_data = array();

		$query = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "mz_catalog_data_to_store WHERE data_id = '" . (int)$data_id . "'");

		foreach ($query->rows as $result) {
			$data_store_data[] = $result['store_id'];
		}

		return $data_store_data;
	}

	public function getDataProducts(int $data_id) : array {
		$data_product_data = array();

		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "mz_catalog_data_to_product WHERE data_id = '" . (int)$data_id . "'");

		foreach ($query->rows as $result) {
			$data_product_data[] = $result['product_id'];
		}

		return $data_product_data;
	}
        
	public function getDataCategories(int $data_id) : array {
		$data_category_data = array();

		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "mz_catalog_data_to_category WHERE data_id = '" . (int)$data_id . "'");

		foreach ($query->rows as $result) {
			$data_category_data[] = $result['category_id'];
		}

		return $data_category_data;
	}

	public function getDataManufacturers(int $data_id) : array {
		$data_manufacturer_data = array();

		$query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "mz_catalog_data_to_manufacturer WHERE data_id = '" . (int)$data_id . "'");

		foreach ($query->rows as $result) {
			$data_manufacturer_data[] = $result['manufacturer_id'];
		}

		return $data_manufacturer_data;
	}

	public function getDataFilter(int $data_id) : array {
		$data_filter_data = array();

		$query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "mz_catalog_data_to_filter WHERE data_id = '" . (int)$data_id . "'");

		foreach ($query->rows as $result) {
			$data_filter_data[] = $result['filter_id'];
		}

		return $data_filter_data;
	}
}