<?php
class ControllerExtensionMazaSitemapStatic extends Controller {
	public function index(): void {
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$routes = array(
			'product/manufacturer',
			'product/special',
			'extension/maza/products',
			'extension/maza/blog/all',
			'extension/maza/blog/home',
			'extension/maza/testimonial',
			'account/login',
			'account/register',
			'information/contact',
		);

		foreach ($routes as $route) {
			$output .= '<url>';
			$output .= '  <loc>' . $this->url->link($route) . '</loc>';
			$output .= '  <changefreq>monthly</changefreq>';
			$output .= '  <priority>0.5</priority>';
			$output .= '</url>';
		}

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	}
}
