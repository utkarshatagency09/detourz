<?php
class ControllerExtensionMazaBlogArticle extends Controller {
	public function index(): void {
		$this->load->language('extension/maza/blog/article');

		$data['breadcrumbs'] = array();
                
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_blog'),
			'href' => $this->url->link('extension/maza/blog/home')
		);

		$this->load->model('extension/maza/blog/category');
		$this->load->model('extension/maza/blog/author');
		$this->load->model('extension/maza/blog/article');
		$this->load->model('catalog/product');

		if($this->config->get('maza_cdn')){
			$this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.css');
			$this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.js');
		} else {
			$this->document->addStyle('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.css');
			$this->document->addScript('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.js');
		}

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_extension_maza_blog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('extension/maza/blog/category', 'path=' . $path)
					);
				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_extension_maza_blog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('extension/maza/blog/category', 'path=' . $this->request->get['path'] . $url),
				);

				$data['category'] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('extension/maza/blog/category', 'path=' . $this->request->get['path'] . $url),
				);
			}
		}

		if (isset($this->request->get['author_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_author'),
				'href' => $this->url->link('extension/maza/blog/author')
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$author_info = $this->model_extension_maza_blog_author->getAuthor($this->request->get['author_id']);

			if ($author_info) {
				$data['breadcrumbs'][] = array(
					'text' => $author_info['name'],
					'href' => $this->url->link('extension/maza/blog/author', 'author_id=' . $this->request->get['author_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('extension/maza/blog/author', $url)
			);
		}

		if (isset($this->request->get['article_id'])) {
			$article_id = (int)$this->request->get['article_id'];
		} else {
			$article_id = 0;
		}

		$article_info = $this->model_extension_maza_blog_article->getArticle($article_id);

		if ($article_info) {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['author_id'])) {
				$url .= '&author_id=' . $this->request->get['author_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $article_info['name'],
				'href' => $this->url->link('extension/maza/blog/article', $url . '&article_id=' . $this->request->get['article_id'])
			);

			$this->document->setTitle($article_info['meta_title']?:$article_info['name']);
			$this->document->setDescription($article_info['meta_description']);
			$this->document->setKeywords($article_info['meta_keyword']);
			$this->document->addLink($this->url->link('extension/maza/blog/article', 'article_id=' . $this->request->get['article_id']), 'canonical');

			$data['heading_title'] = $article_info['name'];

			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

			$data['article_id'] = (int)$this->request->get['article_id'];
                        
			// Extra
			$data['by_author']  = sprintf($this->language->get('text_author'), $this->url->link('extension/maza/blog/author', 'author_id=' . $article_info['author_id']), $article_info['author']);
			$data['timestamp']  = strftime('%e %b %Y', strtotime($article_info['date_available'] ?: $article_info['date_added']));
			$data['viewed']     = $article_info['viewed'];
					
			// Author
			$data['author']['name'] = $article_info['author'];
			$data['author']['description'] = html_entity_decode($article_info['author_description'], ENT_QUOTES, 'UTF-8');
			$data['author']['link'] = $this->url->link('extension/maza/blog/author', 'author_id=' . $article_info['author_id']);

			if ($article_info['author_image']) {
				$data['author']['image'] = $this->model_tool_image->resize($article_info['author_image'], $this->mz_skin_config->get('blog_article_author_thumb_width'), $this->mz_skin_config->get('blog_article_author_thumb_height'));
			} else {
				$data['author']['image'] = $this->model_tool_image->resize('placeholder.png', $this->mz_skin_config->get('blog_article_author_thumb_width'), $this->mz_skin_config->get('blog_article_author_thumb_height'));
			}
                        
			if ($article_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($article_info['image'], $this->mz_skin_config->get('blog_article_image_width'), $this->mz_skin_config->get('blog_article_image_height'));
			} else {
				$data['thumb'] = '';
			}
                        
			$data['srcset'] = $this->model_extension_maza_image->getSrcSet($this->mz_skin_config->get('blog_article_image_srcset'), $article_info['image'], $this->mz_skin_config->get('blog_article_image_width'), $this->mz_skin_config->get('blog_article_image_height'));
                        
			$data['images'] = array();

			$results = $this->model_extension_maza_blog_article->getArticleImages($this->request->get['article_id']);
			foreach ($results as $result) {
				$data['images'][] = array(
					'image' => $this->model_tool_image->resize($result['image'], $this->mz_skin_config->get('blog_article_image_width'), $this->mz_skin_config->get('blog_article_image_height')),
					'srcset' => $this->model_extension_maza_image->getSrcSet($this->mz_skin_config->get('blog_article_image_srcset'), $result['image'], $this->mz_skin_config->get('blog_article_image_width'), $this->mz_skin_config->get('blog_article_image_height')),
				);
			}

			$data['audios'] = $this->model_extension_maza_blog_article->getArticleAudios($this->request->get['article_id']);

			if($data['audios']){
				$this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.2.17/mediaelementplayer.min.css');
				$this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.2.17/mediaelement-and-player.min.js');
			}
                        
			// Comment
			$data['comment_status']     = $this->mz_skin_config->get('blog_comment_status');
			$data['comment_guest']      = $this->mz_skin_config->get('blog_allow_guest_comment') || $this->customer->isLogged();
			$data['comments'] 			= (int)$article_info['comments'];
			$data['is_logged'] 			= $this->customer->isLogged();

			// Captcha
			$data['comment_form_captcha']   = $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $this->mz_skin_config->get('blog_comment_form_captcha');
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
                        
			// Share
			$data['share'] = $this->url->link('extension/maza/blog/article', 'article_id=' . (int)$this->request->get['article_id']);
                        
			// Tags
			$data['tags'] = array();
                        
			if ($article_info['tag']) {
				$tags = explode(',', $article_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('extension/maza/blog/search', 'tag=' . trim($tag))
					);
				}
			}

			$this->model_extension_maza_blog_article->updateViewed($this->request->get['article_id']);

			$data['description'] = $this->mz_load->view(html_entity_decode($article_info['description'], ENT_QUOTES, 'UTF-8'), $data, 'extension/maza/blog/article');

			// Related article
			$data['articles'] = array();

			$results = $this->model_extension_maza_blog_article->getArticleRelated($this->request->get['article_id']);

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
					'href'        => $this->url->link('extension/maza/blog/article', 'path=' . $this->request->get['path'] .  '&article_id=' . $result['article_id'])
				);
			}

			// Related product
			$data['products'] = array();

			$results = $this->model_extension_maza_blog_article->getArticleProduct($this->request->get['article_id']);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
                        
			// Parse component
			$page_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
			$data['mz_component'] = $this->mz_load->view($page_component, $data, 'extension/maza/blog/article');

			// Content
			$data['mz_content'] = $this->mz_load->view($this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]), $data, 'extension/maza/blog/article');

			// Schema
			if ($this->config->get('maza_schema')) {
				$this->mz_schema->add(maza\Schema::breadcrumb($data['breadcrumbs']));
				$this->mz_schema->add(array(
					'@context' => 'https://schema.org',
					'@type' => 'BlogPosting',
					'mainEntityOfPage' =>  [
						'@type' => 'WebPage',
						'@id' => $this->url->link('extension/maza/blog/article', 'article_id=' . $article_info['article_id'])
					],
					'headline' => $article_info['name'],
					'image' => maza\getImageURL($article_info['image']),
					'editor' => $article_info['author'],
					'publisher' => [
						'@type' => 'Organization',
						'name' => $this->config->get('config_name'),
						'logo' => maza\getImageURL($this->config->get('config_logo'))
					],
					'url' => $this->url->link('extension/maza/blog/article', 'article_id=' . $article_info['article_id']),
					'datePublished' => $article_info['date_available'] ?: $article_info['date_added'],
					'dateCreated' => $article_info['date_added'],
					'dateModified' => $article_info['date_modified'],
					'description' => $article_info['meta_description'],
					'articleBody' => $article_info['description'],
					'author' => ['@type' => 'Person', 'name' => $article_info['author']], 
				));
			}
			

			// OGP
			if ($this->config->get('maza_ogp')) {
				$this->mz_document->addOGP('og:type', 'article');
				$this->mz_document->addOGP('og:title', $article_info['meta_title']?:$article_info['name']);
				$this->mz_document->addOGP('og:url', $this->url->link('extension/maza/blog/article', 'article_id=' . $article_info['article_id']));
				$this->mz_document->addOGP('og:description', (string)$article_info['meta_description']);
				$this->mz_document->addOGP('article:published_time', $article_info['date_available'] ?: $article_info['date_added']);
				$this->mz_document->addOGP('article:modified_time', $article_info['date_modified']);
				$this->mz_document->addOGP('article:author', $article_info['author']);

				if (is_file(DIR_IMAGE . $article_info['image'])) {
					$this->mz_document->addOGP('og:image', $this->model_tool_image->resize($article_info['image'], 1200, 630), '');
					$this->mz_document->addOGP('og:image:width', 1200);
					$this->mz_document->addOGP('og:image:height', 630);
					$this->mz_document->addOGP('og:image', $this->model_tool_image->resize($article_info['image'], 200, 200), '');
					$this->mz_document->addOGP('og:image:width', 200);
					$this->mz_document->addOGP('og:image:height', 200);
					$this->mz_document->addOGP('og:image', maza\getImageURL($article_info['image']));
				}

				foreach (explode(',', $article_info['tag']) as $tag){
					$this->mz_document->addOGP('article:tag', $tag);
				}
			}
			       
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/maza/blog/article', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['author_id'])) {
				$url .= '&author_id=' . $this->request->get['author_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('extension/maza/blog/article', $url . '&article_id=' . $article_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['continue'] = $this->url->link('extension/maza/blog/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}    
	}

	public function comment() {
		if(!$this->mz_skin_config->get('blog_comment_status')) return;
                
		$this->load->language('extension/maza/blog/article');

		$this->load->model('extension/maza/blog/comment');
		$this->load->model('extension/maza/blog/article');
                
		if (isset($this->request->get['parent_id'])) {
			$parent_id = (int)$this->request->get['parent_id'];
		} else {
			$parent_id = 0;
		}
                
		if (isset($this->request->get['sub_comment'])) {
			$sub_comment = (int)$this->request->get['sub_comment'];
		} else {
			$sub_comment = 0;
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}
                
		if ($parent_id) {
			$order = 'ASC';
		} else {
			$order = 'DESC';
		}
                
		$limit = 5;

		$artical_info = $this->model_extension_maza_blog_article->getArticle($this->request->get['article_id']);
		
		if($artical_info && $artical_info['allow_comment']){
			$filter_data = array();
			$filter_data['filter_parent_id']    = $parent_id;
			$filter_data['filter_sub_comment']  = $sub_comment;
			$filter_data['order'] = $order;
			$filter_data['start'] = ($page - 1) * $limit;
			$filter_data['limit'] = $limit;
			
			$data['comments'] = array();
			$comment_total = $this->model_extension_maza_blog_comment->getTotalCommentsByArticleId($this->request->get['article_id'], $filter_data);

			$results = $this->model_extension_maza_blog_comment->getCommentsByArticleId($this->request->get['article_id'], $filter_data);
			
			if($parent_id){
				foreach ($results as $result) {
					$data['comments'][] = array(
						'comment_id' => $result['comment_id'],
						'author'     => $result['author'],
						'text'       => nl2br($result['text']),
						'date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
						'parent_id'  => $result['parent_comment_id'],
						'parent_author' => $result['parent_author'],
					);
				}
				if(($page * $limit) < $comment_total){
					$data['view_more'] = $this->url->link('extension/maza/blog/article/comment', 'sub_comment=1&parent_id=' . $parent_id . '&page=' . ((int)$page + 1) . '&article_id=' . (int)$this->request->get['article_id'], true);
					$data['view_more_total'] = $comment_total - ($page * $limit);
				}
			} else {
				foreach ($results as $result) {
					$reply_total = $this->model_extension_maza_blog_comment->getTotalCommentsByArticleId($this->request->get['article_id'], ['filter_parent_id' => $result['comment_id'], 'filter_sub_comment' => 1]);

					if($reply_total){
						$reply_href = $this->url->link('extension/maza/blog/article/comment', 'sub_comment=1&parent_id=' . $result['comment_id'] . '&article_id=' . (int)$this->request->get['article_id'], true);
					} else {
						$reply_href = '';
					}

					$data['comments'][] = array(
						'comment_id' => $result['comment_id'],
						'author'     => $result['author'],
						'text'       => nl2br($result['text']),
						'date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
						'reply_total' => $reply_total,
						'reply_href'  => $reply_href
					);
				}
				
				if(($page * $limit) < $comment_total){
					$data['view_more'] = $this->url->link('extension/maza/blog/article/comment', 'page=' . ((int)$page + 1) . '&article_id=' . (int)$this->request->get['article_id'], true);
					$data['view_more_total'] = $comment_total - ($page * $limit);
				}
			}
			
			$data['parent_id'] = (int)$parent_id;
			$data['page'] = (int)$page;
			
			$this->response->setOutput($this->load->view('extension/maza/blog/comment', $data));
		}
	}

	public function write(): void {
		$json = array();
		
		if($this->mz_skin_config->get('blog_comment_status') && ($this->mz_skin_config->get('blog_allow_guest_comment') || $this->customer->isLogged())){
			$this->load->language('extension/maza/blog/article');

			if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
					$json['error_text'] = $this->language->get('error_text');
				}

				if(!$this->customer->isLogged()){
					if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
						$json['error_name'] = $this->language->get('error_name');
					}

					if(!empty($this->request->post['email']) && !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)){
						$json['error_email'] = $this->language->get('error_email');
					}
				}
				
				// Captcha
				if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $this->mz_skin_config->get('blog_comment_form_captcha')) {
					$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

					if ($captcha) {
						$json['error_captcha'] = $captcha;
					}
				}

				if (empty($json)) {
					$this->load->model('extension/maza/blog/comment');

					$this->model_extension_maza_blog_comment->addComment($this->request->get['article_id'], $this->request->post);

					$json['success'] = $this->language->get('text_success');
				}
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
	/**
	 * search articles by name
	 * @output HTML articles list
	 */
	public function autocomplete(): void {
		$data = array();

		if (!empty($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/blog/article');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_category_id'])) {
				$filter_category_id = $this->request->get['filter_category_id'];
			} else {
				$filter_category_id = '';
			}
                        
			if (isset($this->request->get['filter_sub_category'])) {
				$filter_sub_category = $this->request->get['filter_sub_category'];
			} else {
				$filter_sub_category = 0;
			}
                        
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_category_id' => $filter_category_id,
				'filter_sub_category' => $filter_sub_category,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_extension_maza_blog_article->getArticles($filter_data);

			foreach ($results as $result) {
				if(is_file(DIR_IMAGE . $result['image'])){
					$image = $this->model_tool_image->resize($result['image'], 100, ($this->mz_skin_config->get('blog_article_grid_image_height') * 100) / $this->mz_skin_config->get('blog_article_grid_image_width'));
				} else {
					$image = $this->model_tool_image->resize('mz_no_image.png', 100, ($this->mz_skin_config->get('blog_article_grid_image_height') * 100) / $this->mz_skin_config->get('blog_article_grid_image_width'));
				}
                                
				$data['articles'][] = array(
					'article_id' => $result['article_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'image'      => $image,
					'author'     => $result['author'],
					'author_href' => $this->url->link('extension/maza/blog/author', 'author_id=' . $result['author_id']),
					'comments'    => (int)$result['comments'],
					'viewed'      => (int)$result['viewed'],
					'timestamp'   => date($this->language->get('date_format_short'), strtotime($result['date_available']?:$result['date_added'])),
					'href'        => $this->url->link('extension/maza/blog/article', 'article_id=' . $result['article_id'])
				);
			}
		}

		$this->response->setOutput($this->load->view('extension/maza/blog/autocomplete', $data));
	}
}
