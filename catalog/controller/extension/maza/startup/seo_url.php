<?php
class ControllerExtensionMazaStartupSeoUrl extends Controller {
	public function index(): void {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		// Decode URL
		if(isset($this->request->get['route'])){
			$page = explode('=', $this->request->get['route']);
		} else {
			$page = array();
		}
		
		$pages = array(
			'mz_blog_article_id',
			'mz_blog_category_id',
			'mz_blog_author_id',
			'mz_page_id'
		);
                
		if (isset($this->request->get['_route_']) && $page && in_array($page[0], $pages)) {
			$parts = explode('/', $this->request->get['_route_']);

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}
                        
			foreach ($parts as $part) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
                                
				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);
                                        
					if ($url[0] == 'mz_blog_article_id') {
						$this->request->get['article_id'] = $url[1];
					}

					if ($url[0] == 'mz_blog_category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}

					if ($url[0] == 'mz_blog_author_id') {
						$this->request->get['author_id'] = $url[1];
					}
                                        
					if ($url[0] == 'mz_page_id') {
						$this->request->get['page_id'] = $url[1];
					}
				}
			}
                        
			if (isset($this->request->get['article_id'])) {
				$this->request->get['route'] = 'extension/maza/blog/article';
			} elseif (isset($this->request->get['path'])) {
				$this->request->get['route'] = 'extension/maza/blog/category';
			} elseif (isset($this->request->get['author_id'])) {
				$this->request->get['route'] = 'extension/maza/blog/author';
			} elseif (isset($this->request->get['page_id'])) {
				$this->request->get['route'] = 'extension/maza/page';
			}
		}
	}

	public function rewrite(string $link): string {
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();
                
		if(isset($url_info['query'])){
			parse_str($url_info['query'], $data);
		}
		
		foreach ($data as $key => $value) {
			if (isset($data['route'])) {
				if (($data['route'] == 'extension/maza/blog/article' && $key == 'article_id') || (($data['route'] == 'extension/maza/blog/author' || $data['route'] == 'extension/maza/blog/article') && $key == 'author_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape('mz_blog_' . $key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif ($data['route'] == 'extension/maza/page' && $key == 'page_id') {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape('mz_page_id=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif (strpos($data['route'], 'extension/maza/blog') === 0 && $key == 'path') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'mz_blog_category_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				}
			}
		}

		// Add SQL keyword of route
		if($this->config->get('maza_query_keyword') && version_compare(VERSION, '4.0.0.0') < 0 && !$url && isset($data['route'])){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($data['route']) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
			
			if ($query->num_rows && $query->row['keyword']) {
				$url = '/' . $query->row['keyword'];
			}
		}

		if ($url || (isset($data['route']) && $data['route'] == $this->config->get('action_default'))) {
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			return $link;
		}
	}
}
