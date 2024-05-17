<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazalayout extends model {
    public function addLayout(array $data): int {
		$this->db->query("INSERT INTO " . DB_PREFIX . "layout SET name = '" . $this->db->escape($data['name']) . "', mz_layout_type = '" . $this->db->escape($data['mz_layout_type']) . "', mz_override_skin_id = '" . (int)$data['mz_override_skin_id'] . "'");

		$layout_id = $this->db->getLastId();

		if (isset($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', store_id = '" . (int)$layout_route['store_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "'");
			}
		}

		return $layout_id;
	}

    public function editLayout(int $layout_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "layout SET name = '" . $this->db->escape($data['name']) . "', mz_layout_type = '" . $this->db->escape($data['mz_layout_type']) . "', mz_override_skin_id = '" . (int)$data['mz_override_skin_id'] . "' WHERE layout_id = '" . (int)$layout_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "'");

		if (isset($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', store_id = '" . (int)$layout_route['store_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "'");
			}
		}
	}

    /**
     * Get list of layout
     * @param array $data filter and sort info
     * @return array layout list
     */
    public function getLayouts(array $data = array()): array {
        $sql = "SELECT * FROM " . DB_PREFIX . "layout WHERE 1";
        
        if(!empty($data['filter_name'])){
            $sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array('name');

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
        
    /**
     * Get total of layout
     * @param array $data filter and sort info
     * @return int total
     */
    public function getTotalLayouts(array $data = array()): int {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "layout WHERE 1";
        
        if(!empty($data['filter_name'])){
            $sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }
        
        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}
