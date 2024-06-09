<?php
class ModelExtensionMazaCatalogManufacturer extends Model {
	public function editManufacturer(int $manufacturer_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET mz_featured = '" . (int)$data['mz_featured'] . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "mz_manufacturer_description WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($data['manufacturer_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_manufacturer_description SET manufacturer_id = '" . (int)$manufacturer_id . "', language_id = '" . (int)$language_id . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_manufacturer_to_layout WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['manufacturer_layout'])) {
			foreach ($data['manufacturer_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_manufacturer_to_layout SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}
	}

    public function getManufacturerDescriptions(int $manufacturer_id): array {
		$manufacturer_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_manufacturer_description WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_description_data[$result['language_id']] = array(
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
			);
		}

		return $manufacturer_description_data;
	}

	public function getManufacturerLayouts(int $manufacturer_id): array {
		$manufacturer_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_manufacturer_to_layout WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $manufacturer_layout_data;
	}

	public function getTotalManufacturers(array $data = array()): int {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
