<?php
class ControllerExtensionMazaAccountNotificationChannel extends Controller {
	public function index(): void {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('extension/maza/account/notification/channel', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('extension/maza/account/notification/channel');

		$this->load->model('extension/maza/account/notification');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/maza/account/notification/channel')
		);

		$data['channel'] = $this->url->link('extension/maza/account/notification/channel');
		$data['product'] = $this->url->link('extension/maza/account/notification/product');
		$data['manufacturer'] = $this->url->link('extension/maza/account/notification/manufacturer');

		$data['channels'] = $this->model_extension_maza_account_notification->getChannels();

		$data['action'] = $this->url->link('extension/maza/account/notification/channel/save');

		$data['sms_notification'] = $this->config->get('maza_notification_sms');
		$data['push_notification'] = $this->config->get('maza_notification_push');

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/maza/account/notification/channel', $data));
	}

	public function save(): void {
		$json = [];

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('extension/maza/account/notification/channel', '', true);

			$json['redirect'] = $this->url->link('account/login', '', true);
		}

		if (!$json && $this->request->server['REQUEST_METHOD'] == 'POST'){
			$this->load->language('extension/maza/account/notification/channel');

			$this->load->model('extension/maza/account/notification');

			$channels = [];

			if (isset($this->request->post['channel'])) {
				foreach ($this->request->post['channel'] as $channel_id => $channel) {
					$channels[$channel_id]['channel_id'] = $channel_id;
					$channels[$channel_id]['methods'] = array_intersect(['email', 'sms', 'push'], $channel['methods']);
				}
			}

			$this->model_extension_maza_account_notification->addChannels($this->customer->getId(), $channels);

			$json['title'] = $this->language->get('text_channel');
			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
