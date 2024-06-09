<?php
class ControllerExtensionModuleMzProductListing extends maza\layout\Module {
    private static $instance_count = 0;

    public function index(array $module_setting) {
        // Extension will not work without maza engine
        if(!$this->config->get('maza_status') || empty($module_setting['module_id'])){
            return null;
        }
        
        $this->load->language('extension/module/mz_product_listing');
        
        $this->load->model('extension/maza/module');
        $this->load->model('catalog/product');
        $this->load->model('extension/maza/blog/article');
        
        // Setting
        $setting = $this->model_extension_maza_module->getSetting($module_setting['module_id']);
        if(!$setting || !$setting['status']){
            return null;
        }
        $setting['module_id'] = $module_setting['module_id'];
        
        // Add assets
        if($setting['carousel_status']){
            if($this->config->get('maza_cdn')){
                $this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
                $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.js', 'footer');
            } else {
                $this->document->addStyle('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
                $this->document->addScript('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.js', 'footer');
            }
        }
        
        // Tabs
        $data['tabs'] = $this->getTabs($setting);
        
        $data['mz_suffix'] = $module_setting['mz_suffix']??self::$instance_count++;
        
        if($data['tabs']){
            return $this->mz_load->view($this->template($setting, $data), $data, 'extension/module/mz_product_listing');
        }
	}
    
    private function template(array $setting, array $data = array()): string {
        $carousel_nav_icon_svg = maza\getOfLanguage($setting['carousel_nav_icon_svg']);
        if(empty(maza\getOfLanguage($setting['carousel_nav_icon_font'])) && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg)){
            $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg);
        }
    
        $templete = $this->getCache('mz_product_listing.' . $setting['module_id']); // get static cache
        
        if($templete){
            return $templete;
        }
    
        // Module Title
        $data['heading_title'] = maza\getOfLanguage($setting['title']);
        
        // Image
        $data['product_image_width']     =  $setting['product_image_width'];
        $data['product_image_height']    =  $setting['product_image_height'];
        $data['product_image_position']  =  $setting['product_image_position'];
        
        // Banner
        $data['banner_width']     = $setting['banner_width'];
        $data['banner_height']    = $setting['banner_height'];
        
        if($setting['banner_status']){
            // svg image
            $banner_svg = maza\getOfLanguage($setting['banner_svg']);
            if($banner_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $banner_svg)){
                $data['banner_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $banner_svg);
            } else {
                $data['banner_svg'] = false;
            }
            
            // static Image
            $banner_image = maza\getOfLanguage($setting['banner_image']);
            if($banner_image && is_file(DIR_IMAGE . $banner_image)){
                list($banner_width, $banner_height) = $this->model_extension_maza_image->getEstimatedSize($banner_image, $setting['banner_width'], $setting['banner_height']);
                $data['banner_image'] = $this->model_tool_image->resize($banner_image, $banner_width, $banner_height);
            } else {
                $data['banner_image'] = false;
            }
            
            // banner link
            if ($setting['banner_link_code']) {
                $data['banner_url'] = $this->model_extension_maza_common->createLink($setting['banner_link_code']);
            } else {
                $data['banner_url'] = array();
            }
        }
        
        // Tabs icon
        $data['tab_icon_width']     = $setting['tab_icon_width'];
        $data['tab_icon_height']    = $setting['tab_icon_height'];
        $data['tab_icon_position']  = $setting['tab_icon_position'];
        
        // Column
        $data['column_xs']           = $setting['column_xs'];
        $data['column_sm']           = $setting['column_sm'];
        $data['column_md']           = $setting['column_md'];
        $data['column_lg']           = $setting['column_lg'];
        $data['column_xl']           = $setting['column_xl'];
        
        // carousel
        $data['carousel_status']     = $setting['carousel_status'];
        $data['carousel_autoplay']   = $setting['carousel_autoplay'];
        $data['carousel_pagination'] = $setting['carousel_pagination'];
        //$data['carousel_loop']       = $setting['carousel_loop'];
        $data['carousel_row']        = $setting['carousel_row'];
        
        // carousel navigation icon
        $data['carousel_nav_icon_width'] = $setting['carousel_nav_icon_width'];
        $data['carousel_nav_icon_height'] = $setting['carousel_nav_icon_height'];
        $data['carousel_nav_icon_size'] = $setting['carousel_nav_icon_size'];
        $data['carousel_nav_icon_font'] = false;
        $data['carousel_nav_icon_svg'] = false;
        $data['carousel_nav_icon_image'] = false;
        
        $data['carousel_nav_icon_font'] = maza\getOfLanguage($setting['carousel_nav_icon_font']);

        $carousel_nav_icon_svg = maza\getOfLanguage($setting['carousel_nav_icon_svg']);
        if($carousel_nav_icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg)){
            $data['carousel_nav_icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg);
        } 
        
        $carousel_nav_icon_image = maza\getOfLanguage($setting['carousel_nav_icon_image']);
        if($carousel_nav_icon_image && is_file(DIR_IMAGE . $carousel_nav_icon_image)){
            list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($carousel_nav_icon_image, $setting['carousel_nav_icon_width'], $setting['carousel_nav_icon_height']);
            
            $data['carousel_nav_icon_width'] = $image_width;
            $data['carousel_nav_icon_height'] = $image_height;
            
            $data['carousel_nav_icon_image'] = $this->model_tool_image->resize($carousel_nav_icon_image, $image_width, $image_height);
        }
        
        // Product element
        $data['cart_status']             = $setting['product_grid_cart'];
        $data['compare_status']          = $setting['product_grid_compare'];
        $data['wishlist_status']         = $setting['product_grid_wishlist'];
        $data['quick_view_status']       = $setting['product_grid_quick_view'];
        $data['rating_status']           = $setting['product_grid_rating'];
        $data['description_status']      = $setting['product_grid_description'];
        
        // Special countdown
        if($this->mz_skin_config->get('catalog_special_countdown_status')){
            if($setting['product_grid_countdown'] < 0){
                $data['special_countdown_status']           = $this->mz_skin_config->get('catalog_grid_countdown_status');
            } else {
                $data['special_countdown_status']           = $setting['product_grid_countdown'];
            }
        } else {
            $data['special_countdown_status'] = false;
        }
        
        // Special sold
        if($this->mz_skin_config->get('catalog_special_sold_status')){
            if($setting['product_grid_sold'] < 0){
                $data['special_sold_status']           = $this->mz_skin_config->get('catalog_grid_sold_status');
            } else {
                $data['special_sold_status']           = $setting['product_grid_sold'];
            }
        } else {
            $data['special_sold_status'] = false;
        }
        
        // Image lazy loading
        $data['image_lazy_loading']          = $this->mz_skin_config->get('catalog_grid_image_lazy_loading');
        
        $data['transparent'] = $this->model_extension_maza_image->transparent($setting['product_image_width'], $setting['product_image_height']);
        
        // Srcset size
        $data['srcset_sizes'] = $this->model_extension_maza_image->getSrcSetSize($setting['product_image_srcset'], $setting['product_image_width']);
        
        // layout
        $data['tab_status']          =  $setting['tab_status'];
        $data['url_target']          =  $setting['url_target'];
        
        // Style
        $data['gutter_width']         =  $this->mz_skin_config->get('style_gutter_width');
        $data['breakpoint_sm']        =  $this->mz_skin_config->get('style_breakpoints')['sm'];
        $data['breakpoint_md']        =  $this->mz_skin_config->get('style_breakpoints')['md'];
        $data['breakpoint_lg']        =  $this->mz_skin_config->get('style_breakpoints')['lg'];
        $data['breakpoint_xl']        =  $this->mz_skin_config->get('style_breakpoints')['xl'];
        
        $template = $this->load->view('extension/module/mz_product_listing', $data);
        
        $this->setCache('mz_product_listing.' . $setting['module_id'], $template, false); //set static cache of templare
        
        return $template;
    }
    
    /**
     * Get list of tabs with products
     * @param array $setting module setting
     * @return array tab list
     */
    private function getTabs(array $setting): array {
        $tab_data = $sort_order = array();
        
        foreach ($setting['tabs'] as $tab) {
            $tab_info = array();

            // tab name
            $tab_info['name'] = maza\getOfLanguage($tab['name']);
            
            // skin step
            if(!$tab['status'] || !$tab_info['name']){
                continue;
            }
            
            // Get products
            $tab_info['products'] = array();
            
            $products = $this->getTabProducts($tab, $setting['product_limit']);
            
            foreach ($products as $product) {
                    // Image
                    if (is_file(DIR_IMAGE . $product['image'])) {
                            $image = $this->model_tool_image->resize($product['image'], $setting['product_image_width'], $setting['product_image_height']);
                    } else {
                            $image = $this->model_tool_image->resize('mz_no_image.png', $setting['product_image_width'], $setting['product_image_height']);
                    }
                    
                    // price
                    if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                            $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                            $price = false;
                    }
                    
                    // Special
                    if (!is_null($product['special']) && (float)$product['special'] >= 0) {
                            $special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            $tax_price = (float)$product['special'];
                    } else {
                            $special = false;
                            $tax_price = (float)$product['price'];
                    }
                    
                    // tax
                    if ($setting['product_grid_tax'] && $this->config->get('config_tax')) {
                            $tax = $this->currency->format($tax_price, $this->session->data['currency']);
                    } else {
                            $tax = false;
                    }
                    
                    // Review
                    if ($this->config->get('config_review_status')) {
                            $rating = $product['rating'];
                    } else {
                            $rating = false;
                    }
                    
                    $tab_info['products'][] = array(
                            'product_id'  => $product['product_id'],
                            'thumb'       => $image,
                            'name'        => $product['name'],
                            'description' => utf8_substr(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                            'price'       => $price,
                            'special'     => $special,
                            'tax'         => $tax,
                            'rating'      => $rating,
                            'href'        => $this->url->link('product/product', 'product_id=' . $product['product_id'])
                    );
            }
            
            // Add extra meta data to product list
            $this->mz_hook->fetch('catalog_product_data_list', [$products, &$tab_info['products'], $setting]);
            
            if(empty($tab_info['products'])){
                continue;
            }
            
            // Tab icon
            // font icon
            $tab_info['icon_font'] = maza\getOfLanguage($tab['icon_font']);

            // svg image
            $icon_svg = maza\getOfLanguage($tab['icon_svg']);
            if($icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)){
                $tab_info['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
            } else {
                $tab_info['icon_svg'] = false;
            }

            // Image
            $icon_image = maza\getOfLanguage($tab['icon_image']);
            if($icon_image && is_file(DIR_IMAGE . $icon_image)){
                list($icon_width, $icon_height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $setting['tab_icon_width'], $setting['tab_icon_height']);
                $tab_info['icon_image'] = $this->model_tool_image->resize($icon_image, $icon_width, $icon_height);
            } else {
                $tab_info['icon_image'] = false;
            }
            
            // Sort order of tab
            $sort_order[] = $tab['sort_order'];
            
            $tab_data[] = $tab_info;
        }
        
        // Sort tab
        array_multisort($sort_order, SORT_ASC, SORT_NUMERIC, $tab_data);
        
        return $tab_data;
    }
    
    /**
     * Get products of tab
     * @param array $tab tab data
     * @param int $product_limit product limit
     * @return array list of products
     */
    private function getTabProducts(array $tab, int $product_limit): array {
        // Filter
        $filter = array();
        $filter['start'] = 0;
        $filter['limit'] = $product_limit;
        
        // Auto filter
        $auto_filter = array();
        
        if($tab['auto_filter']){
            if(isset($this->request->get['category_id'])){
                $auto_filter['filter_category_id'] = $this->request->get['category_id'];
            } elseif(isset($this->request->get['path']) && !isset($this->request->get['product_id'])){
                $path = explode('_', (string)$this->request->get['path']);
                $auto_filter['filter_category_id'] = (int)array_pop($path);
            }
            
            if(isset($this->request->get['manufacturer_id'])){
                $auto_filter['filter_manufacturer_id'] = $this->request->get['manufacturer_id'];
            } elseif (isset($this->request->get['product_id'])) {
                $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
                
                if ($product_info && $product_info['manufacturer_id']) {
                    $auto_filter['filter_manufacturer_id'] = $product_info['manufacturer_id'];
                }
            }
        }
        
        // Get custom products
        if($tab['source'] === 'product'){
            $products = array();
            foreach ($tab['custom_product']??[] as $product_id) {
                if($product = $this->model_catalog_product->getProduct($product_id)){
                    $products[] = $product;
                }
            }
            return $products;
        }

        // Get featured products
        if($tab['source'] === 'featured'){
            return $this->model_extension_maza_catalog_product->getFeaturedProducts($product_limit);
        }

        // Get recent viewed products
        if($tab['source'] === 'recent_viewed'){
            $products = array();
            foreach (array_slice($this->session->data['mz_recent_viewed_product']??[], 0, $product_limit) as $product_id) {
                if($product = $this->model_catalog_product->getProduct($product_id)){
                    $products[] = $product;
                }
            }
            return $products;
        }
        
        // Get related products
        if($tab['source'] === 'related'){
            if(isset($this->request->get['product_id'])){
                return $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
            } elseif(isset($this->request->get['article_id'])){
                return $this->model_extension_maza_blog_article->getArticleProduct($this->request->get['article_id']);
            } else {
                return array();
            }
        }
        
        // Get latest products
        if($tab['source'] === 'latest'){
            $filter['sort'] = 'p.date_added';
            $filter['order'] = 'DESC';
        }
        
        // Get special products
        if($tab['source'] === 'special'){
            $filter['filter_special'] = 1;
        }
        
        // Get most viewed products
        if($tab['source'] === 'most_viewed'){
            $filter['sort_order'] = array(
                array('sort' => 'p.viewed', 'order' => 'DESC'),
                array('sort' => 'p.date_added', 'order' => 'DESC')
            );
        }
        
        // Get best selling products
        if($tab['source'] === 'best_seller'){
            $filter['sort'] = 'order_quantity';
            $filter['order'] = 'DESC';
        }
        
        // Get Random products
        if($tab['source'] === 'random'){
            $filter['sort'] = 'random';
        }
        
        // Get products by filter
        if($tab['source'] === 'filter'){
            return $this->getProductsByFilter($tab, $auto_filter, $product_limit);
        }
        
        return $this->model_extension_maza_catalog_product->getProducts(array_merge($filter, $auto_filter));
    }
    
    /**
     * Get products by filter
     * @param array $data product filter
     * @param array $auto_filter Auto filter
     * @param int $product_limit product limit
     * @return array list of filtered products
     */
    private function getProductsByFilter(array $data, array $auto_filter, int $product_limit): array {
        $filter = array();
        
        // Filter category
        if(isset($auto_filter['filter_category_id'])){
            $filter['filter_category_id'] = $auto_filter['filter_category_id'];
        } elseif(isset($data['filter_category'])) {
            $filter['filter_category_id'] = $data['filter_category'];
        } else {
            $filter['filter_category_id'] = array();
        }
        
        
        // Include or exclude sub category
        $filter['filter_sub_category'] = $data['filter_sub_category'];
        
        // Depth of sub category
        $filter['filter_sub_category_depth'] = $data['filter_sub_category_depth'];
        
        // Filter manufacturer
        if(isset($auto_filter['filter_manufacturer_id'])){
            $filter['filter_manufacturer_id'] = $auto_filter['filter_manufacturer_id'];
        } elseif(isset($data['filter_manufacturer'])) {
            $filter['filter_manufacturer_id'] = $data['filter_manufacturer'];
        } else{
            $filter['filter_manufacturer_id'] = null;
        }
        
        
        // Filter product filter
        $filter['filter_filter'] = isset($data['filter_product_filter'])?$data['filter_product_filter']:array();
        
        // Filter price range
        if($data['filter_min_price']){
            $filter['filter_min_price'] = $data['filter_min_price'];
        }
        if($data['filter_max_price']){
            $filter['filter_max_price'] = $data['filter_max_price'];
        }
        
        // Filter only special products
        if($data['filter_special_only']){
            $filter['filter_special'] = $data['filter_special_only'];
        }
        
        // Filter price range
        if($data['filter_min_quantity']){
            $filter['filter_min_quantity'] = $data['filter_min_quantity'];
        }
        if($data['filter_max_quantity']){
            $filter['filter_max_quantity'] = $data['filter_max_quantity'];
        }
        
        // Filter rating range
        if($data['filter_min_rating']){
            $filter['filter_min_rating'] = $data['filter_min_rating'];
        }
        if($data['filter_max_rating']){
            $filter['filter_max_rating'] = $data['filter_max_rating'];
        }
        
        // Filter date added
        if($data['filter_date_add_start']){
            $filter['filter_date_add_start'] = $data['filter_date_add_start'];
        }
        if($data['filter_date_add_end']){
            $filter['filter_date_add_end'] = $data['filter_date_add_end'];
        }
        
        // Sort and order
        $filter['sort'] = $data['filter_sort_by'];
        switch ($filter['sort']) {
            case 'name': $filter['sort'] = 'pd.name';
                break;
            case 'model': $filter['sort'] = 'p.model';
                break;
            case 'quantity': $filter['sort'] = 'p.quantity';
                break;
            case 'price': $filter['sort'] = 'p.price';
                break;
            case 'viewed': $filter['sort'] = 'p.viewed';
                break;
            case 'sort_order': $filter['sort'] = 'p.sort_order';
                break;
            case 'date_added': $filter['sort'] = 'p.date_added';
                break;
            default:
                break;
        }
        
        $filter['order'] = $data['filter_sort_direction'];
        
        // product limit
        $filter['start'] = 0;
        $filter['limit'] = $product_limit;
        
        return $this->model_extension_maza_catalog_product->getProducts($filter);
    }
}