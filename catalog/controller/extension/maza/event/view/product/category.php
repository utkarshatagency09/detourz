<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewProductCategory extends Controller {
    public function before(string $route, array &$data): void {
        $parts = explode('_', (string)$this->request->get['path']);

        $category_id = (int)array_pop($parts);

        // Add category image to refine search
        if (!empty($data['categories'])) {
            $url = '';

			$url .= $this->load->controller('extension/module/mz_filter/url', $url);

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
            
            $data['mz_categories'] = array();

			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);

                if (is_file(DIR_IMAGE . $result['image'])) {
                    $image = $this->model_tool_image->resize($result['image'], $this->mz_skin_config->get('catalog_refine_search_image_width')?:100, $this->mz_skin_config->get('catalog_refine_search_image_height')?:100);
                } else {
                    $image = $this->model_tool_image->resize('no_image.png', $this->mz_skin_config->get('catalog_refine_search_image_width')?:100, $this->mz_skin_config->get('catalog_refine_search_image_height')?:100);
                }

				$data['mz_categories'][] = array(
					'category_id' => $result['category_id'],
					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                    'image' => $image,
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
				);
			}
        }

		// add sort
		$url = '';

		$url .= $this->load->controller('extension/module/mz_filter/url', $url);

		if (isset($this->request->get['filter'])) {
			$url .= '&filter=' . $this->request->get['filter'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$sorts = array();

		$sorts[] = array(
			'text'  => $this->language->get('text_bestseller'),
			'value' => 'order_quantity-DESC',
			'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=order_quantity&order=DESC' . $url)
		);

		$sorts[] = array(
			'text'  => $this->language->get('text_popular'),
			'value' => 'p.viewed-DESC',
			'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.viewed&order=DESC' . $url)
		);

		$sorts[] = array(
			'text'  => $this->language->get('text_newest'),
			'value' => 'p.date_added-DESC',
			'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.date_added&order=DESC' . $url)
		);

		array_splice($data['sorts'], 1, 0, $sorts);

		// Layout builder
		$data['mz_content'] = $this->mz_load->view($this->mz_cache->getVar('mz_content'), $data);
		$data['mz_component'] = $this->mz_load->view($this->mz_cache->getVar('mz_component'), $data);
    }
}
