<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaNotificationPush extends Controller {
	private $error = array();

	public function index(): void {
		$this->load->language('extension/maza/notification/push');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/notification/push');
		$this->load->model('setting/setting');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
			$total_sent = $this->model_extension_maza_notification_push->push($this->request->post);
			$total = $this->model_extension_maza_notification_push->getTotalSubscriptions();

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $total_sent, $total);

			$this->model_setting_setting->editSetting('notification_push', array(
				'notification_push_message' => $this->request->post['message'],
				'notification_push_title' => $this->request->post['title'],
				'notification_push_url' => $this->request->post['url'],
				'notification_push_image' => $this->request->post['image'],
				'notification_push_channel_id' => $this->request->post['channel_id'],
			));
		}

		$this->getForm();
	}

	public function save(): void {
		$this->load->language('extension/maza/notification/push');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
			$this->model_setting_setting->editSetting('notification_push', array(
				'notification_push_message' => $this->request->post['message'],
				'notification_push_title' => $this->request->post['title'],
				'notification_push_url' => $this->request->post['url'],
				'notification_push_image' => $this->request->post['image'],
				'notification_push_channel_id' => $this->request->post['channel_id'],
			));

			$this->session->data['success'] = $this->language->get('text_success_save');
		}

		$this->getForm();
	}

	public function getForm(): void {
		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		// Header
		$header_data                   = array();
		$header_data['title']          = $this->language->get('heading_title');
		$header_data['theme_select']   = $header_data['skin_select'] = false;
		
		$header_data['buttons'][]      = array(
			'id' => 'button-save',
			'name' => false,
			'tooltip' => $this->language->get('button_save'),
			'icon' => 'fa-save',
			'class' => 'btn-info',
			'href' => FALSE,
			'target' => FALSE,
			'formaction' => $this->url->link('extension/maza/notification/push/save', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'form_target_id' => 'form-mz-notification-send',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-send',
			'name' => false,
			'tooltip' => $this->language->get('button_send'),
			'icon' => 'fa-paper-plane',
			'class' => 'btn-primary',
			'href' => FALSE,
			'target' => FALSE,
			'form_target_id' => 'form-mz-notification-send',
		);
		$header_data['form_target_id'] = 'form-mz-notification-push';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		$setting                    = array();
		$setting['channel_id']      = $this->config->get('notification_push_channel_id');
		$setting['title']         	= $this->config->get('notification_push_title');
		$setting['message']         = $this->config->get('notification_push_message');
		$setting['image']           = $this->config->get('notification_push_image');
		$setting['url']             = $this->config->get('notification_push_url');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		}

		// Data
		$data = array_merge($data, $setting);

		$data['action'] = $this->url->link('extension/maza/notification/push', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('extension/maza/notification/channel');

		$data['channels'] = $this->model_extension_maza_notification_channel->getChannels();

		// Image
		$this->load->model('tool/image');

		if (is_file(DIR_IMAGE . $setting['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($setting['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
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

		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/notification/push', $data));
	}

	protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/notification/push')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['title'])) {
			$this->error['title'] = $this->language->get('error_title');
		}

		if (empty($this->request->post['message'])) {
			$this->error['message'] = $this->language->get('error_message');
		}

		return !$this->error;
	}
}