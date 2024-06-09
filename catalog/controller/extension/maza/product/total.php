<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2023, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaProductTotal extends Controller {
	public function index(): void {
		$this->response->setOutput($this->getTotal());
	}
        
	/**
	 * Calculate product total with shipping fee and taxes
	 */
	public function getTotal(): string {
		$this->load->model('catalog/product');

		if (isset($this->request->get['product_id'])) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		} else {
			$product_info = [];
		}

		if ($product_info && $this->customer->isLogged() && $this->customer->getAddressId()) {
			// Override cart class to get only current product in cart
			$this->cart->db = new Class($this->cart->db) extends maza\Wrapper {
				public $product_info = [];
				public function query($sql) {
					if (strpos($sql, "SELECT * FROM " . DB_PREFIX . "cart") === 0) {
						$product_data = [
							'cart_id' 		=> 0,
							'product_id' 	=> $this->product_info['product_id'],
							'recurring_id' 	=> 0,
							'option' 		=> '[]',
							'quantity' 		=> $this->product_info['minimum']?:1,
						];

						$result = new \stdClass();
						$result->num_rows = 1;
						$result->row = $product_data;
						$result->rows = [$product_data];
						return $result;
					}

					return $this->object->query($sql);
				}
			};
			$this->cart->db->product_info = $product_info;

			$backup_session_data = $this->session->data;

			// add shipping address if missing
			if(!isset($this->session->data['shipping_address'])){
				$this->load->model('account/address');

				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				$this->tax->setShippingAddress($this->session->data['shipping_address']['country_id'], $this->session->data['shipping_address']['zone_id']);
			}

			// Load shipping method in sesson
			$this->load->controller('extension/maza/checkout/shipping_method');

			// Find lowest cost
			foreach ($this->session->data['shipping_methods'] as $shipping_method) {
				foreach ($shipping_method['quote'] as $quote) {
					if (!isset($this->session->data['shipping_method']) || $this->session->data['shipping_method']['cost'] > $quote['cost']) {
						$this->session->data['shipping_method'] = $quote;
					}
				}
			}

			// Totals
			$this->load->model('setting/extension');

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

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get('total_' . $result['code'] . '_status')) {
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

			// Restore data
			$this->cart->db 		= $this->cart->db->restore();
			$this->session->data 	= $backup_session_data;
		}

		return $this->load->view('extension/maza/product/total', $data??[]);
	}
}
