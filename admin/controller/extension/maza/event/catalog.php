<?php
class ControllerExtensionMazaEventCatalog extends Controller {
	/**
	 * Clear category connection when category deleted
	 * @param string $route controller route
	 * @param array $param parameter of method
	 * @return void
	 */
	public function deleteCategory(string $route, array $param): void {
		$category_id = $param[0];

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_category WHERE category_id = '" . (int) $category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_to_category WHERE category_id = '" . (int) $category_id . "'");
	}

	/**
	 * Clear manufacturer connection when manufacturer deleted
	 * @param string $route controller route
	 * @param array $param parameter of method
	 * @return void
	 */
	public function deleteManufacturer(string $route, array $param): void {
		$manufacturer_id = $param[0];

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_manufacturer WHERE manufacturer_id = '" . (int) $manufacturer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_subscribe WHERE manufacturer_id = '" . (int) $manufacturer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_manufacturer_description WHERE manufacturer_id = '" . (int) $manufacturer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_manufacturer_to_layout WHERE manufacturer_id = '" . (int) $manufacturer_id . "'");
	}

	/**
	 * Clear product connection when product deleted
	 * @param string $route controller route
	 * @param array $param parameter of method
	 * @return void
	 */
	public function deleteProduct(string $route, array $param): void {
		$product_id = $param[0];

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_product WHERE product_id = '" . (int) $product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_product WHERE product_id = '" . (int) $product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_value_to_product WHERE product_id = '" . (int) $product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_to_tags WHERE product_id = '" . (int) $product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_video WHERE product_id = '" . (int) $product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_video_description WHERE product_id = '" . (int) $product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_audio WHERE product_id = '" . (int) $product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_audio_description WHERE product_id = '" . (int) $product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification WHERE product_id = '" . (int) $product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_subscribe WHERE product_id = '" . (int) $product_id . "'");
	}
}