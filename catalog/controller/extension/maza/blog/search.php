<?php
class ControllerExtensionMazaBlogSearch extends Controller {
	public function index() {
		$this->load->language('extension/maza/blog/search');

		$this->load->model('extension/maza/blog/category');
		$this->load->model('extension/maza/blog/article');

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} elseif (isset($this->request->get['search'])) {
			$tag = $this->request->get['search'];
		} else {
			$tag = '';
		}

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
		} else {
			$category_id = 0;
		}

		if (isset($this->request->get['sub_category'])) {
			$sub_category = $this->request->get['sub_category'];
		} else {
			$sub_category = true;
		}
                
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'a.date_available';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['search'])) {
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->request->get['search']);
		} elseif (isset($this->request->get['tag'])) {
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->language->get('heading_tag') . $this->request->get['tag']);
		} else {
			$this->document->setTitle($this->language->get('heading_title'));
		}

		$data['breadcrumbs'] = array();
                
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_blog'),
			'href' => $this->url->link('extension/maza/blog/home')
		);

		$url = '';

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['tag'])) {
			$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		if (isset($this->request->get['sub_category'])) {
			$url .= '&sub_category=' . $this->request->get['sub_category'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/maza/blog/search', $url)
		);

		if (isset($this->request->get['search'])) {
			$data['heading_title'] = $this->language->get('heading_title') .  ' - ' . $this->request->get['search'];
		} else {
			$data['heading_title'] = $this->language->get('heading_title');
		}
                
		$limit = $this->mz_skin_config->get('blog_article_grid_limit');

		$data['articles'] = array();

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$filter_data = array(
				'filter_name'         => $search,
				'filter_tag'          => $tag,
				'filter_description'  => true,
				'filter_category_id'  => $category_id,
				'filter_sub_category' => $sub_category,
				'sort'                => $sort,
				'order'               => $order,
				'start'               => ($page - 1) * $limit,
				'limit'               => $limit
			);

			$article_total = $this->model_extension_maza_blog_article->getTotalArticles($filter_data);

			$results = $this->model_extension_maza_blog_article->getArticles($filter_data);

			foreach ($results as $result) {
				if ($result['image']) {
						$thumb = $this->model_tool_image->resize($result['image'], $this->mz_skin_config->get('blog_article_grid_image_width'), $this->mz_skin_config->get('blog_article_grid_image_height'));
				} else {
						$thumb = $this->model_tool_image->resize('placeholder.png', $this->mz_skin_config->get('blog_article_grid_image_width'), $this->mz_skin_config->get('blog_article_grid_image_height'));
				}
				
				$srcset = $this->model_extension_maza_image->getSrcSet($this->mz_skin_config->get('blog_article_grid_image_srcset'), $result['image'], $this->mz_skin_config->get('blog_article_grid_image_width'), $this->mz_skin_config->get('blog_article_grid_image_height'));
				
				// Additional images
				if($this->mz_skin_config->get('blog_article_grid_additional_image')){
					$additional_images = $this->model_extension_maza_blog_article->getArticleImages($result['article_id']);
				} else {
					$additional_images = array();
				}
				
				$images = array();
				foreach ($additional_images as $image_result) {
					$images[] = array(
						'image' => $this->model_tool_image->resize($image_result['image'], $this->mz_skin_config->get('blog_article_grid_image_width'), $this->mz_skin_config->get('blog_article_grid_image_height')),
						'srcset' => $this->model_extension_maza_image->getSrcSet($this->mz_skin_config->get('blog_article_grid_image_srcset'), $image_result['image'], $this->mz_skin_config->get('blog_article_grid_image_width'), $this->mz_skin_config->get('blog_article_grid_image_height')),
					);
				}

				// category
				if ($result['category_id']) {
					$category = implode(' > ', array_column($this->model_extension_maza_blog_category->getCategoryPath($result['category_id']), 'name'));
				} else {
					$category =  '';
				}

				$data['articles'][] = array(
						'article_id'  => $result['article_id'],
						'thumb'       => $thumb,
						'srcset'      => $srcset,
						'images'      => $images,
						'name'        => $result['name'],
						'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->mz_skin_config->get('blog_article_grid_description_limit')) . '..',
						'author'     => $result['author'],
						'author_href' => $this->url->link('extension/maza/blog/author', 'author_id=' . $result['author_id']),
						'category'    => $category,
						'category_href' => $this->url->link('extension/maza/blog/category', 'path=' . $result['category_id']),
						'comments'    => (int)$result['comments'],
						'viewed'      => (int)$result['viewed'],
						'timestamp'   => strftime('%e %b %Y', strtotime($result['date_available']?:$result['date_added'])),
						'href'        => $this->url->link('extension/maza/blog/article', 'article_id=' . $result['article_id'])
				);
			}
                        
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			$pagination = new Pagination();
			$pagination->total = $article_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/maza/blog/search', $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($article_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($article_total - $limit)) ? $article_total : ((($page - 1) * $limit) + $limit), $article_total, ceil($article_total / $limit));

		}

		$data['search'] = $search;
		$data['category_id'] = $category_id;
		$data['sub_category'] = $sub_category;

		// Content
		$data['mz_content'] = $this->mz_load->view($this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]), $data, 'extension/maza/blog/search');
                
		// Parse component
		$page_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
		$data['mz_component'] = $this->mz_load->view($page_component, $data, 'extension/maza/blog/search');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/maza/blog/search', $data));
	}
}
