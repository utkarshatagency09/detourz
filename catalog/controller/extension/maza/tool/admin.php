<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaToolAdmin extends Controller {
	public function index(): string {
		$data = array();

		$this->load->language('extension/maza/tool/admin');

		$data['text_dashboard'] = $this->config->get('config_name');
		
		if ($this->config->get('config_maintenance')) {
			$data['text_dashboard'] = $this->language->get('text_maintenance');
		}

		$data['dashboard'] = $this->link('common/dashboard');

		if ($this->config->get('maza_developer_mode')) {
			$data['developer_mode'] = $this->link('extension/maza/system');
		}

		$links = array();

		// Product
		if ($this->mz_document->isRoute('product/product') && isset($this->request->get['product_id'])) {
			$links[] = array(
				'href' => $this->link('catalog/product/edit', 'product_id=' . $this->request->get['product_id']),
				'icon' => 'fas fa-pencil-alt',
				'name' => $this->language->get('text_edit'),
			);
		}

		// Category
		if ($this->mz_document->isRoute('product/category') && isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			$links[] = array(
				'href' => $this->link('catalog/category/edit', 'category_id=' . $category_id),
				'icon' => 'fas fa-pencil-alt',
				'name' => $this->language->get('text_edit'),
			);
			$links[] = array(
				'href' => $this->link('catalog/category/add'),
				'icon' => 'fas fa-plus',
				'name' => $this->language->get('text_add'),
			);
		}

		// Manufacturer
		if ($this->mz_document->isRoute('product/manufacturer/info') && isset($this->request->get['manufacturer_id'])) {
			$links[] = array(
				'href' => $this->link('catalog/manufacturer/edit', 'manufacturer_id=' . $this->request->get['manufacturer_id']),
				'icon' => 'fas fa-pencil-alt',
				'name' => $this->language->get('text_edit'),
			);
			$links[] = array(
				'href' => $this->link('catalog/manufacturer/add'),
				'icon' => 'fas fa-plus',
				'name' => $this->language->get('text_add'),
			);
		}

		// Information
		if ($this->mz_document->isRoute('information/information') && isset($this->request->get['information_id'])) {
			$links[] = array(
				'href' => $this->link('catalog/information/edit', 'information_id=' . $this->request->get['information_id']),
				'icon' => 'fas fa-pencil-alt',
				'name' => $this->language->get('text_edit'),
			);
			$links[] = array(
				'href' => $this->link('catalog/information/add'),
				'icon' => 'fas fa-plus',
				'name' => $this->language->get('text_add'),
			);
		}

		// Page
		if ($this->mz_document->isRoute('extension/maza/page') && isset($this->request->get['page_id'])) {
			$links[] = array(
				'href' => $this->link('extension/maza/page_builder/layout', 'page_id=' . $this->request->get['page_id'] . '&mz_theme_code=' . $this->mz_theme_config->get('theme_code') . '&mz_skin_id=' . $this->mz_skin_config->get('skin_id')),
				'icon' => 'fas fa-pencil-alt',
				'name' => $this->language->get('text_edit'),
			);
			$links[] = array(
				'href' => $this->link('extension/maza/page_builder/add'),
				'icon' => 'fas fa-plus',
				'name' => $this->language->get('text_add'),
			);
		}

		// Testimonial
		if ($this->mz_document->isRoute('extension/maza/testimonial')) {
			$links[] = array(
				'href' => $this->link('extension/maza/testimonial/add'),
				'icon' => 'fas fa-plus',
				'name' => $this->language->get('text_add'),
			);
			$links[] = array(
				'href' => $this->link('extension/maza/testimonial'),
				'icon' => 'fas fa-pencil-alt',
				'name' => $this->language->get('text_testimonial'),
			);
		}

		// Article
		if ($this->mz_document->isRoute('extension/maza/blog/article') && isset($this->request->get['article_id'])) {
			$links[] = array(
				'href' => $this->link('extension/maza/blog/article/edit', 'article_id=' . $this->request->get['article_id']),
				'icon' => 'fas fa-pencil-alt',
				'name' => $this->language->get('text_edit'),
			);
		}

		// Article Category
		if ($this->mz_document->isRoute('extension/maza/blog/category') && isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			$links[] = array(
				'href' => $this->link('extension/maza/blog/category/edit', 'category_id=' . $category_id),
				'icon' => 'fas fa-pencil-alt',
				'name' => $this->language->get('text_edit'),
			);
			$links[] = array(
				'href' => $this->link('extension/maza/blog/category/add'),
				'icon' => 'fas fa-plus',
				'name' => $this->language->get('text_add'),
			);
		}

		// blog author
		if ($this->mz_document->isRoute('extension/maza/blog/author') && isset($this->request->get['author_id'])) {
			$links[] = array(
				'href' => $this->link('extension/maza/blog/author/edit', 'author_id=' . $this->request->get['author_id']),
				'icon' => 'fas fa-pencil-alt',
				'name' => $this->language->get('text_edit'),
			);
			$links[] = array(
				'href' => $this->link('extension/maza/blog/author/add'),
				'icon' => 'fas fa-plus',
				'name' => $this->language->get('text_add'),
			);
		}
		
		// Common
		$links[] = array(
			'href' => $this->link('catalog/product/add'),
			'icon' => 'fas fa-plus',
			'name' => $this->language->get('text_product_add'),
		);

		$links[] = array(
			'href' => $this->link('extension/maza/blog/article/add'),
			'icon' => 'fas fa-plus',
			'name' => $this->language->get('text_article_add'),
		);

		$links[] = array(
			'href' => $this->link('sale/order'),
			'icon' => 'fab fa-opencart',
			'name' => $this->language->get('text_orders'),
		);

		$links[] = array(
			'href' => $this->link('report/report'),
			'icon' => 'fas fa-table',
			'name' => $this->language->get('text_report'),
		);

		$links[] = array(
			'href' => $this->link('customer/customer'),
			'icon' => 'fas fa-users',
			'name' => $this->language->get('text_customers'),
		);

		$data['links'] = $links;

		return $this->load->view('extension/maza/tool/admin', $data);
	}

	private function link(string $route, string $arg = ''): string {
		$link_format = $this->session->data['mz_admin_url'] . 'index.php?route=%s&user_token=' . $this->session->data['user_token'];

		if ($arg) {
			return sprintf($link_format, $route) . '&' . $arg;
		}
		return sprintf($link_format, $route);
	}
}
