<?php
class ModelExtensionMazaNotificationPush extends Model {
	public function push(array $data): int {
		$sql = "INSERT INTO " . DB_PREFIX . "mz_push_notification_queue (`endpoint`, `key_auth`, `key_p256dh`, `title`, `message`, `image`, `url`, `date_added`) SELECT endpoint, key_auth, key_p256dh, '" . $this->db->escape($data['title']) . "', '" . $this->db->escape($data['message']) . "', '" . $this->db->escape($data['image']) . "', '" . $this->db->escape($data['url']) . "', NOW() FROM `" . DB_PREFIX . "mz_push_notification_subscriber` ps";

		if (!empty($data['channel_id'])) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "mz_notification_channel_subscribe` cs ON (ps.customer_id = cs.customer_id) WHERE (cs.channel_id = '" . (int)$data['channel_id'] . "' AND cs.methods LIKE '%push%') OR ps.customer_id = 0";
		}

		$sql .= " GROUP BY ps.endpoint";

		$this->db->query($sql);

		$query = $this->db->query("SELECT ROW_COUNT() AS total_inserted");

		return (int)$query->row['total_inserted'];
	}

	public function getTotalSubscriptions(): int {
		$query = $this->db->query("SELECT COUNT(DISTINCT `endpoint`) AS total FROM `" . DB_PREFIX . "mz_push_notification_subscriber`");

		return (int)$query->row['total'];
	}

	public function addPush(array $data): void {
		$sql = "INSERT INTO " . DB_PREFIX . "mz_push_notification_queue SET `endpoint` = '" . $this->db->escape($data['endpoint']) . "', `key_auth` = '" . $this->db->escape($data['key_auth']) . "', `key_p256dh` = '" . $this->db->escape($data['key_p256dh']) . "', `title` = '" . $this->db->escape($data['title']) . "', `message` = '" . $this->db->escape($data['message']) . "', date_added = NOW()";

		if (!empty($data['url'])) {
			$sql .= ", `url` = '" . $this->db->escape($data['url']) . "'";
		}

		if (!empty($data['image'])) {
			$sql .= ", `image` = '" . $this->db->escape($data['image']) . "'";
		}

		$this->db->query($sql);
	}
}