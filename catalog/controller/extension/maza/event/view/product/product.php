<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewProductProduct extends Controller {
    public function before(string $route, array &$data): void {
		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			// Manufacturer
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);

			if($manufacturer_info && is_file(DIR_IMAGE . $manufacturer_info['image'])){
				$data['mz_manufacturer_image'] = $this->model_tool_image->resize($manufacturer_info['image'], $this->mz_skin_config->get('catalog_product_brand_image_width'), $this->mz_skin_config->get('catalog_product_brand_image_height'));
			}

			// Options
			if (isset($data['options'])) {
				foreach ($data['options'] as &$option) {
					foreach ($option['product_option_value'] as &$option_value) {
						$query = $this->db->query("SELECT image FROM `" . DB_PREFIX . "option_value` WHERE option_value_id = '" . (int)$option_value['option_value_id'] . "'");
		
						$option_value['image'] = $this->model_tool_image->resize($query->row['image'], $this->mz_skin_config->get('catalog_option_image_width')?:50, $this->mz_skin_config->get('catalog_option_image_height')?:50);
					}
				}
			}

			$data['mz_total_url'] 	= $this->url->link('extension/maza/product/total', 'product_id=' . $this->request->get['product_id']);
			$data['logged'] 		= $this->customer->isLogged();
			$data['mz_has_address'] = (bool)$this->customer->getAddressId();
			$data['mz_login'] 		= $this->url->link('account/login');
			$data['mz_address'] 	= $this->url->link('account/address');
			
			// Add extra meta data to product list
			$this->mz_hook->fetch('catalog_product_detail', [$product_info, &$data]);
			
			// Layout builder
			$data['mz_content'] = $this->mz_load->view($this->mz_cache->getVar('mz_content'), $data);
			$data['mz_component'] = $this->mz_load->view($this->mz_cache->getVar('mz_component'), $data);
		}
    }
}
