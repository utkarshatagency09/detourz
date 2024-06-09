<?php
class ModelExtensionMazaCatalogCategory extends Model{
    public function getCategoryPath(int $category_id): string {
		$query = $this->db->query("SELECT path_id FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$category_id . "' ORDER BY `level` ASC");

		return implode('_', array_column($query->rows, 'path_id'));
	}

    public function getProductCategory(int $product_id): int {
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' LIMIT 1");

		if($query->num_rows){
            return $query->row['category_id'];
        }
        return 0;
	}
	
	public function getTotalOrders(int $category_id, bool $sub_category = false): int {
		$sql = "SELECT COUNT(DISTINCT o.order_id) AS total";

		if ($sub_category) {
			$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id) LEFT JOIN " . DB_PREFIX . "order_product op ON (p2c.product_id = op.product_id)";
		} else {
			$sql .= " FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "order_product op ON (p2c.product_id = op.product_id)";
		}
			
		$sql .= " LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = op.order_id)";

		if ($sub_category) {
			$sql .= " WHERE cp.path_id = '" . (int)$category_id . "'";
		} else {
			$sql .= " WHERE p2c.category_id = '" . (int)$category_id . "'";
		}

		if($this->mz_skin_config->get('catalog_sold_order_statuses')){
			$sql .= " AND o.order_status_id IN (" . implode(',',array_map('intval', $this->mz_skin_config->get('catalog_sold_order_statuses'))) . ")";
		}

		$query = $this->db->query($sql);
			
		return $query->row['total'];
	}
}