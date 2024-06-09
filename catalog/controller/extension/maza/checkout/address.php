<?php
class ControllerExtensionMazaCheckoutAddress extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');

		// Payment address
		if (isset($this->session->data['payment_address']['address_id'])) {
			$data['address_id'] = $this->session->data['payment_address']['address_id'];
		} else {
			$data['address_id'] = $this->customer->getAddressId();
		}

		if (isset($this->session->data['payment_address']['country_id'])) {
			$data['country_id'] = $this->session->data['payment_address']['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->session->data['payment_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['payment_address']['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		if (isset($this->session->data['payment_address']['custom_field'])) {
			$data['payment_address_custom_field'] = $this->session->data['payment_address']['custom_field'];
		} else {
			$data['payment_address_custom_field'] = array();
		}

		if (isset($this->session->data['shipping_address_same'])) {
			$data['shipping_address_same'] = $this->session->data['shipping_address_same'];
		} else {
			$data['shipping_address_same'] = true;
		}

		$data['shipping_required'] = $this->cart->hasShipping();

		// Shipping address
		if ($data['shipping_required']) {

			if (isset($this->session->data['shipping_address']['address_id'])) {
				$data['shipping_address_id'] = $this->session->data['shipping_address']['address_id'];
			} else {
				$data['shipping_address_id'] = $this->customer->getAddressId();
			}
	
			if (isset($this->session->data['shipping_address']['postcode'])) {
				$data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
			} else {
				$data['shipping_postcode'] = '';
			}
	
			if (isset($this->session->data['shipping_address']['country_id'])) {
				$data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
			} else {
				$data['shipping_country_id'] = $this->config->get('config_country_id');
			}
	
			if (isset($this->session->data['shipping_address']['zone_id'])) {
				$data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
			} else {
				$data['shipping_zone_id'] = '';
			}
	
			if (isset($this->session->data['shipping_address']['custom_field'])) {
				$data['shipping_address_custom_field'] = $this->session->data['shipping_address']['custom_field'];
			} else {
				$data['shipping_address_custom_field'] = array();
			}
		}

		// Common
		$data['custom_fields'] = array();
		
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'address') {
				$data['custom_fields'][] = $custom_field;
			}
		}

		$this->load->model('account/address');

		$data['addresses'] = $this->model_account_address->getAddresses();

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		// Fix address id
		if ($data['address_id'] && $this->customer->isLogged() && !empty($this->session->data['payment_address']) && $this->session->data['payment_address'] != $this->model_account_address->getAddress($data['address_id'])) {
			$data['address_id'] = 0;
		}
		if ($data['shipping_address_id'] && $this->customer->isLogged() && !empty($this->session->data['shipping_address']) && $this->session->data['shipping_address'] != $this->model_account_address->getAddress($data['shipping_address_id'])) {
			$data['shipping_address_id'] = 0;
		}

		if (empty($data['addresses'])) {
			$data['address_id'] = $data['shipping_address_id'] = 0;
		}

		return $this->load->view('extension/maza/checkout/address', $data);
	}

	/**
	 * On address change event
	 */
	public function update(): void {
		$json = array();

		// Validate cart has products and has stock.
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
			}
		}

		if (!$json) {
			$this->load->model('account/address');
			$this->load->model('localisation/country');
			$this->load->model('localisation/zone');
		
			// Payment address
			if ($this->customer->isLogged() && isset($this->request->post['payment_address']) && $this->request->post['payment_address'] == 'existing') {
				if (!empty($this->request->post['address_id']) && in_array($this->request->post['address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->request->post['address_id']);
				}
			} else if(!empty($this->request->post['country_id']) && !empty($this->request->post['zone_id'])) {
				$this->session->data['payment_address']['country_id'] = $this->request->post['country_id'];
				$this->session->data['payment_address']['zone_id'] = $this->request->post['zone_id'];
				$this->session->data['payment_address']['postcode'] = $this->request->post['postcode'];

				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

				if ($country_info) {
					$this->session->data['payment_address']['country'] = $country_info['name'];
					$this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
					$this->session->data['payment_address']['iso_code_3'] = $country_info['iso_code_3'];
					$this->session->data['payment_address']['address_format'] = $country_info['address_format'];
				}

				$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

				if ($zone_info) {
					$this->session->data['payment_address']['zone'] = $zone_info['name'];
					$this->session->data['payment_address']['zone_code'] = $zone_info['code'];
				}
			}

			// Shipping address
			if ($this->cart->hasShipping()) {
				if (!empty($this->request->post['shipping_address_same'])) {
					$this->session->data['shipping_address'] = $this->session->data['payment_address'];
				} else if ($this->customer->isLogged() && isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'existing') {
					if (!empty($this->request->post['shipping']['address_id']) && in_array($this->request->post['shipping']['address_id'], array_keys($this->model_account_address->getAddresses()))) {
						$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->request->post['shipping']['address_id']);
					}
				} else if(!empty($this->request->post['shipping']['country_id']) && !empty($this->request->post['shipping']['zone_id'])) {
					$this->session->data['shipping_address']['country_id'] = $this->request->post['shipping']['country_id'];
					$this->session->data['shipping_address']['zone_id'] = $this->request->post['shipping']['zone_id'];
					$this->session->data['shipping_address']['postcode'] = $this->request->post['shipping']['postcode'];

					$country_info = $this->model_localisation_country->getCountry($this->request->post['shipping']['country_id']);

					if ($country_info) {
						$this->session->data['shipping_address']['country'] = $country_info['name'];
						$this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
						$this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
						$this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
					}

					$zone_info = $this->model_localisation_zone->getZone($this->request->post['shipping']['zone_id']);

					if ($zone_info) {
						$this->session->data['shipping_address']['zone'] = $zone_info['name'];
						$this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
					}
				}
			}

			// Tax
			if(!empty($this->session->data['payment_address']['country_id']) && !empty($this->session->data['payment_address']['zone_id'])){
				$this->tax->setPaymentAddress($this->session->data['payment_address']['country_id'], $this->session->data['payment_address']['zone_id']);
			}

			if(!empty($this->session->data['shipping_address']['country_id']) && !empty($this->session->data['shipping_address']['zone_id'])){
				$this->tax->setShippingAddress($this->session->data['shipping_address']['country_id'], $this->session->data['shipping_address']['zone_id']);
			}

			// Payment Methods
			$json['payment_method'] = $this->load->controller('extension/maza/checkout/payment_method');

			// Shipping Methods
			if ($this->cart->hasShipping()) {
				$json['shipping_method'] = $this->load->controller('extension/maza/checkout/shipping_method');
			}

			// Total
			$json['total'] = $this->load->controller('extension/maza/checkout/total');

			unset($this->session->data['order_id']);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}