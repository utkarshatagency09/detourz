<?php
class ControllerExtensionModuleMzBlogCategory extends maza\layout\Module {
	private $info = array();
        
	public function index(array $setting) {
		// Extension will not work without maza engine
		if(!$this->config->get('maza_status') || empty($setting['module_id'])){
			return null;
		}
		
		$this->load->model('extension/maza/module');
		
		// Setting
		$this->info = $this->model_extension_maza_module->getSetting($setting['module_id']);
		
		if(!$this->info || !$this->info['status']){
			return null;
		}
		
		$data = array();
		
		$data['heading_title'] = maza\getOfLanguage($this->info['title']);
                

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}

		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}

		$this->load->model('extension/maza/blog/category');

		$this->load->model('extension/maza/blog/article');

		$data['categories'] = array();

		$categories = $this->model_extension_maza_blog_category->getCategories(0);

		foreach ($categories as $category) {
			$children_data = array();

			if ($category['category_id'] == $data['category_id']) {
				$children = $this->model_extension_maza_blog_category->getCategories($category['category_id']);

				foreach($children as $child) {
					$filter_data = array('filter_category_id' => $child['category_id'], 'filter_sub_category' => true);

					$children_data[] = array(
						'category_id' => $child['category_id'],
						'name' => $child['name'] . ($this->info['article_count'] ? ' (' . $this->model_extension_maza_blog_article->getTotalArticles($filter_data) . ')' : ''),
						'href' => $this->url->link('extension/maza/blog/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}
			}

			$filter_data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			);

			$data['categories'][] = array(
				'category_id' => $category['category_id'],
				'name'        => $category['name'] . ($this->info['article_count'] ? ' (' . $this->model_extension_maza_blog_article->getTotalArticles($filter_data) . ')' : ''),
				'children'    => $children_data,
				'href'        => $this->url->link('extension/maza/blog/category', 'path=' . $category['category_id'])
			);
		}

		return $this->load->view('extension/module/mz_blog_category', $data);
	}
}