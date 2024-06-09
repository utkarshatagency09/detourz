<?php
class ModelExtensionMazaBlogArticle extends Model {
	public function getArticle(int $article_id): array {
		$query = $this->db->query("SELECT DISTINCT *, a.date_added AS date_added, a.date_modified AS date_modified, ad.name AS name, ad.description AS description, ad.meta_title AS meta_title, ad.meta_description AS meta_description, ad.meta_keyword AS meta_keyword, a.image, aad.name AS author, aa.image AS author_image, aad.description AS author_description, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_blog_comment c WHERE c.article_id = a.article_id AND c.status = '1' GROUP BY c.article_id) AS comments, (SELECT category_id FROM " . DB_PREFIX . "mz_blog_article_to_category a2c WHERE a2c.article_id = a.article_id GROUP BY a2c.article_id) AS category_id, a.sort_order FROM " . DB_PREFIX . "mz_blog_article a LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_store a2s ON (a.article_id = a2s.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_author aa ON (a.author_id = aa.author_id) LEFT JOIN " . DB_PREFIX . "mz_blog_author_description aad ON (aa.author_id = aad.author_id) WHERE a.article_id = '" . (int)$article_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND aad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND a.status <> '0' AND a.date_available <= NOW() AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
                
		if ($query->num_rows) {
			return array(
				'article_id'       => $query->row['article_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'image'            => $query->row['image'],
				'author_id'        => $query->row['author_id'],
				'author'           => $query->row['author'],
				'author_description' => $query->row['author_description'],
				'author_image'     => $query->row['author_image'],
				'date_available'   => $query->row['date_available'],
				'comments'         => $query->row['comments']??0,
				'category_id'      => $query->row['category_id']??0,
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'allow_comment'    => $query->row['allow_comment'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return array();
		}
	}

	public function getArticles(array $data = array()): array {
		$sql = "SELECT a.article_id";
                
		// Get sort order list
		$sort = array();
		if(isset($data['sort_order'])){
			$sort = array_column($data['sort_order'], 'sort');
		}
		if(isset($data['sort'])){
			array_push($sort, $data['sort']);
		}
                
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "mz_blog_category_path cp LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_category a2c ON (cp.category_id = a2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "mz_blog_article_to_category a2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_filter af ON (a2c.article_id = af.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article a ON (af.article_id = a.article_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article a ON (a2c.article_id = a.article_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "mz_blog_article a";
                        
			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_filter af ON (a.article_id = af.article_id)";
			}
		}
                
                
		// check article_description table is require to use
		// if (!empty($data['filter_name']) || !empty($data['filter_tag']) || in_array('ad.name', $sort)) {
		// 	$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id)";
		// }

		$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_store a2s ON (a.article_id = a2s.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_author aa ON (aa.author_id = a.author_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND a.status = '1' AND a.date_available <= NOW() AND aa.status = '1' AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                
		// check article_description table columns is require to use
		// if (!empty($data['filter_name']) || !empty($data['filter_tag']) || in_array('ad.name', $sort)) {
		// 	$sql .= " AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		// }

		if (!empty($data['filter_category_id'])) {
			if(is_array($data['filter_category_id'])){
				$data['filter_category_id'] = array_map('intval', $data['filter_category_id']);
                        
				if (!empty($data['filter_sub_category'])) {
					$sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";

					if(!empty($data['filter_sub_category_depth'])){
						$sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
					}
				} else {
					$sql .= " AND a2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
				}
			}else{
				if (!empty($data['filter_sub_category'])) {
					$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";

					if(!empty($data['filter_sub_category_depth'])){
						$sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
					}
				} else {
					$sql .= " AND a2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				}
			}
		}
                
		if (!empty($data['filter_filter'])) {
			$implode = array();

			$filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

			foreach ($filters as $filter_id) {
				$implode[] = (int)$filter_id;
			}

			$sql .= " AND af.filter_id IN (" . implode(',', $implode) . ")";
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "ad.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR ad.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "ad.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			$sql .= ")";
		}

		if (!empty($data['filter_author_id'])) {
			if(is_array($data['filter_author_id'])){
				$sql .= " AND a.author_id IN (" . implode(',', array_map('intval', $data['filter_author_id'])) . ")";
			} else {
				$sql .= " AND a.author_id = '" . (int)$data['filter_author_id'] . "'";
			}
		}
                
		if (!empty($data['filter_date_add_end'])) {
			$sql .= " AND a.date_added <= '" . $this->db->escape($data['filter_date_add_end']) . "'";
		}
                
		if (!empty($data['filter_date_add_start'])) {
			$sql .= " AND a.date_added >= '" . $this->db->escape($data['filter_date_add_start']) . "'";
		}
                
		$sql .= " GROUP BY a.article_id";

		$sort_data = array(
			'ad.name',
			'a.sort_order',
			'a.date_added',
			'a.date_available',
			'a.viewed',
			'random'
		);
                
		// sort by multiple sort and order
		if(isset($data['sort_order'])){
			$sql_order_by = TRUE;
			foreach ($data['sort_order'] as $sort_order) {
				if (in_array($sort_order['sort'], $sort_data)) {
					if ($sort_order['sort'] == 'ad.name') {
						$sql .= (($sql_order_by)?' ORDER BY ':', ') . "LCASE(" . $sort_order['sort'] . ")";
					} elseif ($sort_order['sort'] == 'random') {
						$sql .= (($sql_order_by)?' ORDER BY ':', ') . "RAND()";
					} else {
						$sql .= (($sql_order_by)?' ORDER BY ':', ') . $sort_order['sort'];
					}

					if (isset($sort_order['order']) && ($sort_order['order'] == 'DESC')) {
						$sql .= ' DESC';
					} else {
						$sql .= ' ASC';
					}

					$sql_order_by = FALSE;
				}
			}
		} elseif (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'ad.name') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'random') {
				$sql .= " ORDER BY RAND()";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
                        
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
					$sql .= " DESC, a.article_id DESC";
			} else {
					$sql .= " ASC, a.article_id ASC";
			}
		} else {
			$sql .= " ORDER BY a.sort_order";
                        
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
					$sql .= " DESC, a.article_id DESC";
			} else {
					$sql .= " ASC, a.article_id ASC";
			}
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

		$article_data = array();
                
		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$article_data[$result['article_id']] = $this->getArticle($result['article_id']);
		}

		return $article_data;
	}
        
	public function getTotalArticles(array $data = array()): int {
		$sql = "SELECT COUNT(DISTINCT a.article_id) AS total";
                
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "mz_blog_category_path cp LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_category a2c ON (cp.category_id = a2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "mz_blog_article_to_category a2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_filter af ON (a2c.article_id = af.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article a ON (af.article_id = a.article_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article a ON (a2c.article_id = a.article_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "mz_blog_article a";
                        
			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_filter af ON (a.article_id = af.article_id)";
			}
		}
                
                
		// check article_description table is require to use
		// if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
		// 	$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id)";
		// }

		$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_store a2s ON (a.article_id = a2s.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_author aa ON (aa.author_id = a.author_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND a.status = '1' AND a.date_available <= NOW() AND aa.status = '1' AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                
		// check article_description table columns is require to use
		// if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
		// 	$sql .= " AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		// }

		if (!empty($data['filter_category_id'])) {
			if(is_array($data['filter_category_id'])){
				$data['filter_category_id'] = array_map('intval', $data['filter_category_id']);
                        
				if (!empty($data['filter_sub_category'])) {
					$sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";

					if(!empty($data['filter_sub_category_depth'])){
						$sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
					}
				} else {
					$sql .= " AND a2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
				}
			}else{
				if (!empty($data['filter_sub_category'])) {
					$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";

					if(!empty($data['filter_sub_category_depth'])){
						$sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
					}
				} else {
					$sql .= " AND a2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				}
			}
		}
                
		if (!empty($data['filter_filter'])) {
			$implode = array();

			$filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

			foreach ($filters as $filter_id) {
				$implode[] = (int)$filter_id;
			}

			$sql .= " AND af.filter_id IN (" . implode(',', $implode) . ")";
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "ad.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR ad.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "ad.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			$sql .= ")";
		}

		if (!empty($data['filter_author_id'])) {
			if(is_array($data['filter_author_id'])){
				$sql .= " AND a.author_id IN (" . implode(',', array_map('intval', $data['filter_author_id'])) . ")";
			} else {
				$sql .= " AND a.author_id = '" . (int)$data['filter_author_id'] . "'";
			}
		}
                
		if (!empty($data['filter_date_add_end'])) {
			$sql .= " AND a.date_added <= '" . $this->db->escape($data['filter_date_add_end']) . "'";
		}
                
		if (!empty($data['filter_date_add_start'])) {
			$sql .= " AND a.date_added >= '" . $this->db->escape($data['filter_date_add_start']) . "'";
		}
                
		$query = $this->db->query($sql);
                
		return $query->row['total'];
	}

	public function getLatestArticles(int $limit): array {
		$article_data = $this->cache->get('article.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$article_data) {
			$query = $this->db->query("SELECT a.article_id FROM " . DB_PREFIX . "mz_blog_article a LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_store a2s ON (a.article_id = a2s.article_id) WHERE a.status = '1' AND a.date_available <= NOW() AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY a.date_added DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$article_data[$result['article_id']] = $this->getArticle($result['article_id']);
			}

			$this->cache->set('article.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $article_data);
		}

		return $article_data;
	}

	public function getPopularArticles(int $limit): array {
		$article_data = $this->cache->get('article.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);
	
		if (!$article_data) {
			$query = $this->db->query("SELECT a.article_id FROM " . DB_PREFIX . "mz_blog_article a LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_store a2s ON (a.article_id = a2s.article_id) WHERE a.status = '1' AND a.date_available <= NOW() AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY a.viewed DESC, a.date_added DESC LIMIT " . (int)$limit);
	
			foreach ($query->rows as $result) {
				$article_data[$result['article_id']] = $this->getArticle($result['article_id']);
			}
			
			$this->cache->set('article.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $article_data);
		}
		
		return $article_data;
	}

	public function getFeaturedArticles(int $limit): array {
		$article_data = $this->cache->get('article.featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$article_data) {
			$article_data = array();
			
			$query = $this->db->query("SELECT a.article_id FROM " . DB_PREFIX . "mz_blog_article a LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_store a2s ON (a.article_id = a2s.article_id) WHERE a.status = '1' AND a.date_available <= NOW() AND a.featured = '1' AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY a.date_added DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$article_data[$result['article_id']] = $this->getArticle($result['article_id']);
			}

			$this->cache->set('article.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $article_data);
		}

		return $article_data;
	}

	public function getArticleImages(int $article_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_image WHERE article_id = '" . (int)$article_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getArticleAudios(int $article_id): array {
		$query = $this->db->query("SELECT url, title FROM " . DB_PREFIX . "mz_blog_article_audio WHERE article_id = '" . (int)$article_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getArticleRelated(int $article_id): array {
		$article_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_related ar LEFT JOIN " . DB_PREFIX . "mz_blog_article a ON (ar.related_id = a.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_store a2s ON (a.article_id = a2s.article_id) WHERE ar.article_id = '" . (int)$article_id . "' AND a.status = '1' AND a.date_available <= NOW() AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$article_data[$result['related_id']] = $this->getArticle($result['related_id']);
		}

		return $article_data;
	}
        
	public function getArticleProduct(int $article_id): array {
		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_product ap LEFT JOIN " . DB_PREFIX . "product p ON (ap.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE ap.article_id = '" . (int)$article_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getArticleLayoutId(int $article_id): int {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_to_layout WHERE article_id = '" . (int)$article_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories(int $article_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_article_to_category WHERE article_id = '" . (int)$article_id . "'");

		return $query->rows;
	}

	/**
	 * Check is category linked with product
	 */
	public function validateCategory(int $article_id, int $category_id): bool {
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "mz_blog_article_to_category WHERE article_id = '" . (int)$article_id. "' AND category_id = '" . (int)$category_id . "'");

		return $query->num_rows > 0;
	}

	public function updateViewed(int $article_id): void {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_article SET viewed = (viewed + 1) WHERE article_id = '" . (int)$article_id . "'");
	}
}
