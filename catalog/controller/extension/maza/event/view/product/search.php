<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewProductSearch extends Controller {
    public function before(string $route, array &$data): void {
		// add sort
		$url = '';

		$url .= $this->load->controller('extension/module/mz_filter/url', $url);

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['tag'])) {
			$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['description'])) {
			$url .= '&description=' . $this->request->get['description'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		if (isset($this->request->get['sub_category'])) {
			$url .= '&sub_category=' . $this->request->get['sub_category'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($data['sorts'])) {
			$sorts = array();

			$sorts[] = array(
				'text'  => $this->language->get('text_bestseller'),
				'value' => 'order_quantity-DESC',
				'href'  => $this->url->link('product/search', 'sort=order_quantity&order=DESC' . $url)
			);

			$sorts[] = array(
				'text'  => $this->language->get('text_popular'),
				'value' => 'p.viewed-DESC',
				'href'  => $this->url->link('product/search', 'sort=p.viewed&order=DESC' . $url)
			);

			$sorts[] = array(
				'text'  => $this->language->get('text_newest'),
				'value' => 'p.date_added-DESC',
				'href'  => $this->url->link('product/search', 'sort=p.date_added&order=DESC' . $url)
			);

			array_splice($data['sorts'], 1, 0, $sorts);
		}

		// Layout builder
		$data['mz_content'] = $this->mz_load->view($this->mz_cache->getVar('mz_content'), $data);
		$data['mz_component'] = $this->mz_load->view($this->mz_cache->getVar('mz_component'), $data);
    }
}
