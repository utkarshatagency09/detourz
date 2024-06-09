<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2021, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaCatalogProductLabel extends Controller {
	private $error = array();

	public function index(): void {
		$this->load->language('extension/maza/catalog/product_label');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/product_label');

		$this->getList();
	}

	public function add(): void {
		$this->load->language('extension/maza/catalog/product_label');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/product_label');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_catalog_product_label->addLabel($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

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
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$this->response->redirect($this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit(): void {
		$this->load->language('extension/maza/catalog/product_label');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/product_label');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_catalog_product_label->editLabel($this->request->get['product_label_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

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
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$this->response->redirect($this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete(): void {
		$this->load->language('extension/maza/catalog/product_label');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/product_label');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_label_id) {
				$this->model_extension_maza_catalog_product_label->deleteLabel($product_label_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

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
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$this->response->redirect($this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function copy(): void {
		$this->load->language('extension/maza/catalog/product_label');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/product_label');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $product_label_id) {
				$this->model_extension_maza_catalog_product_label->copyLabel($product_label_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

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
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$this->response->redirect($this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList(): void {
		$this->load->model('tool/image');

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

		// $this->load->language('extension/maza/common/column_left');

		// $header_data['menu'] = array();
		// if ($this->user->hasPermission('access', 'extension/maza/catalog/product'))
		// 	$header_data['menu'][] = array('name' => $this->language->get('tab_product'), 'id' => 'tab-mz-product', 'href' => $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/catalog/manufacturer'))
		// 	$header_data['menu'][] = array('name' => $this->language->get('tab_manufacturer'), 'id' => 'tab-mz-manufacturer', 'href' => $this->url->link('extension/maza/catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// $header_data['menu'][] = array('name' => $this->language->get('tab_product_label'), 'id' => 'tab-mz-product-label', 'href' => false);
		// if ($this->user->hasPermission('access', 'extension/maza/catalog/data'))
		// 	$header_data['menu'][] = array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/catalog/document'))
		// 	$header_data['menu'][] = array('name' => $this->language->get('tab_document'), 'id' => 'tab-mz-document', 'href' => $this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/catalog/redirect'))
		// 	$header_data['menu'][] = array('name' => $this->language->get('tab_redirect'), 'id' => 'tab-mz-redirect', 'href' => $this->url->link('extension/maza/catalog/redirect', 'user_token=' . $this->session->data['user_token'] . $url, true));

		// $header_data['menu_active'] = 'tab-mz-product-label';

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
			'class' => 'btn-warning',
			'tooltip' => $this->language->get('button_add'),
			'icon' => 'fa-plus',
			'href' => $this->url->link('extension/maza/catalog/product_label/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => false,
			'form_target_id' => false,
		);
		$header_data['buttons'][] = array(
			'id' => 'button-copy',
			'name' => '',
			'class' => 'btn-default',
			'tooltip' => $this->language->get('button_copy'),
			'icon' => 'fa-copy',
			'formaction' => $this->url->link('extension/maza/catalog/product_label/copy', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => 'form-mz-product-label',
			'confirm' => $this->language->get('text_confirm')
		);
		$header_data['buttons'][] = array(
			'id' => 'button-delete',
			'name' => '',
			'tooltip' => $this->language->get('button_delete'),
			'icon' => 'fa-trash',
			'class' => 'btn-danger',
			'formaction' => $this->url->link('extension/maza/catalog/product_label/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'href' => FALSE,
			'target' => FALSE,
			'form_target_id' => 'form-mz-product-label',
			'confirm' => $this->language->get('text_confirm')
		);
		$header_data['buttons'][] = array(
			'id' => 'button-docs',
			'name' => null,
			'tooltip' => $this->language->get('button_docs'),
			'icon' => 'fa-info',
			'class' => 'btn-default',
			'href' => 'https://docs.pocotheme.com/#page-catalog-product-label',
			'target' => '_blank'
		);

		$header_data['form_target_id'] = 'form-mz-product-label';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Data list
		$data['labels'] = array();

		$filter_data = array(
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$data_total = $this->model_extension_maza_catalog_product_label->getTotalLabels();

		$results = $this->model_extension_maza_catalog_product_label->getLabels($filter_data);

		foreach ($results as $result) {
			$data['labels'][] = array(
				'product_label_id' => $result['product_label_id'],
				'name' => $result['name'],
				'sort_order' => $result['sort_order'],
				'date_modified' => $result['date_modified'],
				'status' => $result['status'],
				'edit' => $this->url->link('extension/maza/catalog/product_label/edit', 'user_token=' . $this->session->data['user_token'] . '&product_label_id=' . $result['product_label_id'] . $url, true),
				'style' => $this->url->link('extension/maza/catalog/product_label/style', 'user_token=' . $this->session->data['user_token'] . '&product_label_id=' . $result['product_label_id'] . $url, true),
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

		$data['sort_name']          = $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_date_modified'] = $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . '&sort=date_modified' . $url, true);
		$data['sort_status']        = $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_order']         = $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);

		$data['sort']  = $sort;
		$data['order'] = $order;

		// Pagination
		$url = '';
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
		$pagination->total = $data_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($data_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($data_total - $this->config->get('config_limit_admin'))) ? $data_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $data_total, ceil($data_total / $this->config->get('config_limit_admin')));

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

		$this->response->setOutput($this->load->view('extension/maza/catalog/product_label_list', $data));
	}

	protected function getForm(): void {
		$this->load->model('setting/store');
		$this->load->model('localisation/language');
		$this->load->model('customer/customer_group');
		$this->load->model('catalog/filter');
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');

		$url = '';

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
		$header_data['title']        = !isset($this->request->get['product_label_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$header_data['theme_select'] = $header_data['skin_select'] = false;
		$header_data['menu'] = array(
			array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
			array('name' => $this->language->get('tab_label'), 'id' => 'tab-mz-label', 'href' => false),
			array('name' => $this->language->get('tab_link'), 'id' => 'tab-mz-link', 'href' => false),
		);

		$header_data['menu_active']    = 'tab-mz-general';
		$header_data['buttons'][]      = array(
			'id' => 'button-save',
			'name' => '',
			'class' => 'btn-primary',
			'tooltip' => $this->language->get('button_save'),
			'icon' => 'fa-save',
			'href' => false,
			'target' => false,
			'form_target_id' => 'form-mz-product-label',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-cancel',
			'name' => '',
			'tooltip' => $this->language->get('button_cancel'),
			'icon' => 'fa-reply',
			'class' => 'btn-default',
			'href' => $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-docs',
			'name' => null,
			'tooltip' => $this->language->get('button_docs'),
			'icon' => 'fa-info',
			'class' => 'btn-default',
			'href' => 'https://docs.pocotheme.com/#page-catalog-data',
			'target' => '_blank'
		);
		$header_data['form_target_id'] = 'form-mz-product-label';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Setting
		$setting = array();

		$setting['status']               = 1;
		$setting['sort_order']           = 0;
		$setting['name']                 = '';
		$setting['product_page_status']  = 1;
		$setting['type']                 = 'special';
		$setting['customer']             = 0; // 0 = ALL, -1 = Guest, 1 = logged
		$setting['label_description']    = array();
		$setting['label_customer_group'] = array();
		$setting['label_store']          = array(0);
		$setting['setting']              = array(
			'special' => 'percentage', // percentage or text
			'new' => '7', // No. days
			'stock_status' => '1', // 1 = out of stock status, 0 = all status
			'selected' => array(),
			'filter' => array(
				'category' => array(),
				'manufacturer' => array(),
				'filter' => array(),
				'price_min' => '',
				'price_max' => '',
				'quantity_min' => '',
				'quantity_max' => '',
				'rating_min' => '',
				'rating_max' => '',
				'special' => 0,
			),
			'controller' => '',
		);


		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		} elseif (isset($this->request->get['product_label_id'])) {
			$setting                         = array_merge($setting, $this->model_extension_maza_catalog_product_label->getLabel($this->request->get['product_label_id']));
			$setting['label_store']          = $this->model_extension_maza_catalog_product_label->getLabelStores($this->request->get['product_label_id']);
			$setting['label_customer_group'] = $this->model_extension_maza_catalog_product_label->getLabelCustomerGroups($this->request->get['product_label_id']);
			$setting['label_description']    = $this->model_extension_maza_catalog_product_label->getLabelDescriptions($this->request->get['product_label_id']);
		}

		// Data
		$data = array_merge($data, $setting);

		if (!isset($this->request->get['product_label_id'])) {
			$data['action'] = $this->url->link('extension/maza/catalog/product_label/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/catalog/product_label/edit', 'user_token=' . $this->session->data['user_token'] . '&product_label_id=' . $this->request->get['product_label_id'] . $url, true);
		}

		// Label types
		$data['list_types'] = array(
			array('id' => 'special', 'name' => $this->language->get('text_special')),
			array('id' => 'new', 'name' => $this->language->get('text_new')),
			array('id' => 'stock_status', 'name' => $this->language->get('text_stock_status')),
			array('id' => 'rating', 'name' => $this->language->get('text_rating')),
			array('id' => 'selected', 'name' => $this->language->get('text_selected')),
			array('id' => 'filter', 'name' => $this->language->get('text_filter')),
			array('id' => 'controller', 'name' => $this->language->get('text_controller')),
		);

		// Special types
		$data['list_special'] = array(
			array('id' => 'percentage', 'name' => $this->language->get('text_percentage')),
			array('id' => 'text', 'name' => $this->language->get('text_text')),
		);

		// Store
		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name' => $this->language->get('text_default')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name' => $store['name']
			);
		}

		// Selected Products
		$data['data_products'] = array();

		if (isset($setting['setting']['selected'])) {
			foreach ($setting['setting']['selected'] as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					$data['data_products'][] = array(
						'product_id' => $product_info['product_id'],
						'name' => $product_info['name']
					);
				}
			}
		}

		// Categories
		$data['data_categories'] = array();

		if (isset($setting['setting']['filter']['category'])) {
			foreach ($setting['setting']['filter']['category'] as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if ($category_info) {
					$data['data_categories'][] = array(
						'category_id' => $category_info['category_id'],
						'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
					);
				}
			}
		}

		// Manufacturers
		$data['data_manufacturers'] = array();

		if (isset($setting['setting']['filter']['manufacturer'])) {
			foreach ($setting['setting']['filter']['manufacturer'] as $manufacturer_id) {
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);

				if ($manufacturer_info) {
					$data['data_manufacturers'][] = array(
						'manufacturer_id' => $manufacturer_info['manufacturer_id'],
						'name' => $manufacturer_info['name']
					);
				}
			}
		}

		// Filter
		$data['data_filters'] = array();

		if (isset($setting['setting']['filter']['filter'])) {
			foreach ($setting['setting']['filter']['filter'] as $filter_id) {
				$filter_info = $this->model_catalog_filter->getFilter($filter_id);

				if ($filter_info) {
					$data['data_filters'][] = array(
						'filter_id' => $filter_info['filter_id'],
						'name' => $filter_info['group'] . ' &gt; ' . $filter_info['name']
					);
				}
			}
		}

		$data['list_customer'] = array(
			array('id' => 0, 'name' => $this->language->get('text_all')),
			array('id' => -1, 'name' => $this->language->get('text_guest')),
			array('id' => 1, 'name' => $this->language->get('text_logged')),
		);

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['languages'] = $this->model_localisation_language->getLanguages();

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

		// Error
		foreach ($this->error as $key => $val) {
			$data['err_' . $key] = $val;
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['default_url'] = '&user_token=' . $this->session->data['user_token'];
		if (isset($this->request->get['mz_theme_code'])) {
			$data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		// Columns
		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/catalog/product_label_form', $data));
	}

	protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/catalog/product_label')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		// name
		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 60)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		// Title
		if (in_array($this->request->post['type'], ['new', 'selected', 'filter']) || ($this->request->post['type'] == 'special' && $this->request->post['setting']['special'] == 'text')) {
			foreach ($this->request->post['label_description'] as $language_id => $value) {
				if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 60)) {
					$this->error['title'][$language_id] = $this->language->get('error_title');
				}
			}
		}

		// Selected
		if ($this->request->post['type'] == 'selected' && empty($this->request->post['setting']['selected'])) {
			$this->error['selected'] = $this->language->get('error_selected');
		}

		// Controller
		if ($this->request->post['type'] == 'controller' && empty($this->request->post['setting']['controller'])) {
			$this->error['controller'] = $this->language->get('error_controller');
		}

		if (!isset($this->error['warning']) && $this->error) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/catalog/product_label')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/catalog/product_label')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function style(): void {
		$this->load->language('extension/maza/catalog/product_label');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/javascript/maza/colorpicker/css/colorpicker.css');
		$this->document->addScript('view/javascript/maza/colorpicker/js/colorpicker.js');

		$this->load->model('extension/maza/catalog/product_label');

		$url = '';

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

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateStyle()) {
			$this->model_extension_maza_catalog_product_label->editStyle($this->request->get['product_label_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$data = array();

		// Header
		$header_data                 = array();
		$header_data['title']        = $this->language->get('text_edit');
		$header_data['theme_select'] = $header_data['skin_select'] = true;
		$header_data['menu'] = array(
			array('name' => $this->language->get('tab_layout'), 'id' => 'tab-mz-layout', 'href' => false),
			array('name' => $this->language->get('tab_color'), 'id' => 'tab-mz-color', 'href' => false),
		);

		$header_data['menu_active']    = 'tab-mz-layout';
		$header_data['buttons'][]      = array(
			'id' => 'button-save',
			'name' => '',
			'class' => 'btn-primary',
			'tooltip' => $this->language->get('button_save'),
			'icon' => 'fa-save',
			'href' => false,
			'target' => false,
			'form_target_id' => 'form-mz-product-label',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-cancel',
			'name' => '',
			'tooltip' => $this->language->get('button_cancel'),
			'icon' => 'fa-reply',
			'class' => 'btn-default',
			'href' => $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-docs',
			'name' => null,
			'tooltip' => $this->language->get('button_docs'),
			'icon' => 'fa-info',
			'class' => 'btn-default',
			'href' => 'https://docs.pocotheme.com/#page-catalog-data',
			'target' => '_blank'
		);
		$header_data['form_target_id'] = 'form-mz-product-label';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Setting
		$setting = array();

		$setting['position']                = 'top_left';
		$setting['shape']                   = 'square';
		$setting['visibility']              = 'always';
		$setting['color_text']              = '';
		$setting['color_bg']                = '';
		$setting['custom_class']            = '';
		$setting['product_page_position']   = 'top_right';
		$setting['product_page_visibility'] = 'always';

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		} else {
			$setting = array_merge($setting, $this->model_extension_maza_catalog_product_label->getStyle($this->request->get['product_label_id']));
		}

		// Data
		$data = array_merge($data, $setting);

		$data['action'] = $this->url->link('extension/maza/catalog/product_label/style', 'user_token=' . $this->session->data['user_token'] . '&product_label_id=' . $this->request->get['product_label_id'] . $url, true);

		// Positions
		$data['list_position'] = array(
			array('id' => 'top_left', 'name' => $this->language->get('text_top_left')),
			array('id' => 'top_right', 'name' => $this->language->get('text_top_right')),
			array('id' => 'center', 'name' => $this->language->get('text_center')),
			array('id' => 'bottom_left', 'name' => $this->language->get('text_bottom_left')),
			array('id' => 'bottom_right', 'name' => $this->language->get('text_bottom_right')),
		);

		// Shape
		$data['list_shape'] = array(
			array('id' => 'square', 'name' => $this->language->get('text_square')),
			array('id' => 'circle', 'name' => $this->language->get('text_circle')),
		);

		// Visibility
		$data['list_visibility'] = array(
			array('id' => 'always', 'name' => $this->language->get('text_always')),
			array('id' => 'hover', 'name' => $this->language->get('text_hover')),
		);

		if (isset($this->error['warning'])) {
			$data['warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}

		// Error
		foreach ($this->error as $key => $val) {
			$data['err_' . $key] = $val;
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['default_url'] = '&user_token=' . $this->session->data['user_token'];
		if (isset($this->request->get['mz_theme_code'])) {
			$data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		// Columns
		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/catalog/product_label_style', $data));
	}

	protected function validateStyle(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/catalog/product_label')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}