<?php
class ControllerExtensionMazaBlogAll extends Controller {
	public function index() {
		$this->load->language('extension/maza/blog/all');

		$this->load->model('extension/maza/blog/article');

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
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
                
		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = (int)$this->mz_skin_config->get('blog_article_grid_limit');
		}

		$url = '';

		if (isset($this->request->get['filter'])) {
			$url .= '&filter=' . $this->request->get['filter'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
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

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/maza/blog/all', $url)
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['articles'] = array();
                        
		$filter_data = array(
			'filter_filter'      => $filter,
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit
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
				'author'      => $result['author'],
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

		if (isset($this->request->get['filter'])) {
			$url .= '&filter=' . $this->request->get['filter'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}


		$pagination = new Pagination();
		$pagination->total = $article_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/maza/blog/all', $url . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($article_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($article_total - $limit)) ? $article_total : ((($page - 1) * $limit) + $limit), $article_total, ceil($article_total / $limit));

		// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
		if ($page == 1) {
			$this->document->addLink($this->url->link('extension/maza/blog/all'), 'canonical');
		} else {
			$this->document->addLink($this->url->link('extension/maza/blog/all', 'page='. $page), 'canonical');
		}
		
		if ($page > 1) {
			$this->document->addLink($this->url->link('extension/maza/blog/all', (($page - 2) ? 'page='. ($page - 1) : '')), 'prev');
		}

		if ($limit && ceil($article_total / $limit) > $page) {
			$this->document->addLink($this->url->link('extension/maza/blog/all', 'page='. ($page + 1)), 'next');
		}
					
		$data['continue'] = $this->url->link('common/home');

		if ($this->config->get('maza_schema')) {
			$this->mz_schema->add(maza\Schema::breadcrumb($data['breadcrumbs']));
		}
		
		if ($this->config->get('maza_ogp')) {
			if ($page == 1) {
				$this->mz_document->addOGP('og:url', $this->url->link('extension/maza/blog/all'));
			} else {
				$this->mz_document->addOGP('og:url', $this->url->link('extension/maza/blog/all', 'page='. $page));
			}

			$this->mz_document->addOGP('og:type', 'website');
		}

		// Content
		$data['mz_content'] = $this->mz_load->view($this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]), $data, 'extension/maza/blog/all');
					
		// Parse component
		$page_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
		$data['mz_component'] = $this->mz_load->view($page_component, $data, 'extension/maza/blog/all');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
					
		$this->response->setOutput($this->load->view('extension/maza/blog/all', $data));
	}
}
