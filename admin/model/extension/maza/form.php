<?php
class ModelExtensionMazaForm extends Model {
	public function addForm(array $data): int {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form SET captcha = '" . $this->db->escape($data['captcha']) . "', spam_keywords = '" . $this->db->escape($data['spam_keywords']) . "', information_id = '" . (int)$data['information_id'] . "', record = '" . (int)$data['record'] . "', email_field_id = '" . (int)$data['email_field_id'] . "', subject_field_id = '" . (int)$data['subject_field_id'] . "', mail_admin_status = '" . (int)$data['mail_admin_status'] . "', mail_admin_to = '" . $this->db->escape($data['mail_admin_to']) . "', mail_customer_status = '" . (int)$data['mail_customer_status'] . "', date_added = NOW(), date_modified = NOW()");

		$form_id = $this->db->getLastId();

		foreach ($data['form_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_description SET form_id = '" . (int)$form_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', success = '" . $this->db->escape($value['success']) . "', submit_text = '" . $this->db->escape($value['submit_text']) . "', mail_customer_subject = '" . $this->db->escape($value['mail_customer_subject']) . "', mail_customer_message = '" . $this->db->escape($value['mail_customer_message']) . "'");
		}

		return $form_id;
	}

	public function editForm(int $form_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_form SET captcha = '" . $this->db->escape($data['captcha']) . "', spam_keywords = '" . $this->db->escape($data['spam_keywords']) . "', information_id = '" . (int)$data['information_id'] . "', record = '" . (int)$data['record'] . "', email_field_id = '" . (int)$data['email_field_id'] . "', subject_field_id = '" . (int)$data['subject_field_id'] . "', mail_admin_status = '" . (int)$data['mail_admin_status'] . "', mail_admin_to = '" . $this->db->escape($data['mail_admin_to']) . "', mail_customer_status = '" . (int)$data['mail_customer_status'] . "', date_modified = NOW() WHERE form_id = '" . (int)$form_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_description WHERE form_id = '" . (int)$form_id . "'");

		foreach ($data['form_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_description SET form_id = '" . (int)$form_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', success = '" . $this->db->escape($value['success']) . "', submit_text = '" . $this->db->escape($value['submit_text']) . "', mail_customer_subject = '" . $this->db->escape($value['mail_customer_subject']) . "', mail_customer_message = '" . $this->db->escape($value['mail_customer_message']) . "'");
		}
	}
        
	public function copyForm(int $form_id): int {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_form WHERE form_id = '" . (int)$form_id . "'");

		if ($query->num_rows) {
			$data = $query->row;
			$data['email_field_id']	  = 0;
			$data['subject_field_id'] = 0;
			$data['form_description'] = $this->getFormDescriptions($form_id);

			return $this->addForm($data);
		}

		return 0;
	}

	public function deleteForm(int $form_id): void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_record_value WHERE form_id = '" . (int)$form_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_record WHERE form_id = '" . (int)$form_id . "'");
		$this->db->query("DELETE f, fd, f2cg, fv, fvd FROM " . DB_PREFIX . "mz_form_field f LEFT JOIN " . DB_PREFIX . "mz_form_field_description fd ON (f.form_field_id = fd.form_field_id) LEFT JOIN " . DB_PREFIX . "mz_form_field_customer_group f2cg ON (f2cg.form_field_id = f.form_field_id) LEFT JOIN " . DB_PREFIX . "mz_form_field_value fv ON (fv.form_field_id = f.form_field_id) LEFT JOIN " . DB_PREFIX . "mz_form_field_value_description fvd ON (fvd.form_field_value_id = fv.form_field_value_id) WHERE f.form_id = '" . (int)$form_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_description WHERE form_id = '" . (int)$form_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form WHERE form_id = '" . (int)$form_id . "'");
	}

	public function getForm(int $form_id): array {
		$query = $this->db->query("SELECT DISTINCT f.*, fd.name FROM " . DB_PREFIX . "mz_form f LEFT JOIN " . DB_PREFIX . "mz_form_description fd ON (f.form_id = fd.form_id) WHERE f.form_id = '" . (int)$form_id . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");
               
		return $query->row;
	}

	public function getForms(array $data = array()): array {
		$sql = "SELECT DISTINCT *, (SELECT COUNT(*) FROM `" . DB_PREFIX . "mz_form_record` WHERE form_id = f.form_id) records FROM " . DB_PREFIX . "mz_form f LEFT JOIN " . DB_PREFIX . "mz_form_description fd ON (f.form_id = fd.form_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND fd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY f.form_id";

		$sort_data = array(
			'fd.name',
			'f.date_added',
			'records'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY fd.name";
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

	public function getTotalForms(array $data = array()): int {
		$sql = "SELECT COUNT(DISTINCT f.form_id) AS total FROM " . DB_PREFIX . "mz_form f LEFT JOIN " . DB_PREFIX . "mz_form_description fd ON (f.form_id = fd.form_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
                
		if (!empty($data['filter_name'])) {
			$sql .= " AND fd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getFormDescriptions(int $form_id): array {
		$form_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_description WHERE form_id = '" . (int)$form_id . "'");

		foreach ($query->rows as $result) {
			$form_description_data[$result['language_id']] = array(
				'name' 					=> $result['name'],
				'success' 				=> $result['success'],
				'submit_text' 			=> $result['submit_text'],
				'mail_customer_subject' => $result['mail_customer_subject'],
				'mail_customer_message' => $result['mail_customer_message'],
			);
		}

		return $form_description_data;
	}

	public function getFields(int $form_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_field f LEFT JOIN " . DB_PREFIX . "mz_form_field_description fd ON (f.form_field_id = fd.form_field_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND f.form_id = '" . (int)$form_id . "'");

		return $query->rows;
	}
}