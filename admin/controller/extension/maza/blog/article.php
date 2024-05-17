<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaBlogArticle extends Controller {
        private $error = array();
    
        public function index(): void {
			$this->load->language('extension/maza/blog/article');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/blog/article');

			$this->getList();
		}
        
        /**
         * Add article
         */
        public function add(): void {
			$this->load->language('extension/maza/blog/article');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/blog/article');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_extension_maza_blog_article->addArticle($this->request->post);

				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';
							
				if (isset($this->request->get['filter_name'])) {
					$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_allow_comment'])) {
					$url .= '&filter_allow_comment=' . $this->request->get['filter_allow_comment'];
				}
				if (isset($this->request->get['filter_author_id'])) {
					$url .= '&filter_author_id=' . $this->request->get['filter_author_id'];
				}
				if (isset($this->request->get['filter_status'])) {
					$url .= '&filter_status=' . $this->request->get['filter_status'];
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
				if(isset($this->request->get['mz_theme_code'])){
					$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
				}
				if(isset($this->request->get['mz_skin_id'])){
					$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
				}

				$this->response->redirect($this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getForm();
		}
        
        /**
         * Edit article
         */
		public function edit(): void {
			$this->load->language('extension/maza/blog/article');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/blog/article');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_extension_maza_blog_article->editArticle($this->request->get['article_id'], $this->request->post);

				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';

				if (isset($this->request->get['filter_name'])) {
					$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_allow_comment'])) {
					$url .= '&filter_allow_comment=' . $this->request->get['filter_allow_comment'];
				}
				if (isset($this->request->get['filter_author_id'])) {
					$url .= '&filter_author_id=' . $this->request->get['filter_author_id'];
				}
				if (isset($this->request->get['filter_status'])) {
					$url .= '&filter_status=' . $this->request->get['filter_status'];
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
				if(isset($this->request->get['mz_theme_code'])){
					$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
				}
				if(isset($this->request->get['mz_skin_id'])){
					$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
				}

				$this->response->redirect($this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getForm();
		}
        
        /**
         * Delete individual article
         */
		public function delete(): void {
			$this->load->language('extension/maza/blog/article');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/blog/article');

			if (isset($this->request->post['selected']) && $this->validateDelete()) {
				foreach ($this->request->post['selected'] as $article_id) {
					$this->model_extension_maza_blog_article->deleteArticle($article_id);
				}

				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';

				if (isset($this->request->get['filter_name'])) {
					$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_allow_comment'])) {
					$url .= '&filter_allow_comment=' . $this->request->get['filter_allow_comment'];
				}
				if (isset($this->request->get['filter_author_id'])) {
					$url .= '&filter_author_id=' . $this->request->get['filter_author_id'];
				}
				if (isset($this->request->get['filter_status'])) {
					$url .= '&filter_status=' . $this->request->get['filter_status'];
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
				if(isset($this->request->get['mz_theme_code'])){
					$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
				}
				if(isset($this->request->get['mz_skin_id'])){
					$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
				}

				$this->response->redirect($this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getList();
		}
        
        /**
         * Copy article
         */
        public function copy(): void {
			$this->load->language('extension/maza/blog/article');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/blog/article');

			if (isset($this->request->post['selected']) && $this->validateCopy()) {
				foreach ($this->request->post['selected'] as $product_id) {
					$this->model_extension_maza_blog_article->copyArticle($product_id);
				}

				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';

				if (isset($this->request->get['filter_name'])) {
					$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_allow_comment'])) {
					$url .= '&filter_allow_comment=' . $this->request->get['filter_allow_comment'];
				}
				if (isset($this->request->get['filter_author_id'])) {
					$url .= '&filter_author_id=' . $this->request->get['filter_author_id'];
				}
				if (isset($this->request->get['filter_status'])) {
					$url .= '&filter_status=' . $this->request->get['filter_status'];
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
				if(isset($this->request->get['mz_theme_code'])){
					$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
				}
				if(isset($this->request->get['mz_skin_id'])){
					$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
				}

				$this->response->redirect($this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getList();
		}
        
        
        /**
         * Get list of article
         */
        protected function getList(): void {
                $this->load->model('tool/image');
            
                if (isset($this->request->get['filter_name'])) {
					$filter_name = $this->request->get['filter_name'];
				} else {
					$filter_name = '';
				}
				if (isset($this->request->get['filter_allow_comment'])) {
					$filter_allow_comment = $this->request->get['filter_allow_comment'];
				} else {
					$filter_allow_comment = '';
				}
				if (isset($this->request->get['filter_author_id'])) {
					$filter_author_id = $this->request->get['filter_author_id'];
				} else {
					$filter_author_id = '';
				}
				if (isset($this->request->get['filter_status'])) {
					$filter_status = $this->request->get['filter_status'];
				} else {
					$filter_status = '';
				}
				if (isset($this->request->get['sort'])) {
					$sort = $this->request->get['sort'];
				} else {
					$sort = 'a.date_added';
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

                $url = '';
                
                if(isset($this->request->get['mz_theme_code'])){
                    $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                    $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
                
                // Header
                $header_data = array();
                $header_data['title'] = $this->language->get('text_list');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                // $header_data['menu'] = array();
                // if ($this->user->hasPermission('access', 'extension/maza/blog')) $header_data['menu'][] = array('name' => $this->language->get('tab_dashboard'), 'id' => 'tab-mz-dashboard', 'href' => $this->url->link('extension/maza/blog', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/category')) $header_data['menu'][] = array('name' => $this->language->get('tab_category'), 'id' => 'tab-mz-category', 'href' => $this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // $header_data['menu'][] = array('name' => $this->language->get('tab_article'), 'id' => 'tab-mz-article', 'href' => false);
                // if ($this->user->hasPermission('access', 'extension/maza/blog/author')) $header_data['menu'][] = array('name' => $this->language->get('tab_author'), 'id' => 'tab-mz-author', 'href' => $this->url->link('extension/maza/blog/author', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/comment')) $header_data['menu'][] = array('name' => $this->language->get('tab_comment'), 'id' => 'tab-mz-comment', 'href' => $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/report')) $header_data['menu'][] = array('name' => $this->language->get('tab_report'), 'id' => 'tab-mz-report', 'href' => $this->url->link('extension/maza/blog/report', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/setting')) $header_data['menu'][] = array('name' => $this->language->get('tab_setting'), 'id' => 'tab-mz-setting', 'href' => $this->url->link('extension/maza/blog/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
                
                
                // $header_data['menu_active'] = 'tab-mz-article';
                
                $url = '';
                if (isset($this->request->get['filter_name'])) {
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_allow_comment'])) {
                        $url .= '&filter_allow_comment=' . $this->request->get['filter_allow_comment'];
                }
                if (isset($this->request->get['filter_author_id'])) {
                        $url .= '&filter_author_id=' . $this->request->get['filter_author_id'];
                }
                if (isset($this->request->get['filter_status'])) {
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
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
                
                $header_data['buttons'][] = array(
                    'id' => 'button-add',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_add'),
                    'icon' => 'fa-plus',
                    'href' => $this->url->link('extension/maza/blog/article/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => false,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-copy',
                    'name' => '',
                    'tooltip' => $this->language->get('button_copy'),
                    'icon' => 'fa-copy',
                    'class' => 'btn-default',
                    'href' => FALSE,
                    'formaction' => $this->url->link('extension/maza/blog/article/copy', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => 'form-mz-article',
                    'confirm' => $this->language->get('text_confirm')
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-delete',
                    'name' => '',
                    'tooltip' => $this->language->get('button_delete'),
                    'icon' => 'fa-trash',
                    'class' => 'btn-danger',
                    'formaction' => $this->url->link('extension/maza/blog/article/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-mz-article',
                    'confirm' => $this->language->get('text_confirm')
                );
                $header_data['form_target_id'] = 'form-mz-article';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Article list
                
                $data['articles'] = array();

				$filter_data = array(
					'filter_name' => $filter_name,
					'filter_allow_comment' => $filter_allow_comment,
					'filter_author_id' => $filter_author_id,
					'filter_status' => $filter_status,
					'sort'  => $sort,
					'order' => $order,
					'start' => ($page - 1) * $this->config->get('config_limit_admin'),
					'limit' => $this->config->get('config_limit_admin')
				);

				$article_total = $this->model_extension_maza_blog_article->getTotalArticles($filter_data);

				$results = $this->model_extension_maza_blog_article->getArticles($filter_data);

				foreach ($results as $result) {
                        if(is_file(DIR_IMAGE . $result['image'])){
                            $image = $this->model_tool_image->resize($result['image'], 40, 40);
                        } else {
                            $image = $this->model_tool_image->resize('no_image.png', 40, 40);
                        }

						if ($result['status'] == '2') {
							$status = $this->language->get('text_unlisted');
						} elseif ($result['status'] == '1'){
							$status = $this->language->get('text_enabled');
						} else {
							$status = $this->language->get('text_disabled');
						}
                    
						$data['articles'][] = array(
							'article_id' => $result['article_id'],
							'name'        => $result['name'],
							'image'       => $image,
							'author'      => $result['author'],
							'status'      => $status,
							'sort_order'  => $result['sort_order'],
							'date_added'  => $result['date_added'],
							'edit'        => $this->url->link('extension/maza/blog/article/edit', 'user_token=' . $this->session->data['user_token'] . '&article_id=' . $result['article_id'] . $url, true),
						);
				}
                
                $this->load->model('extension/maza/blog/author');
                
                $data['authors'] = $this->model_extension_maza_blog_author->getAuthors();

				if(isset($this->session->data['warning'])){
					$data['warning'] = $this->session->data['warning'];
					unset($this->session->data['warning']);
                }
				if (isset($this->session->data['success'])) {
					$data['success'] = $this->session->data['success'];
					unset($this->session->data['success']);
				}
				if (isset($this->request->post['selected'])) {
					$data['selected'] = (array)$this->request->post['selected'];
				} else {
					$data['selected'] = array();
				}
                
                // Sort order
				$url = '';
                if (isset($this->request->get['filter_name'])) {
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_allow_comment'])) {
                        $url .= '&filter_allow_comment=' . $this->request->get['filter_allow_comment'];
                }
                if (isset($this->request->get['filter_author_id'])) {
                        $url .= '&filter_author_id=' . $this->request->get['filter_author_id'];
                }
                if (isset($this->request->get['filter_status'])) {
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
				if ($order == 'ASC') {
						$url .= '&order=DESC';
				} else {
						$url .= '&order=ASC';
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				$data['sort_name'] = $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . '&sort=ad.name' . $url, true);
				$data['sort_sort_order'] = $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . '&sort=a.sort_order' . $url, true);
                $data['sort_status'] = $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . '&sort=a.status' . $url, true);
                $data['sort_date_added'] = $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . '&sort=a.date_added' . $url, true);
                $data['sort_author'] = $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . '&sort=author' . $url, true);
                
                $data['sort'] = $sort;
				$data['order'] = $order;
						
				// Pagination
				$url = '';
                if (isset($this->request->get['filter_name'])) {
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_allow_comment'])) {
                        $url .= '&filter_allow_comment=' . $this->request->get['filter_allow_comment'];
                }
                if (isset($this->request->get['filter_author_id'])) {
                        $url .= '&filter_author_id=' . $this->request->get['filter_author_id'];
                }
                if (isset($this->request->get['filter_status'])) {
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
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
				$pagination->limit = $this->config->get('config_limit_admin');
				$pagination->url = $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

				$data['pagination'] = $pagination->render();

				$data['results'] = sprintf($this->language->get('text_pagination'), ($article_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($article_total - $this->config->get('config_limit_admin'))) ? $article_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $article_total, ceil($article_total / $this->config->get('config_limit_admin')));
                
                $data['filter_name'] = $filter_name;
                $data['filter_allow_comment'] = $filter_allow_comment;
                $data['filter_author_id'] = $filter_author_id;
				$data['filter_status'] = $filter_status;
                
                $data['default_url'] = '&user_token=' . $this->session->data['user_token'];
                if(isset($this->request->get['mz_theme_code'])){
                        $data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }

                $data['user_token'] = $this->session->data['user_token'];
                
                // Columns
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
				$this->response->setOutput($this->load->view('extension/maza/blog/article_list', $data));
        }
        
        /**
         * Form to add or edit Article
         */
        protected function getForm(): void {
                $this->load->model('localisation/language');
                $this->load->model('catalog/filter');
                $this->load->model('catalog/product');
                $this->load->model('setting/store');
                $this->load->model('tool/image');
                $this->load->model('design/layout');
                $this->load->model('extension/maza/blog/author');
                $this->load->model('extension/maza/blog/category');
                
                $url = '';
                
                if (isset($this->request->get['filter_name'])) {
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_allow_comment'])) {
                        $url .= '&filter_allow_comment=' . $this->request->get['filter_allow_comment'];
                }
                if (isset($this->request->get['filter_author_id'])) {
                        $url .= '&filter_author_id=' . $this->request->get['filter_author_id'];
                }
                if (isset($this->request->get['filter_status'])) {
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
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
                
                $data = array();
                
                // Header
                $header_data = array();
                $header_data['title'] = !isset($this->request->get['article_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
                    array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
                    array('name' => $this->language->get('tab_links'), 'id' => 'tab-mz-links', 'href' => false),
                    array('name' => $this->language->get('tab_image'), 'id' => 'tab-mz-image', 'href' => false),
					array('name' => $this->language->get('tab_audio'), 'id' => 'tab-mz-audio', 'href' => false),
                );
				if (version_compare(VERSION, '3.0.0.0') >= 0) { // OC 3 and UP
                    $header_data['menu'][] = array('name' => $this->language->get('tab_seo'), 'id' => 'tab-mz-seo', 'href' => false);
                }
				$header_data['menu'][] = array('name' => $this->language->get('tab_design'), 'id' => 'tab-mz-design', 'href' => false);
                
                $header_data['menu_active'] = 'tab-mz-general';
                $header_data['buttons'][] = array(
                    'id' => 'button-save',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'href' => false,
                    'target' => false,
                    'form_target_id' => 'form-mz-article',
                );
				if (isset($this->request->get['article_id'])) {
					$header_data['buttons'][] = array(
						'id' => 'button-preview',
						'name' => '',
						'tooltip' => $this->language->get('button_preview'),
						'icon' => 'fa-eye',
						'class' => 'btn-info',
						'href' => $this->config->get('mz_store_url') . 'index.php?route=extension/maza/blog/article&article_id=' . $this->request->get['article_id'],
						'target' => '_blank',
						'form_target_id' => false,
					);
				}
                $header_data['buttons'][] = array(
                    'id' => 'button-cancel',
                    'name' => '',
                    'tooltip' => $this->language->get('button_cancel'),
                    'icon' => 'fa-reply',
                    'class' => 'btn-default',
                    'href' => $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => false,
                );
                $header_data['form_target_id'] = 'form-mz-article';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Setting
                $setting = array();
                $setting['image'] = '';
                $setting['date_available'] = date('Y-m-d');
                $setting['author_id'] = 0;
                $setting['sort_order'] = 0;
                $setting['allow_comment'] = true;
                $setting['status'] = '1'; // 1 = Enabled, 2 = Unlisted, 0 = Disabled
				$setting['featured'] = '0';
                $setting['article_description'] = array();
                $setting['article_image'] = array();
				$setting['article_audio'] = array();
                $setting['article_filter'] = array();
                $setting['article_category'] = array();
                $setting['article_related'] = array();
                $setting['article_product'] = array();
                $setting['article_store'] = array(0);
                $setting['article_layout'] = array();

				if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
						$setting['keyword'] = '';
				} else {
						$setting['article_seo_url'] = array();
				}
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } elseif(isset($this->request->get['article_id'])) {
                    $setting = array_merge($setting, $this->model_extension_maza_blog_article->getArticle($this->request->get['article_id']));
                    $setting['article_description'] = $this->model_extension_maza_blog_article->getArticleDescriptions($this->request->get['article_id']);
                    $setting['article_image']      	= $this->model_extension_maza_blog_article->getArticleImages($this->request->get['article_id']);
					$setting['article_audio'] 		= $this->model_extension_maza_blog_article->getArticleAudios($this->request->get['article_id']);
                    $setting['article_filter']      = $this->model_extension_maza_blog_article->getArticleFilters($this->request->get['article_id']);
                    $setting['article_category']  	= $this->model_extension_maza_blog_article->getArticleCategories($this->request->get['article_id']);
                    $setting['article_related']    	= $this->model_extension_maza_blog_article->getArticleRelated($this->request->get['article_id']);
                    $setting['article_product']  	= $this->model_extension_maza_blog_article->getArticleProducts($this->request->get['article_id']);
                    $setting['article_store']       = $this->model_extension_maza_blog_article->getArticleStores($this->request->get['article_id']);
                    $setting['article_layout']      = $this->model_extension_maza_blog_article->getArticleLayouts($this->request->get['article_id']);

					if (version_compare(VERSION, '3.0.0.0') >= 0) {
                        $setting['article_seo_url']     = $this->model_extension_maza_blog_article->getArticleSeoUrls($this->request->get['article_id']);
                    }
                }

                // Data
                $data = array_merge($data, $setting);
                
                if (!isset($this->request->get['article_id'])) {
					$data['action'] = $this->url->link('extension/maza/blog/article/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
				} else {
					$data['action'] = $this->url->link('extension/maza/blog/article/edit', 'user_token=' . $this->session->data['user_token'] . '&article_id=' . $this->request->get['article_id'] . $url, true);
				}
                
                // Author
                $author_info =  $this->model_extension_maza_blog_author->getAuthor($setting['author_id']);
                $data['author'] = ($author_info)?$author_info['name']:'';
                
                // Category
                $data['article_categories'] = array();
				foreach ($setting['article_category'] as $category_id) {
					$category_info = $this->model_extension_maza_blog_category->getCategory($category_id);

					if ($category_info) {
						$data['article_categories'][] = array(
							'category_id' => $category_info['category_id'],
							'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
						);
					}
				}
                
                // Filter
                $data['article_filters'] = array();
				foreach ($setting['article_filter'] as $filter_id) {
					$filter_info = $this->model_catalog_filter->getFilter($filter_id);

					if ($filter_info) {
						$data['article_filters'][] = array(
							'filter_id' => $filter_info['filter_id'],
							'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
						);
					}
				}
                
                // related products
                $data['article_products'] = array();
				foreach ($setting['article_product'] as $product_id) {
					$product_info = $this->model_catalog_product->getProduct($product_id);

					if ($product_info) {
						$data['article_products'][] = array(
							'product_id' => $product_info['product_id'],
							'name'      => $product_info['name']
						);
					}
				}
                
                // Related article
                $data['article_relateds'] = array();
				foreach ($setting['article_related'] as $article_id) {
					$related_info = $this->model_extension_maza_blog_article->getArticle($article_id);

					if ($related_info) {
						$data['article_relateds'][] = array(
							'article_id' => $related_info['article_id'],
							'name'       => $related_info['name']
						);
					}
				}
                
                // Image
                if (is_file(DIR_IMAGE . $setting['image'])) {
					$data['thumb'] = $this->model_tool_image->resize($setting['image'], 100, 100);
				} else {
					$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
				}
                $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
                
                $data['article_images'] = array();

				foreach ($setting['article_image'] as $article_image) {
					if (is_file(DIR_IMAGE . $article_image['image'])) {
						$image = $article_image['image'];
						$thumb = $article_image['image'];
					} else {
						$image = '';
						$thumb = 'no_image.png';
					}

					$data['article_images'][] = array(
						'image'      => $image,
						'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
						'sort_order' => $article_image['sort_order']
					);
				}
                
                // Stores
                $data['stores'] = array();
				$data['stores'][] = array(
					'store_id' => 0,
					'name'     => $this->language->get('text_default')
				);
				$stores = $this->model_setting_store->getStores();
				foreach ($stores as $store) {
					$data['stores'][] = array(
						'store_id' => $store['store_id'],
						'name'     => $store['name']
					);
				}

				$data['list_status'] = array(
					array('id' => '0', 'name' => $this->language->get('text_disabled')),
					array('id' => '1', 'name' => $this->language->get('text_enabled')),
					array('id' => '2', 'name' => $this->language->get('text_unlisted')),
				);
				
                $data['layouts'] = $this->model_design_layout->getLayouts();

                $data['languages'] = $this->model_localisation_language->getLanguages();

                $data['user_token'] = $this->session->data['user_token'];
                
                $data['default_url'] = '&user_token=' . $this->session->data['user_token'];
                if(isset($this->request->get['mz_theme_code'])){
                        $data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
                
				if (isset($this->session->data['success'])) {
					$data['success'] = $this->session->data['success'];
					unset($this->session->data['success']);
				}
                if(isset($this->error['warning'])){
					$data['warning'] = $this->error['warning'];
                } elseif (isset($this->session->data['warning'])) {
					$data['warning'] = $this->session->data['warning'];
					unset($this->session->data['warning']);
				}
                foreach($this->error as $key => $val){
                    $data['err_' . $key] = $val;
                }
                
                // Columns
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
				$this->response->setOutput($this->load->view('extension/maza/blog/article_form', $data));
		}
        
        protected function validateForm(): bool {
			if (!$this->user->hasPermission('modify', 'extension/maza/blog/article')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}

			foreach ($this->request->post['article_description'] as $language_id => $value) {
				if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
					$this->error['name'][$language_id] = $this->language->get('error_name');
				}

				if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
					$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
				}
			}

			if (!empty($this->request->post['article_seo_url'])) {
				$this->load->model('design/seo_url');
				
				foreach ($this->request->post['article_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							if (count(array_keys($language, $keyword)) > 1) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
							}						
							
							$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
							
							foreach ($seo_urls as $seo_url) {
								if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['article_id']) || (($seo_url['query'] != 'mz_blog_article_id=' . $this->request->get['article_id'])))) {
									$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
									
									break;
								}
							}
						}
					}
				}
			}

			// OC 2
			if (!empty($this->request->post['keyword'])) {
				$this->load->model('catalog/url_alias');

				$url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

				if ($url_alias_info && isset($this->request->get['article_id']) && $url_alias_info['query'] != 'mz_blog_article_id=' . $this->request->get['article_id']) {
					$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
				}

				if ($url_alias_info && !isset($this->request->get['article_id'])) {
					$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
				}
			}
			
			if ($this->error && !isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_warning');
			}
			
			return !$this->error;
		}
        
        protected function validateDelete(): bool {
			if (!$this->user->hasPermission('modify', 'extension/maza/blog/article')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}

			return !$this->error;
		}
        
        protected function validateCopy(): bool {
			if (!$this->user->hasPermission('modify', 'extension/maza/blog/article')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}

			return !$this->error;
		}

		public function autocomplete(): void {
			$json = array();

			if (isset($this->request->get['filter_name'])) {
				$this->load->model('extension/maza/blog/article');

				$filter_data = array(
					'filter_name' => $this->request->get['filter_name'],
					'sort'        => 'name',
					'order'       => 'ASC',
					'start'       => 0,
					'limit'       => 5
				);

				$results = $this->model_extension_maza_blog_article->getArticles($filter_data);

				foreach ($results as $result) {
					$json[] = array(
						'article_id' => $result['article_id'],
						'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
			}

			$sort_order = array();

			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['name'];
			}

			array_multisort($sort_order, SORT_ASC, $json);

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
}
