<?php
class ModelExtensionMazaFormField extends Model {
	public function addField(array $data): int {
		$sql = "INSERT INTO " . DB_PREFIX . "mz_form_field SET form_id = '" . (int)$data['form_id'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', `column` = '" . (int)$data['column'] . "', is_required = '" . (int)$data['is_required'] . "', customer = '" . (int)$data['customer'] . "', `type` = '" . $this->db->escape($data['type']) . "', `name` = '" . $this->db->escape(trim($data['name'])) . "', `value` = '" . $this->db->escape($data['value']) . "', `validation` = '" . $this->db->escape(trim($data['validation'])) . "', `decimal` = '" . ((int)$data['decimal'] > 0 ? (int)$data['decimal'] : 0) . "', `date_added` = NOW(), `date_modified` = NOW()";

		if($data['min'] !== ''){
			$sql .= ", `min` = '" . (int)$data['min'] . "'";
		}

		if($data['max'] !== '' && ($data['min'] === '' || $data['max'] > $data['min'])){
			$sql .= ", `max` = '" . (int)$data['max'] . "'";
		}

		$this->db->query($sql);

		$form_field_id = $this->db->getLastId();

		foreach ($data['field_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_field_description SET form_field_id = '" . (int)$form_field_id . "', language_id = '" . (int)$language_id . "', label = '" . $this->db->escape($value['label']) . "', placeholder = '" . $this->db->escape($value['placeholder']) . "', help = '" . $this->db->escape($value['help']) . "', error = '" . $this->db->escape($value['error']) . "'");
		}

		if (isset($data['field_customer_group'])) {
			foreach ($data['field_customer_group'] as $customer_group_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_field_customer_group SET form_field_id = '" . (int)$form_field_id . "', customer_group_id = '" . (int)$customer_group_id . "'");
			}
		}
		
		if (isset($data['field_values'])) {
			foreach ($data['field_values'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_field_value SET form_field_id = '" . (int)$form_field_id . "', sort_order = '" . (int)$value['sort_order'] . "'");
	
				$form_field_value_id = $this->db->getLastId();
	
				foreach ($value['name'] as $language_id => $name) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_field_value_description SET form_field_value_id = '" . (int)$form_field_value_id . "', language_id = '" . (int)$language_id . "', form_field_id = '" . (int)$form_field_id . "', name = '" . $this->db->escape(trim($name)) . "'");
				}
			}
		}
		
		return $form_field_id;
	}

	public function editField(int $form_field_id, array $data): void {
		$sql = "UPDATE " . DB_PREFIX . "mz_form_field SET form_id = '" . (int)$data['form_id'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', `column` = '" . (int)$data['column'] . "', is_required = '" . (int)$data['is_required'] . "', customer = '" . (int)$data['customer'] . "', `type` = '" . $this->db->escape($data['type']) . "', `name` = '" . $this->db->escape($data['name']) . "', `value` = '" . $this->db->escape($data['value']) . "', `validation` = '" . $this->db->escape($data['validation']) . "', `decimal` = '" . ((int)$data['decimal'] > 0 ? (int)$data['decimal'] : 0) . "', `date_modified` = NOW()";

		if($data['min'] !== ''){
			$sql .= ", `min` = '" . (int)$data['min'] . "'";
		} else {
			$sql .= ", `min` = NULL";
		}

		if($data['max'] !== '' && ($data['min'] === '' || $data['max'] > $data['min'])){
			$sql .= ", `max` = '" . (int)$data['max'] . "'";
		} else {
			$sql .= ", `max` = NULL";
		}

		$this->db->query($sql . " WHERE form_field_id = '" . (int)$form_field_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_description WHERE form_field_id = '" . (int)$form_field_id . "'");

		foreach ($data['field_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_field_description SET form_field_id = '" . (int)$form_field_id . "', language_id = '" . (int)$language_id . "', label = '" . $this->db->escape($value['label']) . "', placeholder = '" . $this->db->escape($value['placeholder']) . "', help = '" . $this->db->escape($value['help']) . "', error = '" . $this->db->escape($value['error']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_customer_group WHERE form_field_id = '" . (int)$form_field_id . "'");

		if (isset($data['field_customer_group'])) {
			foreach ($data['field_customer_group'] as $customer_group_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_field_customer_group SET form_field_id = '" . (int)$form_field_id . "', customer_group_id = '" . (int)$customer_group_id . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_value WHERE form_field_id = '" . (int)$form_field_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_value_description WHERE form_field_id = '" . (int)$form_field_id . "'");

		if (isset($data['field_values'])) {
			foreach ($data['field_values'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_field_value SET form_field_id = '" . (int)$form_field_id . "', sort_order = '" . (int)$value['sort_order'] . "'");
	
				$form_field_value_id = $this->db->getLastId();
	
				foreach ($value['name'] as $language_id => $name) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_field_value_description SET form_field_value_id = '" . (int)$form_field_value_id . "', language_id = '" . (int)$language_id . "', form_field_id = '" . (int)$form_field_id . "', name = '" . $this->db->escape($name) . "'");
				}
			}
		}
	}

	public function copyField(int $form_field_id): int {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_form_field WHERE form_field_id = '" . (int)$form_field_id . "'");

		if ($query->num_rows) {
			$data = $query->row;
			$data['status'] = 0;
			$data['field_description'] = $this->getFieldDescriptions($form_field_id);
			$data['field_customer_group'] = $this->getFieldCustomerGroups($form_field_id);
			$data['field_values'] = $this->getFieldValues($form_field_id);

			return $this->addField($data);
		}

		return 0;
	}
        
	public function copyForm(int $from_form_id, int $to_form_id): void {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_form_field WHERE form_id = '" . (int)$from_form_id . "'");
		
		foreach($query->rows as $field){
			$field['form_id'] = $to_form_id;
			$field['field_description'] = $this->getFieldDescriptions($field['form_field_id']);
			$field['field_customer_group'] = $this->getFieldCustomerGroups($field['form_field_id']);
			$field['field_values'] = $this->getFieldValues($field['form_field_id']);

			$this->addField($field);
		}
	}
        
	public function deleteField(int $form_field_id): void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_value_description WHERE form_field_id = '" . (int)$form_field_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_value WHERE form_field_id = '" . (int)$form_field_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_customer_group WHERE form_field_id = '" . (int)$form_field_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_description WHERE form_field_id = '" . (int)$form_field_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field WHERE form_field_id = '" . (int)$form_field_id . "'");
	}

	public function getField(int $form_field_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_field WHERE form_field_id = '" . (int)$form_field_id . "'");
                
		return $query->row;
	}

	public function getFields(array $data = array()): array {
		$sql = "SELECT DISTINCT *, (SELECT `name` FROM " . DB_PREFIX . "mz_form_description WHERE form_id = f.form_id AND language_id = '" . (int)$this->config->get('config_language_id') . "') AS form FROM " . DB_PREFIX . "mz_form_field f LEFT JOIN " . DB_PREFIX . "mz_form_field_description fd ON (f.form_field_id = fd.form_field_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if(isset($data['filter_form_id'])){
			$sql .= " AND f.form_id = '" . (int)$data['filter_form_id'] . "'";
		}

		if(isset($data['filter_type'])){
			$sql .= " AND f.type = '" . $this->db->escape($data['filter_type']) . "'";
		}

		if(isset($data['filter_status'])){
			$sql .= " AND f.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY f.form_field_id";

		$sort_data = array(
			'fd.label',
			'f.sort_order',
			'f.is_required',
			'f.date_added',
			'f.status',
			'f.type',
			'form'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY f.sort_order";
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

	public function getTotalFields(array $data = array()): int {
		$sql = "SELECT COUNT(DISTINCT f.form_field_id) AS total FROM " . DB_PREFIX . "mz_form_field f LEFT JOIN " . DB_PREFIX . "mz_form_field_description fd ON (f.form_field_id = fd.form_field_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if(isset($data['filter_form_id'])){
			$sql .= " AND f.form_id = '" . (int)$data['filter_form_id'] . "'";
		}

		if(isset($data['filter_type'])){
			$sql .= " AND f.type = '" . $this->db->escape($data['filter_type']) . "'";
		}

		if(isset($data['filter_status'])){
			$sql .= " AND f.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
        
	public function getFieldDescriptions(int $form_field_id): array {
		$field_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_field_description WHERE form_field_id = '" . (int)$form_field_id . "'");

		foreach ($query->rows as $result) {
			$field_description_data[$result['language_id']] = array(
				'label' => $result['label'],
				'placeholder' => $result['placeholder'],
				'help' => $result['help'],
				'error' => $result['error'],
			);
		}

		return $field_description_data;
	}

	public function getFieldCustomerGroups(int $form_field_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_field_customer_group WHERE form_field_id = '" . (int)$form_field_id . "'");

		return array_column($query->rows, 'customer_group_id');
	}

	public function getFieldValues(int $form_field_id): array {
		$field_value_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_field_value WHERE form_field_id = '" . (int)$form_field_id . "'");

		foreach ($query->rows as $result) {
			$field_value_data[] = array(
				'sort_order' => $result['sort_order'],
				'name' => $this->getFieldValueDescription($result['form_field_value_id']),
			);
		}

		return $field_value_data;
	}

	private function getFieldValueDescription(int $form_field_value_id): array {
		$field_value_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_field_value_description WHERE form_field_value_id = '" . (int)$form_field_value_id . "'");

		foreach($query->rows as $result){
			$field_value_description_data[$result['language_id']] = $result['name'];
		}

		return $field_value_description_data;
	}
}