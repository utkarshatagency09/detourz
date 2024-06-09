<?php
class ModelExtensionMazaTestimonial extends Model {
    /**
     * Add testimonial
     * @param array $data testimonial data
     * @return int testimonial id
     */
	public function addTestimonial(array $data): int {
        $set = array();
        
        $set[] = "sort_order = '0'";
        $set[] = "date_added = NOW()";
        $set[] = "date_modified = NOW()";
        
        if($this->mz_skin_config->get('testimonial_submit_require_approval')){
            $set[] = "status = '0'";
        } else {
            $set[] = "status = '1'";
        }
        
        if(isset($data['rating'])){
            $set[] = "rating = '" . (int)$data['rating'] . "'";
        }
        
        if (!empty($data['image'])) {
            $set[] = "image = '" . $this->db->escape($data['image']) . "'";
        }
        
        if (!empty($data['email'])) {
            $set[] = "email = '" . $this->db->escape($data['email']) . "'";
        }
                
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_testimonial SET " . implode(',', $set));

		$testimonial_id = $this->db->getLastId();
                
        // Description
		$this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        
        // Extra
        if(!isset($data['extra'])){
            $data['extra'] = '';
        }
                
		foreach ($languages as $language) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_testimonial_description SET testimonial_id = '" . (int)$testimonial_id . "', language_id = '" . (int)$language['language_id'] . "', name = '" . $this->db->escape($data['name']) . "', `extra` = '" . $this->db->escape($data['extra']) . "', description = '" . $this->db->escape($data['description']) . "'");
		}
                
        // Store
        $this->db->query("INSERT INTO " . DB_PREFIX . "mz_testimonial_to_store SET testimonial_id = '" . (int)$testimonial_id . "', store_id = '" . (int)$this->config->get('config_store_id') . "'");

		return $testimonial_id;
	}
        
    /**
     * Get testimonial detail
     * @param int $testimonial_id id
     * @return array testimonial data
     */
    public function getTestimonial(int $testimonial_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_testimonial t LEFT JOIN " . DB_PREFIX . "mz_testimonial_to_store t2s ON (t.testimonial_id = t2s.testimonial_id) LEFT JOIN " . DB_PREFIX . "mz_testimonial_description td ON (t.testimonial_id = td.testimonial_id) WHERE t.testimonial_id = '" . (int)$testimonial_id . "' AND t.status = '1' AND t.date_added <= NOW() AND t2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND td.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");

		return $query->row;
	}
        
    /**
     * Get list of testimonial
     * @param array $data filter
     * @return array testimonials
     */
	public function getTestimonials(array $data = array()): array {
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "mz_testimonial t LEFT JOIN " . DB_PREFIX . "mz_testimonial_to_store t2s ON (t.testimonial_id = t2s.testimonial_id) LEFT JOIN " . DB_PREFIX . "mz_testimonial_description td ON (t.testimonial_id = td.testimonial_id) WHERE td.language_id = '" . (int)$this->config->get('config_language_id') . "' AND t.status = '1' AND t.date_added <= NOW() AND t2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                
        if(!empty($data['filter_min_rating'])){
            $sql .= " AND rating >= '" . (int)$data['filter_min_rating'] . "'";
        }
        
        $sql .= " GROUP BY t.testimonial_id";
        
        $sort_data = array(
			'td.name',
			't.sort_order',
            't.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY t.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
                
        if(!isset($data['sort']) || $data['sort'] != 't.date_added'){
            $sql .= ", t.date_added DESC";
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

    /**
     * Send thank you mail
     * @param string $email_id
     * @return NULL
     */
    public function sendMail(string $email_id): void {
        // Template
        if(isset($this->mz_skin_config->get('testimonial_mail_template')[$this->config->get('config_language_id')])){
            $template = $this->mz_skin_config->get('testimonial_mail_template')[$this->config->get('config_language_id')];
        } else {
            $template = array();
        }
        if(empty($template['subject'])){
            $template['subject'] = $this->language->get('help_mail_subject');
        }
        if(empty($template['message'])){
            $template['message'] = $this->language->get('help_mail_message');
        }
        
        // Send mail
        $this->load->model('extension/maza/common');
        $this->model_extension_maza_common->sendMail($email_id, $template['subject'], $template['message']);
    }
        
    /**
     * Get total testimonial
     * @return int total
     */
	public function getTotalTestimonials(): int {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_testimonial t LEFT JOIN " . DB_PREFIX . "mz_testimonial_to_store t2s ON (t.testimonial_id = t2s.testimonial_id) WHERE t.status = '1' AND t.date_added <= NOW() AND t2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		return $query->row['total'];
	}
}