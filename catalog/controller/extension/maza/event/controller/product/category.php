<?php
class ControllerExtensionMazaEventControllerProductCategory extends Controller {
	public function before(): void {
		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$breadcrumbs = array();

		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$breadcrumbs[] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path . $url)
					);
				}
			}
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info) {
			$breadcrumbs[] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			);

			// Schema
			if ($this->config->get('maza_schema')) {
				$this->mz_schema->add(maza\Schema::breadcrumb($breadcrumbs));
			}

			$categories = $this->model_catalog_category->getCategories($category_id);

			if ($categories) {
				$schema = array(
					'@context' => 'https://schema.org/',
					'@type' => 'ItemList',
					'name' => $category_info['name'],
					'description' => $category_info['meta_description']?:$category_info['description'],
					'itemListElement' => [],
				);

				foreach ($categories as $position => $category) {
					$schema['itemListElement'][] = [
						"@type" => "ListItem",
						"position" => $position + 1,
						"name" => $category['name'],
						"item" => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $category['category_id'])
					];
				}

				$this->mz_schema->add($schema);
			}

			// OGP
			if ($this->config->get('maza_ogp')) {
				$this->mz_document->addOGP('og:type', 'website');
				$this->mz_document->addOGP('og:title', $category_info['meta_title']?:$category_info['name']);
				$this->mz_document->addOGP('og:description', (string)$category_info['meta_description']);
				$this->mz_document->addOGP('og:image', maza\getImageURL($category_info['image']));

				if ($page == 1) {
					$this->mz_document->addOGP('og:url', $this->url->link('product/category', 'path=' . $this->request->get['path']));
				} else {
					$this->mz_document->addOGP('og:url', $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&page='. $page));
				}
			}

			// Load Catalog data 
			$this->load->model('extension/maza/catalog/data');

			$catalog_data = $this->model_extension_maza_catalog_data->getCategoryDatas($category_id);

			$this->load->controller('extension/maza/hooks/data', $catalog_data);
		}

		// Twig template of this page from layout builder, must call before header
		$mz_content = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]);
		$this->mz_cache->setVar('mz_content', $mz_content);

		$mz_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
		$this->mz_cache->setVar('mz_component', $mz_component);
	}
}
