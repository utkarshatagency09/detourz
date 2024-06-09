<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaNewsletter extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/maza/newsletter');

		$this->load->model('extension/maza/asset');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}

		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		// Header
		$header_data = array();

		$header_data['menu'] = array(
			array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
			array('name' => $this->language->get('tab_translate'), 'id' => 'tab-mz-translate', 'href' => false),
			array('name' => $this->language->get('tab_template'), 'id' => 'tab-mz-template', 'href' => false),
		);

		$header_data['menu_active']    = 'tab-mz-general';
		$header_data['buttons'][]      = array(
			'id' => 'button-import',
			'name' => false,
			'tooltip' => $this->language->get('button_import'),
			'icon' => 'fa-upload',
			'class' => 'btn-info',
			'href' => false,
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-export',
			'name' => false,
			'tooltip' => $this->language->get('button_export'),
			'icon' => 'fa-download',
			'class' => 'btn-info',
			'href' => $this->url->link('extension/maza/newsletter/export', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-save',
			'name' => false,
			'tooltip' => $this->language->get('button_save'),
			'icon' => 'fa-save',
			'class' => 'btn-primary',
			'href' => FALSE,
			'target' => FALSE,
			'form_target_id' => 'form-newsletter',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-docs',
			'name' => null,
			'tooltip' => $this->language->get('button_docs'),
			'icon' => 'fa-info',
			'class' => 'btn-default',
			'href' => 'https://docs.pocotheme.com/#page-newsletter-setting',
			'target' => '_blank'
		);
		$header_data['form_target_id'] = 'form-newsletter';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Submit form
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'newsletter', $this->request->post);
			// clear asset files for new settings
			$this->mz_document->clear();

			$data['success'] = $this->language->get('text_success');
		}

		if (isset($this->error['warning'])) {
			$data['warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$data['import'] = $this->url->link('extension/maza/newsletter/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action'] = $this->url->link('extension/maza/newsletter', 'user_token=' . $this->session->data['user_token'] . $url, true);

		// Setting
		$setting = array();

		// General
		$setting['newsletter_confirm_subscribe_status']   = 1;
		$setting['newsletter_confirm_unsubscribe_status'] = 1;
		$setting['newsletter_welcome_mail_status']        = 1;
		$setting['newsletter_required_approval']          = 0;

		// Translate
		$setting['newsletter_translate'] = array();

		// Template
		$setting['newsletter_confirm_subscribe_template']   = array();
		$setting['newsletter_confirm_unsubscribe_template'] = array();
		$setting['newsletter_welcome_mail_template']        = array();


		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		} else {
			$setting = array_merge($setting, $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'newsletter'));
		}

		// Data
		$data               = array_merge($data, $setting);
		$data['user_token'] = $this->session->data['user_token'];

		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/newsletter', $data));
	}

	/**
	 * Send mail
	 */
	public function mail() {
		$this->load->language('extension/maza/newsletter');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		// Header
		$header_data = array();
		$header_data['buttons'][] = array(
			'id' => 'button-send',
			'name' => false,
			'tooltip' => $this->language->get('button_send'),
			'icon' => 'fa-envelope',
			'class' => 'btn-primary',
			'href' => FALSE,
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['buttons'][] = array(
			'id' => 'button-docs',
			'name' => null,
			'tooltip' => $this->language->get('button_docs'),
			'icon' => 'fa-info',
			'class' => 'btn-default',
			'href' => 'https://docs.pocotheme.com/#page-newsletter',
			'target' => '_blank'
		);
		$data['mz_header']        = $this->load->controller('extension/maza/common/header', $header_data);

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('setting/store');

		$data['stores']   = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name' => $this->config->get('config_name')
		);

		$stores = $this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name' => $store['name']
			);
		}

		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/newsletter/mail', $data));
	}

	/**
	 * Subscriber list
	 */
	public function subscriber() {
		$this->load->language('extension/maza/newsletter');
		$this->load->model('extension/maza/newsletter');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}
		if (isset($this->request->get['filter_start_date_added'])) {
			$filter_start_date_added = $this->request->get['filter_start_date_added'];
		} else {
			$filter_start_date_added = null;
		}
		if (isset($this->request->get['filter_end_date_added'])) {
			$filter_end_date_added = $this->request->get['filter_end_date_added'];
		} else {
			$filter_end_date_added = null;
		}
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
		if (isset($this->request->get['filter_is_confirmed'])) {
			$filter_is_confirmed = $this->request->get['filter_is_confirmed'];
		} else {
			$filter_is_confirmed = null;
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'email_id';
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

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
		if (isset($this->request->get['filter_start_date_added'])) {
			$url .= '&filter_start_date_added=' . $this->request->get['filter_start_date_added'];
		}
		if (isset($this->request->get['filter_end_date_added'])) {
			$url .= '&filter_end_date_added=' . $this->request->get['filter_end_date_added'];
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['filter_is_confirmed'])) {
			$url .= '&filter_is_confirmed=' . $this->request->get['filter_is_confirmed'];
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

		// Header
		$header_data = array();

		// $header_data['menu'] = array(
		// 	array('name' => $this->language->get('tab_subscriber'), 'id' => 'tab-mz-subscriber', 'href' => false),
		// 	array('name' => $this->language->get('tab_mail'), 'id' => 'tab-mz-mail', 'href' => $this->url->link('extension/maza/newsletter/mail', 'user_token=' . $this->session->data['user_token'] . $url, true)),
		// 	array('name' => $this->language->get('tab_setting'), 'id' => 'tab-mz-setting', 'href' => $this->url->link('extension/maza/newsletter', 'user_token=' . $this->session->data['user_token'] . $url, true)),
		// );

		// $header_data['menu_active']    = 'tab-mz-subscriber';
		$header_data['buttons'][]      = array(
			'id' => 'button-delete',
			'name' => false,
			'tooltip' => $this->language->get('button_delete'),
			'confirm' => $this->language->get('text_confirm'),
			'icon' => 'fa-trash',
			'class' => 'btn-danger',
			'href' => FALSE,
			'target' => FALSE,
			'formaction' => $this->url->link('extension/maza/newsletter/subscriber_delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'form_target_id' => 'form-mz-subscriber',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-approve',
			'name' => false,
			'tooltip' => $this->language->get('button_approve'),
			'confirm' => $this->language->get('text_confirm'),
			'icon' => 'fa-thumbs-up',
			'class' => 'btn-success',
			'href' => FALSE,
			'target' => FALSE,
			'formaction' => $this->url->link('extension/maza/newsletter/subscriber_approve', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'form_target_id' => 'form-mz-subscriber',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-disapprove',
			'name' => false,
			'tooltip' => $this->language->get('button_disapprove'),
			'confirm' => $this->language->get('text_confirm'),
			'icon' => 'fa-thumbs-down',
			'class' => 'btn-warning',
			'href' => FALSE,
			'target' => FALSE,
			'formaction' => $this->url->link('extension/maza/newsletter/subscriber_disapprove', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'form_target_id' => 'form-mz-subscriber',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-export',
			'name' => false,
			'tooltip' => $this->language->get('button_export'),
			'icon' => 'fa-download',
			'class' => 'btn-info',
			'href' => $this->url->link('extension/maza/newsletter/subscriber_export', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-docs',
			'name' => null,
			'tooltip' => $this->language->get('button_docs'),
			'icon' => 'fa-info',
			'class' => 'btn-default',
			'href' => 'https://docs.pocotheme.com/#page-newsletter',
			'target' => '_blank'
		);
		$header_data['form_target_id'] = 'form-newsletter';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		if (isset($this->error['warning'])) {
			$data['warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$data['subscribers'] = array();

		$filter_data = array(
			'filter_email' => $filter_email,
			'filter_start_date_added' => $filter_start_date_added,
			'filter_end_date_added' => $filter_end_date_added,
			'filter_status' => $filter_status,
			'filter_is_confirmed' => $filter_is_confirmed,
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$subscriber_total = $this->model_extension_maza_newsletter->getTotalSubscribers($filter_data);

		$subscribers = $this->model_extension_maza_newsletter->getSubscribers($filter_data);

		foreach ($subscribers as $subscriber) {
			$data['subscribers'][] = array(
				'subscriber_id' => $subscriber['subscriber_id'],
				'email_id' => $subscriber['email_id'],
				'date_added' => $subscriber['date_added'],
				'confirmed' => $subscriber['is_confirmed'],
				'approved' => $subscriber['status'],
			);
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array) $this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
		if (isset($this->request->get['filter_start_date_added'])) {
			$url .= '&filter_start_date_added=' . $this->request->get['filter_start_date_added'];
		}
		if (isset($this->request->get['filter_end_date_added'])) {
			$url .= '&filter_end_date_added=' . $this->request->get['filter_end_date_added'];
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['filter_is_confirmed'])) {
			$url .= '&filter_is_confirmed=' . $this->request->get['filter_is_confirmed'];
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

		$data['sort_email_id']     = $this->url->link('extension/maza/newsletter/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=email_id' . $url, true);
		$data['sort_date_added']   = $this->url->link('extension/maza/newsletter/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		$data['sort_is_confirmed'] = $this->url->link('extension/maza/newsletter/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=is_confirmed' . $url, true);
		$data['sort_status']       = $this->url->link('extension/maza/newsletter/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
		if (isset($this->request->get['filter_start_date_added'])) {
			$url .= '&filter_start_date_added=' . $this->request->get['filter_start_date_added'];
		}
		if (isset($this->request->get['filter_end_date_added'])) {
			$url .= '&filter_end_date_added=' . $this->request->get['filter_end_date_added'];
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['filter_is_confirmed'])) {
			$url .= '&filter_is_confirmed=' . $this->request->get['filter_is_confirmed'];
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
		$pagination->url   = $this->url->link('extension/maza/newsletter/subscriber', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($subscriber_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($subscriber_total - $this->config->get('config_limit_admin'))) ? $subscriber_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $subscriber_total, ceil($subscriber_total / $this->config->get('config_limit_admin')));

		$data['filter_email']            = $filter_email;
		$data['filter_start_date_added'] = $filter_start_date_added;
		$data['filter_end_date_added']   = $filter_end_date_added;
		$data['filter_status']           = $filter_status;
		$data['filter_is_confirmed']     = $filter_is_confirmed;
		$data['sort']                    = $sort;
		$data['order']                   = $order;

		$data['default_url'] = '&user_token=' . $this->session->data['user_token'];
		if (isset($this->request->get['mz_theme_code'])) {
			$data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/newsletter/subscriber', $data));
	}

	/**
	 * Export subscriber
	 */
	public function subscriber_export() {
		$this->load->language('extension/maza/newsletter');
		$this->load->model('extension/maza/newsletter');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}
		if (isset($this->request->get['filter_start_date_added'])) {
			$filter_start_date_added = $this->request->get['filter_start_date_added'];
		} else {
			$filter_start_date_added = null;
		}
		if (isset($this->request->get['filter_end_date_added'])) {
			$filter_end_date_added = $this->request->get['filter_end_date_added'];
		} else {
			$filter_end_date_added = null;
		}
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
		if (isset($this->request->get['filter_is_confirmed'])) {
			$filter_is_confirmed = $this->request->get['filter_is_confirmed'];
		} else {
			$filter_is_confirmed = null;
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'email_id';
		}
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="newsletter_subscribers.csv"');

		$csv = fopen('php://output', 'wb');
		fputcsv($csv, array('email_id', 'date_added', 'is_confirmed', 'is_approved', 'token'), ',');

		$limit = 500;

		$filter_data = array(
			'filter_email' => $filter_email,
			'filter_start_date_added' => $filter_start_date_added,
			'filter_end_date_added' => $filter_end_date_added,
			'filter_status' => $filter_status,
			'filter_is_confirmed' => $filter_is_confirmed,
			'sort' => $sort,
			'order' => $order,
			'start' => 0,
			'limit' => $limit
		);

		while ($subscribers = $this->model_extension_maza_newsletter->getSubscribers($filter_data)) {
			foreach ($subscribers as $subscriber) {
				$line = array(
					$subscriber['email_id'],
					$subscriber['date_added'],
					$subscriber['is_confirmed'],
					$subscriber['status'],
					$subscriber['token']
				);

				fputcsv($csv, $line, ',');
			}
			$filter_data['start'] += $limit;
		}

		fclose($csv);
	}

	public function send() {
		$this->load->language('extension/maza/newsletter');
		$this->load->model('extension/maza/newsletter');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', 'extension/maza/newsletter')) {
				$json['error']['warning'] = $this->language->get('error_permission');
			}

			if (!$this->request->post['subject']) {
				$json['error']['subject'] = $this->language->get('error_subject');
			}

			if (!$this->request->post['message']) {
				$json['error']['message'] = $this->language->get('error_message');
			}

			if (!$json) {
				$data = array();

				switch ($this->request->post['to']) {
					case 'approved':
						$data = array(
							'filter_status' => 1,
						);
						break;
					case 'confirmed':
						$data = array(
							'filter_is_confirmed' => 1,
						);
						break;
					case 'confirmed_and_approved':
						$data = array(
							'filter_is_confirmed' => 1,
							'filter_status' => 1,
						);
						break;
				}

				$this->load->model('setting/store');

				$store_info = $this->model_setting_store->getStore($this->request->post['store_id']);

				if ($store_info) {
					$store_name = $store_info['name'];
				} else {
					$store_name = $this->config->get('config_name');
				}

				$data['sender'] = html_entity_decode($store_name, ENT_QUOTES, 'UTF-8');

				$this->load->model('setting/setting');

				$setting = $this->model_setting_setting->getSetting('config', $this->request->post['store_id']);
				
				$data['from'] = isset($setting['config_email']) ? $setting['config_email'] : $this->config->get('config_email');
				$data['subject'] = html_entity_decode($this->request->post['subject'], ENT_QUOTES, 'UTF-8');

				$message = '<html dir="ltr" lang="en">' . "\n";
				$message .= '  <head>' . "\n";
				$message .= '    <title>' . $this->request->post['subject'] . '</title>' . "\n";
				$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
				$message .= '  </head>' . "\n";
				$message .= '  <body>' . html_entity_decode($this->request->post['message'], ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
				$message .= '</html>' . "\n";
				$data['body'] = $message;

				$this->model_extension_maza_newsletter->sendNewsletter($data);
				
				$json['success'] = $this->language->get('text_send_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 *  Delete subscriber
	 */
	public function subscriber_delete() {
		$this->load->language('extension/maza/newsletter');
		$this->load->model('extension/maza/newsletter');

		if (isset($this->request->post['selected']) && $this->validate()) {

			foreach ($this->request->post['selected'] as $subscriber_id) {
				$this->model_extension_maza_newsletter->deleteSubscriber($subscriber_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			if (isset($this->request->get['filter_start_date_added'])) {
				$url .= '&filter_start_date_added=' . $this->request->get['filter_start_date_added'];
			}
			if (isset($this->request->get['filter_end_date_added'])) {
				$url .= '&filter_end_date_added=' . $this->request->get['filter_end_date_added'];
			}
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			if (isset($this->request->get['filter_is_confirmed'])) {
				$url .= '&filter_is_confirmed=' . $this->request->get['filter_is_confirmed'];
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

			$this->response->redirect($this->url->link('extension/maza/newsletter/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->subscriber();
	}

	/**
	 * Approve subscriber
	 */
	public function subscriber_approve() {
		$this->load->language('extension/maza/newsletter');
		$this->load->model('extension/maza/newsletter');

		if (isset($this->request->post['selected']) && $this->validate()) {

			foreach ($this->request->post['selected'] as $subscriber_id) {
				$this->model_extension_maza_newsletter->approveSubscriber($subscriber_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			if (isset($this->request->get['filter_start_date_added'])) {
				$url .= '&filter_start_date_added=' . $this->request->get['filter_start_date_added'];
			}
			if (isset($this->request->get['filter_end_date_added'])) {
				$url .= '&filter_end_date_added=' . $this->request->get['filter_end_date_added'];
			}
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			if (isset($this->request->get['filter_is_confirmed'])) {
				$url .= '&filter_is_confirmed=' . $this->request->get['filter_is_confirmed'];
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

			$this->response->redirect($this->url->link('extension/maza/newsletter/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->subscriber();
	}

	/**
	 * Disapprove subscriber
	 */
	public function subscriber_disapprove() {
		$this->load->language('extension/maza/newsletter');
		$this->load->model('extension/maza/newsletter');

		if (isset($this->request->post['selected']) && $this->validate()) {

			foreach ($this->request->post['selected'] as $subscriber_id) {
				$this->model_extension_maza_newsletter->disapproveSubscriber($subscriber_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			if (isset($this->request->get['filter_start_date_added'])) {
				$url .= '&filter_start_date_added=' . $this->request->get['filter_start_date_added'];
			}
			if (isset($this->request->get['filter_end_date_added'])) {
				$url .= '&filter_end_date_added=' . $this->request->get['filter_end_date_added'];
			}
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			if (isset($this->request->get['filter_is_confirmed'])) {
				$url .= '&filter_is_confirmed=' . $this->request->get['filter_is_confirmed'];
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

			$this->response->redirect($this->url->link('extension/maza/newsletter/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->subscriber();
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/maza/newsletter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}


	/**
	 * Export setting
	 */
	public function export() {
		$this->load->model('extension/maza/skin');
		$this->load->language('extension/maza/newsletter');

		$setting = $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'newsletter');

		if ($setting) {
			header('Content-Type: application/json; charset=utf-8');
			header('Content-disposition: attachment; filename="maza.setting.newsletter.' . $this->mz_skin_config->get('skin_code') . '.json"');

			echo json_encode(['type' => 'maza', 'code' => 'newsletter', 'setting' => $setting]);
		} else {
			$this->session->data['warning'] = $this->language->get('error_no_setting');

			$url = '';

			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}

			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$this->response->redirect($this->url->link('extension/maza/newsletter', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
	}

	/**
	 * Import setting
	 */
	public function import() {
		$this->load->language('extension/maza/newsletter');

		$warning = '';

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/maza/newsletter')) {
			$warning = $this->language->get('error_permission');
		} else {
			if (isset($this->request->files['file']['name'])) {
				if (substr($this->request->files['file']['name'], -4) != 'json') {
					$warning = $this->language->get('error_filetype');
				}

				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$warning = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$warning = $this->language->get('error_upload');
			}
		}

		if (!$warning) {
			$file = $this->request->files['file']['tmp_name'];

			if (is_file($file)) {
				$data = json_decode(file_get_contents($file), true);

				if ($data && $data['type'] == 'maza' && $data['code'] == 'newsletter') {
					$this->load->model('extension/maza/skin');

					$this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'newsletter', $data['setting']);

					$this->session->data['success'] = $this->language->get('text_success_import');
				} else {
					$warning = $this->language->get('error_import_file');
				}
			} else {
				$warning = $this->language->get('error_file');
			}
		}

		if ($warning) {
			$this->session->data['warning'] = $warning;
		}

		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}

		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		$this->response->redirect($this->url->link('extension/maza/newsletter', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}
}