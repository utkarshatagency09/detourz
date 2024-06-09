<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaProductQuickView extends Controller {
	public function index(): void {
		$product_info = array();
		
		$this->load->language('product/product');
		$this->load->language('extension/maza/product/product');

		$this->load->model('catalog/product');

		// Get product detail
		if(isset($this->request->post['product_id'])){
			$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);
		}
		
		// Asset
		// CSS
		$data['styles'] = array();
		$data['styles'][] = 'catalog/view/javascript/jquery/magnific/magnific-popup.css';
		$data['styles'][] = $this->mz_document->getRouteCSSFile();
		
		// JS
		$data['scripts'] = array();
		$data['scripts'][] = 'catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js';

		if($this->config->get('maza_cdn')){
			$data['scripts'][] = 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js';
			$data['scripts'][] = 'https://cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js';
			$data['scripts'][] = 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js';
			$data['styles'][] = 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.css';
		} else {
			$data['scripts'][] = 'catalog/view/javascript/maza/javascript/daterangepicker/moment.min.js';
			$data['scripts'][] = 'catalog/view/javascript/maza/javascript/daterangepicker/moment-with-locales.min.js';
			$data['scripts'][] = 'catalog/view/javascript/maza/javascript/daterangepicker/daterangepicker.js';
			$data['styles'][] = 'catalog/view/javascript/maza/javascript/daterangepicker/daterangepicker.css';
		}

		if($this->config->get('maza_cdn')){
			$data['styles'][] = 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.css';
			$data['scripts'][] = 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.js';
		} else {
			$data['styles'][] = 'catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.css';
			$data['scripts'][] = 'catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.js';
		}

		if($product_info){
			$data['heading_title'] = $product_info['name'];
			$data['product_id'] = $product_info['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			if ($product_info['image']) {
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
			} else {
				$data['popup'] = '';
			}

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($product_info['product_id']);

			foreach ($results as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
				);
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$tax_price = (float)$product_info['special'];
			} else {
				$data['special'] = false;
				$tax_price = (float)$product_info['price'];
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format($tax_price, $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($product_info['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($product_info['product_id']) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], $this->mz_skin_config->get('catalog_option_image_width'), $this->mz_skin_config->get('catalog_option_image_height')),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}
			
			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);

			$data['review_status'] = $this->config->get('config_review_status');

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];
			
			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}
			
			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$product_info['product_id']);
			
			$data['recurrings'] = $this->model_catalog_product->getProfiles($product_info['product_id']);
			
			$this->load->model('catalog/manufacturer');
			
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
			
			if($manufacturer_info && is_file(DIR_IMAGE . $manufacturer_info['image'])){
				$data['mz_manufacturer_image'] = $this->model_tool_image->resize($manufacturer_info['image'], $this->mz_skin_config->get('catalog_product_brand_image_width'), $this->mz_skin_config->get('catalog_product_brand_image_height'));
			}
			
			// Add extra meta data to product list
			$this->mz_hook->fetch('catalog_product_detail', [$product_info, &$data]);

			$this->model_catalog_product->updateViewed($product_info['product_id']);

			// Document before
			$this->load->controller('extension/maza/event/document/before');
			
			// Content
			$data['mz_content'] = $this->mz_load->view($this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]), $data, 'extension/maza/product/quick_view');

			// Page component
			$page_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
			$data['page_component'] = $this->mz_load->view($page_component, $data, 'extension/maza/product/quick_view');

			// Document after
			$this->load->controller('extension/maza/event/document/after');
			
			$this->response->setOutput($this->load->view('extension/maza/product/quick_view', $data));
		}
	}
}
