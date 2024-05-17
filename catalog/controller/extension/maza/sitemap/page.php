<?php
class ControllerExtensionMazaSitemapPage extends Controller {
	public function index(): void {
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$this->load->model('extension/maza/page');

		$pages = $this->model_extension_maza_page->getPages();

		foreach ($pages as $page) {
			$output .= '<url>';
			$output .= '  <loc>' . $this->url->link('extension/maza/page', 'page_id=' . $page['page_id']) . '</loc>';
			$output .= '  <changefreq>weekly</changefreq>';
			$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($page['date_modified'])) . '</lastmod>';
			$output .= '  <priority>0.5</priority>';
			$output .= '</url>';
		}

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	}
}
