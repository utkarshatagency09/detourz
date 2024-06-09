<?php
class ControllerExtensionMazaEventModelCatalogProduct extends Controller {
	public function getProductAfter(string $route, array $param, &$output): void {
        $product_id = $param[0];

        if ($output) {
            $query = $this->db->query("SELECT stock_status_id FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$product_id . "'");

            $output['stock_status_id'] = $query->row['stock_status_id'];
        }
    }

    public function getProductsBefore(string $route, array $param): array {
        if(empty($param[0])){
            return $this->model_extension_maza_catalog_product->getProducts();
        } else {
            return $this->model_extension_maza_catalog_product->getProducts($param[0]);
        }
    }
    
    public function getTotalProductsBefore(string $route, array $param): int {
        if(empty($param[0])){
            return $this->model_extension_maza_catalog_product->getTotalProducts();
        } else {
            return $this->model_extension_maza_catalog_product->getTotalProducts($param[0]);
        }
    }

	public function getProductAttributesBefore() {
		if ($this->mz_document->isRoute('product/compare') && !$this->mz_skin_config->get('catalog_compare_attribute_status')) {
			return array();
		}
	}
}
