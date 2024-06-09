<?php
class ControllerExtensionMazaEventLanguage extends Controller {
	// public function index(&$route, &$args) {
	// 	foreach ($this->language->all() as $key => $value) {
	// 		if (!isset($args[$key])) {
	// 			$args[$key] = $value;
	// 		}
	// 	}
	// }	
	
	// // 1. Before controller load store all current loaded language data
	// public function before(&$route, &$output) {
	// 	$this->language->set('backup', $this->language->all());
	// }
	
	// // 2. After contoller load restore old language data
	// public function after(&$route, &$args, &$output) {
	// 	$data = $this->language->get('backup');
		
	// 	if (is_array($data)) {
	// 		foreach ($data as $key => $value) {
	// 			$this->language->set($key, $value);
	// 		}
	// 	}
	// }

	public function addLanguage(String $route, array $param) : void {
		$this->load->model('localisation/language');

		$language = $this->model_localisation_language->getLanguageByCode($param[0]['code']);

		$language_id = $language['language_id'];

		// Product tag view
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_tags WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_product_tags WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $tag) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_tags SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($tag['name']) . "', viewed = '" . (int)$tag['viewed'] . "', used = '" . (int)$tag['used'] . "'");
		}

		// Blog tag view
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_tags WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_tags WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $tag) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_tags SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($tag['name']) . "', viewed = '" . (int)$tag['viewed'] . "', used = '" . (int)$tag['used'] . "'");
		}

		// Blog Category description
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_category_description WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_category_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $category) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_category_description SET category_id = '" . (int)$category['category_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($category['name']) . "', description = '" . $this->db->escape($category['description']) . "', meta_title = '" . $this->db->escape($category['meta_title']) . "', meta_description = '" . $this->db->escape($category['meta_description']) . "', meta_keyword = '" . $this->db->escape($category['meta_keyword']) . "'");
		}

		// Blog Author description
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_author_description WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_author_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $author) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_author_description SET author_id = '" . (int)$author['author_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($author['name']) . "', description = '" . $this->db->escape($author['description']) . "', meta_title = '" . $this->db->escape($author['meta_title']) . "', meta_description = '" . $this->db->escape($author['meta_description']) . "', meta_keyword = '" . $this->db->escape($author['meta_keyword']) . "'");
		}

		// Article
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_description WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $article) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_description SET article_id = '" . (int)$article['article_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($article['name']) . "', description = '" . $this->db->escape($article['description']) . "', tag = '" . $this->db->escape($article['tag']) . "', meta_title = '" . $this->db->escape($article['meta_title']) . "', meta_description = '" . $this->db->escape($article['meta_description']) . "', meta_keyword = '" . $this->db->escape($article['meta_keyword']) . "'");
		}

		// Testimonial
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_testimonial_description WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_testimonial_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $testimonial) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_testimonial_description SET testimonial_id = '" . (int)$testimonial['testimonial_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($testimonial['name']) . "', `extra` = '" . $this->db->escape($testimonial['extra']) . "', description = '" . $this->db->escape($testimonial['description']) . "'");
		}

		// Page builder
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_page_description WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_page_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $page) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_page_description SET page_id = '" . (int)$page['page_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($page['name']) . "', meta_title = '" . $this->db->escape($page['meta_title']) . "', meta_description = '" . $this->db->escape($page['meta_description']) . "', meta_keyword = '" . $this->db->escape($page['meta_keyword']) . "'");
		}

		// Filter
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_description WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_filter_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $filter) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_filter_description SET filter_id = '" . (int)$filter['filter_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($filter['name']) . "'");
		}

		// Filter value
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_value_description WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_filter_value_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_filter_value_description SET value_id = '" . (int)$value['value_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		// Form
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_description WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $form) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_description SET form_id = '" . (int)$form['form_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($form['name']) . "', success = '" . $this->db->escape($form['success']) . "', submit_text = '" . $this->db->escape($form['submit_text']) . "', mail_customer_subject = '" . $this->db->escape($form['mail_customer_subject']) . "', mail_customer_message = '" . $this->db->escape($form['mail_customer_message']) . "'");
		}
		
		// Form field
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_description WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_field_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $field) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_field_description SET form_field_id = '" . (int)$field['form_field_id'] . "', language_id = '" . (int)$language_id . "', label = '" . $this->db->escape($field['label']) . "', placeholder = '" . $this->db->escape($field['placeholder']) . "', help = '" . $this->db->escape($field['help']) . "', error = '" . $this->db->escape($field['error']) . "'");
		}

		// Form field value
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_value_description WHERE language_id = '" . (int)$language_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_field_value_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_form_field_value_description SET form_field_value_id = '" . (int)$value['form_field_value_id'] . "', language_id = '" . (int)$language_id . "', form_field_id = '" . (int)$value['form_field_id'] . "', name = '" . $this->db->escape($value['name']) . "'");
		}
	}

	public function deleteLanguage(String $route, array $param) : void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_tags WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_tags WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_category_description WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_author_description WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_description WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_testimonial_description WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_page_description WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_description WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_value_description WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_description WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_description WHERE language_id = '" . (int)$param[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_form_field_value_description WHERE language_id = '" . (int)$param[0] . "'");
	}
}