<?php
class ControllerExtensionMazaCheckoutAccount extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');

		if (!$this->config->get('config_checkout_guest') || $this->config->get('config_customer_price')){
			$data['checkout_guest'] = false;
			$this->session->data['account'] = 'register';
		} else {
			$data['checkout_guest'] = true;
		}

		if (isset($this->session->data['account'])) {
			$data['account'] = $this->session->data['account'];
		} else {
			$data['account'] = 'register';
		}

		$data['forgotten'] = $this->url->link('account/forgotten', '', true);

		$data['customer_groups'] = array();

		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');

			$customer_groups = $this->model_account_customer_group->getCustomerGroups();

			foreach ($customer_groups  as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$data['customer_groups'][] = $customer_group;
				}
			}
		}

		$data['checkout_guest'] = ($this->config->get('config_checkout_guest') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());

		if (isset($this->session->data['guest']['customer_group_id'])) {
			$data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
		} else {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		if (isset($this->session->data['guest']['firstname'])) {
			$data['firstname'] = $this->session->data['guest']['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->session->data['guest']['lastname'])) {
			$data['lastname'] = $this->session->data['guest']['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->session->data['guest']['email'])) {
			$data['email'] = $this->session->data['guest']['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->session->data['guest']['telephone'])) {
			$data['telephone'] = $this->session->data['guest']['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
			if (isset($this->session->data['guest']['fax'])) {
				$data['fax'] = $this->session->data['guest']['fax'];
			} else {
				$data['fax'] = '';
			}
		}

		if (isset($this->session->data['payment_address']['company'])) {
			$data['company'] = $this->session->data['payment_address']['company'];
		} else {
			$data['company'] = '';
		}

		if (isset($this->session->data['payment_address']['address_1'])) {
			$data['address_1'] = $this->session->data['payment_address']['address_1'];
		} else {
			$data['address_1'] = '';
		}

		if (isset($this->session->data['payment_address']['address_2'])) {
			$data['address_2'] = $this->session->data['payment_address']['address_2'];
		} else {
			$data['address_2'] = '';
		}

		if (isset($this->session->data['payment_address']['postcode'])) {
			$data['postcode'] = $this->session->data['payment_address']['postcode'];
		} elseif (isset($this->session->data['shipping_address']['postcode'])) {
			$data['postcode'] = $this->session->data['shipping_address']['postcode'];
		} else {
			$data['postcode'] = '';
		}

		if (isset($this->session->data['payment_address']['city'])) {
			$data['city'] = $this->session->data['payment_address']['city'];
		} else {
			$data['city'] = '';
		}

		if (isset($this->session->data['payment_address']['country_id'])) {
			$data['country_id'] = $this->session->data['payment_address']['country_id'];
		} elseif (isset($this->session->data['shipping_address']['country_id'])) {
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->session->data['payment_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['payment_address']['zone_id'];
		} elseif (isset($this->session->data['shipping_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		if (isset($this->session->data['guest']['shipping_address'])) {
			$data['shipping_address_same'] = $this->session->data['guest']['shipping_address'];
		} else {
			$data['shipping_address_same'] = true;
		}
		
		// Shipping
		if ($this->cart->hasShipping()) {
			if (isset($this->session->data['shipping_address']['firstname'])) {
				$data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
			} else {
				$data['shipping_firstname'] = '';
			}
	
			if (isset($this->session->data['shipping_address']['lastname'])) {
				$data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
			} else {
				$data['shipping_lastname'] = '';
			}
	
			if (isset($this->session->data['shipping_address']['company'])) {
				$data['shipping_company'] = $this->session->data['shipping_address']['company'];
			} else {
				$data['shipping_company'] = '';
			}
	
			if (isset($this->session->data['shipping_address']['address_1'])) {
				$data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
			} else {
				$data['shipping_address_1'] = '';
			}
	
			if (isset($this->session->data['shipping_address']['address_2'])) {
				$data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
			} else {
				$data['shipping_address_2'] = '';
			}
	
			if (isset($this->session->data['shipping_address']['postcode'])) {
				$data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
			} else {
				$data['shipping_postcode'] = '';
			}
	
			if (isset($this->session->data['shipping_address']['city'])) {
				$data['shipping_city'] = $this->session->data['shipping_address']['city'];
			} else {
				$data['shipping_city'] = '';
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
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		// Custom Fields
		$this->load->model('account/custom_field');

		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields();

		$data['shipping_required'] = $this->cart->hasShipping();
		
		return $this->load->view('extension/maza/checkout/account', $data);
	}

	public function edit(){
		$this->load->language('checkout/checkout');

		$data['telephone'] = $this->customer->getTelephone();

		return $this->load->view('extension/maza/checkout/account_edit', $data);
	}
}
