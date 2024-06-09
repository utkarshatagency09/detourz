<?php
/**
 * @package		MazaTheme
 * @auther		Jay padaliya
 * @copyright   Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaNotificationChannel extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/maza/notification/channel');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/notification/channel');

		$this->getList();
	}

	/**
	 * Add channel
	 */
	public function add() {
		$this->load->language('extension/maza/notification/channel');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/notification/channel');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_notification_channel->addChannel($this->request->post);

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

			$this->response->redirect($this->url->link('extension/maza/notification/channel', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	/**
	 * Edit channel
	 */
	public function edit() {
		$this->load->language('extension/maza/notification/channel');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/notification/channel');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_notification_channel->editChannel($this->request->get['channel_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('extension/maza/notification/channel', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	/**
	 * Delete individual channel
	 */
	public function delete() {
		$this->load->language('extension/maza/notification/channel');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/notification/channel');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $channel_id) {
				$this->model_extension_maza_notification_channel->deleteChannel($channel_id);
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

			$this->response->redirect($this->url->link('extension/maza/notification/channel', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}


	/**
	 * Get list of channel
	 */
	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'cd.name';
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

		$header_data['buttons'][]      = array(
			'id' => 'button-add',
			'name' => '',
			'class' => 'btn-primary',
			'tooltip' => $this->language->get('button_add'),
			'icon' => 'fa-plus',
			'href' => $this->url->link('extension/maza/notification/channel/add', 'user_token=' . $this->session->data['user_token'], true),
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
			'form_target_id' => 'form-mz-channel',
			'confirm' => $this->language->get('text_confirm')
		);
		$header_data['form_target_id'] = 'form-mz-channel';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Channel list
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

		$data['add']    = $this->url->link('extension/maza/notification/channel/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/maza/notification/channel/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('tool/image');

		$data['channels'] = array();

		$filter_data = array(
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$channel_total = $this->model_extension_maza_notification_channel->getTotalChannels();

		$results = $this->model_extension_maza_notification_channel->getChannels($filter_data);

		foreach ($results as $result) {
			$data['channels'][] = array(
				'channel_id' => $result['channel_id'],
				'name' => $result['name'],
				'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'sort_order' => $result['sort_order'],
				'date_added' => $result['date_added'],
				'edit' => $this->url->link('extension/maza/notification/channel/edit', 'user_token=' . $this->session->data['user_token'] . '&channel_id=' . $result['channel_id'] . $url, true),
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

		$data['sort_name']       = $this->url->link('extension/maza/notification/channel', 'user_token=' . $this->session->data['user_token'] . '&sort=cd.name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/maza/notification/channel', 'user_token=' . $this->session->data['user_token'] . '&sort=c.sort_order' . $url, true);
		$data['sort_status']     = $this->url->link('extension/maza/notification/channel', 'user_token=' . $this->session->data['user_token'] . '&sort=c.status' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/maza/notification/channel', 'user_token=' . $this->session->data['user_token'] . '&sort=c.date_added' . $url, true);

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
		$pagination->total = $channel_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('extension/maza/notification/channel', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($channel_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($channel_total - $this->config->get('config_limit_admin'))) ? $channel_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $channel_total, ceil($channel_total / $this->config->get('config_limit_admin')));

		$data['user_token'] = $this->session->data['user_token'];

		// Columns
		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/notification/channel_list', $data));
	}

	/**
	 * Form to add or edit Channel
	 */
	protected function getForm() {
		$this->load->model('localisation/language');
		$this->load->model('setting/store');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'cd.name';
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
		$header_data['title']        = !isset($this->request->get['channel_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$header_data['theme_select'] = $header_data['skin_select'] = false;
		$header_data['menu'] = array(
			array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
			array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false)
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
			'form_target_id' => 'form-mz-channel',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-cancel',
			'name' => '',
			'tooltip' => $this->language->get('button_cancel'),
			'icon' => 'fa-reply',
			'class' => 'btn-default',
			'href' => $this->url->link('extension/maza/notification/channel', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['form_target_id'] = 'form-mz-channel';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Setting
		$setting                        = array();
		$setting['sort_order']          = 0;
		$setting['default']             = false;
		$setting['status']              = true;
		$setting['channel_description'] = array();
		$setting['channel_store']       = array(0);

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		} elseif (isset($this->request->get['channel_id'])) {
			$setting                        = array_merge($setting, $this->model_extension_maza_notification_channel->getChannel($this->request->get['channel_id']));
			$setting['channel_description'] = $this->model_extension_maza_notification_channel->getChannelDescriptions($this->request->get['channel_id']);
			$setting['channel_store']       = $this->model_extension_maza_notification_channel->getChannelStores($this->request->get['channel_id']);
		}

		// Data
		$data = array_merge($data, $setting);

		if (!isset($this->request->get['channel_id'])) {
			$data['action'] = $this->url->link('extension/maza/notification/channel/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/notification/channel/edit', 'user_token=' . $this->session->data['user_token'] . '&channel_id=' . $this->request->get['channel_id'] . $url, true);
		}

		// Stores
		$data['stores']   = array();
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

		$this->response->setOutput($this->load->view('extension/maza/notification/channel_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/maza/notification/channel')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['channel_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/maza/notification/channel')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/notification/channel');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort' 		=> 'cd.name',
				'order' 	=> 'ASC',
				'start' 	=> 0,
				'limit' 	=> 5
			);

			$results = $this->model_extension_maza_notification_channel->getChannels($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'channel_id' => $result['channel_id'],
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