<?php
class ControllerExtensionMazaSitemap extends Controller {
	public function index() {
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		// static
		$output .= '<sitemap>';
		$output .= '  <loc>' . $this->url->link('extension/maza/sitemap/static') . '</loc>';
		$output .= '</sitemap>';

		// Category
		$output .= '<sitemap>';
		$output .= '  <loc>' . $this->url->link('extension/maza/sitemap/category') . '</loc>';
		$output .= '</sitemap>';

		// Manufacturer
		$output .= '<sitemap>';
		$output .= '  <loc>' . $this->url->link('extension/maza/sitemap/manufacturer') . '</loc>';
		$output .= '</sitemap>';

		// Product
		$output .= $this->load->controller('extension/maza/sitemap/product/sitemap');

		// Informations
		$output .= '<sitemap>';
		$output .= '  <loc>' . $this->url->link('extension/maza/sitemap/information') . '</loc>';
		$output .= '</sitemap>';

		// Page builder
		$this->load->model('extension/maza/page');

		$page_total = $this->model_extension_maza_page->getTotalPages();

		if ($page_total > 0) {
			$output .= '<sitemap>';
			$output .= '  <loc>' . $this->url->link('extension/maza/sitemap/page') . '</loc>';
			$output .= '</sitemap>';
		}

		// Blog category
		$this->load->model('extension/maza/blog/category');

		$category_total = $this->model_extension_maza_blog_category->getTotalCategories();

		if ($category_total > 0) {
			$output .= '<sitemap>';
			$output .= '  <loc>' . $this->url->link('extension/maza/sitemap/blog_category') . '</loc>';
			$output .= '</sitemap>';
		}

		// BLog article
		$this->load->model('extension/maza/blog/article');

		$article_total = $this->model_extension_maza_blog_article->getTotalArticles();

		if ($article_total > 0) {
			$output .= '<sitemap>';
			$output .= '  <loc>' . $this->url->link('extension/maza/sitemap/blog_article') . '</loc>';
			$output .= '</sitemap>';
			$output .= '<sitemap>';
			$output .= '  <loc>' . $this->url->link('extension/maza/sitemap/blog_author') . '</loc>';
			$output .= '</sitemap>';
		}

		$output .= '</sitemapindex>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	}
}
