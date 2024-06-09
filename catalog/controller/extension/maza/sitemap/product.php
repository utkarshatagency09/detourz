<?php
class ControllerExtensionMazaSitemapProduct extends Controller {
	private $limit = 1000;

	public function index(): void {
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$this->load->model('catalog/product');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$filter_data = array(
			'start' => ($page - 1) * $this->limit,
			'limit' => $this->limit
		);

		$products = $this->model_catalog_product->getProducts($filter_data);

		foreach ($products as $product) {
			$output .= '<url>';
			$output .= '  <loc>' . $this->url->link('product/product', 'product_id=' . $product['product_id']) . '</loc>';
			$output .= '  <changefreq>weekly</changefreq>';
			$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>';
			$output .= '  <priority>1.0</priority>';
			$output .= '</url>';
		}

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	}

	public function sitemap(): string {
		$this->load->model('catalog/product');

		$product_total = $this->model_catalog_product->getTotalProducts();

		$total_pages = ceil($product_total / $this->limit);

		$page = 1;

		$output = '';

		while ($page <= $total_pages) {
			$output .= '<sitemap>';
			$output .= '  <loc>' . $this->url->link('extension/maza/sitemap/product', 'page=' . $page) . '</loc>';
			$output .= '</sitemap>';

			$page++;
		}

		return $output;
	}
}
