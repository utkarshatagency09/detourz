<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionModuleMzTags extends model {
        
        /**
         * Get tags of product
         * @param array $data filter data
         * @return array tags
         */
        public function getProductTags($data) {
                $sql = "SELECT t.tag_id, t.name, t.viewed, t.used";
                
                // Join require table for filter
                if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "mz_product_to_tags p2t ON (pf.product_id = p2t.product_id) LEFT JOIN " . DB_PREFIX . "mz_product_tags t ON (t.tag_id = p2t.tag_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "mz_product_to_tags p2t ON (p2c.product_id = p2t.product_id) LEFT JOIN " . DB_PREFIX . "mz_product_tags t ON (t.tag_id = p2t.tag_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "mz_product_tags t LEFT JOIN `" . DB_PREFIX . "mz_product_to_tags` p2t ON (p2t.tag_id = t.tag_id)";
                        
                        if (!empty($data['filter_filter'])) {
                                $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2t.product_id = pf.product_id)";
                        }
                        
		}
                
                $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2t.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND t.language_id = '" . $this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                
                if (!empty($data['filter_category_id'])) {
                    if(is_array($data['filter_category_id'])){
                        $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);
                        
                        if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
                                if(!empty($data['filter_sub_category_depth'])){
                                    $sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
                                }
			} else {
				$sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
			}
                    }else{
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                                if(!empty($data['filter_sub_category_depth'])){
                                    $sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
                                }
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
                    }
		}
                
                if (!empty($data['filter_filter'])) {
                        $implode = array();

                        $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                        foreach ($filters as $filter_id) {
                                $implode[] = (int)$filter_id;
                        }

                        $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
                }
                
                if (!empty($data['filter_manufacturer_id'])) {
                    if(is_array($data['filter_manufacturer_id'])){
                        $sql .= " AND p.manufacturer_id IN (" . implode(',', array_map('intval', $data['filter_manufacturer_id'])) . ")";
                    } else {
                        $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                    }
		}
                
                $sql .= " GROUP BY t.tag_id";
                
                $sort_data = array(
                        'viewed',
                        'used'
                );

                if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                        $sql .= " ORDER BY " . $data['sort'];
                } else {
                        $sql .= " ORDER BY name";
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
        
        /**
         * Get tags of blog article
         * @param array $data filter data
         * @return array tags
         */
        public function getBlogTags($data) {
                $sql = "SELECT t.tag_id, t.name, t.viewed, t.used";
                
                // Join require table for filter
                if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "mz_blog_category_path cp LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_category a2c ON (cp.category_id = a2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "mz_blog_article_to_category a2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_filter af ON (a2c.article_id = af.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_tags a2t ON (pf.article_id = a2t.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article_tags t ON (t.tag_id = a2t.tag_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_tags a2t ON (a2c.article_id = a2t.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article_tags t ON (t.tag_id = a2t.tag_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "mz_blog_article_tags t LEFT JOIN `" . DB_PREFIX . "mz_blog_article_to_tags` a2t ON (p2t.tag_id = t.tag_id)";
                        
                        if (!empty($data['filter_filter'])) {
                                $sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article_filter af ON (a2t.article_id = af.article_id)";
                        }
		}
                
                $sql .= " LEFT JOIN " . DB_PREFIX . "mz_blog_article a ON (a2t.article_id = a.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_article_to_store a2s ON (a.article_id = a2s.article_id) LEFT JOIN " . DB_PREFIX . "mz_blog_author aa ON (aa.author_id = a.author_id) WHERE a.status = '1' AND  t.language_id = '" . $this->config->get('config_language_id') . "' AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                
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
                
                if (!empty($data['filter_author_id'])) {
                    if(is_array($data['filter_author_id'])){
                        $sql .= " AND a.author_id IN (" . implode(',', array_map('intval', $data['filter_author_id'])) . ")";
                    } else {
                        $sql .= " AND a.author_id = '" . (int)$data['filter_author_id'] . "'";
                    }
		}
                
                $sql .= " GROUP BY t.tag_id";
                
                $sort_data = array(
                        'viewed',
                        'used'
                );

                if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                        $sql .= " ORDER BY " . $data['sort'];
                } else {
                        $sql .= " ORDER BY name";
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
        
        /**
         * Get tag by name
         * @param string $tag_name tag name
         * @return array tag
         */
        public function getProductTag($tag_name){
            if($tag_name){
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_product_tags` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND name = LCASE('" . $this->db->escape($tag_name) . "')");
                
                return $query->row;
            }
        }
        
        /**
         * Get tag by name
         * @param string $tag_name tag name
         * @return array tag
         */
        public function getBlogTag($tag_name){
            if($tag_name){
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_blog_article_tags` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND name = LCASE('" . $this->db->escape($tag_name) . "')");
                
                return $query->row;
            }
        }
        
        /**
         * Add tag into product
         * @param int $product_id product id to be link with tag
         * @param array $tags list of tag to link with product
         * @param int $language_id language id
         */
        public function addProductTags($product_id, $tags, $language_id){
                $this->db->query("DELETE p2t FROM `" . DB_PREFIX . "mz_product_to_tags` p2t LEFT JOIN `" . DB_PREFIX . "mz_product_tags` t ON (p2t.tag_id = t.tag_id) WHERE p2t.product_id = '" . (int)$product_id . "' AND t.language_id = '" . (int)$language_id . "'");
                
                $tags = array_filter(array_map('trim', $tags));
                
                foreach($tags as $tag){
                    $query = $this->db->query("SELECT tag_id FROM `" . DB_PREFIX . "mz_product_tags` WHERE language_id = '" . (int)$language_id . "' AND name = LCASE('" . $this->db->escape($tag) . "')");
                    
                    if($query->num_rows){
                        $tag_id = $query->row['tag_id'];
                    } else {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_product_tags` SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($tag) . "'");
                        $tag_id = $this->db->getLastId();
                    }
                    
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_product_to_tags` SET tag_id = '" . (int)$tag_id . "', product_id = '" . (int)$product_id . "'");
                }
        }
        
        /**
         * Add tag into blog article
         * @param int $article_id article id to be link with tag
         * @param array $tags list of tag to link with blog
         * @param int $language_id language id
         */
        public function addBlogTags($article_id, $tags, $language_id){
                $this->db->query("DELETE a2t FROM `" . DB_PREFIX . "mz_blog_article_to_tags` a2t LEFT JOIN `" . DB_PREFIX . "mz_blog_article_tags` t ON (a2t.tag_id = t.tag_id) WHERE a2t.article_id = '" . (int)$article_id . "' AND t.language_id = '" . (int)$language_id . "'");
                
                $tags = array_filter(array_map('trim', $tags));
                
                foreach($tags as $tag){
                    $query = $this->db->query("SELECT tag_id FROM `" . DB_PREFIX . "mz_blog_article_tags` WHERE language_id = '" . (int)$language_id . "' AND name = LCASE('" . $this->db->escape($tag) . "')");
                    
                    if($query->num_rows){
                        $tag_id = $query->row['tag_id'];
                    } else {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_blog_article_tags` SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($tag) . "'");
                        $tag_id = $this->db->getLastId();
                    }
                    
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_blog_article_to_tags` SET tag_id = '" . (int)$tag_id . "', article_id = '" . (int)$article_id . "'");
                }
        }
        
        /**
         * Increase viewed count of tag
         * @param string $tag_name tag name
         */
        public function updateProductTagViewed($tag_name) {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_product_tags SET viewed = (viewed + 1) WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND  name = LCASE('" . $this->db->escape($tag_name) . "')");
	}
        
        /**
         * Increase viewed count of tag
         * @param string $tag_name tag name
         */
        public function updateBlogTagViewed($tag_name) {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_article_tags SET viewed = (viewed + 1) WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND  name = LCASE('" . $this->db->escape($tag_name) . "')");
	}
}
