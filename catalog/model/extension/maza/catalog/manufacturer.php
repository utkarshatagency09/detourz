<?php
class ModelExtensionMazaCatalogManufacturer extends Model {
	public function getManufacturers(array $data = array()): array {
                $sql = "SELECT m.*";
                
                if(isset($data['sort']) && $data['sort'] == 'o.total_order'){
                    $sql .= ", o.*";
                }
                
                $included_table_product = false;
                
                if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
                        
                        $included_table_product = true;
		} elseif(!empty($data['filter_filter'])) {
			$sql .= " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                        
                        $included_table_product = true;
                }
                
                if(isset($data['sort']) && $data['sort'] == 'o.total_order'){
                        if(!$included_table_product){
                            $sql .= " FROM " . DB_PREFIX . "product p";
                            $included_table_product = true;
                        }
                        
                        $sql .= " LEFT JOIN (SELECT op.product_id, SUM(op.quantity) AS total_order FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) WHERE o.order_status_id > '0' GROUP BY op.product_id) AS o ON (o.product_id = p.product_id)";
                }
                
                if($included_table_product){
                    $sql .= " LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id = p.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";
                } else {
                    $sql .= " FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id)";
                }
                
                $sql .= "  WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                
                if($included_table_product){
                    $sql .= "AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                }
                
                if (!empty($data['filter_category_id'])) {
                    if(is_array($data['filter_category_id'])){
                        $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);
                        
                        if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
                                if(!empty($data['filter_sub_category_depth'])){
                                    $sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
                                }
			} else {
				$sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
			}
                    }else{
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                                if(!empty($data['filter_sub_category_depth'])){
                                    $sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
                                }
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
                    }
		}
                
                if (!empty($data['filter_filter'])) {
                        $implode = array();

                        $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                        foreach ($filters as $filter_id) {
                                $implode[] = (int)$filter_id;
                        }

                        $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
                }
                
                $sql .= " GROUP BY m.manufacturer_id";
                
                $sort_data = array(
                        'm.name',
                        'm.sort_order',
                        'm.manufacturer_id',
                        'o.total_order',
                );

                if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                        $sql .= " ORDER BY " . $data['sort'];
                } else if($data['sort'] == 'random'){
                        $sql .= " ORDER BY RAND()";
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
	}

        public function getManufacturerDescription(int $manufacturer_id): array {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_manufacturer_description WHERE manufacturer_id = '" . (int)$manufacturer_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

                return $query->row;
        }

        public function getManufacturerLayoutId($manufacturer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_manufacturer_to_layout WHERE manufacturer_id = '" . (int)$manufacturer_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

        public function getFeaturedManufacturers($limit) {
		$manufacturer_data = $this->cache->get('manufacturer.mz_featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$limit);

		if (!$manufacturer_data) {
                        $manufacturer_data = array();
                        
			$query = $this->db->query("SELECT m.manufacturer_id FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.mz_featured = '1' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY m.sort_order ASC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$manufacturer_data[$result['manufacturer_id']] = $this->model_catalog_manufacturer->getManufacturer($result['manufacturer_id']);
			}

			$this->cache->set('manufacturer.mz_featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$limit, $manufacturer_data);
		}

		return $manufacturer_data;
	}
}