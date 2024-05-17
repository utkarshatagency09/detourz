<?php
class ModelExtensionMazaNotificationSend extends Model {
	public function getSubscribers(array $data = array()) {
		$sql = "SELECT ps.*, c.*, ns.*, IFNULL(c.email, ns.email) email, (SELECT methods FROM " . DB_PREFIX . "mz_notification_channel_subscribe cs WHERE cs.customer_id = c.customer_id AND cs.channel_id = '" . (int)$data['channel_id'] . "') methods FROM " . DB_PREFIX . "mz_notification_subscribe ns LEFT JOIN " . DB_PREFIX . "customer c ON (ns.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "mz_push_notification_subscriber ps ON (ps.customer_id = c.customer_id)";

		if (!empty($data['product_id'])) {
			$sql .= " WHERE ns.product_id = '" . (int)$data['product_id'] . "'";

			if (!empty($data['manufacturer_id'])) {
				$sql .= " OR ns.manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
			}
		} elseif (!empty($data['manufacturer_id'])) {
			$sql .= " WHERE ns.manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
		} else {
			throw new UnexpectedValueException('Values as not as a expected');
		}

		$query = $this->mz_db->query($sql, true);

		foreach($query->rows as $row){
			if (empty($row['customer_id'])) {
				$row['methods'] = ['email'];
			} else {
				$row['methods'] = explode(',', $row['methods']);
			}

			yield $row;
		}
	}

	public function getChannelSubscribers(array $data = array()) {
		$query = $this->mz_db->query("SELECT * FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "mz_push_notification_subscriber ps ON (ps.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "mz_notification_channel_subscribe cs ON (cs.customer_id = c.customer_id) WHERE cs.channel_id = '" . (int)$data['channel_id'] . "' GROUP BY c.customer_id", true);

		foreach($query->rows as $row){
			$row['methods'] = explode(',', $row['methods']);

			yield $row;
		}

		// Guest newsletter
		$query = $this->mz_db->query("SELECT n.email_id as email FROM " . DB_PREFIX . "mz_newsletter n LEFT JOIN " . DB_PREFIX . "customer c ON (c.email = n.email_id) WHERE n.status = 1 AND c.customer_id IS NULL", true);

		foreach($query->rows as $row){
			$row['methods'] = ['email'];

			yield $row;
		}
	}
}