<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaProductProduct extends Controller {
        
	/**
	 * Calculate product price with option
	 */
	public function priceWithOptions(): void {
		$json = array();

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}
                
		if (isset($this->request->post['option'])) {
			$option = array_filter($this->request->post['option']);
		} else {
			$option = array();
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info && ($this->customer->isLogged() || !$this->config->get('config_customer_price'))) {
			$option_price = 0;
			$option_points = 0;
			
			foreach ($option as $product_option_id => $value) {
				$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "'");

				if ($option_query->num_rows) {
					if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
						$option_value_query = $this->db->query("SELECT option_value_id, quantity, subtract, price, price_prefix, points, points_prefix FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int)$value . "' AND product_option_id = '" . (int)$product_option_id . "'");

						if ($option_value_query->num_rows) {
							if ($option_value_query->row['price_prefix'] == '+') {
								$option_price += $option_value_query->row['price'];
							} elseif ($option_value_query->row['price_prefix'] == '-') {
								$option_price -= $option_value_query->row['price'];
							}

							if ($option_value_query->row['points_prefix'] == '+') {
								$option_points += $option_value_query->row['points'];
							} elseif ($option_value_query->row['points_prefix'] == '-') {
								$option_points -= $option_value_query->row['points'];
							}
						}
					} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
						foreach ($value as $product_option_value_id) {
							$option_value_query = $this->db->query("SELECT option_value_id, quantity, subtract, price, price_prefix, points, points_prefix FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int)$product_option_value_id . "' AND product_option_id = '" . (int)$product_option_id . "'");

							if ($option_value_query->num_rows) {
								if ($option_value_query->row['price_prefix'] == '+') {
									$option_price += $option_value_query->row['price'];
								} elseif ($option_value_query->row['price_prefix'] == '-') {
									$option_price -= $option_value_query->row['price'];
								}

								if ($option_value_query->row['points_prefix'] == '+') {
									$option_points += $option_value_query->row['points'];
								} elseif ($option_value_query->row['points_prefix'] == '-') {
									$option_points -= $option_value_query->row['points'];
								}
							}
						}
					}
				}
			}
                        
			$price = (float)$product_info['price'] + $option_price;

			if(!is_null($product_info['special']) && (float)$product_info['special'] >= 0){
				$special = (float)$product_info['special'] + $option_price;
				$tax_price = (float)$product_info['special'];
				$price = $special * ($product_info['price'] / $product_info['special']); // For same discount
			} else {
				$special = false;
				$tax_price = (float)$product_info['price'];
			}
			
			$tax = $tax_price + $option_price;
			
			$json['price'] = $this->currency->format($this->tax->calculate($price, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			
			if ($special !== false) {
				$json['special'] = $this->currency->format($this->tax->calculate($special, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$json['special'] = false;
			}
                        
			if ($this->config->get('config_tax')) {
				$json['tax'] = $this->currency->format($tax, $this->session->data['currency']);
			} else {
				$json['tax'] = false;
			}
                        
			if ((int)$product_info['points']) {
				$json['points'] = $product_info['points'] + $option_points;
			} else {
				$json['points'] = false;
			} 
                        
			$json['special_discount'] = false;
			if($special !== false){
				$price_amount = $this->tax->calculate($price, $product_info['tax_class_id'], $this->config->get('config_tax'));
				$special_amount = $this->tax->calculate($special, $product_info['tax_class_id'], $this->config->get('config_tax'));
				
				$json['special_discount'] = '-' . round(($price_amount - $special_amount) / $price_amount * 100) . '%';
			}
                        
			// Discount
			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$json['discounts'] = array();
                        
			foreach ($discounts as $discount) {
				$json['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'] + $option_price, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
	/**
	 * Auto complete product
	 * @output HTML product list
	 */
	public function autocomplete(): void {
		$data = array();

		if (!empty($this->request->get['filter_name'])) {
			$this->load->model('catalog/product');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_category_id'])) {
				$filter_category_id = $this->request->get['filter_category_id'];
			} else {
				$filter_category_id = '';
			}
                        
			if (isset($this->request->get['filter_sub_category'])) {
				$filter_sub_category = $this->request->get['filter_sub_category'];
			} else {
				$filter_sub_category = 0;
			}
                        
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_category_id' => $filter_category_id,
				'filter_sub_category' => $filter_sub_category,
				'start'        => 0,
				'limit'        => $limit
			);

			$data['products'] = array();

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
                                
				$image_width = 80;
				if(is_file(DIR_IMAGE . $result['image'])){
					$image = $this->model_tool_image->resize($result['image'], $image_width, ($this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height') * $image_width) / $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'));
				} else {
					$image = $this->model_tool_image->resize('mz_no_image.png', $image_width, ($this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height') * $image_width) / $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'));
				}
				
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ($price && !is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
				
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}
                                
				$data['products'][] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'image'      => $image,
					'model'      => $result['model'],
					'rating'     => $rating,
					'price'      => $price,
					'special'    => $special,
					'tax'        => $tax,
					'href'       => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
		}

		$this->response->setOutput($this->load->view('product/autocomplete', $data));
	}
}
