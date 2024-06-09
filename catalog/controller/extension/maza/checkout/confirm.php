<?php
class ControllerExtensionMazaCheckoutConfirm extends Controller {
	public function index(): void {
		$this->load->model('checkout/order');
		$this->load->model('extension/maza/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']??0);

		if (empty($order_info)) {
			$this->response->redirect($this->url->link('checkout/cart'));
		} else {
			$this->load->language('account/order');
			$this->load->language('extension/maza/checkout/confirm');

			$this->document->setTitle($this->language->get('heading_title'));

			if($this->config->get('maza_cdn')){
				$this->document->addScript('https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', 'footer');
				$this->document->addScript('https://cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js', 'footer');
				$this->document->addScript('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', 'footer');
				$this->document->addStyle('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.css', 'stylesheet', 'all', 'footer');
			} else {
				$this->document->addScript('catalog/view/javascript/maza/javascript/daterangepicker/moment.min.js', 'footer');
				$this->document->addScript('catalog/view/javascript/maza/javascript/daterangepicker/moment-with-locales.min.js', 'footer');
				$this->document->addScript('catalog/view/javascript/maza/javascript/daterangepicker/daterangepicker.js', 'footer');
				$this->document->addStyle('catalog/view/javascript/maza/javascript/daterangepicker/daterangepicker.css', 'stylesheet', 'all', 'footer');
			}

			// Required by klarna
			if ($this->config->get('payment_klarna_account') || $this->config->get('payment_klarna_invoice')) {
				$this->document->addScript('http://cdn.klarna.com/public/kitt/toc/v1.0/js/klarna.terms.min.js');
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_cart'),
				'href' => $this->url->link('checkout/cart')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_checkout'),
				'href' => $this->url->link('checkout/checkout')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/maza/checkout/confirm', '', true)
			);

			$this->load->model('tool/upload');

			$data['products'] = array();

			$products = $this->model_extension_maza_order->getOrderProducts($order_info['order_id']);

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_extension_maza_order->getOrderOptions($order_info['order_id'], $product['order_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				$data['products'][] = array(
					'order_product_id'    => $product['order_product_id'],
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'quantity'   => $product['quantity'],
					'price'    	 => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    	 => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
				);
			}

			// Gift Voucher
			$data['vouchers'] = array();

			$vouchers = $this->model_extension_maza_order->getOrderVouchers($order_info['order_id']);

			foreach ($vouchers as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency'])
				);
			}

			$data['totals'] = array();

			$totals = $this->model_extension_maza_order->getOrderTotals($order_info['order_id']);

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
				);
			}

			// Payment address
			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "," . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
			);

			$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$data['payment_method'] = $order_info['payment_method'];

			// Shipping address
			if ($order_info['shipping_method']) {
				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "," . '{country}';
				}
	
				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);
	
				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);
	
				$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
	
				$data['shipping_method'] = $order_info['shipping_method'];
			}

			$data['comment'] = nl2br($order_info['comment']);
			
			$data['payment'] = $this->load->controller('extension/payment/' . $order_info['payment_code']);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/maza/checkout/confirm', $data));
		}
	}
}
