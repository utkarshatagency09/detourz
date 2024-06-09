<?php
class ModelExtensionMazaBlogComment extends Model {
	public function addComment($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mz_blog_comment SET author = '" . $this->db->escape($data['author']) . "', article_id = '" . (int)$data['article_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', parent_comment_id = '" . (int)$data['parent_comment_id'] . "', status = '" . (int)$data['status'] . "', email = '" . $this->db->escape($data['email']) . "', website = '" . $this->db->escape($data['website']) . "', date_added = '" . $this->db->escape($data['date_added']) . "'");

		$comment_id = $this->db->getLastId();
                
                // MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_blog_comment_path` WHERE comment_id = '" . (int)$data['parent_comment_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "mz_blog_comment_path` SET `comment_id` = '" . (int)$comment_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "mz_blog_comment_path` SET `comment_id` = '" . (int)$comment_id . "', `path_id` = '" . (int)$comment_id . "', `level` = '" . (int)$level . "'");

		return $comment_id;
	}

	public function editComment($comment_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_comment SET author = '" . $this->db->escape($data['author']) . "', article_id = '" . (int)$data['article_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', parent_comment_id = '" . (int)$data['parent_comment_id'] . "', status = '" . (int)$data['status'] . "', email = '" . $this->db->escape($data['email']) . "', website = '" . $this->db->escape($data['website']) . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE comment_id = '" . (int)$comment_id . "'");
                
                // MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_blog_comment_path` WHERE path_id = '" . (int)$comment_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $comment_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_blog_comment_path` WHERE comment_id = '" . (int)$comment_path['comment_id'] . "' AND level < '" . (int)$comment_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_blog_comment_path` WHERE comment_id = '" . (int)$data['parent_comment_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_blog_comment_path` WHERE comment_id = '" . (int)$comment_path['comment_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "mz_blog_comment_path` SET comment_id = '" . (int)$comment_path['comment_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_blog_comment_path` WHERE comment_id = '" . (int)$comment_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_blog_comment_path` WHERE comment_id = '" . (int)$data['parent_comment_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "mz_blog_comment_path` SET comment_id = '" . (int)$comment_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "mz_blog_comment_path` SET comment_id = '" . (int)$comment_id . "', `path_id` = '" . (int)$comment_id . "', level = '" . (int)$level . "'");
		}
	}

	public function deleteComment($comment_id) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_comment_path WHERE comment_id = '" . (int)$comment_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_comment_path WHERE path_id = '" . (int)$comment_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteComment($result['comment_id']);
		}
                
		$this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_comment WHERE comment_id = '" . (int)$comment_id . "'");
	}
        
        public function repairComments($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_blog_comment WHERE parent_id = '" . (int)$parent_id . "'");

		foreach ($query->rows as $comment) {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "mz_blog_comment_path` WHERE comment_id = '" . (int)$comment['comment_id'] . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_blog_comment_path` WHERE comment_id = '" . (int)$parent_id . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "mz_blog_comment_path` SET comment_id = '" . (int)$comment['comment_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "mz_blog_comment_path` SET comment_id = '" . (int)$comment['comment_id'] . "', `path_id` = '" . (int)$comment['comment_id'] . "', level = '" . (int)$level . "'");

			$this->repairCategories($comment['comment_id']);
		}
	}
        
        public function approveComment($comment_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_comment SET status = 1 WHERE comment_id = '" . (int)$comment_id . "'");
	}
        
        public function disapproveComment($comment_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_comment SET status = 0 WHERE comment_id = '" . (int)$comment_id . "'");
	}

	public function getComment($comment_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT ad.name FROM " . DB_PREFIX . "mz_blog_article_description ad WHERE ad.article_id = c.article_id AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "') AS article, (SELECT CONCAT(firstname, ' ', lastname) FROM " . DB_PREFIX . "customer WHERE customer_id = c.customer_id LIMIT 1) customer_name FROM " . DB_PREFIX . "mz_blog_comment c WHERE c.comment_id = '" . (int)$comment_id . "'");

		return $query->row;
	}

	public function getComments($data = array()) {
		$sql = "SELECT c.comment_id,  c.parent_comment_id, ad.name, c.status, c.date_added, (CASE WHEN cs.customer_id IS NOT NULL THEN CONCAT(cs.firstname, ' ', cs.lastname) ELSE c.author END) author FROM " . DB_PREFIX . "mz_blog_comment c LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (c.article_id = ad.article_id) LEFT JOIN " . DB_PREFIX . "customer cs ON (cs.customer_id = c.customer_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_article'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_article']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND (CASE WHEN cs.customer_id IS NOT NULL THEN CONCAT(cs.firstname, ' ', cs.lastname) ELSE c.author END) LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'ad.name',
			'author',
			'c.status',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY c.date_added";
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

	public function getTotalComments($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_blog_comment c LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (c.article_id = ad.article_id) LEFT JOIN " . DB_PREFIX . "customer cs ON (cs.customer_id = c.customer_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_article'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_article']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND (CASE WHEN cs.customer_id IS NOT NULL THEN CONCAT(cs.firstname, ' ', cs.lastname) ELSE c.author END) LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalCommentsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_blog_comment WHERE status = '0'");

		return $query->row['total'];
	}
}