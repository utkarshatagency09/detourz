<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaBlogCategory extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/maza/blog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/blog/category');

		$this->getList();
	}

	/**
	 * Add category
	 */
	public function add() {
		$this->load->language('extension/maza/blog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/blog/category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_blog_category->addCategory($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$this->response->redirect($this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	/**
	 * Edit category
	 */
	public function edit() {
		$this->load->language('extension/maza/blog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/blog/category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_blog_category->editCategory($this->request->get['category_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$this->response->redirect($this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	/**
	 * Delete individual category
	 */
	public function delete() {
		$this->load->language('extension/maza/blog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/blog/category');

		if (isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $category_id) {
				$this->model_extension_maza_blog_category->deleteCategory($category_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$this->response->redirect($this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}


	/**
	 * Repair category path
	 */
	public function repair() {
		$this->load->language('extension/maza/blog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/blog/category');

		if ($this->validate()) {
			$this->model_extension_maza_blog_category->repairCategories();

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$this->response->redirect($this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	/**
	 * Get list of category
	 */
	protected function getList() {

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		// Header
		$header_data                 = array();
		$header_data['title']        = $this->language->get('text_list');
		$header_data['theme_select'] = $header_data['skin_select'] = false;
		// $header_data['menu'] = array();
		// if ($this->user->hasPermission('access', 'extension/maza/blog')) $header_data['menu'][] = array('name' => $this->language->get('tab_dashboard'), 'id' => 'tab-mz-dashboard', 'href' => $this->url->link('extension/maza/blog', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// $header_data['menu'][] = array('name' => $this->language->get('tab_category'), 'id' => 'tab-mz-category', 'href' => false);
		// if ($this->user->hasPermission('access', 'extension/maza/blog/article')) $header_data['menu'][] = array('name' => $this->language->get('tab_article'), 'id' => 'tab-mz-article', 'href' => $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/blog/author')) $header_data['menu'][] = array('name' => $this->language->get('tab_author'), 'id' => 'tab-mz-author', 'href' => $this->url->link('extension/maza/blog/author', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/blog/comment')) $header_data['menu'][] = array('name' => $this->language->get('tab_comment'), 'id' => 'tab-mz-comment', 'href' => $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/blog/report')) $header_data['menu'][] = array('name' => $this->language->get('tab_report'), 'id' => 'tab-mz-report', 'href' => $this->url->link('extension/maza/blog/report', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/blog/setting')) $header_data['menu'][] = array('name' => $this->language->get('tab_setting'), 'id' => 'tab-mz-setting', 'href' => $this->url->link('extension/maza/blog/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));


		// $header_data['menu_active'] = 'tab-mz-category';
		$header_data['buttons'][]      = array(
			'id' => 'button-add',
			'name' => '',
			'class' => 'btn-primary',
			'tooltip' => $this->language->get('button_add'),
			'icon' => 'fa-plus',
			'href' => $this->url->link('extension/maza/blog/category/add', 'user_token=' . $this->session->data['user_token'], true),
			'target' => false,
			'form_target_id' => false,
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-delete',
			'name' => '',
			'tooltip' => $this->language->get('button_delete'),
			'icon' => 'fa-trash',
			'class' => 'btn-danger',
			'href' => FALSE,
			'target' => FALSE,
			'form_target_id' => 'form-mz-category',
			'confirm' => $this->language->get('text_confirm')
		);
		$header_data['form_target_id'] = 'form-mz-category';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Category list
		$url = '';
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
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

		$data['add']    = $this->url->link('extension/maza/blog/category/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/maza/blog/category/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['repair'] = $this->url->link('extension/maza/blog/category/repair', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['categories'] = array();

		$filter_data = array(
			'filter_name' => $filter_name,
			'filter_status' => $filter_status,
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$category_total = $this->model_extension_maza_blog_category->getTotalCategories();

		$results = $this->model_extension_maza_blog_category->getCategories($filter_data);

		foreach ($results as $result) {
			$data['categories'][] = array(
				'category_id' => $result['category_id'],
				'name' => $result['name'],
				'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'sort_order' => $result['sort_order'],
				'edit' => $this->url->link('extension/maza/blog/category/edit', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $result['category_id'] . $url, true),
			);
		}

		if (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array) $this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		// Sort order
		$url = '';
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
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

		$data['sort_name']       = $this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);
		$data['sort_status']     = $this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);

		$data['sort']  = $sort;
		$data['order'] = $order;

		// Pagination
		$url = '';
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination        = new Pagination();
		$pagination->total = $category_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

		$data['filter_name']   = $filter_name;
		$data['filter_status'] = $filter_status;

		$data['default_url'] = '&user_token=' . $this->session->data['user_token'];
		if (isset($this->request->get['mz_theme_code'])) {
			$data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		$data['user_token'] = $this->session->data['user_token'];

		// Columns
		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/blog/category_list', $data));
	}

	/**
	 * Form to add or edit Category
	 */
	protected function getForm() {
		$this->load->model('localisation/language');
		$this->load->model('catalog/filter');
		$this->load->model('setting/store');
		$this->load->model('tool/image');
		$this->load->model('design/layout');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
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
		$header_data                 = array();
		$header_data['title']        = !isset($this->request->get['category_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$header_data['theme_select'] = $header_data['skin_select'] = false;

		$header_data['menu'] = array(
			array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
			array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
		);
		if (version_compare(VERSION, '3.0.0.0') >= 0) { // OC 3 and UP
			$header_data['menu'][] = array('name' => $this->language->get('tab_seo'), 'id' => 'tab-mz-seo', 'href' => false);
		}
		$header_data['menu'][] = array('name' => $this->language->get('tab_design'), 'id' => 'tab-mz-design', 'href' => false);

		$header_data['menu_active']    = 'tab-mz-general';
		$header_data['buttons'][]      = array(
			'id' => 'button-save',
			'name' => '',
			'class' => 'btn-primary',
			'tooltip' => $this->language->get('button_save'),
			'icon' => 'fa-save',
			'href' => false,
			'target' => false,
			'form_target_id' => 'form-mz-category',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-cancel',
			'name' => '',
			'tooltip' => $this->language->get('button_cancel'),
			'icon' => 'fa-reply',
			'class' => 'btn-default',
			'href' => $this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['form_target_id'] = 'form-mz-category';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Setting
		$setting                         = array();
		$setting['category_description'] = array();
		$setting['path']                 = '';
		$setting['parent_id']            = 0;
		$setting['category_filter']      = array();
		$setting['category_store']       = array(0);
		$setting['image']                = '';
		$setting['top']                  = 0;
		$setting['column']               = 1;
		$setting['sort_order']           = 0;
		$setting['status']               = true;

		$setting['category_layout'] = array();

		if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
			$setting['keyword'] = '';
		} else {
			$setting['category_seo_url'] = array();
		}

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		} elseif (isset($this->request->get['category_id'])) {
			$setting                         = array_merge($setting, $this->model_extension_maza_blog_category->getCategory($this->request->get['category_id']));
			$setting['category_description'] = $this->model_extension_maza_blog_category->getCategoryDescriptions($this->request->get['category_id']);
			$setting['category_filter']      = $this->model_extension_maza_blog_category->getCategoryFilters($this->request->get['category_id']);
			$setting['category_store']       = $this->model_extension_maza_blog_category->getCategoryStores($this->request->get['category_id']);
			$setting['category_layout']      = $this->model_extension_maza_blog_category->getCategoryLayouts($this->request->get['category_id']);

			if (version_compare(VERSION, '3.0.0.0') >= 0) {
				$setting['category_seo_url'] = $this->model_extension_maza_blog_category->getCategorySeoUrls($this->request->get['category_id']);
			}
		}

		// Data
		$data = array_merge($data, $setting);

		if (!isset($this->request->get['category_id'])) {
			$data['action'] = $this->url->link('extension/maza/blog/category/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/blog/category/edit', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $this->request->get['category_id'] . $url, true);
		}

		// Filter
		$data['category_filters'] = array();
		foreach ($setting['category_filter'] as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['category_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name' => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}

		// Stores
		$data['stores']   = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name' => $this->language->get('text_default')
		);
		$stores           = $this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name' => $store['name']
			);
		}

		// Image
		if (is_file(DIR_IMAGE . $setting['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($setting['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);


		// General
		$data['layouts']    = $this->model_design_layout->getLayouts();
		$data['languages']  = $this->model_localisation_language->getLanguages();
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
		if (isset($this->error['warning'])) {
			$data['warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}
		foreach ($this->error as $key => $val) {
			$data['err_' . $key] = $val;
		}

		// Columns
		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/blog/category_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/maza/blog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['category_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if (isset($this->request->get['category_id']) && $this->request->post['parent_id']) {
			$results = $this->model_extension_maza_blog_category->getCategoryPath($this->request->post['parent_id']);

			foreach ($results as $result) {
				if ($result['path_id'] == $this->request->get['category_id']) {
					$this->error['parent'] = $this->language->get('error_parent');

					break;
				}
			}
		}

		if (!empty($this->request->post['category_seo_url'])) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['category_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['category_id']) || ($seo_url['query'] != 'mz_blog_category_id=' . $this->request->get['category_id']))) {
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

			if ($url_alias_info && isset($this->request->get['category_id']) && $url_alias_info['query'] != 'mz_blog_category_id=' . $this->request->get['category_id']) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}

			if ($url_alias_info && !isset($this->request->get['category_id'])) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/maza/blog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/blog/category');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort' => 'name',
				'order' => 'ASC',
				'start' => 0,
				'limit' => 5
			);

			$results = $this->model_extension_maza_blog_category->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
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