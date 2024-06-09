<?php
class ModelExtensionMazaCatalogProductLabel extends Model {
	public function getLabels() : array {
		$sql = "SELECT pl.*, (SELECT name FROM " . DB_PREFIX . "mz_product_label_description pld WHERE pld.product_label_id = pl.product_label_id AND pld.language_id = '" . (int)$this->config->get('config_language_id') . "') name FROM " . DB_PREFIX . "mz_product_label pl LEFT JOIN " . DB_PREFIX . "mz_product_label_customer_group pl2cg ON (pl2cg.product_label_id = pl.product_label_id) LEFT JOIN " . DB_PREFIX . "mz_product_label_to_store pl2s ON (pl2s.product_label_id = pl.product_label_id) WHERE pl.status = 1 AND pl2cg.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pl2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		// customer value 0 = ALL, -1 = Guest, 1 = logged
        if($this->customer->isLogged()){
            $sql .= " AND pl.customer >= 0";
        } else {
            $sql .= " AND pl.customer <= 0";
        }

		$query = $this->db->query($sql . " GROUP BY pl.product_label_id ORDER BY pl.sort_order ASC");

		$data_labels = array();

		foreach($query->rows as $row){
			$data_labels[] = array(
				'product_label_id' 		=> $row['product_label_id'],
				'name' 					=> $row['name'],
				'type' 					=> $row['type'],
				'setting' 				=> json_decode($row['setting'], true),
				'product_page_status' 	=> $row['product_page_status'],
				'style'					=> $this->getStyle($row['product_label_id'])
			);
		}

		return $data_labels;
	}

	public function getStyle(int $product_label_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_product_label_style` WHERE product_label_id = '" . (int)$product_label_id . "' AND skin_id = '" . (int)$this->mz_skin_config->get('skin_id') . "'");

		if ($query->row) {
			return $query->row;
		}

		return array(
			'position' => 'top_left',
			'shape' => 'square',
			'visibility' => 'always',
			'color_text' => '',
			'color_bg' => '',
			'custom_class' => '',
			'product_page_position' => 'top_right',
			'product_page_visibility' => 'always',
		);
	}

	public function isProductInCategory(int $product_id, array $category) : bool {
		$query = $this->db->query("SELECT category_id FROM `" . DB_PREFIX . "product_to_category` WHERE product_id = '" . (int)$product_id . "' AND category_id IN ('" . implode("','", array_map('intval', $category)) . "')");

		return (bool)$query->num_rows;
	}

	public function isProductInFilter(int $product_id, array $filter) : bool {
		$query = $this->db->query("SELECT filter_id FROM `" . DB_PREFIX . "product_filter` WHERE product_id = '" . (int)$product_id . "' AND filter_id IN ('" . implode("','", array_map('intval', $filter)) . "')");

		return (bool)$query->num_rows;
	}
}