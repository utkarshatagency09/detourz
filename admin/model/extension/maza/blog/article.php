<?php
class ModelExtensionMazaBlogArticle extends Model {
	public function addArticle(array $data): int {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article SET date_available = '" . $this->db->escape($data['date_available']) . "', author_id = '" . (int)$data['author_id'] . "', status = '" . (int)$data['status'] . "', featured = '" . (int)$data['featured'] . "', allow_comment = '" . (int)$data['allow_comment'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW(), date_modified = NOW()");

		$article_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_article SET image = '" . $this->db->escape($data['image']) . "' WHERE article_id = '" . (int)$article_id . "'");
		}

		foreach ($data['article_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_description SET article_id = '" . (int)$article_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['article_store'])) {
			foreach ($data['article_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_to_store SET article_id = '" . (int)$article_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['article_image'])) {
			foreach ($data['article_image'] as $article_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_image SET article_id = '" . (int)$article_id . "', image = '" . $this->db->escape($article_image['image']) . "', sort_order = '" . (int)$article_image['sort_order'] . "'");
			}
		}

		if (isset($data['article_audio'])) {
			foreach ($data['article_audio'] as $article_audio) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_audio SET article_id = '" . (int)$article_id . "', url = '" . $this->db->escape($article_audio['url']) . "', title = '" . $this->db->escape($article_audio['title']) . "', sort_order = '" . (int)$article_audio['sort_order'] . "'");
			}
		}

		if (isset($data['article_category'])) {
			foreach ($data['article_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_to_category SET article_id = '" . (int)$article_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['article_filter'])) {
			foreach ($data['article_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_filter SET article_id = '" . (int)$article_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}
                
		if (isset($data['article_product'])) {
			foreach ($data['article_product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_product SET article_id = '" . (int)$article_id . "', product_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['article_related'])) {
			foreach ($data['article_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_related WHERE article_id = '" . (int)$article_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_related SET article_id = '" . (int)$article_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_related WHERE article_id = '" . (int)$related_id . "' AND related_id = '" . (int)$article_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_related SET article_id = '" . (int)$related_id . "', related_id = '" . (int)$article_id . "'");
			}
		}
		
		// SEO URL
		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			if($data['keyword']){
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'mz_blog_article_id=" . (int)$article_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}
		} else if (isset($data['article_seo_url'])) {
			foreach ($data['article_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mz_blog_article_id=" . (int)$article_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		if (isset($data['article_layout'])) {
			foreach ($data['article_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_to_layout SET article_id = '" . (int)$article_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		return $article_id;
	}

	public function editArticle(int $article_id, array $data): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_article SET date_available = '" . $this->db->escape($data['date_available']) . "', author_id = '" . (int)$data['author_id'] . "', status = '" . (int)$data['status'] . "', featured = '" . (int)$data['featured'] . "', allow_comment = '" . (int)$data['allow_comment'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_article SET image = '" . $this->db->escape($data['image']) . "' WHERE article_id = '" . (int)$article_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_description WHERE article_id = '" . (int)$article_id . "'");

		foreach ($data['article_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_description SET article_id = '" . (int)$article_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_to_store WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_store'])) {
			foreach ($data['article_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_to_store SET article_id = '" . (int)$article_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_image WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_image'])) {
			foreach ($data['article_image'] as $article_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_image SET article_id = '" . (int)$article_id . "', image = '" . $this->db->escape($article_image['image']) . "', sort_order = '" . (int)$article_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_audio WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_audio'])) {
			foreach ($data['article_audio'] as $article_audio) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_audio SET article_id = '" . (int)$article_id . "', url = '" . $this->db->escape($article_audio['url']) . "', title = '" . $this->db->escape($article_audio['title']) . "', sort_order = '" . (int)$article_audio['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_to_category WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_category'])) {
			foreach ($data['article_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_to_category SET article_id = '" . (int)$article_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_filter WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_filter'])) {
			foreach ($data['article_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_filter SET article_id = '" . (int)$article_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}
                
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_product WHERE article_id = '" . (int)$article_id . "'");
                
		if (isset($data['article_product'])) {
			foreach ($data['article_product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_product SET article_id = '" . (int)$article_id . "', product_id = '" . (int)$product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_related WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_related WHERE related_id = '" . (int)$article_id . "'");

		if (isset($data['article_related'])) {
			foreach ($data['article_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_related WHERE article_id = '" . (int)$article_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_related SET article_id = '" . (int)$article_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_related WHERE article_id = '" . (int)$related_id . "' AND related_id = '" . (int)$article_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_related SET article_id = '" . (int)$related_id . "', related_id = '" . (int)$article_id . "'");
			}
		}
		
		// SEO URL
		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'mz_blog_article_id=" . (int)$article_id . "'");

			if ($data['keyword']) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'mz_blog_article_id=" . (int)$article_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}
		} else {
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mz_blog_article_id=" . (int)$article_id . "'");
		
			if (isset($data['article_seo_url'])) {
				foreach ($data['article_seo_url']as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mz_blog_article_id=" . (int)$article_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_to_layout WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_layout'])) {
			foreach ($data['article_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_article_to_layout SET article_id = '" . (int)$article_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}
	}

	public function copyArticle(int $article_id): void {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_blog_article a WHERE a.article_id = '" . (int)$article_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';
			$data['featured'] = '0';

			$data['article_description'] = $this->getArticleDescriptions($article_id);
			$data['article_filter'] = $this->getArticleFilters($article_id);
			$data['article_image'] = $this->getArticleImages($article_id);
			$data['article_related'] = $this->getArticleRelated($article_id);
			$data['article_category'] = $this->getArticleCategories($article_id);
			$data['article_layout'] = $this->getArticleLayouts($article_id);
			$data['article_store'] = $this->getArticleStores($article_id);

			$this->addArticle($data);
		}
	}

	public function deleteArticle(int $article_id): void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_filter WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_image WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_related WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_related WHERE related_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_to_category WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_to_layout WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_to_store WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_comment WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_description WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article WHERE article_id = '" . (int)$article_id . "'");

		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			$this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE query = 'mz_blog_article_id=" . (int)$article_id . "'");
		} else {
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mz_blog_article_id=" . (int)$article_id . "'");
		}
	}

	public function getArticle(int $article_id): array {
		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			$query = $this->db->query("SELECT DISTINCT *, (SELECT DISTINCT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'mz_blog_article_id=" . (int)$article_id . "') AS keyword FROM " . DB_PREFIX . "mz_blog_article a LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id) WHERE a.article_id = '" . (int)$article_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		} else {
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_blog_article a LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id) WHERE a.article_id = '" . (int)$article_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		}

		return $query->row;
	}

	public function getArticles(array $data = array()): array {
		$sql = "SELECT *, (SELECT name FROM " . DB_PREFIX . "mz_blog_author_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND author_id = a.author_id) author FROM " . DB_PREFIX . "mz_blog_article a LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_allow_comment']) && $data['filter_allow_comment'] !== '') {
			$sql .= " AND a.allow_comment = '" . (int)$data['filter_allow_comment'] . "'";
		}
                
                if (isset($data['filter_author_id']) && $data['filter_author_id'] !== '') {
			$sql .= " AND a.author_id = '" . (int)$data['filter_author_id'] . "'";
		}
                
                if (!empty($data['filter_start_date'])) {
			$sql .= " AND a.date_added >= '" . $this->db->escape($data['filter_start_date']) . "'";
		}
                
                if (!empty($data['filter_end_date'])) {
			$sql .= " AND a.date_added <= '" . $this->db->escape($data['filter_end_date']) . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND a.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY a.article_id";

		$sort_data = array(
			'ad.name',
			'a.status',
			'author',
			'a.date_added',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY a.date_added";
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
        
	public function getTotalArticles(array $data = array()): int {
		$sql = "SELECT COUNT(DISTINCT a.article_id) AS total FROM " . DB_PREFIX . "mz_blog_article a LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_allow_comment']) && $data['filter_allow_comment'] !== '') {
			$sql .= " AND a.allow_comment = '" . (int)$data['filter_allow_comment'] . "'";
		}
                
		if (isset($data['filter_author_id']) && $data['filter_author_id'] !== '') {
			$sql .= " AND a.author_id = '" . (int)$data['filter_author_id'] . "'";
		}
                
		if (!empty($data['filter_start_date'])) {
			$sql .= " AND a.date_added >= '" . $this->db->escape($data['filter_start_date']) . "'";
		}
                
		if (!empty($data['filter_end_date'])) {
			$sql .= " AND a.date_added <= '" . $this->db->escape($data['filter_end_date']) . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND a.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getArticlesByCategoryId(int $category_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article a LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_category a2c ON (a.article_id = a2c.article_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND a2c.category_id = '" . (int)$category_id . "' ORDER BY ad.name ASC");

		return $query->rows;
	}

	public function getArticleDescriptions(int $article_id): array {
		$article_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_description WHERE article_id = '" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $article_description_data;
	}

	public function getArticleCategories(int $article_id): array {
		$article_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_to_category WHERE article_id = '" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_category_data[] = $result['category_id'];
		}

		return $article_category_data;
	}

	public function getArticleFilters(int $article_id): array {
		$article_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_filter WHERE article_id = '" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_filter_data[] = $result['filter_id'];
		}

		return $article_filter_data;
	}
        
	public function getArticleProducts(int $article_id): array {
		$article_product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_product WHERE article_id = '" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_product_data[] = $result['product_id'];
		}

		return $article_product_data;
	}

	public function getArticleImages(int $article_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_image WHERE article_id = '" . (int)$article_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getArticleAudios(int $article_id): array {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_audio WHERE article_id = '" . (int)$article_id . "' ORDER BY sort_order ASC");

        return $query->rows;
    }

	public function getArticleStores(int $article_id): array {
		$article_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_to_store WHERE article_id = '" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_store_data[] = $result['store_id'];
		}

		return $article_store_data;
	}
	
	public function getArticleSeoUrls(int $article_id): array {
		$article_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'mz_blog_article_id=" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $article_seo_url_data;
	}
	
	public function getArticleLayouts(int $article_id): array {
		$article_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_to_layout WHERE article_id = '" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $article_layout_data;
	}

	public function getArticleRelated(int $article_id): array {
		$article_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_related WHERE article_id = '" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_related_data[] = $result['related_id'];
		}

		return $article_related_data;
	}

	public function getTotalArticlesByAuthorId(int $author_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_blog_article WHERE author_id = '" . (int)$author_id . "'");

		return $query->row['total'];
	}
        
	public function getTotalArticlesByLayoutId(int $layout_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_blog_article_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
}
