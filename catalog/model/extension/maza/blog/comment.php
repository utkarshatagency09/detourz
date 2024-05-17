<?php
class ModelExtensionMazaBlogComment extends Model {
	public function addComment($article_id, $data) {
                $sql = "INSERT INTO " . DB_PREFIX . "mz_blog_comment SET customer_id = '" . (int)$this->customer->getId() . "', article_id = '" . (int)$article_id . "', text = '" . $this->db->escape($data['text']) . "', date_added = NOW()";
                
                // Is require approval for guest or all
                if(($this->mz_skin_config->get('blog_comment_require_approval') < 0 && !$this->customer->isLogged()) || ($this->mz_skin_config->get('blog_comment_require_approval') == '1')){
                    $sql .= ", status = 0";
                } else {
                    $sql .= ", status = 1";
                }
                
                // Reply to
                if(isset($data['parent_comment_id'])){
                    $sql .= ", parent_comment_id = '" . (int)$data['parent_comment_id'] . "'";
                } else {
                    $data['parent_comment_id'] = 0;
                }
                
                if(!$this->customer->isLogged()){
                    $sql .= ", author = '" . $this->db->escape($data['name']) . "'";
                }
                
                if(!$this->customer->isLogged() && !empty($data['email'])){
                    $sql .= ", email = '" . $this->db->escape($data['email']) . "'";
                }
                
                if(!empty($data['website'])){
                    $sql .= ", website = '" . $this->db->escape($data['website']) . "'";
                }
                
		$this->db->query($sql);
                
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

	public function getCommentsByArticleId($article_id, $data = array()) {
                $sql = "SELECT c.comment_id, c.parent_comment_id, c.text, c.date_added, (CASE WHEN cs.customer_id IS NOT NULL THEN CONCAT(cs.firstname, ' ', cs.lastname) ELSE c.author END) author";
                
                if(!empty($data['filter_parent_id'])){
                    $sql .= ", (SELECT (CASE WHEN pcs.customer_id IS NOT NULL THEN CONCAT(pcs.firstname, ' ', pcs.lastname) ELSE pc.author END) FROM " . DB_PREFIX . "mz_blog_comment pc LEFT JOIN " . DB_PREFIX . "customer pcs ON (pcs.customer_id = pc.customer_id) WHERE pc.comment_id = c.parent_comment_id LIMIT 1) parent_author";
                }
                
                if(isset($data['filter_parent_id']) && !empty($data['filter_sub_comment'])){
                    $sql .= " FROM " . DB_PREFIX . "mz_blog_comment_path cp LEFT JOIN " . DB_PREFIX . "mz_blog_comment c ON (cp.comment_id = c.comment_id)";
                } else {
                    $sql .= " FROM " . DB_PREFIX . "mz_blog_comment c";
                }
                
                $sql .= " LEFT JOIN " . DB_PREFIX . "customer cs ON (cs.customer_id = c.customer_id) WHERE c.article_id = '" . (int)$article_id . "' AND c.status = '1'";
                
                if(isset($data['filter_parent_id'])){
                    if(!empty($data['filter_sub_comment'])){
                        $sql .= " AND cp.path_id = '" . (int)$data['filter_parent_id'] . "' AND cp.comment_id <> cp.path_id";
                    } else {
                        $sql .= " AND c.parent_comment_id = '" . (int)$data['filter_parent_id'] . "'";
                    }
                }
                
                $sql .= " GROUP BY c.comment_id ORDER BY c.date_added";
                
                if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
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

	public function getTotalCommentsByArticleId($article_id, $data = array()) {
		$sql = "SELECT COUNT(DISTINCT c.comment_id) as total";
                
                if(isset($data['filter_parent_id']) && !empty($data['filter_sub_comment'])){
                    $sql .= " FROM " . DB_PREFIX . "mz_blog_comment_path cp LEFT JOIN " . DB_PREFIX . "mz_blog_comment c ON (cp.comment_id = c.comment_id)";
                } else {
                    $sql .= " FROM " . DB_PREFIX . "mz_blog_comment c";
                }
                
                $sql .= " WHERE c.article_id = '" . (int)$article_id . "' AND c.status = '1'";
                
                if(isset($data['filter_parent_id'])){
                    if(!empty($data['filter_sub_comment'])){
                        $sql .= " AND cp.path_id = '" . (int)$data['filter_parent_id'] . "' AND cp.comment_id <> cp.path_id";
                    } else {
                        $sql .= " AND c.parent_comment_id = '" . (int)$data['filter_parent_id'] . "'";
                    }
                }
                
                $query = $this->db->query($sql);

		return $query->row['total'];
	}
}