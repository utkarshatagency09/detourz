<?php
class ModelExtensionMazaNotification extends Model {
	public function addNotification(array $data): int {
		$sql = "INSERT INTO " . DB_PREFIX . "mz_notification SET customer_id = '" . (int)$data['customer_id'] . "', type = '" . $this->db->escape($data['type']) . "', message = '" . $this->db->escape($data['message']) . "', date_added = NOW()";

		if (!empty($data['product_id'])) {
			$sql .= ", product_id = '" . (int)$data['product_id'] . "'";
		}

		if (!empty($data['manufacturer_id'])) {
			$sql .= ", manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
		}

		if (!empty($data['article_id'])) {
			$sql .= ", article_id = '" . (int)$data['article_id'] . "'";
		}

		$this->db->query($sql);

		return $this->db->getLastId();
	}

	public function addSubscriber(array $data): int {
		$sql = "INSERT INTO " . DB_PREFIX . "mz_notification_subscribe SET customer_id = '" . (int)$data['customer_id'] . "', email = '" . $this->db->escape($data['email']) . "', `token` = '" . $this->db->escape(token(32)) . "', date_added = NOW()";

		if (!empty($data['manufacturer_id'])) {
			$sql .= ", product_id = 0, manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
		} else {
			$sql .= ", product_id = '" . (int)$data['product_id'] . "', manufacturer_id = 0";
		}

		$this->db->query($sql);

		return $this->db->getLastId();
	}

	public function editSubscriber(int $subscribe_id, array $data): void {
		$sql = "UPDATE " . DB_PREFIX . "mz_notification_subscribe SET customer_id = '" . (int)$data['customer_id'] . "', email = '" . $this->db->escape($data['email']) . "'";

		if (!empty($data['manufacturer_id'])) {
			$sql .= ", product_id = 0, manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
		} else {
			$sql .= ", product_id = '" . (int)$data['product_id'] . "', manufacturer_id = 0";
		}

		$this->db->query($sql . " WHERE subscribe_id = '" . (int)$subscribe_id . "'");
	}

	public function deleteSubscriber(int $subscribe_id): void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_subscribe WHERE subscribe_id = '" . (int)$subscribe_id . "'");
	}

	public function getSubscriber(int $subscribe_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_notification_subscribe WHERE subscribe_id = '" . (int)$subscribe_id . "'");

		return $query->row;
	}

	public function getSubscribers(array $data = array()): array {
		$sql = "SELECT DISTINCT *, (SELECT CONCAT(firstname, ' ', lastname) FROM " . DB_PREFIX . "customer WHERE customer_id = s.customer_id) customer, (SELECT name FROM " . DB_PREFIX . "product_description WHERE product_id = s.product_id AND language_id = '" . (int)$this->config->get('config_language_id') . "') product, (SELECT name FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = s.manufacturer_id) manufacturer FROM " . DB_PREFIX . "mz_notification_subscribe s WHERE 1";
  
		if (!empty($data['filter_customer_id'])) {
			$sql .= " AND s.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND s.email = '" . $this->db->escape($data['filter_email']) . "'";
		}

		if (!empty($data['filter_product_id'])) {
			$sql .= " AND s.product_id = '" . (int)$data['filter_product_id'] . "'";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND s.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$sql .= " GROUP BY s.subscribe_id";

		$sort_data = array(
			's.date_added',
			'customer',
			'product',
			'manufacturer'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY s.date_added";
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

	public function getTotalSubscribers(array $data = array()): int {
		$sql = "SELECT COUNT(DISTINCT subscribe_id) AS total FROM " . DB_PREFIX . "mz_notification_subscribe s WHERE 1";
  
		if (!empty($data['filter_customer_id'])) {
			$sql .= " AND s.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND s.email = '" . $this->db->escape($data['filter_email']) . "'";
		}

		if (!empty($data['filter_product_id'])) {
			$sql .= " AND s.product_id = '" . (int)$data['filter_product_id'] . "'";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND s.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}