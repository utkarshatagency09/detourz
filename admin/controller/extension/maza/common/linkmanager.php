<?php
class ControllerExtensionMazaCommonLinkManager extends Controller {
	public function index(): void {
		$this->load->language('extension/maza/common/linkmanager');

		$data['target'] = $this->request->get['target'];

		$data['list_system'] = array(
			array('id' => 'home', 'name' => $this->language->get('text_home')),
			array('id' => 'wishlist', 'name' => $this->language->get('text_wishlist')),
			array('id' => 'compare', 'name' => $this->language->get('text_compare')),
			array('id' => 'checkout', 'name' => $this->language->get('text_checkout')),
			array('id' => 'account', 'name' => $this->language->get('text_account')),
			array('id' => 'login', 'name' => $this->language->get('text_login')),
			array('id' => 'logout', 'name' => $this->language->get('text_logout')),
			array('id' => 'order', 'name' => $this->language->get('text_order')),
			array('id' => 'register', 'name' => $this->language->get('text_register')),
			array('id' => 'cart', 'name' => $this->language->get('text_cart')),
			array('id' => 'contact', 'name' => $this->language->get('text_contact')),
			array('id' => 'return', 'name' => $this->language->get('text_return')),
			array('id' => 'special', 'name' => $this->language->get('text_special')),
			array('id' => 'search', 'name' => $this->language->get('text_search')),
			array('id' => 'manufacturer', 'name' => $this->language->get('text_manufacturer')),
			array('id' => 'sitemap', 'name' => $this->language->get('text_sitemap')),
			array('id' => 'tracking', 'name' => $this->language->get('text_tracking')),
			array('id' => 'voucher', 'name' => $this->language->get('text_voucher')),
			array('id' => 'affiliate_login', 'name' => $this->language->get('text_affiliate_login')),
			array('id' => 'affiliate_register', 'name' => $this->language->get('text_affiliate_register')),
			array('id' => 'blog/home', 'name' => $this->language->get('text_blog_home')),
			array('id' => 'blog/all', 'name' => $this->language->get('text_blog_all')),
			array('id' => 'blog/search', 'name' => $this->language->get('text_blog_search')),
			array('id' => 'maza/products', 'name' => $this->language->get('text_products')),
			array('id' => 'maza/testimonial', 'name' => $this->language->get('text_testimonials')),
		);
		
		array_multisort(array_column($data['list_system'], 'name'), $data['list_system']);

		$this->load->model('catalog/information');

		$data['informations'] = $this->model_catalog_information->getInformations();

		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('extension/maza/common/linkmanager', $data));
	}

	public function info(string $code): array {
		$this->load->language('extension/maza/common/linkmanager');

		$exploded = explode('.', $code);
		$type = array_shift($exploded);
		$value = implode('.', $exploded);
		$title = '';

		if ($type == 'system') {
			if(strpos($value, 'blog/') === 0){
				$title = $this->language->get('text_' . str_replace('/', '_', $value));
			} elseif($value == 'maza/products'){
				$title = $this->language->get('text_products');
			} elseif($value == 'maza/testimonial'){
				$title = $this->language->get('text_testimonials');
			} else {
				$title = $this->language->get('text_' . $value);
			}
		} elseif ($type == 'information') {
			$this->load->model('extension/maza/catalog/information');

			$information = $this->model_extension_maza_catalog_information->getInformation($value);
			if ($information) {
				$title = $information['title'];
			}
		} elseif ($type == 'category') {
			$this->load->model('catalog/category');
			
			$category_info = $this->model_catalog_category->getCategory($value);
			if ($category_info) {
				$title = ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'];
			}
		} elseif ($type == 'product') {
			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($value);
			if ($product_info) {
				$title = $product_info['name'];
			}
		} elseif ($type == 'manufacturer') {
			$this->load->model('catalog/manufacturer');

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($value);
			if ($manufacturer_info) {
				$title = $manufacturer_info['name'];
			}
		} elseif ($type == 'blog_category') {
			$this->load->model('extension/maza/blog/category');

			$category_info = $this->model_extension_maza_blog_category->getCategory($value);
			if ($category_info) {
				$title = ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'];
			}
		} elseif ($type == 'blog_article') {
			$this->load->model('extension/maza/blog/article');

			$article_info = $this->model_extension_maza_blog_article->getArticle($value);
			if ($article_info) {
				$title = $article_info['name'];
			}
		} elseif ($type == 'page_builder') {
			$this->load->model('extension/maza/page_builder');

			$page_info = $this->model_extension_maza_page_builder->getPage($value);
			if ($page_info) {
				$title = $page_info['name'];
			}
		} elseif (in_array($type, ['route', 'custom', 'popup', 'drawer', 'collapsible'])){
			$title = $value;
		}

		if ($title) {
			return array(
				'type' => $this->language->get('entry_' . $type),
				'title' => $title,
				'code' => $code,
			);
		}

		return array();
	}
}