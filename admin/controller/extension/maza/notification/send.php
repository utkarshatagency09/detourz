<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaNotificationSend extends Controller {
	private $error = array();

	public function index(): void {
		$this->load->language('extension/maza/notification/send');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('setting/setting');
		$this->load->model('tool/image');
		$this->load->model('extension/maza/blog/article');
		$this->load->model('extension/maza/notification');
		$this->load->model('extension/maza/notification/send');
		$this->load->model('extension/maza/notification/push');
		$this->load->model('extension/maza/tool/mail');
		$this->load->model('extension/maza/tool/sms');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
			if (!empty($this->request->post['product_id'])) {
				$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);
			} else {
				$product_info = [];
			}

			if (!empty($this->request->post['manufacturer_id'])) {
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->post['manufacturer_id']);
			} else {
				$manufacturer_info = [];
			}

			if (!empty($this->request->post['article_id'])) {
				$article_info = $this->model_extension_maza_blog_article->getArticle($this->request->post['article_id']);
			} else {
				$article_info = [];
			}

			// Push image
			if ($product_info && is_file(DIR_IMAGE . $product_info['image'])) {
				$push_image = $this->model_tool_image->resize($product_info['image'], 728, 360);
			} else {
				$push_image = '';
			}

			// Push url
			if ($product_info) {
				$push_url = $this->config->get('mz_store_url') . 'index.php?route=product/product&product_id=' . $product_info['product_id'];
			} elseif ($article_info) {
				$push_url = $this->config->get('mz_store_url') . 'index.php?route=extension/maza/blog/article&article_id=' . $article_info['article_id'];
			} else {
				$push_url = $this->request->post['push_url'];
			}

			// website notification
			if (in_array($this->request->get['type'], ['product', 'channel'])) {
				$notification_message = $this->request->post['message'];
			} else {
				$notification_message = '';
			}

			if ($product_info) {
				$filter_data = $product_info;
			} elseif ($manufacturer_info) {
				$filter_data = $manufacturer_info;
			}
			$filter_data['channel_id'] = $this->request->post['channel_id'];

			if ($this->request->get['type'] == 'channel') {
				$subscribers = $this->model_extension_maza_notification_send->getChannelSubscribers($filter_data);
			} else {
				$subscribers = $this->model_extension_maza_notification_send->getSubscribers($filter_data);
			}

			$count = 0;

			foreach ($subscribers as $subscriber) {
				// Notification
				if ($subscriber['customer_id']) {
					$this->model_extension_maza_notification->addNotification([
						'customer_id' => $subscriber['customer_id'],
						'type' => $this->request->get['type'],
						'message' => $this->parseShortcode($notification_message, $subscriber, $product_info, $manufacturer_info),
						'product_id' => $this->request->post['product_id'] ?? 0,
						'manufacturer_id' => $this->request->post['manufacturer_id'] ?? 0,
						'article_id' => $this->request->post['article_id'] ?? 0,
					]);
				}

				// SMS
				if ($this->config->get('maza_notification_sms') && in_array('sms', $subscriber['methods']) && $subscriber['telephone']) {
					$this->model_extension_maza_tool_sms->addSMS([
						'telephone' => $subscriber['telephone'],
						'message' => strip_tags($this->parseShortcode($this->request->post['sms_message'], $subscriber, $product_info, $manufacturer_info)),
					]);
				}

				// Push
				if ($this->config->get('maza_notification_push') && in_array('push', $subscriber['methods']) && $subscriber['endpoint']) {
					$this->model_extension_maza_notification_push->addPush([
						'endpoint' 		=> $subscriber['endpoint'],
						'key_auth' 		=> $subscriber['key_auth'],
						'key_p256dh' 	=> $subscriber['key_p256dh'],
						'title' 		=> strip_tags($this->parseShortcode($this->request->post['push_title'], $subscriber, $product_info, $manufacturer_info)),
						'message' 		=> strip_tags($this->parseShortcode($this->request->post['push_message'], $subscriber, $product_info, $manufacturer_info)),
						'image'			=> $push_image,
						'url'			=> $push_url
					]);
				}

				// Mail
				if (in_array('email', $subscriber['methods'])) {
					if ($subscriber['customer_id']) {
						if ($product_info) {
							$data['unsubscribe'] = sprintf($this->language->get('text_unsubscribe'), $this->config->get('mz_store_url') . 'index.php?route=extension/maza/notification/unsubscribe_mail&product_id=' . $product_info['product_id']);
						} elseif ($manufacturer_info) {
							$data['unsubscribe'] = sprintf($this->language->get('text_unsubscribe'), $this->config->get('mz_store_url') . 'index.php?route=extension/maza/notification/unsubscribe_mail&manufacturer_id=' . $manufacturer_info['manufacturer_id']);
						}
					}
	
					$data['message'] = $this->parseShortcode(html_entity_decode($this->request->post['mail_message']), $subscriber, $product_info, $manufacturer_info, $article_info);
	
					$this->model_extension_maza_tool_mail->addMail([
						'to' => $subscriber['email'],
						'from' => $this->config->get('config_email'),
						'sender' => $this->config->get('config_name'),
						'subject' => $this->parseShortcode($this->request->post['mail_subject'], $subscriber, $product_info, $manufacturer_info, $article_info),
						'body' => $this->load->view('extension/maza/mail/notification', $data),
					]);
				}

				$count++;
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success_send'), $count);

			$this->model_setting_setting->editSetting('notification_send_' . $this->request->get['type'], array(
				'notification_send_' . $this->request->get['type'] . '_message' => $this->request->post['message'] ?? '',
				'notification_send_' . $this->request->get['type'] . '_mail_subject' => $this->request->post['mail_subject'],
				'notification_send_' . $this->request->get['type'] . '_mail_message' => $this->request->post['mail_message'],
				'notification_send_' . $this->request->get['type'] . '_sms_message' => $this->request->post['sms_message']??'',
				'notification_send_' . $this->request->get['type'] . '_push_title' => $this->request->post['push_title']??'',
				'notification_send_' . $this->request->get['type'] . '_push_message' => $this->request->post['push_message']??'',
				'notification_send_' . $this->request->get['type'] . '_push_url' => $this->request->post['push_url']??'',
			));
		}

		$this->getForm();
	}

	public function save(): void {
		$this->load->language('extension/maza/notification/send');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('extension/maza/blog/article');
		$this->load->model('setting/setting');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
			$this->model_setting_setting->editSetting('notification_send_' . $this->request->get['type'], array(
				'notification_send_' . $this->request->get['type'] . '_message' => $this->request->post['message'] ?? '',
				'notification_send_' . $this->request->get['type'] . '_mail_subject' => $this->request->post['mail_subject'],
				'notification_send_' . $this->request->get['type'] . '_mail_message' => $this->request->post['mail_message'],
				'notification_send_' . $this->request->get['type'] . '_sms_message' => $this->request->post['sms_message']??'',
				'notification_send_' . $this->request->get['type'] . '_push_title' => $this->request->post['push_title']??'',
				'notification_send_' . $this->request->get['type'] . '_push_message' => $this->request->post['push_message']??'',
				'notification_send_' . $this->request->get['type'] . '_push_url' => $this->request->post['push_url']??'',
			));

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->getForm();
	}

	public function getForm(): void {
		$url = '';

		if (isset($this->request->get['type'])) {
			$url .= '&type=' . $this->request->get['type'];
		} else {
			$url .= '&type=channel';
		}
		if (isset($this->request->get['product_id'])) {
			$url .= '&product_id=' . $this->request->get['product_id'];
		}
		if (isset($this->request->get['manufacturer_id'])) {
			$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
		}
		if (isset($this->request->get['article_id'])) {
			$url .= '&article_id=' . $this->request->get['article_id'];
		}
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
			'formaction' => $this->url->link('extension/maza/notification/send/save', 'user_token=' . $this->session->data['user_token'] . $url, true),
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
		$header_data['form_target_id'] = 'form-mz-notification-send';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		$type = $this->request->get['type'] ?? 'channel';

		$setting                    = array();
		$setting['product_id']      = $this->request->get['product_id'] ?? 0;
		$setting['manufacturer_id'] = $this->request->get['manufacturer_id'] ?? 0;
		$setting['article_id']      = $this->request->get['article_id'] ?? 0;
		$setting['channel_id']      = '';
		$setting['message']         = $this->config->get('notification_send_' . $type . '_message');
		$setting['mail_subject']    = $this->config->get('notification_send_' . $type . '_mail_subject');
		$setting['mail_message']    = $this->config->get('notification_send_' . $type . '_mail_message');
		$setting['sms_message']     = $this->config->get('notification_send_' . $type . '_sms_message');
		$setting['push_title']      = $this->config->get('notification_send_' . $type . '_push_title');
		$setting['push_message']    = $this->config->get('notification_send_' . $type . '_push_message');
		$setting['push_url']    	= $this->config->get('notification_send_' . $type . '_push_url');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		}

		// Data
		$data = array_merge($data, $setting);

		$data['action'] = $this->url->link('extension/maza/notification/send', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if ($setting['product_id']) {
			$product_info = $this->model_catalog_product->getProduct($setting['product_id']);

			if ($product_info) {
				$data['product'] = $product_info['name'];
			}
		}
		if ($setting['manufacturer_id']) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($setting['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			}
		}
		if ($setting['article_id']) {
			$article_info = $this->model_extension_maza_blog_article->getArticle($setting['article_id']);

			if ($article_info) {
				$data['article'] = $article_info['name'];
			}
		}

		$this->load->model('extension/maza/notification/channel');

		$data['channels'] = $this->model_extension_maza_notification_channel->getChannels();

		$url = '';

		if (isset($this->request->get['product_id'])) {
			$url .= '&product_id=' . $this->request->get['product_id'];
		}
		if (isset($this->request->get['manufacturer_id'])) {
			$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
		}
		if (isset($this->request->get['article_id'])) {
			$url .= '&article_id=' . $this->request->get['article_id'];
		}
		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		$data['list_types'] = array(
			array(
				'id' => 'channel',
				'name' => $this->language->get('text_channel'),
				'href' => $this->url->link('extension/maza/notification/send', 'user_token=' . $this->session->data['user_token'] . '&type=channel' . $url, true),
			),
			array(
				'id' => 'product',
				'name' => $this->language->get('text_product'),
				'href' => $this->url->link('extension/maza/notification/send', 'user_token=' . $this->session->data['user_token'] . '&type=product' . $url, true),
			),
			array(
				'id' => 'price',
				'name' => $this->language->get('text_price'),
				'href' => $this->url->link('extension/maza/notification/send', 'user_token=' . $this->session->data['user_token'] . '&type=price' . $url, true),
			),
			array(
				'id' => 'availability',
				'name' => $this->language->get('text_availability'),
				'href' => $this->url->link('extension/maza/notification/send', 'user_token=' . $this->session->data['user_token'] . '&type=availability' . $url, true),
			),
			array(
				'id' => 'coming',
				'name' => $this->language->get('text_coming'),
				'href' => $this->url->link('extension/maza/notification/send', 'user_token=' . $this->session->data['user_token'] . '&type=coming' . $url, true),
			),
		);

		$data['sms_status'] 	= $this->config->get('maza_notification_sms');
		$data['push_status'] 	= $this->config->get('maza_notification_push');
		$data['type']       	= $type;

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

		$this->response->setOutput($this->load->view('extension/maza/notification/send', $data));
	}

	protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/notification/send')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->get['type']) || !in_array($this->request->get['type'], ['channel', 'product', 'price', 'availability', 'coming'])) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['product_id']) && in_array($this->request->get['type'], ['price', 'availability'])) {
			$this->error['product'] = $this->language->get('error_product');
		}

		if (empty($this->request->post['manufacturer_id']) && in_array($this->request->get['type'], ['coming'])) {
			$this->error['manufacturer'] = $this->language->get('error_manufacturer');
		}

		if (empty($this->request->post['channel_id'])) {
			$this->error['channel'] = $this->language->get('error_channel');
		}

		if ($this->request->get['type'] == 'product') {
			if (empty($this->request->post['product_id']) && empty($this->request->post['manufacturer_id'])) {
				$this->error['product'] = $this->language->get('error_product');
			}
		}

		if (in_array($this->request->get['type'], ['product', 'channel']) && empty($this->request->post['message'])) {
			$this->error['message'] = $this->language->get('error_message');
		}

		// Mail
		if (empty($this->request->post['mail_subject'])) {
			$this->error['mail_subject'] = $this->language->get('error_subject');
		}

		if (empty($this->request->post['mail_message'])) {
			$this->error['mail_message'] = $this->language->get('error_message');
		}

		// SMS
		if ($this->config->get('maza_notification_sms') && empty($this->request->post['sms_message'])) {
			$this->error['sms_message'] = $this->language->get('error_message');
		}

		// Push
		if ($this->config->get('maza_notification_push')) {
			if (empty($this->request->post['push_title'])) {
				$this->error['push_title'] = $this->language->get('error_title');
			}
			if (empty($this->request->post['push_message'])) {
				$this->error['push_message'] = $this->language->get('error_message');
			}
		}

		return !$this->error;
	}

	private function parseShortcode(string $text, array $subscriber, array $product_info = [], array $manufacturer_info = [], array $article_info = []): string {
		if ($subscriber['customer_id']) {
			$customer = $subscriber['firstname'];
		} else {
			$customer = $this->language->get('text_user');
		}

		// customer
		$text = str_replace('{customer}', $customer, $text);

		// Product
		if ($product_info) {
			$product_url = $this->config->get('mz_store_url') . 'index.php?route=product/product&product_id=' . $product_info['product_id'];

			// Product name
			$text = str_replace('{product_name}', $product_info['name'], $text);

			// Product link
			$text = str_replace('{product_link}', $product_url, $text);

			// product
			$text = str_replace('{product}', sprintf('<a href="%s">%s</a>', $product_url, $product_info['name']), $text);
		}

		// Manufacturer
		if ($manufacturer_info) {
			$manufacturer_url = $this->config->get('mz_store_url') . 'index.php?route=product/manufacturer/info&manufacturer_id=' . $manufacturer_info['manufacturer_id'];

			// Manufacturer name
			$text = str_replace('{manufacturer_name}', $manufacturer_info['name'], $text);

			// Manufacturer link
			$text = str_replace('{manufacturer_link}', $manufacturer_url, $text);

			// manufacturer
			$text = str_replace('{manufacturer}', sprintf('<a href="%s">%s</a>', $manufacturer_url, $manufacturer_info['name']), $text);
		}

		// Article
		if ($article_info) {
			$article_url = $this->config->get('mz_store_url') . 'index.php?route=extension/maza/blog/article&article_id=' . $article_info['article_id'];

			// article name
			$text = str_replace('{article_name}', $article_info['name'], $text);

			// article link
			$text = str_replace('{article_link}', $article_url, $text);

			// article
			$text = str_replace('{article}', sprintf('<a href="%s">%s</a>', $article_url, $article_info['name']), $text);
		}

		return $text;
	}
}