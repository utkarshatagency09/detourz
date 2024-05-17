<?php
class ControllerExtensionModuleMzCategoryWall extends maza\layout\Module {
    private static $instance_count = 0;

    private $setting = array();

    public function index(array $module_setting) {
        // Extension will not work without maza engine
        if (!$this->config->get('maza_status') || empty($module_setting['module_id'])) {
            return;
        }

        $this->load->model('extension/maza/module');
        $this->load->model('extension/maza/catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');

        // Setting
        $this->setting = $this->model_extension_maza_module->getSetting($module_setting['module_id']);

        if (!$this->setting || !$this->setting['status']) {
            return;
        }
        $this->setting['module_id'] = $module_setting['module_id'];

        if ($this->setting['carousel_status']) {
            if ($this->config->get('maza_cdn')) {
                $this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
                $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.js', 'footer');
            } else {
                $this->document->addStyle('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
                $this->document->addScript('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.js', 'footer');
            }
        }

        if ($this->setting['lazy_loading']) {
            $this->document->addScript('//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js', 'footer');
            $this->document->addScript('//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.plugins.min.js', 'footer');
        }

        $data = array();

        // Categories
        $data['categories'] = $this->getCategories();

        $data['mz_suffix'] = $module_setting['mz_suffix'] ?? self::$instance_count++;

        if ($data['categories']) {
            return $this->mz_load->view($this->template($data), $data, 'extension/module/mz_category_wall');
        }
    }

    private function template($data = array()) {
        if (empty($this->setting['carousel_nav_icon_font'][$this->config->get('config_language_id')]) && !empty($this->setting['carousel_nav_icon_svg'][$this->config->get('config_language_id')]) && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $this->setting['carousel_nav_icon_svg'][$this->config->get('config_language_id')])) {
            $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $this->setting['carousel_nav_icon_svg'][$this->config->get('config_language_id')]);
        }

        $templete = $this->getCache('mz_category_wall.' . $this->setting['module_id']); // get static cache

        if ($templete) {
            return $templete;
        }

        // Settings
        if ($this->setting['title'] && isset($this->setting['title'][$this->config->get('config_language_id')])) {
            $data['heading_title'] = $this->setting['title'][$this->config->get('config_language_id')];
        } else {
            $data['heading_title'] = '';
        }

        $data['show_content']      = $this->setting['show_content'];
        $data['image_position']    = $this->setting['image_position'];
        $data['product_count']     = $this->setting['category_product_count'];
        $data['carousel_status']   = $this->setting['carousel_status'];
        $data['carousel_autoplay'] = $this->setting['carousel_autoplay'];
        $data['carousel_loop']     = $this->setting['carousel_loop'];
        $data['carousel_row']      = $this->setting['carousel_row'];
        $data['column_xs']         = $this->setting['column_xs'];
        $data['column_sm']         = $this->setting['column_sm'];
        $data['column_md']         = $this->setting['column_md'];
        $data['column_lg']         = $this->setting['column_lg'];
        $data['column_xl']         = $this->setting['column_xl'];
        $data['image_width']       = $this->setting['image_width'];
        $data['image_height']      = $this->setting['image_height'];
        $data['lazy_loading']      = $this->setting['lazy_loading'];

        // Carousel navigation icon
        $data['carousel_nav_icon_width']  = $this->setting['carousel_nav_icon_width'];
        $data['carousel_nav_icon_height'] = $this->setting['carousel_nav_icon_height'];
        $data['carousel_nav_icon_size']   = $this->setting['carousel_nav_icon_size'];
        $data['carousel_nav_icon_font']   = false;
        $data['carousel_nav_icon_svg']    = false;
        $data['carousel_nav_icon_image']  = false;

        if (!empty($this->setting['carousel_nav_icon_font'][$this->config->get('config_language_id')])) {
            $data['carousel_nav_icon_font'] = $this->setting['carousel_nav_icon_font'][$this->config->get('config_language_id')];

        } elseif (!empty($this->setting['carousel_nav_icon_svg'][$this->config->get('config_language_id')]) && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $this->setting['carousel_nav_icon_svg'][$this->config->get('config_language_id')])) {
            $data['carousel_nav_icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $this->setting['carousel_nav_icon_svg'][$this->config->get('config_language_id')]);

        } elseif (!empty($this->setting['carousel_nav_icon_image'][$this->config->get('config_language_id')]) && is_file(DIR_IMAGE . $this->setting['carousel_nav_icon_image'][$this->config->get('config_language_id')])) {
            list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($this->setting['carousel_nav_icon_image'][$this->config->get('config_language_id')], $this->setting['carousel_nav_icon_width'], $this->setting['carousel_nav_icon_height']);

            $data['carousel_nav_icon_width']  = $image_width;
            $data['carousel_nav_icon_height'] = $image_height;

            $data['carousel_nav_icon_image'] = $this->model_tool_image->resize($this->setting['carousel_nav_icon_image'][$this->config->get('config_language_id')], $image_width, $image_height);
        }

        // Style
        $data['gutter_width']  = $this->mz_skin_config->get('style_gutter_width');
        $data['breakpoint_sm'] = $this->mz_skin_config->get('style_breakpoints')['sm'];
        $data['breakpoint_md'] = $this->mz_skin_config->get('style_breakpoints')['md'];
        $data['breakpoint_lg'] = $this->mz_skin_config->get('style_breakpoints')['lg'];
        $data['breakpoint_xl'] = $this->mz_skin_config->get('style_breakpoints')['xl'];

        if ($this->setting['lazy_loading']) {
            $data['transparent'] = $this->model_extension_maza_image->transparent($this->setting['image_width'], $this->setting['image_height']);
        }
        $template = $this->load->view('extension/module/mz_category_wall', $data);

        $this->setCache('mz_category_wall.' . $this->setting['module_id'], $template, false); //set static cache of templare

        return $template;
    }

    /**
     * Get categories list
     * @return array Categories
     */
    private function getCategories() {
        $categories = array();

        // Featured source
        if ($this->setting['category_source'] == 'featured') {
            foreach ($this->setting['featured_category'] ?? [] as $category_id) {

                if ($this->setting['featured_sub_category']) { // Get sub categories of feature category
                    $categories = array_merge($categories, $this->model_catalog_category->getCategories($category_id));
                } else {
                    $category_info = $this->model_catalog_category->getCategory($category_id);

                    if ($category_info) {
                        $categories[] = $category_info;
                    }
                }

            }
        }
        // Auto
        else {
            $category_id = 0;

            if (isset($this->request->get['category_id'])) {
                $category_id = $this->request->get['category_id'];
            } elseif (isset($this->request->get['path'])) {
                $path        = explode('_', $this->request->get['path']);
                $category_id = array_pop($path);
            }

            $categories = $this->model_catalog_category->getCategories($category_id);
        }

        $data = array();

        foreach ($categories as $category_info) {
            if (is_file(DIR_IMAGE . $category_info['image'])) {
                $image = $this->model_tool_image->resize($category_info['image'], $this->setting['image_width'], $this->setting['image_height']);
            } else {
                $image = $this->model_tool_image->resize('mz_no_image.png', $this->setting['image_width'], $this->setting['image_height']);
            }

            if ($this->setting['category_product_count'] || $this->setting['category_sort'] == 'products') {
                $product_total = (int) $this->model_catalog_product->getTotalProducts(array('filter_category_id' => $category_info['category_id'], 'filter_sub_category' => true));
            } else {
                $product_total = null;
            }

            if ($this->setting['category_sort'] == 'orders') {
                $order_total = (int) $this->model_extension_maza_catalog_category->getTotalOrders($category_info['category_id'], true);
            } else {
                $order_total = null;
            }

            $data[] = array(
                'category_id' => $category_info['category_id'],
                'name' => $category_info['name'],
                'sort_order' => (int) $category_info['sort_order'],
                'date_added' => $category_info['date_added'],
                'image' => $image,
                'product_total' => $product_total,
                'order_total' => $order_total,
                'href' => $this->url->link('product/category', 'path=' . $this->model_extension_maza_catalog_category->getCategoryPath($category_info['category_id']))
            );
        }

        // Sort base
        if ($this->setting['category_sort'] == 'products') {
            $sort_base = array_column($data, 'product_total');
        } elseif ($this->setting['category_sort'] == 'orders') {
            $sort_base = array_column($data, 'order_total');
        } else {
            $sort_base = array_column($data, $this->setting['category_sort']);
        }

        // Sort order
        if ($this->setting['category_order'] == 'ASC') {
            $sort_order = SORT_ASC;
        } else {
            $sort_order = SORT_DESC;
        }

        // Sort flag
        if (in_array($this->setting['category_sort'], ['products', 'orders', 'sort_order'])) {
            $sort_flag = SORT_NUMERIC;
        } else {
            $sort_flag = SORT_STRING;
        }

        // Sort categories
        array_multisort($sort_base, $sort_order, $sort_flag, $data);

        // Limit
        $data = array_slice($data, 0, $this->setting['category_limit']);

        return $data;
    }

}