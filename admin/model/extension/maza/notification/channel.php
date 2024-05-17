<?php
class ModelExtensionMazaNotificationChannel extends Model {
	public function addChannel(array $data): int {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_notification_channel SET `default` = '" . (int)$data['default'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

		$channel_id = $this->db->getLastId();

		foreach ($data['channel_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_notification_channel_description SET channel_id = '" . (int)$channel_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		if (isset($data['channel_store'])) {
			foreach ($data['channel_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_notification_channel_to_store SET channel_id = '" . (int)$channel_id . "', store_id = '" . (int)$store_id . "'");
			}

			// If default then enable for all existing customers
			if ($data['default']) {
				foreach ($data['channel_store'] as $store_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "mz_notification_channel_subscribe (customer_id, channel_id, methods) SELECT customer_id, '" . (int)$channel_id . "', 'email,sms,push' FROM " . DB_PREFIX . "customer WHERE store_id = '" . (int)$store_id . "'");
				}
			}
		}

		return $channel_id;
	}

	public function editChannel(int $channel_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_notification_channel SET `default` = '" . (int)$data['default'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE channel_id = '" . (int)$channel_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_channel_description WHERE channel_id = '" . (int)$channel_id . "'");

		foreach ($data['channel_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_notification_channel_description SET channel_id = '" . (int)$channel_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_channel_to_store WHERE channel_id = '" . (int)$channel_id . "'");

		if (isset($data['channel_store'])) {
			foreach ($data['channel_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_notification_channel_to_store SET channel_id = '" . (int)$channel_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
	}

	public function deleteChannel(int $channel_id): void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_channel WHERE channel_id = '" . (int)$channel_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_channel_description WHERE channel_id = '" . (int)$channel_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_channel_to_store WHERE channel_id = '" . (int)$channel_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_channel_subscribe WHERE channel_id = '" . (int)$channel_id . "'");
	}

	public function getChannel(int $channel_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_notification_channel c LEFT JOIN " . DB_PREFIX . "mz_notification_channel_description cd ON (c.channel_id = cd.channel_id) WHERE c.channel_id = '" . (int)$channel_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");

		return $query->row;
	}

	public function getChannels(array $data = array()): array {
		if ($data) {
			$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "mz_notification_channel c LEFT JOIN " . DB_PREFIX . "mz_notification_channel_description cd ON (c.channel_id = cd.channel_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			if (!empty($data['filter_name'])) {
				$sql .= " AND ad.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			}

			$sql .= " GROUP BY c.channel_id";

			$sort_data = array(
				'cd.name',
				'c.sort_order',
				'c.date_added',
				'c.status',
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY cd.name";
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
		} else {
			$query = $this->db->query("SELECT c.channel_id, cd.name FROM " . DB_PREFIX . "mz_notification_channel c LEFT JOIN " . DB_PREFIX . "mz_notification_channel_description cd ON (c.channel_id = cd.channel_id) LEFT JOIN `" . DB_PREFIX . "mz_notification_channel_to_store` c2s ON (c.channel_id = c2s.channel_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.status = 1 AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY c.channel_id ORDER BY c.sort_order ASC");

			return $query->rows;;
		}
	}

	public function getChannelDescriptions(int $channel_id) {
		$channel_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_notification_channel_description WHERE channel_id = '" . (int)$channel_id . "'");

		foreach ($query->rows as $result) {
			$channel_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description']
			);
		}

		return $channel_description_data;
	}

	public function getChannelStores(int $channel_id) {
		$channel_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_notification_channel_to_store WHERE channel_id = '" . (int)$channel_id . "'");

		foreach ($query->rows as $result) {
			$channel_store_data[] = $result['store_id'];
		}

		return $channel_store_data;
	}
	
	public function getTotalChannels() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_notification_channel");

		return $query->row['total'];
	}
}