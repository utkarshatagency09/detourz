<?php
class ControllerExtensionMazaEventControllerProductProduct extends Controller {
	public function before(): void {
		$this->load->language('extension/maza/product/product');

		if($this->config->get('maza_cdn')){
			$this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
			$this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.js', 'footer');
		} else {
			$this->document->addStyle('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
			$this->document->addScript('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.js', 'footer');
		}

		$breadcrumbs = array();

		$breadcrumbs[] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->load->model('catalog/category');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$breadcrumbs[] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path)
					);
				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$breadcrumbs[] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
				);
			}
		} else {
			$category_id = 0;
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$breadcrumbs[] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$breadcrumbs[] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
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

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$breadcrumbs[] = array(
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			// price
			if(!is_null($product_info['special']) && (float)$product_info['special'] >= 0 && (float)$product_info['price'] > 0){
				$mz_price_num = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
			} else {
				$mz_price_num = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
			}

			// Load Catalog data 
			$this->load->model('extension/maza/catalog/data');

			$product_info['category_id'] = $category_id;

			$catalog_data = $this->model_extension_maza_catalog_data->getProductDatas($product_info);

			$this->load->controller('extension/maza/hooks/data', $catalog_data);

			$url = '';
		
			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
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

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$breadcrumbs[] = array(
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			);

			// Schema
			if ($this->config->get('maza_schema')) {
				$this->mz_schema->add(maza\Schema::breadcrumb($breadcrumbs));
				
				$schema = array(
					'@context' => 'https://schema.org/',
					'@type' => 'Product',
					'name' => $product_info['name'],
					'image' => maza\getImageURL($product_info['image']),
					'description' => $product_info['meta_description']?:$product_info['description'],
					'sku' =>  $product_info['sku'],
					'upc' =>  $product_info['upc'],
					'jan' =>  $product_info['jan'],
					'isbn' =>  $product_info['isbn'],
					'mpn' =>  $product_info['mpn'],
					'offers' => [
						'@type' => 'Offer',
						'priceCurrency' => $this->config->get('config_currency'),
						'price' => $mz_price_num,
						'priceValidUntil' => date('c', strtotime('+1 year')),
						'availability' => $product_info['quantity']?'https://schema.org/InStock':'https://schema.org/OutOfStock',
						'url' => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					]
				);
		
				if ($product_info['rating'] > 0) {
					$schema['aggregateRating'] = [
						'@type' => 'AggregateRating',
						'ratingValue' => $product_info['rating'],
						'reviewCount' => $product_info['reviews']
					];
				}
		
				if ($product_info['manufacturer']) {
					$schema['brand'] = [
						'@type' => 'Brand',
						'name' => $product_info['manufacturer'],
						'url' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id'])
					];
				}
		
				$this->load->model('catalog/review');
		
				$reviews = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], 0, 1);
		
				if($reviews){
					$schema['review'] = [
						'@type' => 'Review',
						'reviewBody' => $reviews[0]['text'],
						'reviewRating' => [
							'@type' => 'Rating',
							'ratingValue' => (int)$reviews[0]['rating'],
							'bestRating' => 5,
						],
						'dateCreated' => $reviews[0]['date_added'],
						'author' => [
							'@type' => 'Person',
							'name' => $reviews[0]['author']
						]
					];
				}

				$this->mz_schema->add($schema);
			}
			

			// OGP
			if ($this->config->get('maza_ogp')) {
				$this->mz_document->addOGP('og:type', 'product');
				$this->mz_document->addOGP('og:title', $product_info['meta_title']?:$product_info['name']);
				$this->mz_document->addOGP('og:description', (string)$product_info['meta_description']);
				$this->mz_document->addOGP('og:url', $this->url->link('product/product', 'product_id=' . $product_info['product_id']));

				if (is_file(DIR_IMAGE . $product_info['image'])) {
					$this->mz_document->addOGP('og:image', $this->model_tool_image->resize($product_info['image'], 1200, 630), '');
					$this->mz_document->addOGP('og:image:width', 1200);
					$this->mz_document->addOGP('og:image:height', 630);
					$this->mz_document->addOGP('og:image', $this->model_tool_image->resize($product_info['image'], 200, 200), '');
					$this->mz_document->addOGP('og:image:width', 200);
					$this->mz_document->addOGP('og:image:height', 200);
					$this->mz_document->addOGP('og:image', maza\getImageURL($product_info['image']));
				}
			}

			// Twig template of this page from layout builder, must call before header
			$mz_content = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]);
			$this->mz_cache->setVar('mz_content', $mz_content);

			$mz_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
			$this->mz_cache->setVar('mz_component', $mz_component);
		}
	}
}
