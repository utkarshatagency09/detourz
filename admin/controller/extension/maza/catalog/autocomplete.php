<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaCatalogAutocomplete extends Controller {
	public function filter_group() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/catalog/filter');
						
			if(isset($this->request->get['limit'])){
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => $limit
			);

			$filter_groups = $this->model_extension_maza_catalog_filter->getFilterGroups($filter_data);

			foreach ($filter_groups as $filter_group) {
				$json[] = array(
					'filter_group_id' => $filter_group['filter_group_id'],
					'name'      => strip_tags(html_entity_decode($filter_group['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function option() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_category'])) {
			$this->load->language('catalog/option');

			$this->load->model('extension/maza/catalog/option');

			$this->load->model('tool/image');
						
			if(isset($this->request->get['limit'])){
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}
			
			$filter_type = array();
			
			if(isset($this->request->get['filter_category'])){
				if($this->request->get['filter_category'] == 'choose'){
					$filter_type = array('select', 'radio', 'checkbox');
				}
			}

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'filter_type' => $filter_type,
				'start'       => 0,
				'limit'       => $limit
			);

			$options = $this->model_extension_maza_catalog_option->getOptions($filter_data);

			foreach ($options as $option) {
				$json[] = array(
					'option_id'    => $option['option_id'],
					'name'         => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
					'type'         => $option['type']
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function attribute() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/attribute');
						
			if(isset($this->request->get['limit'])){
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => $limit
			);

			$results = $this->model_catalog_attribute->getAttributes($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'attribute_id'    => $result['attribute_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'attribute_group' => $result['attribute_group']
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function attribute_value() {
		$json = array();

		if (isset($this->request->get['filter_name']) && isset($this->request->get['filter_language_id']) && isset($this->request->get['attribute_id'])) {
			if(isset($this->request->get['limit'])){
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$query = $this->db->query("SELECT attribute_id, `text` FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$this->request->get['attribute_id'] . "' AND language_id = '" . (int)$this->request->get['filter_language_id'] . "' AND `text` LIKE '" . $this->db->escape($this->request->get['filter_name']) . "%' GROUP BY `text` LIMIT $limit");

			foreach ($query->rows as $result) {
				$json[] = array(
					'name'            => strip_tags(html_entity_decode($result['text'], ENT_QUOTES, 'UTF-8')),
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
