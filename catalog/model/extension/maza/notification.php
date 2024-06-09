<?php
class ModelExtensionMazaNotification extends Model {
	public function getNotifications(): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_notification WHERE customer_id = '" . (int)$this->customer->getId() . "' ORDER BY date_added DESC LIMIT 10");

		return $query->rows;
	}

	public function getTotalNotifications(): int {
		$query = $this->db->query("SELECT COUNT(*) total FROM " . DB_PREFIX . "mz_notification WHERE customer_id = '" . (int)$this->customer->getId() . "' AND `read` = 0");

		return $query->row['total'];
	}

	public function readAll(): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_notification SET `read` = 1 WHERE customer_id = '" . (int)$this->customer->getId() . "' AND `read` = 0");
	}

    public function addProductSubscribe(int $product_id): void {
		if ($this->customer->isLogged()) {
			$this->deleteProductSubscribe($product_id);

			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_notification_subscribe SET product_id = '" . (int)$product_id . "', customer_id = '" . (int)$this->customer->getId() . "', `token` = '" . $this->db->escape(token(32)) . "', date_added = now()");
		}
	}

	public function deleteProductSubscribe(int $product_id): void {
		if ($this->customer->isLogged() && $product_id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_subscribe WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "'");
		}
	}

	public function isProductSubscribed(int $product_id): bool {
		if ($this->customer->isLogged()) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_notification_subscribe WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "'");

			if ($query->num_rows) {
				return true;
			}
		}
		
		return false;
	}

	public function addManufacturerSubscribe(int $manufacturer_id): void {
		if ($this->customer->isLogged()) {
			$this->deleteManufacturerSubscribe($manufacturer_id);
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_notification_subscribe SET manufacturer_id = '" . (int)$manufacturer_id . "', customer_id = '" . (int)$this->customer->getId() . "', `token` = '" . $this->db->escape(token(32)) . "', date_added = now()");
		}
	}

	public function deleteManufacturerSubscribe(int $manufacturer_id): void {
		if ($this->customer->isLogged() && $manufacturer_id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "mz_notification_subscribe WHERE customer_id = '" . (int)$this->customer->getId() . "' AND manufacturer_id = '" . (int)$manufacturer_id . "'");
		}
	}

	public function isManufacturerSubscribed(int $manufacturer_id): bool {
		if ($this->customer->isLogged()) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_notification_subscribe WHERE customer_id = '" . (int)$this->customer->getId() . "' AND manufacturer_id = '" . (int)$manufacturer_id . "'");

			if ($query->num_rows) {
				return true;
			}
		}
		
		return false;
	}

	public function getSubscriber(int $subscribe_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_notification_subscribe WHERE subscribe_id = '" . (int)$subscribe_id . "'");

		return $query->row;
	}
}