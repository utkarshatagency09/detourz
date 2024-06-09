<?php
class ModelExtensionMazaMenu extends Model {
	public function addMenu(array $data): int {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_menu SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', date_modified = NOW()");

		return $this->db->getLastId();
	}

	public function editMenu(int $menu_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_menu SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', date_modified = NOW() WHERE menu_id = '" . (int)$menu_id . "'");
	}
        
        public function copyMenu(int $menu_id) {
		$menu_info = $this->getMenu($menu_id);

		if ($menu_info) {
			$menu_info['status'] = '0';
                        
                        // Copy menu
			$copy_menu_id = $this->addMenu($menu_info);
                        
                        // Copy menu items
                        $query = $this->db->query("SELECT item_id FROM `" . DB_PREFIX . "mz_menu_item` WHERE parent_item_id = 0 AND menu_id = '" . (int)$menu_id . "'");
                        foreach($query->rows as $item){
                            $this->duplicateItem($item['item_id'], 0, $copy_menu_id);
                        }
                        
                        return $copy_menu_id;
		}
	}

	public function deleteMenu(int $menu_id): void {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_menu_item` WHERE menu_id = '" . (int)$menu_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_menu` WHERE menu_id = '" . (int)$menu_id . "'");
	}

	public function getMenu(int $menu_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "mz_menu` WHERE menu_id = '" . (int)$menu_id . "'");

		return $query->row;
	}

	public function getMenus(array $data = array()): array {
                $sql = "SELECT * FROM " . DB_PREFIX . "mz_menu WHERE 1";
                
                if(!empty($data['filter_name'])){
                    $sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
                }
                
                if(isset($data['filter_status'])){
                    $sql .= " AND status = '" . (int)$data['filter_status'] . "'";
                }

                $sort_data = array(
                        'name',
                        'status'
                );

                if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                        $sql .= " ORDER BY " . $data['sort'];
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

	public function getTotalMenus(array $data = array()): int {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_menu WHERE 1"; 
                
                if(!empty($data['filter_name'])){
                    $sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
                }
                
                if(isset($data['filter_status'])){
                    $sql .= " AND status = '" . (int)$data['filter_status'] . "'";
                }

                
                $query = $this->db->query($sql);

		return $query->row['total'];
	}
        
        
        /**
         * Add menu item
         * @param int $menu_id module id
         * @param array $data item data
         * @return item id
         */
        public function addItem(int $menu_id, array $data): int {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_menu_item` SET menu_id = '" . (int)$menu_id . "', name = '" . $this->db->escape($data['name']) . "', `type` = '" . $this->db->escape($data['type']) . "', status = '" . (int)$data['status'] . "', customer = '" . (int)$data['customer'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', parent_item_id = '" . (int)$data['parent_item_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', `setting` = '" . $this->db->escape(json_encode($data['setting'])) . "'");
                return $this->db->getLastId();
        }
        
        /**
         * Edit menu item
         * @param int $item_id item id
         * @param array $data item data
         * @return NULL
         */
        public function editItem(int $item_id, array $data): void {
                if($data['type'] !== 'link'){
                    $this->db->query("UPDATE `" . DB_PREFIX . "mz_menu_item` SET parent_item_id = '0' WHERE parent_item_id = '" . (int)$item_id . "'");
                }
                
                $this->db->query("UPDATE `" . DB_PREFIX . "mz_menu_item` SET  name = '" . $this->db->escape($data['name']) . "', `type` = '" . $this->db->escape($data['type']) . "', status = '" . (int)$data['status'] . "', customer = '" . (int)$data['customer'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', parent_item_id = '" . (int)$data['parent_item_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', `setting` = '" . $this->db->escape(json_encode($data['setting'])) . "' WHERE item_id = '" . (int)$item_id . "'");
        }
        
        /**
         * delete menu item
         * @param int $item_id item id
         * @return NULL
         */
        public function deleteItem(int $item_id): void {
                // Delete child items
                $query = $this->db->query("SELECT item_id FROM `" . DB_PREFIX . "mz_menu_item` WHERE parent_item_id = '" . (int)$item_id . "'");
                foreach($query->rows as $item){
                    $this->deleteItem($item['item_id']);
                }
                
                // Delete itself
                $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_menu_item` WHERE item_id = '" . (int)$item_id . "'");
        }
        
        /**
         * Get menu item detail
         * @param int $item_id menu item id
         * @return array item detail
         */
        public function getItem(int $item_id): array {
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_menu_item` WHERE item_id = '" . (int)$item_id .  "'");
                
                if($query->num_rows){
                    $query->row['setting'] = json_decode($query->row['setting'], TRUE)?:array();
                }
                
                return $query->row;
        }
    
        /**
         * Get menu items
         * @param int $menu_id module id
         * @param int $parent_item_id
         * @param int $level
         * @param array $path 
         * @return array items
         */
        public function getItems(int $menu_id, int $parent_item_id = 0, $type = null, $skip_item_id = null, int $level = 0, array $path = array()): array {
                $items = array();
                
                $sql = "SELECT * FROM `" . DB_PREFIX . "mz_menu_item` WHERE menu_id = '" . (int)$menu_id . "' AND parent_item_id = '" . (int)$parent_item_id . "'";
                
                if($type){
                    $sql .= " AND `type` = '" . $this->db->escape($type) . "'";
                }
                
                $query = $this->db->query($sql . " ORDER BY sort_order ASC");
                
                foreach ($query->rows as $item) {
                    if($skip_item_id == $item['item_id']){
                        continue;
                    }
                    
                    $item_path = $path;
                    array_push($item_path, $item['name']);
                    $item['path'] = implode(' > ', $item_path);
                    $item['level'] = $level;
                    
                    $items[] = $item;
                    
                    foreach($this->getItems($menu_id, $item['item_id'], $type, $skip_item_id, $item['level'] + 1, $item_path) as $child_item){
                        $items[] = $child_item;
                    }
                }
                
                return $items;
        }
        
        
        /**
         * duplicate menu item
         * @param int $item_id item id
         * @paran Int $copy_to_parent
         * @paran Int $copy_to_menu
         * @return new duplicate item id
         */
        public function duplicateItem(int $item_id, $copy_to_parent = null, $copy_to_menu = null): int {
                $item_info = $this->getItem($item_id);
                
                // Copy to menu id
                if($copy_to_menu){
                    $item_info['menu_id'] = $copy_to_menu;
                }
                
                // Copy to parent id
                if($copy_to_parent){
                    $item_info['parent_item_id'] = $copy_to_parent;
                }
                
                $duplicate_item_id =  $this->addItem($item_info['menu_id'], $item_info);
                
                // Duplicate child items
                $query = $this->db->query("SELECT item_id FROM `" . DB_PREFIX . "mz_menu_item` WHERE parent_item_id = '" . (int)$item_info['item_id'] . "'");
                foreach($query->rows as $item){
                    $this->duplicateItem($item['item_id'], $duplicate_item_id, $copy_to_menu);
                }
                
                return $duplicate_item_id;
        }
}