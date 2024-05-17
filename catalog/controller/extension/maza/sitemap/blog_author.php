<?php
class ControllerExtensionMazaSitemapBlogAuthor extends Controller {
	public function index(): void {
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$query = $this->db->query("SELECT author_id FROM " . DB_PREFIX . "mz_blog_author");

		foreach ($query->rows as $author) {
			$output .= '<url>';
			$output .= '  <loc>' . $this->url->link('extension/maza/blog/author', 'author_id=' . $author['author_id']) . '</loc>';
			$output .= '  <changefreq>weekly</changefreq>';
			$output .= '  <priority>0.5</priority>';
			$output .= '</url>';
		}

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	}
}
