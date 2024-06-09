<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaAccountNotification extends model {
    public function getProducts(array $data = array()): array {
		$sql = "SELECT p.product_id FROM " . DB_PREFIX . "mz_notification_subscribe ns LEFT JOIN " . DB_PREFIX . "product p ON (ns.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE ns.customer_id = '" . (int)$this->customer->getId() . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY p.product_id ORDER BY ns.date_added DESC";
  
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

	public function getTotalProducts(array $data = array()): int {
		$query = $this->db->query("SELECT COUNT(DISTINCT p.product_id) total FROM " . DB_PREFIX . "mz_notification_subscribe ns LEFT JOIN " . DB_PREFIX . "product p ON (ns.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE ns.customer_id = '" . (int)$this->customer->getId() . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY p.product_id");

		return $query->row['total'];
	}

	public function getManufacturers(array $data = array()): array {
		$sql = "SELECT m.* FROM " . DB_PREFIX . "mz_notification_subscribe ns LEFT JOIN " . DB_PREFIX . "manufacturer m ON (ns.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE ns.customer_id = '" . (int)$this->customer->getId() . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY m.manufacturer_id ORDER BY ns.date_added DESC";
  
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

	public function getTotalManufacturers(array $data = array()): int {
		$query = $this->db->query("SELECT COUNT(DISTINCT m.manufacturer_id) total FROM " . DB_PREFIX . "mz_notification_subscribe ns LEFT JOIN " . DB_PREFIX . "manufacturer m ON (ns.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE ns.customer_id = '" . (int)$this->customer->getId() . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY m.manufacturer_id");

		return $query->row['total'];
	}

	public function getChannels(): array {
		$data_channels = [];

		if ($this->customer->isLogged()) {
			$query = $this->db->query("SELECT c.channel_id, cd.name, cd.description, (SELECT methods FROM " . DB_PREFIX . "mz_notification_channel_subscribe cs WHERE cs.channel_id = c.channel_id AND cs.customer_id = '" . (int)$this->customer->getId() . "') methods FROM " . DB_PREFIX . "mz_notification_channel c LEFT JOIN " . DB_PREFIX . "mz_notification_channel_description cd ON (c.channel_id = cd.channel_id) LEFT JOIN `" . DB_PREFIX . "mz_notification_channel_to_store` c2s ON (c.channel_id = c2s.channel_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.status = 1 AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY c.channel_id ORDER BY c.sort_order ASC");

			foreach ($query->rows as $channel) {
				$data_channels[$channel['channel_id']] = [
					'channel_id' => $channel['channel_id'],
					'name' => $channel['name'],
					'description' => $channel['description'],
					'methods' => explode(',', $channel['methods']??''),
				];
			}
		}

		return $data_channels;
	}

	public function getDefaultChannels(): array {
		$data_channels = [];

		$query = $this->db->query("SELECT c.channel_id FROM " . DB_PREFIX . "mz_notification_channel c LEFT JOIN `" . DB_PREFIX . "mz_notification_channel_to_store` c2s ON (c.channel_id = c2s.channel_id) WHERE c.status = 1 AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY c.channel_id");

		foreach ($query->rows as $channel) {
			$data_channels[$channel['channel_id']] = [
				'channel_id' => $channel['channel_id'],
				'methods' => ['email', 'sms'],
			];
		}

		return $data_channels;
	}

	public function addChannels(int $customer_id, array $channels): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_notification_channel_subscribe` WHERE customer_id = '" . (int)$customer_id . "'");

		foreach ($channels as $channel) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "mz_notification_channel_subscribe` SET customer_id = '" . (int)$customer_id . "', channel_id = '" . (int)$channel['channel_id'] . "', methods = '" . $this->db->escape(implode(',', $channel['methods'])) . "'");
		}
	}
}
