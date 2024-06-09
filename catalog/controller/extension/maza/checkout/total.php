<?php
class ControllerExtensionMazaCheckoutTotal extends Controller {
	public function index(): string {
		// Totals
		$this->load->model('extension/maza/opencart');

		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;
		
		// Because __call can not keep var references so we put them into an array. 			
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);
		
		// Display prices
		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
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
		}

		$data['totals'] = array();

		foreach ($totals as $total) {
			$data['totals'][] = array(
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
			);
		}

		return $this->load->view('extension/maza/checkout/total', $data);
	}

	public function module(): string {
		$data['modules'] = array();
		
		$files = glob(DIR_APPLICATION . '/controller/extension/total/*.php');

		if ($files) {
			foreach ($files as $file) {
				$result = $this->load->controller('extension/total/' . basename($file, '.php'));
				
				if ($result && basename($file, '.php') !== 'shipping') {
					$data['modules'][] = $result;
				}
			}
		}

		return $this->load->view('extension/maza/checkout/total_module', $data);
	}

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
			$json['payment_method'] = $this->load->controller('extension/maza/checkout/payment_method');

			// Total
			$json['total'] = $this->load->controller('extension/maza/checkout/total');

			unset($this->session->data['order_id']);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
