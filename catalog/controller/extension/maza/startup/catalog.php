<?php
class ControllerExtensionMazaStartupCatalog extends Controller {
    public function index(): void {
		// Find category herency and set correct path
		$this->load->model('extension/maza/catalog/category');
		$this->load->model('extension/maza/blog/category');

		$category_id = 0;
		if(isset($this->request->get['path']) && (strpos($this->mz_document->getRoute(), 'product/') === 0 || strpos($this->mz_document->getRoute(), 'extension/maza/blog/') === 0)){
			$parts = explode('_', (string)$this->request->get['path']);
			$category_id = (int)array_pop($parts);    
		}

		// Product category
		if($this->mz_document->isRoute('product/product') && isset($this->request->get['product_id'])){
			if(!$category_id && isset($this->request->get['category_id'])){
				$category_id = $this->request->get['category_id'];
			}
			if(!$category_id || !$this->model_extension_maza_catalog_product->validateCategory($this->request->get['product_id'], $category_id)){
				$category_id = $this->model_extension_maza_catalog_category->getProductCategory($this->request->get['product_id']);
			}
		}

		if($category_id && strpos($this->mz_document->getRoute(), 'product/') === 0){
			$this->request->get['path'] = $this->model_extension_maza_catalog_category->getCategoryPath($category_id);
		}

		// Blog category
		if($this->mz_document->isRoute('extension/maza/blog/article') && isset($this->request->get['article_id'])){
			$this->load->model('extension/maza/blog/article');

			if(!$category_id && isset($this->request->get['category_id'])){
				$category_id = $this->request->get['category_id'];
			}
			if(!$category_id || !$this->model_extension_maza_blog_article->validateCategory($this->request->get['article_id'], $category_id)){
				$category_id = $this->model_extension_maza_blog_category->getArticleCategory($this->request->get['article_id']);
			}
		}

		if($category_id && strpos($this->mz_document->getRoute(), 'extension/maza/blog/') === 0){
			$this->request->get['path'] = implode('_', array_column($this->model_extension_maza_blog_category->getCategoryPath($category_id), 'path_id'));
		}
    }
}
