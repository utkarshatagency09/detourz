<?php
class ControllerExtensionMazaCheckoutPaymentMethod extends Controller {
	public function index(): string {
		$this->load->language('checkout/checkout');

		if (isset($this->session->data['payment_address'])) {
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
			
			$this->load->model('extension/maza/opencart');

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

			// Payment Methods
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

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$this->session->data['payment_methods'] = $method_data;
		}

		if (isset($this->session->data['payment_method']) && (empty($this->session->data['payment_methods']) || !array_key_exists($this->session->data['payment_method']['code'], $this->session->data['payment_methods']))) {
			unset($this->session->data['payment_method']);
		}

		if (isset($this->request->post['payment_method']) && (empty($this->session->data['payment_methods']) || !array_key_exists($this->request->post['payment_method'], $this->session->data['payment_methods']))) {
			unset($this->request->post['payment_method']);
		}

		if (empty($this->session->data['payment_methods'])) {
			$data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact'));
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['payment_methods'])) {
			$data['payment_methods'] = $this->session->data['payment_methods'];
		} else {
			$data['payment_methods'] = array();
		}

		if (isset($this->request->post['payment_method'])) {
			$data['code'] = $this->request->post['payment_method'];
		} elseif (isset($this->session->data['payment_method']['code'])) {
			$data['code'] = $this->session->data['payment_method']['code'];
		} else if (!empty($this->session->data['payment_methods'])) {
			$code = array_keys($this->session->data['payment_methods'])[0];

			$this->session->data['payment_method'] = $this->session->data['payment_methods'][$code];
			$data['code'] = $code;
		} else {
			$data['code'] = '';
		}

		return $this->load->view('extension/maza/checkout/payment_method', $data);
	}

	/**
	 * On payment change event
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
			if (isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
			}
	
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
