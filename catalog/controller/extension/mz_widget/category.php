<?php
class ControllerExtensionMzWidgetCategory extends maza\layout\Widget {
	public function index($setting) {
            if($setting['widget_category_of'] == 'blog' || ($setting['widget_category_of'] == 'auto_switch' && strpos($this->mz_document->getRoute(), 'extension/maza/blog') === 0)){
                    $this->load->language('common/menu');

                    // Blog category
                    $this->load->model('extension/maza/blog/category');

                    $this->load->model('extension/maza/blog/article');

                    $data['categories'] = array();

                    $categories = $this->model_extension_maza_blog_category->getCategories(0);

                    foreach ($categories as $category) {
                            if ($category['top']) {
                                    // Level 2
                                    $children_data = array();

                                    $children = $this->model_extension_maza_blog_category->getCategories($category['category_id']);

                                    foreach ($children as $child) {
                                            $filter_data = array(
                                                    'filter_category_id'  => $child['category_id'],
                                                    'filter_sub_category' => true
                                            );

                                            $children_data[] = array(
                                                    'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_extension_maza_blog_article->getTotalArticles($filter_data) . ')' : ''),
                                                    'href'  => $this->url->link('extension/maza/blog/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                                            );
                                    }

                                    // Level 1
                                    $data['categories'][] = array(
                                            'name'     => $category['name'],
                                            'children' => $children_data,
                                            'column'   => $category['column'] ? $category['column'] : 1,
                                            'href'     => $this->url->link('extension/maza/blog/category', 'path=' . $category['category_id'])
                                    );
                            }
                    }

                    return $this->load->view('common/menu', $data);
            } else {
                    // Product category
                    return $this->load->controller('common/menu');
            }
	}
}
