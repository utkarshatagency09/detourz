<?php
class ControllerExtensionMazaCheckout extends Controller {
	public function index(): void {
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->response->redirect($this->url->link('checkout/cart'));
		}
		
		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$this->response->redirect($this->url->link('checkout/cart'));
			}
		}

		$this->load->language('checkout/checkout');
		$this->load->language('extension/maza/checkout');

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
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$this->load->model('account/address');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');

		// Default addresses
		if (empty($this->session->data['payment_address'])) {
			if ($this->customer->isLogged() && $this->customer->getAddressId()) {
				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			} else {
				$this->session->data['payment_address']['country_id'] = $this->config->get('config_country_id');
				$this->session->data['payment_address']['zone_id'] = $this->config->get('config_zone_id');
				$this->session->data['payment_address']['postcode'] = '';

				$country_info = $this->model_localisation_country->getCountry($this->config->get('config_country_id'));

				if ($country_info) {
					$this->session->data['payment_address']['country'] = $country_info['name'];
					$this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
					$this->session->data['payment_address']['iso_code_3'] = $country_info['iso_code_3'];
					$this->session->data['payment_address']['address_format'] = $country_info['address_format'];
				}

				$zone_info = $this->model_localisation_zone->getZone($this->config->get('config_zone_id'));

				if ($zone_info) {
					$this->session->data['payment_address']['zone'] = $zone_info['name'];
					$this->session->data['payment_address']['zone_code'] = $zone_info['code'];
				}
			}
			$this->tax->setPaymentAddress($this->session->data['payment_address']['country_id'], $this->session->data['payment_address']['zone_id']);
		}
		
		if ($this->cart->hasShipping() && empty($this->session->data['shipping_address'])) {
			$this->session->data['shipping_address'] = $this->session->data['payment_address'];
			$this->tax->setShippingAddress($this->session->data['shipping_address']['country_id'], $this->session->data['shipping_address']['zone_id']);
		}
		
		if ($this->customer->isLogged()) {
			$data['address'] = $this->load->controller('extension/maza/checkout/address');
			$data['account'] = $this->load->controller('extension/maza/checkout/account/edit');
		} else {
			$data['account'] = $this->load->controller('extension/maza/checkout/account');
		}

		// Payment Methods
		$data['payment_method'] = $this->load->controller('extension/maza/checkout/payment_method');

		// Shipping Methods
		if ($this->cart->hasShipping()) {
			$data['shipping_method'] = $this->load->controller('extension/maza/checkout/shipping_method');
		}

		// Cart
		$data['cart'] = $this->load->controller('extension/maza/checkout/cart');

		// Total
		$data['total'] = $this->load->controller('extension/maza/checkout/total');

		// Module
		$data['module'] = $this->load->controller('extension/maza/checkout/total/module');

		if (isset($this->session->data['comment'])) {
			$data['comment'] = $this->session->data['comment'];
		} else {
			$data['comment'] = '';
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		if ($this->cart->hasShipping() && empty($this->session->data['shipping_methods'])) {
			$data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		}

		// Captcha
		if (!$this->customer->isLogged() && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && (in_array('register', (array)$this->config->get('config_captcha_page')) || in_array('guest', (array)$this->config->get('config_captcha_page')))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
		} else {
			$data['captcha'] = '';
		}

		// Account register agree
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info) {
				$data['text_account_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title']);
			} else {
				$data['text_account_agree'] = '';
			}
		} else {
			$data['text_account_agree'] = '';
		}

		if (isset($this->session->data['account_agree'])) {
			$data['account_agree'] = $this->session->data['account_agree'];
		} else {
			$data['account_agree'] = '';
		}

		// checkout agree
		if ($this->config->get('config_checkout_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), true), $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		if (isset($this->session->data['agree'])) {
			$data['agree'] = $this->session->data['agree'];
		} else {
			$data['agree'] = '';
		}

		$data['logged'] = $this->customer->isLogged();

		$data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter'), $this->config->get('config_name'));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/maza/checkout', $data));
	}

	public function save(): void {
		$this->load->language('checkout/checkout');
		$this->load->language('extension/maza/checkout');

		$this->load->model('account/customer');
		$this->load->model('account/address');
		$this->load->model('catalog/information');
		$this->load->model('extension/maza/opencart');

		$json = array();

		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}

		// Reload checkout if customer logged from another page
		if ($this->customer->isLogged() && !empty($this->request->post['account'])) {
			$json['redirect'] = $this->url->link('checkout/checkout');
		}

		// Check if guest checkout is available.
		if (!$json && !$this->customer->isLogged() && $this->request->post['account'] == 'guest' && (!$this->config->get('config_checkout_guest') || $this->config->get('config_customer_price') || $this->cart->hasDownload())) {
			$json['error']['warning'] = $this->language->get('error_guest');
		}

		// Required login if login is selected in account
		if (!$json && !$this->customer->isLogged() && $this->request->post['account'] == 'login') {
			$json['error']['warning'] = $this->language->get('error_login');
		}

		$shipping_required = $this->cart->hasShipping();

		if (!$json) {
			// Personal Info for guest or register
			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}

			if (!$this->customer->isLogged()) {
				if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
					$json['error']['email'] = $this->language->get('error_email');
				}

				if ($this->request->post['account'] == 'register') {
					if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
						$json['error']['email'] = $this->language->get('error_exists');
					}
		
					if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
						$json['error']['password'] = $this->language->get('error_password');
					}
		
					if ($this->request->post['confirm'] != $this->request->post['password']) {
						$json['error']['confirm'] = $this->language->get('error_confirm');
					}

					if ($this->config->get('config_account_id')) {
						$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
		
						if ($information_info && !isset($this->request->post['account_agree'])) {
							$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
						}
					}
				}
			}

			// payment address
			if (!$this->customer->isLogged() || empty($this->request->post['payment_address']) || $this->request->post['payment_address'] == 'new') {
				if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
					$json['error']['firstname'] = $this->language->get('error_firstname');
				}
	
				if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
					$json['error']['lastname'] = $this->language->get('error_lastname');
				}
				
				if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
					$json['error']['address_1'] = $this->language->get('error_address_1');
				}
	
				if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
					$json['error']['city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
					$json['error']['postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['country_id'] == '') {
					$json['error']['country'] = $this->language->get('error_country');
				}

				if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
					$json['error']['zone'] = $this->language->get('error_zone');
				}

				// Customer Group
				if (!$this->customer->isLogged() && isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$customer_group_id = $this->request->post['customer_group_id'];
				} else {
					$customer_group_id = $this->config->get('config_customer_group_id');
				}

				// Custom field validation
				$this->load->model('account/custom_field');

				$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

				foreach ($custom_fields as $custom_field) {
					if ((!$this->customer->isLogged() && $custom_field['location'] == 'account') || $custom_field['location'] == 'address') {
						if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
							$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						}
					}
				}
			} else {
				if (empty($this->request->post['address_id'])) {
					$json['error']['warning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$json['error']['warning'] = $this->language->get('error_address');
				}
			}


			// Shipping address
			if ($shipping_required && empty($this->request->post['shipping_address_same']) && (!$this->customer->isLogged() || empty($this->request->post['shipping_address']) || $this->request->post['shipping_address'] == 'new')) {
				if ((utf8_strlen(trim($this->request->post['shipping']['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['shipping']['firstname'])) > 32)) {
					$json['error']['shipping_firstname'] = $this->language->get('error_firstname');
				}
	
				if ((utf8_strlen(trim($this->request->post['shipping']['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['shipping']['lastname'])) > 32)) {
					$json['error']['shipping_lastname'] = $this->language->get('error_lastname');
				}
	
				if ((utf8_strlen(trim($this->request->post['shipping']['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['shipping']['address_1'])) > 128)) {
					$json['error']['shipping_address_1'] = $this->language->get('error_address_1');
				}
	
				if ((utf8_strlen(trim($this->request->post['shipping']['city'])) < 2) || (utf8_strlen(trim($this->request->post['shipping']['city'])) > 128)) {
					$json['error']['shipping_city'] = $this->language->get('error_city');
				}
	
				$country_info = $this->model_localisation_country->getCountry($this->request->post['shipping']['country_id']);
	
				if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['shipping']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['shipping']['postcode'])) > 10)) {
					$json['error']['shipping_postcode'] = $this->language->get('error_postcode');
				}
	
				if ($this->request->post['shipping']['country_id'] == '') {
					$json['error']['shipping_country'] = $this->language->get('error_country');
				}
	
				if (!isset($this->request->post['shipping']['zone_id']) || $this->request->post['shipping']['zone_id'] == '' || !is_numeric($this->request->post['shipping']['zone_id'])) {
					$json['error']['shipping_zone'] = $this->language->get('error_zone');
				}

				// Customer Group
				if (!$this->customer->isLogged() && isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$customer_group_id = $this->request->post['customer_group_id'];
				} else {
					$customer_group_id = $this->config->get('config_customer_group_id');
				}
	
				// Custom field validation
				$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);
	
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address') { 
						if ($custom_field['required'] && empty($this->request->post['shipping']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['shipping_custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['shipping']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
							$json['error']['shipping_custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						}
					}
				}
			}

			// Captcha
			if (!$this->customer->isLogged() && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array($this->request->post['account'], (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error']['captcha'] = $captcha;
				}
			}

			if ($this->config->get('config_checkout_id')) {
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

				if ($information_info && !isset($this->request->post['agree'])) {
					$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}

			// Success for account and address
			// Not logged
			if (!$json && !$this->customer->isLogged()) {
				if ($this->request->post['account'] == 'guest') {
					$this->session->data['account'] = 'guest';

					$this->session->data['guest']['customer_group_id'] = $customer_group_id;
					$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
					$this->session->data['guest']['lastname'] = $this->request->post['lastname'];
					$this->session->data['guest']['email'] = $this->request->post['email'];
					$this->session->data['guest']['telephone'] = $this->request->post['telephone'];

					if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
						$this->session->data['guest']['fax'] = $this->request->post['fax'];
					}

					if (isset($this->request->post['custom_field']['account'])) {
						$this->session->data['guest']['custom_field'] = $this->request->post['custom_field']['account'];
					} else {
						$this->session->data['guest']['custom_field'] = array();
					}

					if ($shipping_required) {
						if (!empty($this->request->post['shipping_address_same'])) {
							$this->session->data['guest']['shipping_address'] = true;
						} else {
							$this->session->data['guest']['shipping_address'] = false;
						}
					}
				}
				
				if ($this->request->post['account'] == 'register') {
					$this->session->data['account'] = 'register';

					$customer_id = $this->model_account_customer->addCustomer($this->request->post);

					$this->session->data['customer_id'] = $customer_id;

					// Default Payment Address
					$this->load->model('account/address');
						
					$address_id = $this->model_account_address->addAddress($customer_id, $this->request->post);
					
					// Set the address as default
					$this->model_account_customer->editAddressId($customer_id, $address_id);
					
					// Clear any previous login attempts for unregistered accounts.
					$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

					$this->load->model('account/customer_group');

					$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
					
					if (!$customer_group_info || $customer_group_info['approval']) {
						$json['error']['warning'] = $this->language->get('error_register_approval');
					}

					if ($shipping_required) {
						if (!empty($this->request->post['shipping_address_same'])) {
							$this->session->data['shipping_address_same'] = true;
						} else {
							$this->session->data['shipping_address_same'] = false;
							$this->model_account_address->addAddress($customer_id, $this->request->post['shipping']);
						}
					}

					unset($this->session->data['guest']);
				}

				if (!$json) {
					// payment address
					$this->session->data['payment_address']['firstname'] = $this->request->post['firstname'];
					$this->session->data['payment_address']['lastname'] = $this->request->post['lastname'];
					$this->session->data['payment_address']['company'] = $this->request->post['company'];
					$this->session->data['payment_address']['address_1'] = $this->request->post['address_1'];
					$this->session->data['payment_address']['address_2'] = $this->request->post['address_2'];
					$this->session->data['payment_address']['postcode'] = $this->request->post['postcode'];
					$this->session->data['payment_address']['city'] = $this->request->post['city'];
					$this->session->data['payment_address']['country_id'] = $this->request->post['country_id'];
					$this->session->data['payment_address']['zone_id'] = $this->request->post['zone_id'];

					$this->load->model('localisation/country');

					$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

					if ($country_info) {
						$this->session->data['payment_address']['country'] = $country_info['name'];
						$this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
						$this->session->data['payment_address']['iso_code_3'] = $country_info['iso_code_3'];
						$this->session->data['payment_address']['address_format'] = $country_info['address_format'];
					} else {
						$this->session->data['payment_address']['country'] = '';
						$this->session->data['payment_address']['iso_code_2'] = '';
						$this->session->data['payment_address']['iso_code_3'] = '';
						$this->session->data['payment_address']['address_format'] = '';
					}

					if (isset($this->request->post['custom_field']['address'])) {
						$this->session->data['payment_address']['custom_field'] = $this->request->post['custom_field']['address'];
					} else {
						$this->session->data['payment_address']['custom_field'] = array();
					}

					$this->load->model('localisation/zone');

					$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

					if ($zone_info) {
						$this->session->data['payment_address']['zone'] = $zone_info['name'];
						$this->session->data['payment_address']['zone_code'] = $zone_info['code'];
					} else {
						$this->session->data['payment_address']['zone'] = '';
						$this->session->data['payment_address']['zone_code'] = '';
					}

					// Shipping address
					if ($shipping_required) {
						if (!empty($this->request->post['shipping_address_same'])) {
							$this->session->data['shipping_address'] = $this->session->data['payment_address'];
						} else {
							$this->session->data['shipping_address']['firstname'] = $this->request->post['shipping']['firstname'];
							$this->session->data['shipping_address']['lastname'] = $this->request->post['shipping']['lastname'];
							$this->session->data['shipping_address']['company'] = $this->request->post['shipping']['company'];
							$this->session->data['shipping_address']['address_1'] = $this->request->post['shipping']['address_1'];
							$this->session->data['shipping_address']['address_2'] = $this->request->post['shipping']['address_2'];
							$this->session->data['shipping_address']['postcode'] = $this->request->post['shipping']['postcode'];
							$this->session->data['shipping_address']['city'] = $this->request->post['shipping']['city'];
							$this->session->data['shipping_address']['country_id'] = $this->request->post['shipping']['country_id'];
							$this->session->data['shipping_address']['zone_id'] = $this->request->post['shipping']['zone_id'];

							$this->load->model('localisation/country');

							$country_info = $this->model_localisation_country->getCountry($this->request->post['shipping']['country_id']);

							if ($country_info) {
								$this->session->data['shipping_address']['country'] = $country_info['name'];
								$this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
								$this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
								$this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
							} else {
								$this->session->data['shipping_address']['country'] = '';
								$this->session->data['shipping_address']['iso_code_2'] = '';
								$this->session->data['shipping_address']['iso_code_3'] = '';
								$this->session->data['shipping_address']['address_format'] = '';
							}

							$this->load->model('localisation/zone');

							$zone_info = $this->model_localisation_zone->getZone($this->request->post['shipping']['zone_id']);

							if ($zone_info) {
								$this->session->data['shipping_address']['zone'] = $zone_info['name'];
								$this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
							} else {
								$this->session->data['shipping_address']['zone'] = '';
								$this->session->data['shipping_address']['zone_code'] = '';
							}

							if (isset($this->request->post['shipping']['custom_field'])) {
								$this->session->data['shipping_address']['custom_field'] = $this->request->post['shipping']['custom_field']['address'];
							} else {
								$this->session->data['shipping_address']['custom_field'] = array();
							}
						}
					}
				}
			}

			// logged
			if (!$json && $this->customer->isLogged()) {
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET telephone = '" . $this->db->escape($this->request->post['telephone']) . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");

				if (isset($this->request->post['payment_address']) && $this->request->post['payment_address'] == 'existing') {
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->request->post['address_id']);
				} else {
					$address_id = $this->model_account_address->addAddress($this->customer->getId(), $this->request->post);

					$this->session->data['payment_address'] = $this->model_account_address->getAddress($address_id);

					// If no default address ID set we use the last address
					if (!$this->customer->getAddressId()) {
						$this->model_account_customer->editAddressId($this->customer->getId(), $address_id);
					}
				}

				if ($shipping_required) {
					if (!empty($this->request->post['shipping_address_same'])) {
						$this->session->data['shipping_address'] = $this->session->data['payment_address'];
						$this->session->data['shipping_address_same'] = true;
					} else if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'existing') {
						$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->request->post['shipping']['address_id']);
						$this->session->data['shipping_address_same'] = false;
					} else {
						$address_id = $this->model_account_address->addAddress($this->customer->getId(), $this->request->post['shipping']);

						$this->session->data['shipping_address'] = $this->model_account_address->getAddress($address_id);

						// If no default address ID set we use the last address
						if (!$this->customer->getAddressId()) {
							$this->model_account_customer->editAddressId($this->customer->getId(), $address_id);
						}

						$this->session->data['shipping_address_same'] = false;
					}
				}
			}

			if (!$json) {
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['order_id']);

				$this->session->data['comment'] = strip_tags($this->request->post['comment']);
			}
		}

		// Payment Method
		if (!$json) {
			// Totals
			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			$sort_order = array();

			$results = $this->model_extension_maza_opencart->getExtensions('total');

			foreach ($results as $key => $value) {
				if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				} else {
					$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				}
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status') || $this->config->get($result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);
					
					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}
			
			$method_data = array();

			$results = $this->model_extension_maza_opencart->getExtensions('payment');

			$recurring = $this->cart->hasRecurringProducts();

			foreach ($results as $result) {
				if ($this->config->get('payment_' . $result['code'] . '_status') || $this->config->get($result['code'] . '_status')) {
					$this->load->model('extension/payment/' . $result['code']);

					$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

					if ($method) {
						if ($recurring) {
							if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
								$method_data[$result['code']] = $method;
							}
						} else {
							$method_data[$result['code']] = $method;
						}
					}
				}
			}

			$this->session->data['payment_methods'] = $method_data;
			
			if (empty($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
				$json['error']['warning'] = $this->language->get('error_payment');
			} else {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
			}
		}

		// Shipping method
		if (!$json && $shipping_required) {
			$method_data = array();

			$results = $this->model_extension_maza_opencart->getExtensions('shipping');

			foreach ($results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status') || $this->config->get($result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);

					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

					if ($quote) {
						$method_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}

			$this->session->data['shipping_methods'] = $method_data;

			if (!isset($this->request->post['shipping_method'])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			} else {
				$shipping = explode('.', $this->request->post['shipping_method']);
	
				if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
					$json['error']['warning'] = $this->language->get('error_shipping');
				}
			}

			if (!$json) {
				$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
			}
		}

		// Add order
		if (!$json) {
			$order_data = array();

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			$sort_order = array();

			$results = $this->model_extension_maza_opencart->getExtensions('total');

			foreach ($results as $key => $value) {
				if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				} else {
					$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				}
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status') || $this->config->get($result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);

					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}

			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);

			$order_data['totals'] = $totals;

			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');

			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				if ($this->request->server['HTTPS']) {
					$order_data['store_url'] = HTTPS_SERVER;
				} else {
					$order_data['store_url'] = HTTP_SERVER;
				}
			}

			if ($this->customer->isLogged() || !empty($this->session->data['customer_id'])) {

				if ($this->customer->isLogged()) {
					$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

					$order_data['customer_id'] = $this->customer->getId();
				} else {
					$customer_info = $this->model_account_customer->getCustomer($this->session->data['customer_id']);

					$order_data['customer_id'] = $this->session->data['customer_id'];
				}
				
				$order_data['customer_group_id'] = $customer_info['customer_group_id'];
				$order_data['firstname'] = $customer_info['firstname'];
				$order_data['lastname'] = $customer_info['lastname'];
				$order_data['email'] = $customer_info['email'];
				$order_data['telephone'] = $customer_info['telephone'];
				$order_data['custom_field'] = json_decode($customer_info['custom_field'], true);

				if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
					$order_data['fax'] = $customer_info['fax'];
				}
			} elseif (isset($this->session->data['guest'])) {
				$order_data['customer_id'] = 0;
				$order_data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
				$order_data['firstname'] = $this->session->data['guest']['firstname'];
				$order_data['lastname'] = $this->session->data['guest']['lastname'];
				$order_data['email'] = $this->session->data['guest']['email'];
				$order_data['telephone'] = $this->session->data['guest']['telephone'];
				$order_data['custom_field'] = $this->session->data['guest']['custom_field'];

				if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
					$order_data['fax'] = $this->session->data['guest']['fax'];
				}
			}

			$order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
			$order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
			$order_data['payment_company'] = $this->session->data['payment_address']['company'];
			$order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
			$order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
			$order_data['payment_city'] = $this->session->data['payment_address']['city'];
			$order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
			$order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
			$order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
			$order_data['payment_country'] = $this->session->data['payment_address']['country'];
			$order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
			$order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
			$order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

			if (isset($this->session->data['payment_method']['title'])) {
				$order_data['payment_method'] = $this->session->data['payment_method']['title'];
			} else {
				$order_data['payment_method'] = '';
			}

			if (isset($this->session->data['payment_method']['code'])) {
				$order_data['payment_code'] = $this->session->data['payment_method']['code'];
			} else {
				$order_data['payment_code'] = '';
			}

			if ($shipping_required) {
				$order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
				$order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
				$order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
				$order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
				$order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
				$order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
				$order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
				$order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
				$order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
				$order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
				$order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
				$order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
				$order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

				if (isset($this->session->data['shipping_method']['title'])) {
					$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
				} else {
					$order_data['shipping_method'] = '';
				}

				if (isset($this->session->data['shipping_method']['code'])) {
					$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
				} else {
					$order_data['shipping_code'] = '';
				}
			} else {
				$order_data['shipping_firstname'] = '';
				$order_data['shipping_lastname'] = '';
				$order_data['shipping_company'] = '';
				$order_data['shipping_address_1'] = '';
				$order_data['shipping_address_2'] = '';
				$order_data['shipping_city'] = '';
				$order_data['shipping_postcode'] = '';
				$order_data['shipping_zone'] = '';
				$order_data['shipping_zone_id'] = '';
				$order_data['shipping_country'] = '';
				$order_data['shipping_country_id'] = '';
				$order_data['shipping_address_format'] = '';
				$order_data['shipping_custom_field'] = array();
				$order_data['shipping_method'] = '';
				$order_data['shipping_code'] = '';
			}

			$order_data['products'] = array();

			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['value'],
						'type'                    => $option['type']
					);
				}

				$order_data['products'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
					'reward'     => $product['reward']
				);
			}

			// Gift Voucher
			$order_data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$order_data['vouchers'][] = array(
						'description'      => $voucher['description'],
						'code'             => token(10),
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'amount'           => $voucher['amount']
					);
				}
			}

			$order_data['comment'] = $this->session->data['comment'];
			$order_data['total'] = $total_data['total'];

			if (isset($this->request->cookie['tracking'])) {
				$order_data['tracking'] = $this->request->cookie['tracking'];

				$subtotal = $this->cart->getSubTotal();

				// Affiliate
				$affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

				if ($affiliate_info) {
					$order_data['affiliate_id'] = $affiliate_info['customer_id'];
					$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
				}

				// Marketing
				$this->load->model('checkout/marketing');

				$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

				if ($marketing_info) {
					$order_data['marketing_id'] = $marketing_info['marketing_id'];
				} else {
					$order_data['marketing_id'] = 0;
				}
			} else {
				$order_data['affiliate_id'] = 0;
				$order_data['commission'] = 0;
				$order_data['marketing_id'] = 0;
				$order_data['tracking'] = '';
			}

			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
			$order_data['currency_code'] = $this->session->data['currency'];
			$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$order_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$order_data['accept_language'] = '';
			}

			$this->load->model('checkout/order');

			$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

			$json['redirect'] = $this->url->link('extension/maza/checkout/confirm');
		}

		if (!$this->customer->isLogged() && !empty($this->session->data['customer_id']) && $this->session->data['account'] == 'register') {
			$this->customer->login($this->request->post['email'], $this->request->post['password']);

			if (empty($this->session->data['order_id'])) {
				$json['redirect'] = $this->url->link('checkout/checkout');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
