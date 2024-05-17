<?php
class ControllerExtensionMazaSitemapBlogCategory extends Controller {
	public function index(): void {
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$this->load->model('extension/maza/blog/category');

		$output .= $this->getBlogCategories(0);

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	}

	protected function getBlogCategories(int $parent_id, string $current_path = ''): string {
		$output = '';

		$results = $this->model_extension_maza_blog_category->getCategories($parent_id);

		foreach ($results as $result) {
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}

			$output .= '<url>';
			$output .= '  <loc>' . $this->url->link('extension/maza/blog/category', 'path=' . $new_path) . '</loc>';
			$output .= '  <changefreq>weekly</changefreq>';
			$output .= '  <priority>0.7</priority>';
			$output .= '</url>';

			$output .= $this->getBlogCategories($result['category_id'], $new_path);
		}

		return $output;
	}
}
