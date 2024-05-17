<?php
class ControllerExtensionMazaSitemapBlogArticle extends Controller {
	public function index(): void {
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$this->load->model('extension/maza/blog/article');

		$start = 0;
		$limit = 100;

		while($articles = $this->model_extension_maza_blog_article->getArticles(['start' => $start, 'limit' => $limit])){
			foreach ($articles as $article) {
				$output .= '<url>';
				$output .= '  <loc>' . $this->url->link('extension/maza/blog/article', 'article_id=' . $article['article_id']) . '</loc>';
				$output .= '  <changefreq>weekly</changefreq>';
				$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($article['date_modified'])) . '</lastmod>';
				$output .= '  <priority>1.0</priority>';
				$output .= '</url>';
			}

			$start += $limit;
		}

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	}
}
