<?php
class ControllerExtensionMzWidgetSearch extends maza\layout\Widget {
	public function index(array $setting): string {
        $this->load->language('extension/mz_widget/search');
        
        $data = array();
        
        $data['search_button_type'] = $setting['widget_search_button_type'];
        
        if($setting['widget_autocomplete_status']){
            $data['autocomplete'] = $setting['widget_autocomplete_limit'];
        } else {
            $data['autocomplete'] = 0;
        }
        
        if(isset($this->request->get['search'])){
            $data['search'] = $this->request->get['search'];
        } else {
            $data['search'] = false;
        }
        
        if($setting['widget_search_route'] == 'blog' || ($setting['widget_search_route'] == 'auto_switch' && strpos($this->mz_document->getRoute(), 'extension/maza/blog') === 0)){
            $route_target = 'extension/maza/blog/search';
            $data['autocomplete_route'] = 'extension/maza/blog/article/autocomplete';
            $data['placeholder'] = maza\getOfLanguage($setting['widget_blog_placeholder']);
        } else {
            $route_target = 'product/search';
            $data['autocomplete_route'] = 'extension/maza/product/product/autocomplete';
            $data['placeholder'] = maza\getOfLanguage($setting['widget_product_placeholder']);
        }

        // Product category
        if($route_target == 'product/search' && $setting['widget_product_category_status']){
            $this->load->model('catalog/category');
            $data['categories'] = $this->getProductCategories(0, $setting['widget_product_category_depth']);
            
            if(isset($this->request->get['category_id'])){
                $category_info = $this->model_catalog_category->getCategory($this->request->get['category_id']);

                if($category_info){
                    $data['category_name'] = $category_info['name'];
                    $data['category_id'] = $category_info['category_id'];
                }
            }
        }
        
        // Blog category
        if($route_target == 'extension/maza/blog/search' && $setting['widget_blog_category_status']){
            $this->load->model('extension/maza/blog/category');
            $data['categories'] = $this->getBlogCategories(0, $setting['widget_blog_category_depth']);
            
            if(isset($this->request->get['category_id'])){
                $category_info = $this->model_extension_maza_blog_category->getCategory($this->request->get['category_id']);

                if($category_info){
                    $data['category_name'] = $category_info['name'];
                    $data['category_id'] = $category_info['category_id'];
                }
            }
        }
        
        $data['action'] = $this->url->link($route_target);
        $data['route']  = $route_target;
                
		return $this->load->view('extension/mz_widget/search', $data);	
	}
        
    /**
     * Get product category by level
     * @param int $parent_id parent id
     * @param int $depth depth of child category
     */
    private function getProductCategories(int $parent_id, int $depth = 1): array {
        $data = array();

        if($depth){
            $categories = $this->model_catalog_category->getCategories($parent_id);

            foreach ($categories as $category_info) {
                $data[] = array(
                    'category_id' => $category_info['category_id'],
                    'name'        => $category_info['name'],
                    'children'    => $this->getProductCategories($category_info['category_id'], $depth - 1)
                );
            }
        }
        
        return $data;
    }
    
    /**
     * Get blog category by level
     * @param int $parent_id parent id
     * @param int $depth depth of child category
     */
    private function getBlogCategories(int $parent_id, int $depth = 1): array {
        $data = array();

        if($depth){
            $categories = $this->model_extension_maza_blog_category->getCategories($parent_id);

            foreach ($categories as $category_info) {
                $data[] = array(
                    'category_id' => $category_info['category_id'],
                    'name'        => $category_info['name'],
                    'children'    => $this->getBlogCategories($category_info['category_id'], $depth - 1)
                );
            }
        }

        return $data;
    }
}
