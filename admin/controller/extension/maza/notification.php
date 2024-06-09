<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaNotification extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/maza/notification');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/notification');

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/maza/notification');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/notification');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_notification->addSubscriber($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_customer_id'])) {
				$url .= '&filter_customer_id=' . (int) $this->request->get['filter_customer_id'];
			}
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . (int) $this->request->get['filter_product_id'];
			}
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . (int) $this->request->get['filter_manufacturer_id'];
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

			$this->response->redirect($this->url->link('extension/maza/notification', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/maza/notification');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/notification');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_notification->editSubscriber($this->request->get['subscribe_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_customer_id'])) {
				$url .= '&filter_customer_id=' . (int) $this->request->get['filter_customer_id'];
			}
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . (int) $this->request->get['filter_product_id'];
			}
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . (int) $this->request->get['filter_manufacturer_id'];
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

			$this->response->redirect($this->url->link('extension/maza/notification', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/maza/notification');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/notification');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $subscribe_id) {
				$this->model_extension_maza_notification->deleteSubscriber($subscribe_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_customer_id'])) {
				$url .= '&filter_customer_id=' . (int) $this->request->get['filter_customer_id'];
			}
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . (int) $this->request->get['filter_product_id'];
			}
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . (int) $this->request->get['filter_manufacturer_id'];
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

			$this->response->redirect($this->url->link('extension/maza/notification', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_customer_id'])) {
			$filter_customer_id = $this->request->get['filter_customer_id'];
		} else {
			$filter_customer_id = null;
		}
		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}
		if (isset($this->request->get['filter_product_id'])) {
			$filter_product_id = $this->request->get['filter_product_id'];
		} else {
			$filter_product_id = null;
		}
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$filter_manufacturer_id = $this->request->get['filter_manufacturer_id'];
		} else {
			$filter_manufacturer_id = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'date_added';
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
		// $header_data['menu']   = array();
		// $header_data['menu'][] = array('name' => $this->language->get('tab_send'), 'id' => 'tab-mz-send', 'href' => $this->url->link('extension/maza/notification/send', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// $header_data['menu'][] = array('name' => $this->language->get('tab_subscriber'), 'id' => 'tab-mz-subscriber', 'href' => false);

		// $header_data['menu_active'] = 'tab-mz-subscriber';

		$header_data['buttons'][] = array(
			'id' => 'button-add',
			'name' => '',
			'class' => 'btn-primary',
			'tooltip' => $this->language->get('button_add'),
			'icon' => 'fa-plus',
			'href' => $this->url->link('extension/maza/notification/add', 'user_token=' . $this->session->data['user_token'], true),
			'target' => false,
			'form_target_id' => false,
		);
		$header_data['buttons'][] = array(
			'id' => 'button-delete',
			'name' => '',
			'tooltip' => $this->language->get('button_delete'),
			'icon' => 'fa-trash',
			'class' => 'btn-danger',
			'href' => FALSE,
			'target' => FALSE,
			'confirm' => $this->language->get('text_confirm'),
			'form_target_id' => 'form-mz-subscriber',
		);

		$header_data['form_target_id'] = 'form-mz-subscriber';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		$url = '';

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . (int) $this->request->get['filter_customer_id'];
		}
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . (int) $this->request->get['filter_product_id'];
		}
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . (int) $this->request->get['filter_manufacturer_id'];
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

		// $data['add'] = $this->url->link('extension/maza/notification/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/maza/notification/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('tool/image');

		$data['subscribers'] = array();

		$filter_data = array(
			'filter_customer_id' => $filter_customer_id,
			'filter_email' => $filter_email,
			'filter_product_id' => $filter_product_id,
			'filter_manufacturer_id' => $filter_manufacturer_id,
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$subscriber_total = $this->model_extension_maza_notification->getTotalSubscribers($filter_data);

		$results = $this->model_extension_maza_notification->getSubscribers($filter_data);

		foreach ($results as $result) {
			$data['subscribers'][] = array(
				'subscribe_id' => $result['subscribe_id'],
				'customer' => $result['customer'],
				'email' => $result['email'],
				'product' => $result['product'],
				'manufacturer' => $result['manufacturer'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit' => $this->url->link('extension/maza/notification/edit', 'user_token=' . $this->session->data['user_token'] . '&subscribe_id=' . $result['subscribe_id'] . $url, true),
				'customer_edit' => $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'] . $url, true),
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

		$url = '';

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . (int) $this->request->get['filter_customer_id'];
		}
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . (int) $this->request->get['filter_product_id'];
		}
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . (int) $this->request->get['filter_manufacturer_id'];
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

		$data['sort_customer']     = $this->url->link('extension/maza/notification', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_product']      = $this->url->link('extension/maza/notification', 'user_token=' . $this->session->data['user_token'] . '&sort=product' . $url, true);
		$data['sort_manufacturer'] = $this->url->link('extension/maza/notification', 'user_token=' . $this->session->data['user_token'] . '&sort=manufacturer' . $url, true);
		$data['sort_date_added']   = $this->url->link('extension/maza/notification', 'user_token=' . $this->session->data['user_token'] . '&sort=s.date_added' . $url, true);

		$data['sort']  = $sort;
		$data['order'] = $order;

		$url = '';

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . (int) $this->request->get['filter_customer_id'];
		}
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . (int) $this->request->get['filter_product_id'];
		}
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . (int) $this->request->get['filter_manufacturer_id'];
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
		$pagination->total = $subscriber_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('extension/maza/notification', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['filter_email'] = $filter_email;

		if ($filter_customer_id) {
			$this->load->model('customer/customer');

			$customer_info = $this->model_customer_customer->getCustomer($filter_customer_id);

			if ($customer_info) {
				$data['filter_customer']    = $customer_info['firstname'] . ' ' . $customer_info['lastname'];
				$data['filter_customer_id'] = $filter_customer_id;
			}
		}

		if ($filter_product_id) {
			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($filter_product_id);

			if ($product_info) {
				$data['filter_product']    = $product_info['name'];
				$data['filter_product_id'] = $filter_product_id;
			}
		}

		if ($filter_manufacturer_id) {
			$this->load->model('catalog/manufacturer');

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($filter_manufacturer_id);

			if ($manufacturer_info) {
				$data['filter_manufacturer']    = $manufacturer_info['name'];
				$data['filter_manufacturer_id'] = $filter_manufacturer_id;
			}
		}

		$data['results'] = sprintf($this->language->get('text_pagination'), ($subscriber_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($subscriber_total - $this->config->get('config_limit_admin'))) ? $subscriber_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $subscriber_total, ceil($subscriber_total / $this->config->get('config_limit_admin')));

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

		$this->response->setOutput($this->load->view('extension/maza/notification/subscriber_list', $data));
	}

	protected function getForm() {
		$url = '';

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . (int) $this->request->get['filter_customer_id'];
		}
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . (int) $this->request->get['filter_product_id'];
		}
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . (int) $this->request->get['filter_manufacturer_id'];
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
		$header_data['title']        = !isset($this->request->get['subscribe_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$header_data['theme_select'] = $header_data['skin_select'] = false;
		$header_data['menu'] = array(
			array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
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
			'form_target_id' => 'form-mz-subscriber',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-cancel',
			'name' => '',
			'tooltip' => $this->language->get('button_cancel'),
			'icon' => 'fa-reply',
			'class' => 'btn-default',
			'href' => $this->url->link('extension/maza/notification', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['form_target_id'] = 'form-mz-subscriber';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Setting
		$setting                    = array();
		$setting['customer_id']     = 0;
		$setting['email']           = '';
		$setting['product_id']      = 0;
		$setting['manufacturer_id'] = 0;

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		} elseif (isset($this->request->get['subscribe_id'])) {
			$setting = array_merge($setting, $this->model_extension_maza_notification->getSubscriber($this->request->get['subscribe_id']));
		}

		// Data
		$data = array_merge($data, $setting);

		if (!isset($this->request->get['subscribe_id'])) {
			$data['action'] = $this->url->link('extension/maza/notification/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/notification/edit', 'user_token=' . $this->session->data['user_token'] . '&subscribe_id=' . $this->request->get['subscribe_id'] . $url, true);
		}

		if ($setting['customer_id']) {
			$this->load->model('customer/customer');

			$customer_info = $this->model_customer_customer->getCustomer($setting['customer_id']);

			if ($customer_info) {
				$data['customer'] = $customer_info['firstname'] . ' ' . $customer_info['lastname'];
			}
		}

		if ($setting['product_id']) {
			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($setting['product_id']);

			if ($product_info) {
				$data['product'] = $product_info['name'];
			}
		}

		if ($setting['manufacturer_id']) {
			$this->load->model('catalog/manufacturer');

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($setting['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			}
		}

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

		$this->response->setOutput($this->load->view('extension/maza/notification/subscriber_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/maza/notification')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['email']) && empty($this->request->post['customer_id'])) {
			$this->error['customer'] = $this->language->get('error_customer');
		}

		if (empty($this->request->post['product_id']) && empty($this->request->post['manufacturer_id'])) {
			$this->error['manufacturer'] = $this->language->get('error_manufacturer');
		}

		if (!empty($this->request->post['email']) && !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/maza/notification')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}