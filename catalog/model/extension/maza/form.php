<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaForm extends model {
        public function getForm(int $form_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form f LEFT JOIN " . DB_PREFIX . "mz_form_description fd ON (f.form_id = fd.form_id) WHERE f.form_id = '" . (int)$form_id . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");
               
		return $query->row;
	}

        public function getFields(int $form_id): array {
                $sql = "SELECT * FROM " . DB_PREFIX . "mz_form_field f LEFT JOIN " . DB_PREFIX . "mz_form_field_description fd ON (f.form_field_id = fd.form_field_id) LEFT JOIN `" . DB_PREFIX . "mz_form_field_customer_group` f2g ON (f.form_field_id = f2g.form_field_id) WHERE f.form_id = '" . (int)$form_id . "' AND f.status = 1 AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND f2g.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'";
		
                // customer value 0 = ALL, -1 = Guest, 1 = logged
                if($this->customer->isLogged()){
                        $sql .= " AND f.customer >= 0";
                } else {
                        $sql .= " AND f.customer <= 0";
                }

                $sql .= " GROUP BY f.form_field_id ORDER BY f.sort_order ASC";
                
                $query = $this->db->query($sql);

		return $query->rows;
	}

        public function getFieldValues(int $form_field_id): array {
                $values_data = array();

                $query = $this->db->query("SELECT fvd.name FROM `" . DB_PREFIX . "mz_form_field_value` fv LEFT JOIN `" . DB_PREFIX . "mz_form_field_value_description` fvd ON (fv.form_field_value_id = fvd.form_field_value_id) WHERE fv.form_field_id = '" . (int)$form_field_id . "' AND fvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY fv.sort_order ASC");

                foreach($query->rows as $value){
                        $values_data[] = $value['name'];
                }

                return $values_data;
        }

        public function addRecord(int $form_id, array $field_data): int {
                $sql = "INSERT INTO `" . DB_PREFIX . "mz_form_record` SET form_id = '" . (int)$form_id . "', language_id = '" . (int)$this->config->get('config_language_id') . "', currency_id = '" . (int)$this->currency->getId($this->session->data['currency']) . "', store_id = '" . $this->config->get('config_store_id') . "', customer_id = '" . ($this->customer->getId()??0) . "', page_url = '" . $this->db->escape($this->request->post['mz_page_url']) . "', ip_address = '" . $this->db->escape(getenv('REMOTE_ADDR')) . "', date_added = NOW()";

                if(!empty($this->request->post['mz_product_id'])){
                        $sql .= ", product_id = '" . (int)$this->request->post['mz_product_id'] . "'";
                }
                if(!empty($this->request->post['mz_manufacturer_id'])){
                        $sql .= ", manufacturer_id = '" . (int)$this->request->post['mz_manufacturer_id'] . "'";
                }
                if(!empty($this->request->post['mz_category_id'])){
                        $sql .= ", category_id = '" . (int)$this->request->post['mz_category_id'] . "'";
                }

                $this->db->query($sql);

                $form_record_id = $this->db->getLastId();

                foreach($field_data as $name => $value){
                        $sql = "INSERT INTO `" . DB_PREFIX . "mz_form_record_value` SET form_id = '" . (int)$form_id . "', form_record_id = '" . (int)$form_record_id . "', name = '" . $this->db->escape($name) . "'";

                        if(is_array($value)){
                                $sql .= ", value = '" . $this->db->escape(json_encode($value)) . "'";
                        } else {
                                $sql .= ", value = '" . $this->db->escape($value) . "'";
                        }

                        $this->db->query($sql);
                }

                return $form_record_id;
        }
}
