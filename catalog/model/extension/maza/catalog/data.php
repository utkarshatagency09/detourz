<?php
class ModelExtensionMazaCatalogData extends Model{
    /**
     * Get active datas of category
     */
    public function getCategoryDatas(int $category_id): array {
        $data_data = array();

        $sql = "SELECT d.data_id, d.hook, d.sort_order, d.setting FROM `" . DB_PREFIX . "mz_catalog_data_to_category` d2c LEFT JOIN `" . DB_PREFIX . "mz_catalog_data` d ON (d.data_id = d2c.data_id) LEFT JOIN " . DB_PREFIX . "category_path cp ON (cp.path_id = d2c.category_id AND d.sub_category = 1) LEFT JOIN `" . DB_PREFIX . "mz_catalog_data_to_store` d2s ON (d.data_id = d2s.data_id) WHERE d.page = 'category' AND (d.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' OR d.customer_group_id = 0)";

        // customer value 0 = ALL, -1 = Guest, 1 = logged
        if($this->customer->isLogged()){
            $sql .= " AND d.customer >= 0";
        } else {
            $sql .= " AND d.customer <= 0";
        }

        $sql .= " AND d.status = 1 AND (d.date_start IS NULL OR d.date_start <= NOW()) AND (d.date_end IS NULL OR d.date_end > NOW()) AND (cp.category_id = '" . $category_id . "' OR d2c.category_id = '" . $category_id . "') AND d2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY d.data_id ORDER BY d.sort_order ASC";

        $query = $this->db->query($sql);
        
        foreach($query->rows as $row){
            $data_data[] = array(
                'data_id' => $row['data_id'],
                'hook' => $row['hook'],
                'sort_order' => $row['sort_order'],
                'setting' => json_decode($row['setting'], true),
            );
        }
        
        return $data_data;
    }

    /**
     * Get active datas of manufacturers
     */
    public function getManufacturerDatas(int $manufacturer_id): array {
        $data_data = array();

        $sql = "SELECT d.data_id, d.hook, d.sort_order, d.setting FROM `" . DB_PREFIX . "mz_catalog_data_to_manufacturer` d2m LEFT JOIN `" . DB_PREFIX . "mz_catalog_data` d ON (d.data_id = d2m.data_id) LEFT JOIN `" . DB_PREFIX . "mz_catalog_data_to_store` d2s ON (d.data_id = d2s.data_id) WHERE d2m.manufacturer_id = '" . (int)$manufacturer_id . "' AND d.page = 'manufacturer' AND (d.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' OR d.customer_group_id = 0)";

        // customer value 0 = ALL, -1 = Guest, 1 = logged
        if($this->customer->isLogged()){
            $sql .= " AND d.customer >= 0";
        } else {
            $sql .= " AND d.customer <= 0";
        }

        $sql .= " AND d.status = 1 AND (d.date_start IS NULL OR d.date_start <= NOW()) AND (d.date_end IS NULL OR d.date_end > NOW()) AND d2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY d.data_id ORDER BY d.sort_order ASC";

        $query = $this->db->query($sql);
        
        foreach($query->rows as $row){
            $data_data[] = array(
                'data_id' => $row['data_id'],
                'hook' => $row['hook'],
                'sort_order' => $row['sort_order'],
                'setting' => json_decode($row['setting'], true),
            );
        }
        
        return $data_data;
    }

    /**
     * Get active datas of product
     */
    public function getProductDatas(array $data): array {
        $data_data = array();

        // Custom
        $sql = "SELECT d.data_id, d.hook, d.sort_order, d.setting FROM `" . DB_PREFIX . "mz_catalog_data` d LEFT JOIN `" . DB_PREFIX . "mz_catalog_data_to_product` d2p ON (d.data_id = d2p.data_id) LEFT JOIN `" . DB_PREFIX . "mz_catalog_data_to_store` d2s ON (d.data_id = d2s.data_id) WHERE d.page = 'product' AND (d.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' OR d.customer_group_id = 0)";

        // customer value 0 = ALL, -1 = Guest, 1 = logged
        if($this->customer->isLogged()){
            $sql .= " AND d.customer >= 0";
        } else {
            $sql .= " AND d.customer <= 0";
        }

        $sql .= " AND d2p.product_id = '" . (int)$data['product_id'] . "' AND d.is_filter = 0 AND d.status = 1 AND (d.date_start IS NULL OR d.date_start <= NOW()) AND (d.date_end IS NULL OR d.date_end > NOW()) AND d2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY d.data_id ORDER BY d.sort_order ASC";
        
        $query = $this->db->query($sql);
        
        foreach($query->rows as $row){
            $data_data[] = array(
                'data_id' => $row['data_id'],
                'hook' => $row['hook'],
                'sort_order' => $row['sort_order'],
                'setting' => json_decode($row['setting'], true),
            );
        }

        // Filter
        $sql = "SELECT d.data_id, d.hook, d.sort_order, d.setting FROM `" . DB_PREFIX . "mz_catalog_data` d LEFT JOIN `" . DB_PREFIX . "mz_catalog_data_to_manufacturer` d2m ON (d.data_id = d2m.data_id) LEFT JOIN `" . DB_PREFIX . "mz_catalog_data_to_category` d2c ON (d.data_id = d2c.data_id) LEFT JOIN " . DB_PREFIX . "category_path cp ON (cp.path_id = d2c.category_id AND d.sub_category = 1) LEFT JOIN `" . DB_PREFIX . "mz_catalog_data_to_filter` d2f ON (d.data_id = d2f.data_id) LEFT JOIN `" . DB_PREFIX . "product_filter` pf ON (pf.product_id = '" . (int)$data['product_id'] . "' AND pf.filter_id = d2f.filter_id) LEFT JOIN `" . DB_PREFIX . "mz_catalog_data_to_store` d2s ON (d.data_id = d2s.data_id) WHERE d.page = 'product' AND (d.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' OR d.customer_group_id = 0)";

        // customer value 0 = ALL, -1 = Guest, 1 = logged
        if($this->customer->isLogged()){
            $sql .= " AND d.customer >= 0";
        } else {
            $sql .= " AND d.customer <= 0";
        }

        $sql .= " AND (d2m.data_id IS NULL OR d2m.manufacturer_id = '" . (int)$data['manufacturer_id'] . "') AND (d2f.data_id IS NULL OR pf.product_id IS NOT NULL) AND (d2c.data_id IS NULL OR d2c.category_id = '" . (int)($data['category_id']??0) . "' OR cp.category_id = '" . (int)($data['category_id']??0) . "')";

        if (!is_null($data['special']) && (float)$data['special'] >= 0) {
            $sql .= " AND (d.filter_price_min IS NULL OR d.filter_price_min <= '" . (float)$data['special'] . "') AND (d.filter_price_max IS NULL OR d.filter_price_max >= '" . (float)$data['special'] . "')";
        } else {
            $sql .= " AND d.filter_special = 0 AND (d.filter_price_min IS NULL OR d.filter_price_min <= '" . (float)$data['price'] . "') AND (d.filter_price_max IS NULL OR d.filter_price_max >= '" . (float)$data['price'] . "')";
        }

        $sql .= " AND (d.filter_quantity_min IS NULL OR d.filter_quantity_min <= '" . (int)$data['quantity'] . "') AND (d.filter_quantity_max IS NULL OR d.filter_quantity_max >= '" . (int)$data['quantity'] . "') AND d.is_filter = 1 AND d.status = 1 AND (d.date_start IS NULL OR d.date_start <= NOW()) AND (d.date_end IS NULL OR d.date_end > NOW()) AND d2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY d.data_id ORDER BY d.sort_order ASC";

        $query = $this->db->query($sql);

        foreach($query->rows as $row){
            $data_data[] = array(
                'data_id' => $row['data_id'],
                'hook' => $row['hook'],
                'sort_order' => $row['sort_order'],
                'setting' => json_decode($row['setting'], true),
            );
        }
        
        return $data_data;
    }
}