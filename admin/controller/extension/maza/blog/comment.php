<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaBlogComment extends Controller {
        private $error = array();
    
        public function index() {
			$this->load->language('extension/maza/blog/comment');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/blog/comment');

			$this->getList();
		}
        
        /**
         * Add comment
         */
        public function add() {
			$this->load->language('extension/maza/blog/comment');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/blog/comment');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_extension_maza_blog_comment->addComment($this->request->post);

				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';
							
				if (isset($this->request->get['filter_article'])) {
					$url .= '&filter_article=' . urlencode(html_entity_decode($this->request->get['filter_article'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_author'])) {
					$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_status'])) {
					$url .= '&filter_status=' . $this->request->get['filter_status'];
				}
				if (isset($this->request->get['filter_date_added'])) {
					$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

				$this->response->redirect($this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getForm();
		}
        
        /**
         * Edit comment
         */
		public function edit() {
			$this->load->language('extension/maza/blog/comment');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/blog/comment');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_extension_maza_blog_comment->editComment($this->request->get['comment_id'], $this->request->post);

				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';

				if (isset($this->request->get['filter_article'])) {
					$url .= '&filter_article=' . urlencode(html_entity_decode($this->request->get['filter_article'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_author'])) {
					$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_status'])) {
					$url .= '&filter_status=' . $this->request->get['filter_status'];
				}
				if (isset($this->request->get['filter_date_added'])) {
					$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

				$this->response->redirect($this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getForm();
		}
        
        /**
         * Delete individual comment
         */
		public function delete() {
			$this->load->language('extension/maza/blog/comment');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/blog/comment');

			if (isset($this->request->post['selected']) && $this->validateDelete()) {
				foreach ($this->request->post['selected'] as $comment_id) {
					$this->model_extension_maza_blog_comment->deleteComment($comment_id);
				}

				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';

				if (isset($this->request->get['filter_article'])) {
					$url .= '&filter_article=' . urlencode(html_entity_decode($this->request->get['filter_article'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_author'])) {
					$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_status'])) {
					$url .= '&filter_status=' . $this->request->get['filter_status'];
				}
				if (isset($this->request->get['filter_date_added'])) {
					$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

				$this->response->redirect($this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getList();
		}
        
        /**
         * approve comment
         */
		public function approve() {
			$this->load->language('extension/maza/blog/comment');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/blog/comment');

			if (isset($this->request->post['selected']) && $this->validateDelete()) {
				foreach ($this->request->post['selected'] as $comment_id) {
					$this->model_extension_maza_blog_comment->approveComment($comment_id);
				}

				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';

				if (isset($this->request->get['filter_article'])) {
					$url .= '&filter_article=' . urlencode(html_entity_decode($this->request->get['filter_article'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_author'])) {
					$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_status'])) {
					$url .= '&filter_status=' . $this->request->get['filter_status'];
				}
				if (isset($this->request->get['filter_date_added'])) {
					$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

				$this->response->redirect($this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getList();
		}
        
        /**
         * disapprove comment
         */
	public function disapprove() {
		$this->load->language('extension/maza/blog/comment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/blog/comment');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $comment_id) {
				$this->model_extension_maza_blog_comment->disapproveComment($comment_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_article'])) {
				$url .= '&filter_article=' . urlencode(html_entity_decode($this->request->get['filter_article'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
        /**
         * Get list of comment
         */
        protected function getList() {
                $this->load->model('tool/image');
            
                if (isset($this->request->get['filter_article'])) {
			$filter_article = $this->request->get['filter_article'];
		} else {
			$filter_article = '';
		}
                if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}
                if (isset($this->request->get['filter_author'])) {
			$filter_author = $this->request->get['filter_author'];
		} else {
			$filter_author = '';
		}
                if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}
                if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'c.date_added';
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
                if (isset($this->request->get['filter_article'])) {
                        $url .= '&filter_article=' . urlencode(html_entity_decode($this->request->get['filter_article'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_author'])) {
                        $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_status'])) {
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if (isset($this->request->get['filter_date_added'])) {
                        $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
                
                // Header
                $header_data = array();
                $header_data['title'] = $this->language->get('text_list');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                // $header_data['menu'] = array();
                // if ($this->user->hasPermission('access', 'extension/maza/blog')) $header_data['menu'][] = array('name' => $this->language->get('tab_dashboard'), 'id' => 'tab-mz-dashboard', 'href' => $this->url->link('extension/maza/blog', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/category')) $header_data['menu'][] = array('name' => $this->language->get('tab_category'), 'id' => 'tab-mz-category', 'href' => $this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/article')) $header_data['menu'][] = array('name' => $this->language->get('tab_article'), 'id' => 'tab-mz-article', 'href' => $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/author')) $header_data['menu'][] = array('name' => $this->language->get('tab_author'), 'id' => 'tab-mz-author', 'href' => $this->url->link('extension/maza/blog/author', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // $header_data['menu'][] = array('name' => $this->language->get('tab_comment'), 'id' => 'tab-mz-comment', 'href' => false);
                // if ($this->user->hasPermission('access', 'extension/maza/blog/report')) $header_data['menu'][] = array('name' => $this->language->get('tab_report'), 'id' => 'tab-mz-report', 'href' => $this->url->link('extension/maza/blog/report', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/setting')) $header_data['menu'][] = array('name' => $this->language->get('tab_setting'), 'id' => 'tab-mz-setting', 'href' => $this->url->link('extension/maza/blog/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
                
                
                // $header_data['menu_active'] = 'tab-mz-comment';
                $header_data['buttons'][] = array(
                    'id' => 'button-add',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_add'),
                    'icon' => 'fa-plus',
                    'href' => $this->url->link('extension/maza/blog/comment/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => false,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-approve',
                    'name' => '',
                    'tooltip' => $this->language->get('button_approve'),
                    'icon' => 'fa-thumbs-up',
                    'class' => 'btn-success',
                    'formaction' => $this->url->link('extension/maza/blog/comment/approve', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-mz-comment',
                    'confirm' => $this->language->get('text_confirm')
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-disapprove',
                    'name' => '',
                    'tooltip' => $this->language->get('button_disapprove'),
                    'icon' => 'fa-thumbs-down',
                    'class' => 'btn-warning',
                    'formaction' => $this->url->link('extension/maza/blog/comment/disapprove', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-mz-comment',
                    'confirm' => $this->language->get('text_confirm')
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-delete',
                    'name' => '',
                    'tooltip' => $this->language->get('button_delete'),
                    'icon' => 'fa-trash',
                    'class' => 'btn-danger',
                    'formaction' => $this->url->link('extension/maza/blog/comment/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-mz-comment',
                    'confirm' => $this->language->get('text_confirm')
                );
                $header_data['form_target_id'] = 'form-mz-comment';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Comment list
                
                
                $data['comments'] = array();

		$filter_data = array(
			'filter_article'    => $filter_article,
			'filter_author'     => $filter_author,
			'filter_status'     => $filter_status,
			'filter_date_added' => $filter_date_added,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$comment_total = $this->model_extension_maza_blog_comment->getTotalComments($filter_data);

		$results = $this->model_extension_maza_blog_comment->getComments($filter_data);

		foreach ($results as $result) {
                    
                        $parent_comment = $this->model_extension_maza_blog_comment->getComment($result['parent_comment_id']);
                        if($parent_comment){
                            $reply_to = ($parent_comment['author']?$parent_comment['author']:$parent_comment['customer_name']) . " (ID:{$parent_comment['comment_id']})";
                        } else {
                            $reply_to = '-';
                        }
                        
                        
			$data['comments'][] = array(
				'comment_id'  => $result['comment_id'],
				'name'       => $result['name'],
				'author'     => $result['author'],
                                'reply_to'   => $reply_to,
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'       => $this->url->link('extension/maza/blog/comment/edit', 'user_token=' . $this->session->data['user_token'] . '&comment_id=' . $result['comment_id'] . $url, true)
			);
		}
                

		if(isset($this->session->data['warning'])){
                        $data['warning'] = $this->session->data['warning'];
                        unset($this->session->data['warning']);
                }elseif (isset($this->error['warning'])) {
			$data['warning'] = $this->error['warning'];
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
                if (isset($this->request->get['filter_article'])) {
                        $url .= '&filter_article=' . urlencode(html_entity_decode($this->request->get['filter_article'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_author'])) {
                        $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_status'])) {
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if (isset($this->request->get['filter_date_added'])) {
                        $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

		$data['sort_article'] = $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . '&sort=ad.name' . $url, true);
		$data['sort_author'] = $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . '&sort=author' . $url, true);
		$data['sort_status'] = $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . '&sort=c.status' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . '&sort=c.date_added' . $url, true);
                
                $data['sort'] = $sort;
		$data['order'] = $order;
                
                // Pagination
		$url = '';
                if (isset($this->request->get['filter_article'])) {
                        $url .= '&filter_article=' . urlencode(html_entity_decode($this->request->get['filter_article'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_author'])) {
                        $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_status'])) {
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if (isset($this->request->get['filter_date_added'])) {
                        $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
		$pagination->total = $comment_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($comment_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($comment_total - $this->config->get('config_limit_admin'))) ? $comment_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $comment_total, ceil($comment_total / $this->config->get('config_limit_admin')));
                
                $data['filter_article'] = $filter_article;
                $data['filter_date_added'] = $filter_date_added;
                $data['filter_author'] = $filter_author;
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
                
		$this->response->setOutput($this->load->view('extension/maza/blog/comment_list', $data));
        }
        
        /**
         * Form to add or edit Comment
         */
        protected function getForm() {
                $this->load->model('extension/maza/blog/article');
                
                $url = '';
                
                if (isset($this->request->get['filter_article'])) {
                        $url .= '&filter_article=' . urlencode(html_entity_decode($this->request->get['filter_article'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_author'])) {
                        $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($this->request->get['filter_status'])) {
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if (isset($this->request->get['filter_date_added'])) {
                        $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
                $header_data['title'] = !isset($this->request->get['comment_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu_active'] = 'tab-mz-general';
                $header_data['buttons'][] = array(
                    'id' => 'button-save',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'href' => false,
                    'target' => false,
                    'form_target_id' => 'form-mz-comment',
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-cancel',
                    'name' => '',
                    'tooltip' => $this->language->get('button_cancel'),
                    'icon' => 'fa-reply',
                    'class' => 'btn-default',
                    'href' => $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => false,
                );
                $header_data['form_target_id'] = 'form-mz-comment';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Setting
                $setting = array();
                $setting['article_id'] = '';
                $setting['article'] = '';
                $setting['author'] = '';
                $setting['parent_comment_id'] = 0;
                $setting['text'] = '';
                $setting['date_added'] = date('Y-m-d');
                $setting['status'] = '';
                $setting['email'] = '';
                $setting['website'] = '';
                $setting['customer_id'] = 0;
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } elseif(isset($this->request->get['comment_id'])) {
                    $setting = array_merge($setting, $this->model_extension_maza_blog_comment->getComment($this->request->get['comment_id']));
                }

                // Data
                $data = array_merge($data, $setting);
                
                if (!isset($this->request->get['comment_id'])) {
			$data['action'] = $this->url->link('extension/maza/blog/comment/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/blog/comment/edit', 'user_token=' . $this->session->data['user_token'] . '&comment_id=' . $this->request->get['comment_id'] . $url, true);
		}
                
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
                
		$this->response->setOutput($this->load->view('extension/maza/blog/comment_form', $data));
	}
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/maza/blog/comment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['article_id']) {
			$this->error['article'] = $this->language->get('error_article');
		}
                
                if(empty($this->request->post['customer_name'])){
                    unset($this->request->post['customer_id']);
                }

		if (empty($this->request->post['customer_id']) && ((utf8_strlen($this->request->post['author']) < 3) || (utf8_strlen($this->request->post['author']) > 64))) {
			$this->error['author'] = $this->language->get('error_author');
		}

		if (utf8_strlen($this->request->post['text']) < 1) {
			$this->error['text'] = $this->language->get('error_text');
		}
                
                if($this->request->post['parent_comment_id']){
                    $comment_info = $this->model_extension_maza_blog_comment->getComment($this->request->post['parent_comment_id']);
                    
                    if(!$comment_info || $comment_info['article_id'] != $this->request->post['article_id'] || $comment_info['comment_id'] == $this->request->get['comment_id']){
                        $this->error['reply_to'] = $this->language->get('error_reply_to');
                    }
                }
                
                if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
        
        protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/maza/blog/comment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        protected function validateApprove() {
		if (!$this->user->hasPermission('modify', 'extension/maza/blog/comment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

//	public function autocomplete() {
//		$json = array();
//
//		if (isset($this->request->get['filter_name'])) {
//			$this->load->model('extension/maza/blog/comment');
//
//			$filter_data = array(
//				'filter_name' => $this->request->get['filter_name'],
//				'sort'        => 'name',
//				'order'       => 'ASC',
//				'start'       => 0,
//				'limit'       => 5
//			);
//
//			$results = $this->model_extension_maza_blog_comment->getComments($filter_data);
//
//			foreach ($results as $result) {
//				$json[] = array(
//					'comment_id' => $result['comment_id'],
//					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
//				);
//			}
//		}
//
//		$sort_order = array();
//
//		foreach ($json as $key => $value) {
//			$sort_order[$key] = $value['name'];
//		}
//
//		array_multisort($sort_order, SORT_ASC, $json);
//
//		$this->response->addHeader('Content-Type: application/json');
//		$this->response->setOutput(json_encode($json));
//	}
}
