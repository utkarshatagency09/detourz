<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2021, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaCatalogData extends Controller {
	private $error = array();

	public function index(): void {
		$this->load->language('extension/maza/catalog/data');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/data');

		$this->getList();
	}

	/**
	 * Add data
	 */
	public function add(): void {
		$this->load->language('extension/maza/catalog/data');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/data');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_catalog_data->addData($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
			}
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
			}
			if (isset($this->request->get['filter_page'])) {
				$url .= '&filter_page=' . $this->request->get['filter_page'];
			}
			if (isset($this->request->get['filter_hook'])) {
				$url .= '&filter_hook=' . $this->request->get['filter_hook'];
			}
			if (isset($this->request->get['filter_date_end'])) {
				$url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	/**
	 * Edit data
	 */
	public function edit(): void {
		$this->load->language('extension/maza/catalog/data');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/data');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_catalog_data->editData($this->request->get['data_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
			}
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
			}
			if (isset($this->request->get['filter_page'])) {
				$url .= '&filter_page=' . $this->request->get['filter_page'];
			}
			if (isset($this->request->get['filter_hook'])) {
				$url .= '&filter_hook=' . $this->request->get['filter_hook'];
			}
			if (isset($this->request->get['filter_date_end'])) {
				$url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	/**
	 * Delete individual data
	 */
	public function delete(): void {
		$this->load->language('extension/maza/catalog/data');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/data');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $data_id) {
				$this->model_extension_maza_catalog_data->deleteData($data_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
			}
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
			}
			if (isset($this->request->get['filter_page'])) {
				$url .= '&filter_page=' . $this->request->get['filter_page'];
			}
			if (isset($this->request->get['filter_hook'])) {
				$url .= '&filter_hook=' . $this->request->get['filter_hook'];
			}
			if (isset($this->request->get['filter_date_end'])) {
				$url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	/**
	 * copy individual data
	 */
	public function copy(): void {
		$this->load->language('extension/maza/catalog/data');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/data');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $data_id) {
				$this->model_extension_maza_catalog_data->copyData($data_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
			}
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
			}
			if (isset($this->request->get['filter_page'])) {
				$url .= '&filter_page=' . $this->request->get['filter_page'];
			}
			if (isset($this->request->get['filter_hook'])) {
				$url .= '&filter_hook=' . $this->request->get['filter_hook'];
			}
			if (isset($this->request->get['filter_date_end'])) {
				$url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	/**
	 * Get list of data
	 */
	protected function getList(): void {
		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_product_id'])) {
			$filter_product_id = $this->request->get['filter_product_id'];
		} else {
			$filter_product_id = '';
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = '';
		}

		if (isset($this->request->get['filter_manufacturer_id'])) {
			$filter_manufacturer_id = $this->request->get['filter_manufacturer_id'];
		} else {
			$filter_manufacturer_id = '';
		}

		if (isset($this->request->get['filter_page'])) {
			$filter_page = $this->request->get['filter_page'];
		} else {
			$filter_page = '';
		}

		if (isset($this->request->get['filter_hook'])) {
			$filter_hook = $this->request->get['filter_hook'];
		} else {
			$filter_hook = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'd.name';
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
		// if ($this->user->hasPermission('access', 'extension/maza/catalog/product_label'))
		// 	$header_data['menu'][] = array('name' => $this->language->get('tab_product_label'), 'id' => 'tab-mz-product-label', 'href' => $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// $header_data['menu'][] = array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false);
		// if ($this->user->hasPermission('access', 'extension/maza/catalog/document'))
		// 	$header_data['menu'][] = array('name' => $this->language->get('tab_document'), 'id' => 'tab-mz-document', 'href' => $this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/catalog/redirect'))
		// 	$header_data['menu'][] = array('name' => $this->language->get('tab_redirect'), 'id' => 'tab-mz-redirect', 'href' => $this->url->link('extension/maza/catalog/redirect', 'user_token=' . $this->session->data['user_token'] . $url, true));

		// $header_data['menu_active'] = 'tab-mz-data';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
		}
		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
		}
		if (isset($this->request->get['filter_page'])) {
			$url .= '&filter_page=' . $this->request->get['filter_page'];
		}
		if (isset($this->request->get['filter_hook'])) {
			$url .= '&filter_hook=' . $this->request->get['filter_hook'];
		}
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
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

		$header_data['buttons'][] = array(
			'id' => 'button-add',
			'name' => '',
			'class' => 'btn-warning',
			'tooltip' => $this->language->get('button_add'),
			'icon' => 'fa-plus',
			'href' => $this->url->link('extension/maza/catalog/data/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => false,
			'form_target_id' => false,
		);
		$header_data['buttons'][] = array(
			'id' => 'button-copy',
			'name' => '',
			'class' => 'btn-default',
			'tooltip' => $this->language->get('button_copy'),
			'icon' => 'fa-copy',
			'formaction' => $this->url->link('extension/maza/catalog/data/copy', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => 'form-mz-data',
			'confirm' => $this->language->get('text_confirm')
		);
		$header_data['buttons'][] = array(
			'id' => 'button-delete',
			'name' => '',
			'tooltip' => $this->language->get('button_delete'),
			'icon' => 'fa-trash',
			'class' => 'btn-danger',
			'formaction' => $this->url->link('extension/maza/catalog/data/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'href' => FALSE,
			'target' => FALSE,
			'form_target_id' => 'form-mz-data',
			'confirm' => $this->language->get('text_confirm')
		);
		$header_data['buttons'][] = array(
			'id' => 'button-docs',
			'name' => null,
			'tooltip' => $this->language->get('button_docs'),
			'icon' => 'fa-info',
			'class' => 'btn-default',
			'href' => 'https://docs.pocotheme.com/#page-catalog-data',
			'target' => '_blank'
		);

		$header_data['form_target_id'] = 'form-mz-data';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Data list
		$data['datas'] = array();

		$filter_data = array(
			'filter_name' => $filter_name,
			'filter_product_id' => $filter_product_id,
			'filter_category_id' => $filter_category_id,
			'filter_manufacturer_id' => $filter_manufacturer_id,
			'filter_page' => $filter_page,
			'filter_hook' => $filter_hook,
			'filter_date_end' => $filter_date_end,
			'filter_status' => $filter_status,
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$data_total = $this->model_extension_maza_catalog_data->getTotalDatas($filter_data);

		$results = $this->model_extension_maza_catalog_data->getDatas($filter_data);

		foreach ($results as $result) {
			$data['datas'][] = array(
				'data_id' => $result['data_id'],
				'name' => $result['name'],
				'page' => $this->language->get('text_' . $result['page']),
				'hook' => $this->language->get('text_' . $result['hook']),
				'sort_order' => $result['sort_order'],
				'date_modified' => $result['date_modified'],
				'status' => $result['status'],
				'edit' => $this->url->link('extension/maza/catalog/data/edit', 'user_token=' . $this->session->data['user_token'] . '&data_id=' . $result['data_id'] . $url, true),
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
		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
		}
		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
		}
		if (isset($this->request->get['filter_page'])) {
			$url .= '&filter_page=' . $this->request->get['filter_page'];
		}
		if (isset($this->request->get['filter_hook'])) {
			$url .= '&filter_hook=' . $this->request->get['filter_hook'];
		}
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
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

		$data['sort_name']          = $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . '&sort=d.name' . $url, true);
		$data['sort_page']          = $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . '&sort=d.page' . $url, true);
		$data['sort_hook']          = $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . '&sort=d.hook' . $url, true);
		$data['sort_date_modified'] = $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . '&sort=d.date_modified' . $url, true);
		$data['sort_status']        = $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . '&sort=d.status' . $url, true);
		$data['sort_order']         = $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . '&sort=d.sort_order' . $url, true);

		$data['sort']  = $sort;
		$data['order'] = $order;

		// Pagination
		$url = '';
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
		}
		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
		}
		if (isset($this->request->get['filter_page'])) {
			$url .= '&filter_page=' . $this->request->get['filter_page'];
		}
		if (isset($this->request->get['filter_hook'])) {
			$url .= '&filter_hook=' . $this->request->get['filter_hook'];
		}
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
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
		$pagination->total = $data_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($data_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($data_total - $this->config->get('config_limit_admin'))) ? $data_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $data_total, ceil($data_total / $this->config->get('config_limit_admin')));

		$data['filter_name']            = $filter_name;
		$data['filter_product_id']      = $filter_product_id;
		$data['filter_category_id']     = $filter_category_id;
		$data['filter_manufacturer_id'] = $filter_manufacturer_id;
		$data['filter_page']            = $filter_page;
		$data['filter_hook']            = $filter_hook;
		$data['filter_date_end']        = $filter_date_end;
		$data['filter_status']          = $filter_status;

		$product_info = $this->model_catalog_product->getProduct($filter_product_id);
		if ($product_info) {
			$data['filter_product'] = $product_info['name'];
		}

		$category_info = $this->model_catalog_category->getCategory($filter_category_id);
		if ($category_info) {
			$data['filter_category'] = $category_info['name'];
		}

		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($filter_manufacturer_id);
		if ($manufacturer_info) {
			$data['filter_manufacturer'] = $manufacturer_info['name'];
		}

		$data['list_page'] = array(
			array('id' => 'product', 'name' => $this->language->get('text_product')),
			array('id' => 'category', 'name' => $this->language->get('text_category')),
			array('id' => 'manufacturer', 'name' => $this->language->get('text_manufacturer')),
		);

		$data['list_hook'] = array(
			array('id' => 'tab', 'name' => $this->language->get('text_tab')),
			array('id' => 'accordion', 'name' => $this->language->get('text_accordion')),
			array('id' => 'faq', 'name' => $this->language->get('text_faq')),
			array('id' => 'popup', 'name' => $this->language->get('text_popup')),
		);

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

		$this->response->setOutput($this->load->view('extension/maza/catalog/data_list', $data));
	}

	/**
	 * Form to add or edit Data
	 */
	protected function getForm(): void {
		$this->load->model('setting/store');
		$this->load->model('localisation/language');
		$this->load->model('customer/customer_group');
		$this->load->model('catalog/filter');
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('extension/maza/opencart');
		$this->load->model('extension/maza/extension');
		$this->load->model('extension/maza/content_builder');
		$this->load->model('extension/maza/catalog/data');
		$this->load->model('tool/image');

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
		}
		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
		}
		if (isset($this->request->get['filter_page'])) {
			$url .= '&filter_page=' . $this->request->get['filter_page'];
		}
		if (isset($this->request->get['filter_hook'])) {
			$url .= '&filter_hook=' . $this->request->get['filter_hook'];
		}
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
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
		$header_data['title']        = !isset($this->request->get['data_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$header_data['theme_select'] = $header_data['skin_select'] = false;
		$header_data['menu'] = array(
			array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
			array('name' => $this->language->get('tab_title'), 'id' => 'tab-mz-title', 'href' => false),
			array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
			array('name' => $this->language->get('tab_link'), 'id' => 'tab-mz-link', 'href' => false),
			array('name' => $this->language->get('tab_page'), 'id' => 'tab-mz-page', 'href' => false),
			array('name' => $this->language->get('tab_design'), 'id' => 'tab-mz-design', 'href' => false),
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
			'form_target_id' => 'form-mz-data',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-cancel',
			'name' => '',
			'tooltip' => $this->language->get('button_cancel'),
			'icon' => 'fa-reply',
			'class' => 'btn-default',
			'href' => $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . $url, true),
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
		$header_data['form_target_id'] = 'form-mz-data';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Setting
		$setting = array();

		$setting['status']     = 1;
		$setting['sort_order'] = 0;
		$setting['name']       = '';

		$setting['customer']          = 0; // 0 = ALL, -1 = Guest, 1 = logged
		$setting['customer_group_id'] = 0;
		$setting['date_start']        = null;
		$setting['date_end']          = null;
		$setting['data_store']        = array(0);

		$setting['page']                = 'product'; // product|category|manufacturer
		$setting['is_filter']           = 0; // For product page
		$setting['filter_special']      = 0;
		$setting['filter_quantity_min'] = null;
		$setting['filter_quantity_max'] = null;
		$setting['filter_price_min']    = null;
		$setting['filter_price_max']    = null;
		$setting['sub_category']        = 1;
		$setting['data_product']        = array();
		$setting['data_category']       = array();
		$setting['data_manufacturer']   = array();
		$setting['data_filter']         = array();

		$setting['hook'] = 'tab'; // tab|accordion|FAQ|popup

		$setting['setting'] = array(
			'title' => array(),
			'icon_font' => array(),
			'icon_svg' => array(),
			'icon_image' => array(),
			'icon_width' => '',
			'icon_height' => '',
			'icon_size' => '',
			'value_type' => 'html', // html|content_builder|module|widget
			'value_html' => array(),
			'value_content_builder_id' => 0,
			'value_module' => '',
			'value_widget_code' => '',
			'value_widget_data' => '',
			// 'value_design_code' 		=> '',
			// 'value_design_data' 		=> '',
			'popup_unique_id' => 'mz-popup-' . mt_rand(),
			'popup_size' => 'lg',
			'popup_close_button' => 1,
			'popup_auto_start_status' => 0,
			'popup_auto_start_delay' => 3,
			'popup_auto_close_status' => 0,
			'popup_auto_close_delay' => 6,
			'popup_do_not_show_again' => 0,
		);


		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		} elseif (isset($this->request->get['data_id'])) {
			$setting                      = array_merge($setting, $this->model_extension_maza_catalog_data->getData($this->request->get['data_id']));
			$setting['data_store']        = $this->model_extension_maza_catalog_data->getDataStores($this->request->get['data_id']);
			$setting['data_product']      = $this->model_extension_maza_catalog_data->getDataProducts($this->request->get['data_id']);
			$setting['data_category']     = $this->model_extension_maza_catalog_data->getDataCategories($this->request->get['data_id']);
			$setting['data_manufacturer'] = $this->model_extension_maza_catalog_data->getDataManufacturers($this->request->get['data_id']);
			$setting['data_filter']       = $this->model_extension_maza_catalog_data->getDataFilter($this->request->get['data_id']);
		}

		// Data
		$data = array_merge($data, $setting);

		if (!isset($this->request->get['data_id'])) {
			$data['action'] = $this->url->link('extension/maza/catalog/data/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/catalog/data/edit', 'user_token=' . $this->session->data['user_token'] . '&data_id=' . $this->request->get['data_id'] . $url, true);
		}

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

		// product
		$data['data_products'] = array();

		foreach ($setting['data_product'] as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$data['data_products'][] = array(
					'product_id' => $product_info['product_id'],
					'name' => $product_info['name']
				);
			}
		}

		// Categories
		$data['data_categories'] = array();

		foreach ($setting['data_category'] as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['data_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}

		// manufacturers
		$data['data_manufacturers'] = array();

		foreach ($setting['data_manufacturer'] as $manufacturer_id) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);

			if ($manufacturer_info) {
				$data['data_manufacturers'][] = array(
					'manufacturer_id' => $manufacturer_info['manufacturer_id'],
					'name' => $manufacturer_info['name']
				);
			}
		}

		// Filter
		$data['data_filters'] = array();

		foreach ($setting['data_filter'] as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['data_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name' => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}

		// Content builder name
		$content_builder_info = $this->model_extension_maza_content_builder->getContent($setting['setting']['value_content_builder_id']);

		if ($content_builder_info) {
			$data['setting_content_builder_name'] = $content_builder_info['name'];
		} else {
			$data['setting_content_builder_name'] = '';
		}

		// Icon thumb Image
		$data['thumb_setting_icon_image'] = array();

		if ($setting['setting']['icon_image']) {
			foreach ($setting['setting']['icon_image'] as $language_id => $image) {
				if ($image) {
					$data['thumb_setting_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
				} else {
					$data['thumb_setting_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
				}
			}
		}


		// Icon thumb svg
		$data['thumb_setting_icon_svg'] = array();

		if ($setting['setting']['icon_svg']) {
			foreach ($setting['setting']['icon_svg'] as $language_id => $image_svg) {
				if ($image_svg) {
					$data['thumb_setting_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
				} else {
					$data['thumb_setting_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
				}
			}
		}

		$data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['placeholder_svg']   = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
		$data['placeholder_font']  = 'fa fa-font';

		$data['list_customer'] = array(
			array('id' => 0, 'name' => $this->language->get('text_all')),
			array('id' => -1, 'name' => $this->language->get('text_guest')),
			array('id' => 1, 'name' => $this->language->get('text_logged')),
		);

		$data['list_values'] = array(
			array('code' => 'html', 'text' => $this->language->get('text_html')),
			array('code' => 'module', 'text' => $this->language->get('text_module')),
			array('code' => 'widget', 'text' => $this->language->get('text_widget')),
			// array('code' => 'design', 'text' => $this->language->get('text_design')),
			array('code' => 'content_builder', 'text' => $this->language->get('text_content_builder')),
		);

		$data['list_page'] = array(
			array('id' => 'product', 'name' => $this->language->get('text_product')),
			array('id' => 'category', 'name' => $this->language->get('text_category')),
			array('id' => 'manufacturer', 'name' => $this->language->get('text_manufacturer')),
		);

		$data['list_hook'] = array(
			array('id' => 'tab', 'name' => $this->language->get('text_tab')),
			array('id' => 'accordion', 'name' => $this->language->get('text_accordion')),
			array('id' => 'faq', 'name' => $this->language->get('text_faq')),
			array('id' => 'popup', 'name' => $this->language->get('text_popup')),
		);

		// Modules
		$data['extensions'] = array();

		// Get a list of installed modules
		$extensions = $this->model_extension_maza_opencart->getInstalled('module');

		// Add all the modules which have multiple settings for each module
		foreach ($extensions as $code) {
			$this->load->language('extension/module/' . $code, 'extension');

			if (version_compare(VERSION, '3.0.0.0') < 0) { // For opencart 2
				$heading_title = $this->language->get('heading_title');
			} else {
				$heading_title = $this->language->get('extension')->get('heading_title');
			}

			$module_data = array();

			$modules = $this->model_extension_maza_opencart->getModulesByCode($code);

			foreach ($modules as $module) {
				$module_data[] = array(
					'name' => strip_tags($module['name']),
					'code' => $code . '.' . $module['module_id']
				);
			}

			if ($this->config->has('module_' . $code . '_status') || $this->config->has($code . '_status') || $module_data) {
				$data['extensions'][] = array(
					'name' => strip_tags($heading_title),
					'code' => $code,
					'module' => $module_data
				);
			}
		}

		$data['widgets'] = $this->model_extension_maza_extension->getWidgets();
		// $data['designs'] = $this->model_extension_maza_extension->getDesigns();
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['list_popup_size'] = array(
			array('id' => 'xl', 'name' => $this->language->get('text_xl')),
			array('id' => 'lg', 'name' => $this->language->get('text_lg')),
			array('id' => 'md', 'name' => $this->language->get('text_md')),
			array('id' => 'sm', 'name' => $this->language->get('text_sm')),
		);

		$data['languages']  = $this->model_localisation_language->getLanguages();
		$data['user_token'] = $this->session->data['user_token'];

		$data['default_url'] = '&user_token=' . $this->session->data['user_token'];
		if (isset($this->request->get['mz_theme_code'])) {
			$data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

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

		$this->response->setOutput($this->load->view('extension/maza/catalog/data_form', $data));
	}

	protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/catalog/data')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		// name
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 255)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		// Title
		if ($this->request->post['hook'] !== 'popup') { // Title is optional for popup
			foreach ($this->request->post['setting']['title'] as $language_id => $value) {
				if ((utf8_strlen($value) < 2) || (utf8_strlen($value) > 255)) {
					$this->error['setting']['title'][$language_id] = $this->language->get('error_title');
				}
			}
		}

		// Widget
		if ($this->request->post['setting']['value_type'] == 'widget' && (empty($this->request->post['setting']['value_widget_code']) || empty($this->request->post['setting']['value_widget_data']))) {
			$this->error['setting']['value_widget'] = $this->language->get('error_widget');
		}


		// Product page
		if ($this->request->post['page'] === 'product' && $this->request->post['is_filter'] === '0' && empty($this->request->post['data_product'])) {
			$this->error['data_product'] = $this->language->get('error_product');
		}

		// Category page
		if ($this->request->post['page'] === 'category' && empty($this->request->post['data_category'])) {
			$this->error['data_category'] = $this->language->get('error_category');
		}

		// Manufacturer page
		if ($this->request->post['page'] === 'manufacturer' && empty($this->request->post['data_manufacturer'])) {
			$this->error['data_manufacturer'] = $this->language->get('error_manufacturer');
		}

		// Popup unique id
		if ($this->request->post['hook'] === 'popup' && empty($this->request->post['setting']['popup_unique_id'])) {
			$this->error['setting']['popup_unique_id'] = $this->language->get('error_unique_id');
		}

		if (!isset($this->error['warning']) && $this->error) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/catalog/data')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/catalog/data')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete(): void {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/catalog/data');

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start' => 0,
				'limit' => $limit
			);

			$datas = $this->model_extension_maza_catalog_data->getDatas($filter_data);

			foreach ($datas as $data) {
				$json[] = array(
					'data_id' => $data['data_id'],
					'name' => strip_tags(html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8'))
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