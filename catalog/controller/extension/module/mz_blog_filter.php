<?php
class ControllerExtensionModuleMzBlogFilter extends maza\layout\Module {
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

		$category_id = end($parts);

		$this->load->model('extension/maza/blog/category');

		$category_info = $this->model_extension_maza_blog_category->getCategory($category_id);

		if ($category_info) {
			$this->load->language('extension/module/filter');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['action'] = str_replace('&amp;', '&', $this->url->link('extension/maza/blog/category', 'path=' . $this->request->get['path'] . $url));

			if (isset($this->request->get['filter'])) {
				$data['filter_category'] = explode(',', $this->request->get['filter']);
			} else {
				$data['filter_category'] = array();
			}

			$this->load->model('extension/maza/blog/article');

			$data['filter_groups'] = array();

			$filter_groups = $this->model_extension_maza_blog_category->getCategoryFilters($category_id);

			if ($filter_groups) {
				foreach ($filter_groups as $filter_group) {
					$childen_data = array();

					foreach ($filter_group['filter'] as $filter) {
						$filter_data = array(
							'filter_category_id' => $category_id,
							'filter_filter'      => $filter['filter_id']
						);

						$childen_data[] = array(
							'filter_id' => $filter['filter_id'],
							'name'      => $filter['name'] . ($this->info['article_count'] ? ' (' . $this->model_extension_maza_blog_article->getTotalArticles($filter_data) . ')' : '')
						);
					}

					$data['filter_groups'][] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $childen_data
					);
				}

				return $this->load->view('extension/module/mz_blog_filter', $data);
			}
		}
	}
}