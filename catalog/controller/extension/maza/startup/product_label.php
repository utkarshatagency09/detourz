<?php
class ControllerExtensionMazaStartupProductLabel extends Controller {
	private $labels = array();

    public function index(): void {
		$this->registry->set('mz_product_label', $this);

		$this->load->model('extension/maza/catalog/product_label');

		$this->labels = $this->model_extension_maza_catalog_product_label->getLabels();
    }

	public function getLabels(array $product_info, bool $is_product_page = false): array {
		$label_data = array();
		
		foreach ($this->labels as $label) {
			if ($is_product_page && !$label['product_page_status']) {
				continue;
			}

			$text = $this->{$label['type']}($product_info, $label);

			if ($text) {
				$class = $style = array();

				if ($is_product_page) {
					$position = $label['style']['product_page_position'];

					if ($label['style']['product_page_visibility'] == 'hover') {
						$class[] = 'hover';
					}
				} else {
					$position = $label['style']['position'];

					if ($label['style']['visibility'] == 'hover') {
						$class[] = 'hover';
					}
				}

				$class[] = $label['style']['shape'];

				if ($label['style']['custom_class']) {
					$class[] = $label['style']['custom_class'];
				}

				if ($label['style']['color_text']) {
					$style[] = 'color:' . $label['style']['color_text'];
				}
				if ($label['style']['color_bg']) {
					$style[] = 'background-color:' . $label['style']['color_bg'];
				}

				$label_data[$position][] = array(
					'product_label_id' => $label['product_label_id'],
					'text' => $text,
					'class' => implode(' ', $class),
					'style' => implode(';', $style),
				);
			}
		}

		return $label_data;
	}

	/**
	 * Special label
	 */
	private function special(array $product_info, array $label): string {
		if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {
			if ($label['setting']['special'] == 'percentage') {
				$price_amount = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
				$special_amount = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
				
				return '-' . round(($price_amount - (float)$special_amount) / $price_amount *  100) . '%';
			} else {
				return $label['name'];
			}
		}

		return '';
	}

	/**
	 * new label
	 */
	private function new(array $product_info, array $label): string {
		if(strtotime($product_info['date_available']) > strtotime('-' . (int)$label['setting']['new'] . ' day')){
			return $label['name']??'';
		}

		return '';
	}

	/**
	 * stock status label
	 */
	private function stock_status(array $product_info, array $label): string {
		if($label['setting']['stock_status'] && $product_info['quantity'] > 0){
			return '';
		}

		if ($product_info['quantity'] > 0) {
			return $this->language->get('text_instock');
		} elseif($product_info['stock_status']){
			return $product_info['stock_status'];
		} else {
			return '';
		}
	}

	/**
	 * rating label
	 */
	private function rating(array $product_info, array $label): string {
		if ($this->config->get('config_review_status') && $product_info['rating'] > 0) {
			return '<i class="fas fa-star mr-1"></i>' . (int)$product_info['rating'];
		} else {
			return '';
		}
	}

	/**
	 * Selected product label
	 */
	private function selected(array $product_info, array $label): string {
		if (!empty($label['setting']['selected']) && in_array($product_info['product_id'], $label['setting']['selected'])) {
			return $label['name'];
		} else {
			return '';
		}
	}

	/**
	 * filter product label
	 */
	private function filter(array $product_info, array $label): string {
		$filter = $label['setting']['filter'];

		if (!empty($filter['category']) && !$this->model_extension_maza_catalog_product_label->isProductInCategory($product_info['product_id'], $filter['category'])) {
			return '';
		}

		if (!empty($filter['filter']) && !$this->model_extension_maza_catalog_product_label->isProductInFilter($product_info['product_id'], $filter['filter'])) {
			return '';
		}

		if (!empty($filter['manufacturer']) && !in_array($product_info['manufacturer_id'], $filter['manufacturer'])) {
			return '';
		}

		if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {
			$price = (float)$product_info['special'];
		} else {
			$price = (float)$product_info['price'];
		}

		if ($filter['price_min'] != '' && $price < $filter['price_min']) {
			return '';
		}

		if ($filter['price_max'] != '' && $price > $filter['price_max']) {
			return '';
		}

		if ($filter['quantity_min'] != '' && $product_info['quantity'] < $filter['quantity_min']) {
			return '';
		}

		if ($filter['quantity_max'] != '' && $product_info['quantity'] > $filter['quantity_max']) {
			return '';
		}

		if ($filter['rating_min'] != '' && $product_info['rating'] < $filter['rating_min']) {
			return '';
		}

		if ($filter['rating_max'] != '' && $product_info['rating'] > $filter['rating_max']) {
			return '';
		}

		if ($filter['special'] && !$product_info['special']) {
			return '';
		}

		return $label['name'];
	}

	/**
	 * controller label
	 */
	private function controller(array $product_info, array $label): string {
		if (!empty($label['setting']['controller'])) {
			return $this->load->controller($label['setting']['controller'], $product_info);
		} else {
			return '';
		}
	}
}
