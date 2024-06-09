<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaNotification extends Controller {
	public function index(): void {
		$this->load->language('extension/maza/notification');
		$this->load->model('extension/maza/notification');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('extension/maza/blog/article');

		$data['notifications'] = array();

		$notifications = $this->model_extension_maza_notification->getNotifications();

		foreach ($notifications as $notification){
			if ($notification['product_id']) {
				$product_info = $this->model_catalog_product->getProduct($notification['product_id']);
			} else {
				$product_info = array();
			}

			if ($notification['manufacturer_id']) {
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($notification['manufacturer_id']);
			} else {
				$manufacturer_info = array();
			}

			if ($notification['article_id']) {
				$article_info = $this->model_extension_maza_blog_article->getArticle($notification['article_id']);
			} else {
				$article_info = array();
			}

			if ($product_info && is_file(DIR_IMAGE . $product_info['image'])) {
				$image = $this->model_tool_image->resize($product_info['image'], 50, 50);
			} elseif ($article_info && is_file(DIR_IMAGE . $article_info['image'])) {
				$image = $this->model_tool_image->resize($article_info['image'], 50, 50);
			} elseif ($manufacturer_info && is_file(DIR_IMAGE . $manufacturer_info['image'])) {
				$image = $this->model_tool_image->resize($manufacturer_info['image'], 50, 50);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', 50, 50);
			}

			if ($product_info) {
				$href = $this->url->link('product/product', 'product_id=' . $product_info['product_id']);
			} elseif ($article_info) {
				$href = $this->url->link('extension/maza/blog/article', 'article_id=' . $article_info['article_id']);
			} elseif ($manufacturer_info) {
				$href = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_info['manufacturer_id']);
			} else {
				$href = null;
			}

			if ($notification['type'] == 'price' && $product_info) {
				$message = $this->parseProductShortcode($this->language->get('data_price'), $product_info);
			} else if($notification['type'] == 'availability' && $product_info){
				$message = $this->parseProductShortcode($this->language->get('data_availability'), $product_info);
			} else if($notification['type'] == 'coming' && $article_info){
				$message = $article_info['name'];
			} else {
				$message = $notification['message'];
			}

			if ($notification['type'] == 'price') {
				$title = $this->language->get('text_price');
			} else if($notification['type'] == 'availability' && $product_info){
				$title = ($product_info['quantity'] <= 0) ? $product_info['stock_status'] : $this->language->get('text_instock');
			} elseif ($notification['type'] == 'coming') {
				$title = $this->language->get('text_coming');
			} elseif ($product_info) {
				$title = $product_info['name'];
			} elseif(!$message){
				continue;
			} else {
				$title = null;
			}

			$data['notifications'][] = array(
				'notification_id' 	=> $notification['notification_id'],
				'title'				=> $title,
				'message' 			=> $message,
				'image'			 	=> $image,
				'elapsed' 			=> maza\timeElapsed($notification['date_added']),
				'unread' 			=> !$notification['read'],
				'href'				=> $href
			);
		}

		$this->model_extension_maza_notification->readAll();

		$this->response->setOutput($this->load->view('extension/maza/notification', $data));
	}

	private function parseProductShortcode(string $text, array $product_info): string {
		// Product name
		$text = str_replace('{product}', $product_info['name'], $text);

		// Price
		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {
				$price = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			}
		} else {
			$price = false;
		}

		$text = str_replace('{price}', $price, $text);

		return $text;
	}

	public function subscribe(): void {
		$json = array();

		$this->load->language('extension/maza/notification');

		if (!$this->customer->isLogged()) {
			$json['toast'] = $this->load->controller('extension/maza/common/toast/login', $this->language->get('error_login'));
		}

		if (!$json && $this->config->get('maza_notification_status')) {
			$this->load->model('extension/maza/notification');
			
			// Subscribe for product notification
			if (isset($this->request->post['product_id'])) {
				$this->load->model('catalog/product');

				$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);

				if ($product_info) {
					if($product_info['manufacturer_id']){
						$this->model_extension_maza_notification->deleteManufacturerSubscribe($product_info['manufacturer_id']);
					}
					
					$this->model_extension_maza_notification->addProductSubscribe($product_info['product_id']);

					$json['success'] = true;
				}
			}

			// Subscribe for manufacturer notification
			if (isset($this->request->post['manufacturer_id'])) {
				$this->load->model('catalog/manufacturer');

				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->post['manufacturer_id']);

				if ($manufacturer_info) {
					$this->model_extension_maza_notification->addManufacturerSubscribe($manufacturer_info['manufacturer_id']);

					$json['success'] = true;
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function unsubscribe(): void {
		$json = array();

		if ($this->config->get('maza_notification_status') && $this->customer->isLogged()) {
			$this->load->model('extension/maza/notification');
			$this->load->model('catalog/product');

			// Product
			if (isset($this->request->post['product_id'])) {
				$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);

				if ($product_info) {
					if ($product_info['manufacturer_id']) {
						$this->model_extension_maza_notification->deleteManufacturerSubscribe($product_info['manufacturer_id']);
					}
					$this->model_extension_maza_notification->deleteProductSubscribe($product_info['product_id']);

					$json['success'] = true;
				}
			}
			
			// Manufacturer
			if (!empty($this->request->post['manufacturer_id'])) {
				$this->model_extension_maza_notification->deleteManufacturerSubscribe($this->request->post['manufacturer_id']);

				$json['success'] = true;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function unsubscribe_mail(): void {
		$this->load->language('extension/maza/notification');

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->request->server['REQUEST_URI'];

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if ($this->config->get('maza_notification_status') && $this->customer->isLogged()) {
			$this->load->model('extension/maza/notification');
			$this->load->model('catalog/product');

			if(isset($this->request->get['product_id'])){
				$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			} else {
				$product_info = [];
			}

			if ($product_info) {
				if ($product_info['manufacturer_id']) {
					$this->model_extension_maza_notification->deleteManufacturerSubscribe($product_info['manufacturer_id']);
				}
				$this->model_extension_maza_notification->deleteProductSubscribe($product_info['product_id']);
			}

			if(isset($this->request->get['manufacturer_id'])){
				$this->model_extension_maza_notification->deleteManufacturerSubscribe($this->request->get['manufacturer_id']);
			}
		}

		$data['text_message'] = $this->language->get('text_unsubscribe_message');
		
		$data['continue'] = $this->url->link('common/home');
		
		$data['class'] = 'extension-maza-notification-unsubscribe';
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}
