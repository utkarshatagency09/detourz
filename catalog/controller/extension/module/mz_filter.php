<?php
class ControllerExtensionModuleMzFilter extends maza\layout\Module {
    static $module_class_id = 0;
    private $info = array(); // Module setting
    private $category_id = 0; // Active category id
    private $sub_category = 0; // Sub category
    private $route = 'common/home'; // Current route
    private $common_product_table = 'mz_temp_common_product'; // Temporary product table by common filter
    private $filter_product_table = ''; // Temporary product table by common filter and scaler filter
    private $common_param = array(); // Common get parameter of page
    private $filter_param = array(); // Filter parameter
    private $common_cache_key;

    public function __construct($registry) {
        parent::__construct($registry);

        // Route
        if (isset($this->request->get['target_route'])) {
            $this->route = $this->request->get['target_route'];
        } elseif (isset($this->request->get['route'])) {
            $this->route = $this->request->get['route'];
        }

        // Category
        if (isset($this->request->get['category_id'])) {
            $this->category_id = $this->request->get['category_id'];
        } elseif (isset($this->request->get['path'])) {
            $path              = explode('_', (string) $this->request->get['path']);
            $this->category_id = end($path);
        }

        if (isset($this->request->get['sub_category'])) {
            $this->sub_category = $this->request->get['sub_category'];
        } elseif ($this->route !== 'product/search') {
            $this->sub_category = $this->mz_skin_config->get('catalog_sub_category_product');
        }
    }

    public function index(array $setting) {
        // Extension will not work without maza engine
        if (!$this->config->get('maza_status') || empty($setting['module_id'])) {
            return null;
        }

        $this->load->model('extension/maza/module');
        $this->load->model('extension/module/mz_filter');

        // Setting
        $this->info                    = $this->model_extension_maza_module->getSetting($setting['module_id']);
        $this->info['search_on_limit'] = 10;

        if (!$this->info || !$this->info['status']) {
            return null;
        }

        // Language
        $this->load->language('extension/module/mz_filter');
        $this->translate(); // Translate language

        $this->document->addStyle('catalog/view/javascript/maza/javascript/jquery-ui-1.12.1/jquery-ui.min.css', 'stylesheet', 'all', 'footer');
        $this->document->addScript('catalog/view/javascript/maza/javascript/jquery-ui-1.12.1/jquery-ui.min.js', 'footer');
        $this->document->addScript('catalog/view/javascript/maza/javascript/jquery-ui-1.12.1/jquery.ui.touch-punch.min.js', 'footer');
        $this->document->addScript('catalog/view/javascript/maza/javascript/filter.js', 'footer');

        $this->loadData(); // Generate Data

        // Setting
        $data['count_product']    = $this->info['count_product'];
        $data['ajax']             = $this->info['ajax'];
        $data['delay']            = $this->info['delay'];
        $data['reset_all']        = $this->info['reset_all'];
        $data['reset_group']      = $this->info['reset_group'];
        $data['sort_by']          = $this->info['sort_by'];
        $data['overflow']         = $this->info['overflow'];
        $data['hide_zero_filter'] = $this->info['hide_zero_filter'];

        if (isset($this->mz_skin_config->get('style_breakpoints')[$this->info['collapsed']])) {
            $data['collapsed'] = (int) $this->mz_skin_config->get('style_breakpoints')[$this->info['collapsed']];
        } else {
            $data['collapsed'] = (int) $this->info['collapsed'];
        }

        $data['filters'] = array();

        // Price
        if ($this->info['filter']['price']['status']) {
            $data['filters'][] = $this->getPriceFilter();
        }

        // Sub category
        if ($this->info['filter']['sub_category']['status'] && $this->category_id && $this->sub_category) {
            $data['filters'][] = $this->getSubCategoryFilter();
        }

        // Manufacturer
        if (empty($this->request->get['manufacturer_id']) && $this->info['filter']['manufacturer']['status']) {
            $data['filters'][] = $this->getManufacturerFilter();
        }

        // Search
        if ($this->info['filter']['search']['status']) {
            $data['filters'][] = $this->getSearchFilter();
        }

        // Availability
        if ($this->info['filter']['availability']['status']) {
            $data['filters'][] = $this->getAvailabilityFilter();
        }

        // Discount
        if ($this->info['filter']['discount']['status']) {
            $data['filters'][] = $this->getDiscountFilter();
        }

        // Rating
        if ($this->info['filter']['rating']['status']) {
            $data['filters'][] = $this->getRatingFilter();
        }

        // Filter
        if ($this->info['filter']['filter']['status']) {
            $data['filters'] = array_merge($data['filters'], $this->getFilterFilter());
        }

        // Custom
        if ($this->info['filter']['custom']['status']) {
            $data['filters'] = array_merge($data['filters'], $this->getCustomFilter());
        }

        if (isset($this->request->get['description'])) {
            $data['search_in_description'] = $this->request->get['description'];
        } else {
            $data['search_in_description'] = $this->info['filter']['search']['description'];
        }

        $url = '';

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['path'])) {
            $url .= '&path=' . $this->request->get['path'];
        }

        if (isset($this->request->get['search'])) {
            $url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['tag'])) {
            $url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['description'])) {
            $url .= '&description=' . $this->request->get['description'];
        }

        if (isset($this->request->get['category_id'])) {
            $url .= '&category_id=' . (int) $this->request->get['category_id'];
        }

        if (isset($this->request->get['sub_category'])) {
            $url .= '&sub_category=' . $this->request->get['sub_category'];
        }

        if (isset($this->request->get['manufacturer_id'])) {
            $url .= '&manufacturer_id=' . (int) $this->request->get['manufacturer_id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        // Module setting
        $url .= '&sub_category=' . $this->sub_category;
        if ($this->info['count_product']) {
            $url .= '&_count_product';
        }
        if (empty($this->request->get['manufacturer_id']) && $this->info['filter']['manufacturer']['status']) {
            $url .= '&_f_manufacturer';
        }
        if ($this->info['filter']['sub_category']['status']) {
            $url .= '&_f_sub_category';
        }
        if ($this->info['filter']['availability']['status']) {
            if ($this->info['filter']['availability']['stock_status']) {
                $url .= '&_f_stock_status';
            } else {
                $url .= '&_f_availability';
            }
        }
        if ($this->info['filter']['rating']['status']) {
            $url .= '&_f_rating';
        }
        if ($this->info['filter']['discount']['status']) {
            $url .= '&_f_discount';
        }
        if ($this->info['filter']['filter']['status']) {
            $url .= '&_f_filter';
        }
        if ($this->info['filter']['filter']['require_category']) {
            $url .= '&_filter_require_category';
        }
        if ($this->info['filter']['custom']['status']) {
            $url .= '&_f_custom';
        }
        if ($this->info['filter']['custom']['require_category']) {
            $url .= '&_custom_require_category';
        }
        $url .= '&target_route=' . $this->request->get['route'] . '&module_id=' . $setting['module_id'];

        $data['requestURL'] = 'index.php?route=extension/module/mz_filter/product' . $url;

        $this->dropData(); // Delete data

        $sort_order = array_column($data['filters'], 'sort_order');
        array_multisort($sort_order, SORT_ASC, SORT_NUMERIC, $data['filters']);

        $data['module_class_id'] = self::$module_class_id++;

        if ($data['filters']) {
            return $this->load->view('extension/module/mz_filter', $data);
        }
    }

    /**
     * Price filter
     * @return array
     */
    private function getPriceFilter() {
        $min_price = $selected_min = floor($this->currency->format($this->model_extension_module_mz_filter->getMinimumPrice($this->common_product_table), $this->session->data['currency'], null, false));
        $max_price = $selected_max = ceil($this->currency->format($this->model_extension_module_mz_filter->getMaximumPrice($this->common_product_table), $this->session->data['currency'], null, false));

        // Filter Price
        if (!empty($this->request->get['mz_fp'])) {
            $price = explode('p', $this->request->get['mz_fp']);

            if (!empty($price[0])) { // Minimum price
                $selected_min = $price[0];
            }

            if (!empty($price[1])) { // Maximum price
                $selected_max = $price[1];
            }
        }

        return array(
            'type' => 'price',
            'sort_order' => $this->info['filter']['price']['sort_order'],
            'collapse' => $this->info['filter']['price']['collapse'],
            'selected' => array('min' => $selected_min, 'max' => $selected_max),
            'min_price' => $min_price,
            'max_price' => $max_price
        );
    }

    /**
     * Sub category filter
     * @return array
     */
    private function getSubCategoryFilter(): array {
        // user selected
        if (!empty($this->request->get['mz_fsc'])) {
            $selected = explode('.', $this->request->get['mz_fsc']);
        } else {
            $selected = array();
        }

        $data = array(
            'type' => 'sub_category',
            'status' => false,
            'sort_order' => $this->info['filter']['sub_category']['sort_order'],
            'collapse' => $this->info['filter']['sub_category']['collapse'],
            'input_type' => $this->info['filter']['sub_category']['input_type'],
            'list_type' => $this->info['filter']['sub_category']['list_type'],
            'image_width' => $this->info['filter']['sub_category']['image_width'] ?: 30,
            'image_height' => $this->info['filter']['sub_category']['image_height'] ?: 30,
            'values' => array()
        );

        $filter_param = $this->filter_param;
        unset($filter_param['filter_category_id']);
        unset($filter_param['filter_sub_category']);

        // Get post filtered sub category
        $post_filter_sub_categories = array(); // available sub category after applying all filter

        if ($filter_param) {
            $filter_data                = $filter_param;
            $filter_data['field_total'] = $this->info['count_product'];

            $sub_categories = $this->model_extension_module_mz_filter->getSubCategories($this->filter_product_table ?: $this->common_product_table, $this->category_id, $filter_data);

            foreach ($sub_categories as $sub_category) {
                $post_filter_sub_categories[$sub_category['category_id']] = $sub_category;
            }
        }

        // Get pre filtered sub category
        $sub_categories = $this->common_cache_key ? $this->cache->get($this->common_cache_key . '.sub_category') : array();

        if (empty($sub_categories)) {
            $filter_data = array();

            if ($this->info['count_product']) {
                $filter_data['field_total'] = $this->info['count_product'];
            }

            $sub_categories = $this->model_extension_module_mz_filter->getSubCategories($this->common_product_table, $this->category_id, $filter_data);

            if ($this->common_cache_key) {
                $this->cache->set($this->common_cache_key . '.sub_category', $sub_categories);
            }
        }

        $sort_order = array();

        foreach ($sub_categories as $sub_category) {
            $image = null;

            if ($this->info['filter']['sub_category']['list_type'] !== 'text') {
                if (is_file(DIR_IMAGE . $sub_category['image'])) {
                    $image = $this->model_tool_image->resize($sub_category['image'], $this->info['filter']['sub_category']['image_width'] ?: 30, $this->info['filter']['sub_category']['image_height'] ?: 30);
                } else {
                    $image = $this->model_tool_image->resize('no_image.png', $this->info['filter']['sub_category']['image_width'] ?: 30, $this->info['filter']['sub_category']['image_height'] ?: 30);
                }
            }

            if (isset($post_filter_sub_categories[$sub_category['category_id']])) {
                $status = true;
                $total  = $this->info['count_product'] ? $post_filter_sub_categories[$sub_category['category_id']]['total'] : null;
            } elseif ($filter_param) {
                $status = false;
                $total  = $this->info['count_product'] ? 0 : null;
            } else {
                $status = true;
                $total  = $this->info['count_product'] ? $sub_category['total'] : null;
            }

            if ($this->info['count_product'] && $this->info['sort_by'] == 'product') {
                $sort_order[] = $total;
            } else {
                $sort_order[] = $sub_category['sort_order'];
            }

            if (!$data['status'] && $status) {
                $data['status'] = true;
            }

            $data['values'][] = array(
                'category_id' => $sub_category['category_id'],
                'name' => $sub_category['name'],
                'image' => $image,
                'total' => $total,
                'selected' => in_array($sub_category['category_id'], $selected),
                'status' => $status
            );
        }

        // Sort values
        if ($this->info['count_product'] && $this->info['sort_by'] == 'product') {
            array_multisort($sort_order, SORT_DESC, SORT_NUMERIC, array_column($data['values'], 'name'), $data['values']);
        } else {
            array_multisort($sort_order, SORT_ASC, SORT_NUMERIC, array_column($data['values'], 'name'), $data['values']);
        }

        // Search status
        if ($this->info['filter']['sub_category']['search'] == 1 || ($this->info['filter']['sub_category']['search'] == -1 && $this->info['search_on_limit'] < count($data['values']))) {
            $data['search'] = true;
        } else {
            $data['search'] = false;
        }

        return $data;
    }

    /**
     * Manufacturer filter
     * @return array
     */
    private function getManufacturerFilter(): array {
        // user selected
        if (!empty($this->request->get['mz_fm'])) {
            $selected = explode('.', $this->request->get['mz_fm']);
        } else {
            $selected = array();
        }

        $data = array(
            'type' => 'manufacturer',
            'status' => false,
            'sort_order' => $this->info['filter']['manufacturer']['sort_order'],
            'collapse' => $this->info['filter']['manufacturer']['collapse'],
            'input_type' => $this->info['filter']['manufacturer']['input_type'],
            'list_type' => $this->info['filter']['manufacturer']['list_type'],
            'image_width' => $this->info['filter']['manufacturer']['image_width'] ?: 30,
            'image_height' => $this->info['filter']['manufacturer']['image_height'] ?: 30,
            'values' => array()
        );

        $filter_param = $this->filter_param;
        unset($filter_param['filter_manufacturer_id']);

        // Get post filtered manufacturer
        $post_filter_manufacturers = array(); // available manufacturers after applying filter

        if ($filter_param) {
            $filter_data                = $filter_param;
            $filter_data['field_total'] = $this->info['count_product'];

            $manufacturers = $this->model_extension_module_mz_filter->getManufacturers($this->filter_product_table ?: $this->common_product_table, $filter_data);

            foreach ($manufacturers as $manufacturer) {
                $post_filter_manufacturers[$manufacturer['manufacturer_id']] = $manufacturer;
            }
        }

        // Get pre filtered manufacturer
        $manufacturers = $this->common_cache_key ? $this->cache->get($this->common_cache_key . '.manufacturer') : array();

        if (empty($manufacturers)) {
            $manufacturers = $this->model_extension_module_mz_filter->getManufacturers($this->common_product_table, ['field_total' => $this->info['count_product']]);

            if ($this->common_cache_key) {
                $this->cache->set($this->common_cache_key . '.manufacturer', $manufacturers);
            }
        }

        $sort_order = array();

        foreach ($manufacturers as $manufacturer) {
            $image = null;

            if ($this->info['filter']['manufacturer']['list_type'] !== 'text') {
                if (is_file(DIR_IMAGE . $manufacturer['image'])) {
                    $image = $this->model_tool_image->resize($manufacturer['image'], $this->info['filter']['manufacturer']['image_width'] ?: 30, $this->info['filter']['manufacturer']['image_height'] ?: 30);
                } else {
                    $image = $this->model_tool_image->resize('no_image.png', $this->info['filter']['manufacturer']['image_width'] ?: 30, $this->info['filter']['manufacturer']['image_height'] ?: 30);
                }
            }

            if (isset($post_filter_manufacturers[$manufacturer['manufacturer_id']])) {
                $status = true;
                $total  = $this->info['count_product'] ? $post_filter_manufacturers[$manufacturer['manufacturer_id']]['total'] : null;
            } elseif ($filter_param) {
                $status = false;
                $total  = $this->info['count_product'] ? 0 : null;
            } else {
                $status = true;
                $total  = $this->info['count_product'] ? $manufacturer['total'] : null;
            }

            if (!$data['status'] && $status) {
                $data['status'] = true;
            }

            if ($this->info['count_product'] && $this->info['sort_by'] == 'product') {
                $sort_order[] = $total;
            } else {
                $sort_order[] = $manufacturer['sort_order'];
            }

            $data['values'][] = array(
                'manufacturer_id' => $manufacturer['manufacturer_id'],
                'name' => $manufacturer['name'],
                'image' => $image,
                'total' => $total,
                'selected' => in_array($manufacturer['manufacturer_id'], $selected),
                'status' => $status
            );
        }

        // Sort values
        if ($this->info['count_product'] && $this->info['sort_by'] == 'product') {
            array_multisort($sort_order, SORT_DESC, SORT_NUMERIC, array_column($data['values'], 'name'), $data['values']);
        } else {
            array_multisort($sort_order, SORT_ASC, SORT_NUMERIC, array_column($data['values'], 'name'), $data['values']);
        }


        // Search status
        if ($this->info['filter']['manufacturer']['search'] == 1 || ($this->info['filter']['manufacturer']['search'] == -1 && $this->info['search_on_limit'] < count($data['values']))) {
            $data['search'] = true;
        } else {
            $data['search'] = false;
        }

        return $data;
    }

    /**
     * Search filter
     * @return array
     */
    private function getSearchFilter(): array {
        $keyword = '';

        // user selected
        if (!empty($this->request->get['mz_fq'])) {
            $keyword = $this->request->get['mz_fq'];
        }

        return array(
            'type' => 'search',
            'sort_order' => $this->info['filter']['search']['sort_order'],
            'collapse' => $this->info['filter']['search']['collapse'],
            'keyword' => $keyword
        );
    }

    /**
     * Stock status filter
     * @return array
     */
    private function getStockStatusFilter(): array {
        // user selected
        if (!empty($this->request->get['mz_fss'])) {
            $selected = explode('.', $this->request->get['mz_fss']);
        } else {
            $selected = array();
        }

        $data = array(
            'type' => 'stock_status',
            'status' => false,
            'sort_order' => $this->info['filter']['availability']['sort_order'],
            'collapse' => $this->info['filter']['availability']['collapse'],
            'input_type' => $this->info['filter']['availability']['input_type'],
            'values' => array()
        );

        $filter_param = $this->filter_param;
        unset($filter_param['filter_stock_status']);

        // Get after filtered, stock status
        $post_filter_stock_statuses = array(); // available stock status after applying filter

        if ($filter_param) {
            $filter_data                = $filter_param;
            $filter_data['field_total'] = $this->info['count_product'];

            $stock_statuses = $this->model_extension_module_mz_filter->getStockStatuses($this->filter_product_table ?: $this->common_product_table, $filter_data);

            foreach ($stock_statuses as $stock_status) {
                $post_filter_stock_statuses[$stock_status['stock_status_id']] = $stock_status;
            }
        }

        // Get before filtered, stock status
        $stock_statuses = $this->common_cache_key ? $this->cache->get($this->common_cache_key . '.stock_status') : array();

        if (empty($stock_statuses)) {
            $stock_statuses = $this->model_extension_module_mz_filter->getStockStatuses($this->common_product_table, ['field_total' => $this->info['count_product']]);

            if ($this->common_cache_key) {
                $this->cache->set($this->common_cache_key . '.stock_status', $stock_statuses);
            }
        }

        $sort_order = array();

        foreach ($stock_statuses as $stock_status) {
            if (isset($post_filter_stock_statuses[$stock_status['stock_status_id']])) {
                $status = true;
                $total  = $this->info['count_product'] ? $post_filter_stock_statuses[$stock_status['stock_status_id']]['total'] : null;
            } elseif ($filter_param) {
                $status = false;
                $total  = $this->info['count_product'] ? 0 : null;
            } else {
                $status = true;
                $total  = $this->info['count_product'] ? $stock_status['total'] : null;
            }

            if (!$data['status'] && $status) {
                $data['status'] = true;
            }

            if ($this->info['count_product'] && $this->info['sort_by'] == 'product') {
                $sort_order[] = $total;
            }

            $data['values'][] = array(
                'stock_status_id' => $stock_status['stock_status_id'],
                'name' => $stock_status['name'],
                'total' => $total,
                'selected' => in_array($stock_status['stock_status_id'], $selected),
                'status' => $status
            );
        }

        // In stock status
        if ($filter_param) {
            $total_in_stock = $this->model_extension_module_mz_filter->getTotalProductsByStock($this->filter_product_table ?: $this->common_product_table, true, $filter_param);
        } else {
            $total_in_stock = $this->model_extension_module_mz_filter->getTotalProductsByStock($this->filter_product_table ?: $this->common_product_table, true, $filter_param);
        }
        array_unshift($data['values'], array(
            'stock_status_id' => -1,
            'name' => $this->language->get('text_in_stock'),
            'total' => $this->info['count_product'] ? $total_in_stock : null,
            'selected' => in_array(-1, $selected),
            'status' => (bool) $total_in_stock
        ));
        if (!$data['status'] && $total_in_stock) {
            $data['status'] = true;
        }


        if ($this->info['count_product'] && $this->info['sort_by'] == 'product') {
            array_unshift($sort_order, $total_in_stock);
        }

        // Sort values
        if ($this->info['count_product'] && $this->info['sort_by'] == 'product') {
            array_multisort($sort_order, SORT_DESC, SORT_NUMERIC, array_column($data['values'], 'name'), $data['values']);
        }

        return $data;
    }

    /**
     * Availability filter
     * @return array
     */
    private function getAvailabilityFilter(): array {
        if ($this->info['filter']['availability']['stock_status']) {
            return $this->getStockStatusFilter();
        }

        // User selected
        if (isset($this->request->get['mz_fs'])) {
            $selected = $this->request->get['mz_fs'];
        } else {
            $selected = null;
        }

        $filter_param = $this->filter_param;
        unset($filter_param['filter_in_stock']);

        $total = array(); // Total product by stock status

        if ($filter_param) { // Get post filted availability statua
            $total['in_stock']     = $this->model_extension_module_mz_filter->getTotalProductsByStock($this->filter_product_table ?: $this->common_product_table, true, $filter_param);
            $total['out_of_stock'] = $this->model_extension_module_mz_filter->getTotalProductsByStock($this->filter_product_table ?: $this->common_product_table, false, $filter_param);
        }

        if (!$total) {
            $total['in_stock']     = $this->model_extension_module_mz_filter->getTotalProductsByStock($this->common_product_table, true);
            $total['out_of_stock'] = $this->model_extension_module_mz_filter->getTotalProductsByStock($this->common_product_table, false);
        }

        $values                 = array();
        $values['in_stock']     = array(
            'total' => $this->info['count_product'] ? $total['in_stock'] : null,
            'selected' => (!is_null($selected) && $selected) ? true : false,
            'status' => (bool) $total['in_stock']
        );
        $values['out_of_stock'] = array(
            'total' => $this->info['count_product'] ? $total['out_of_stock'] : null,
            'selected' => (!is_null($selected) && !$selected) ? true : false,
            'status' => (bool) $total['out_of_stock']
        );

        return array(
            'type' => 'availability',
            'sort_order' => $this->info['filter']['availability']['sort_order'],
            'collapse' => $this->info['filter']['availability']['collapse'],
            'values' => $values
        );
    }

    /**
     * Discount filter
     * @return array
     */
    private function getDiscountFilter(): array {
        // User selected
        if (!empty($this->request->get['mz_fd'])) {
            $selected = $this->request->get['mz_fd'];
        } else {
            $selected = 0;
        }

        $data = array(
            'type' => 'discount',
            'sort_order' => $this->info['filter']['discount']['sort_order'],
            'collapse' => $this->info['filter']['discount']['collapse'],
            'values' => array()
        );

        $discount_group = array();

        foreach (range(10, 50, 10) as $rate) {
            $data['values'][(string) $rate] = array(
                'name' => sprintf($this->language->get('text_discount_rate'), $rate),
                'value' => $rate,
                'selected' => ($rate == $selected) ? true : false,
                'total' => 0,
                'status' => false
            );

            $discount_group[] = $rate;
        }

        $discount_group = array_reverse($discount_group);

        $filter_param = $this->filter_param;
        unset($filter_param['filter_min_special_perc']);

        // Get post filtered discount
        $post_filter_discount = array();

        if ($filter_param) {
            $filter_data                   = $filter_param;
            $filter_data['field_total']    = $this->info['count_product'];
            $filter_data['discount_group'] = $discount_group;

            $discounts = $this->model_extension_module_mz_filter->getDiscounts($this->filter_product_table ?: $this->common_product_table, $filter_data);

            foreach ($discounts as $discount) {
                $post_filter_discount[$discount['discount']] = $discount;
            }
        }

        // Get discount before filter
        $discounts = $this->common_cache_key ? $this->cache->get($this->common_cache_key . '.discount') : array();

        if (empty($discounts)) {
            $filter_data                   = array();
            $filter_data['field_total']    = $this->info['count_product'];
            $filter_data['discount_group'] = $discount_group;

            $discounts = $this->model_extension_module_mz_filter->getDiscounts($this->common_product_table, $filter_data);

            if ($this->common_cache_key) {
                $this->cache->set($this->common_cache_key . '.discount', $discounts);
            }
        }

        foreach ($discounts as $discount) {

            if (isset($post_filter_discount[$discount['discount']])) {
                $status = true;
                $total  = $this->info['count_product'] ? $post_filter_discount[$discount['discount']]['total'] : null;
            } elseif ($filter_param) {
                $status = false;
                $total  = $this->info['count_product'] ? 0 : null;
            } else {
                $status = true;
                $total  = $this->info['count_product'] ? $discount['total'] : null;
            }

            if (isset($data['values'][$discount['discount']])) {
                $data['values'][$discount['discount']] = array(
                    'name' => sprintf($this->language->get('text_discount_rate'), $discount['discount']),
                    'value' => $discount['discount'],
                    'selected' => ($discount['discount'] == $selected) ? true : false,
                    'total' => $total,
                    'status' => $status
                );
            }
        }

        return $data;
    }

    /**
     * Rating filter
     * @return array
     */
    private function getRatingFilter(): array {
        // User selected
        if (!empty($this->request->get['mz_fr'])) {
            $selected = $this->request->get['mz_fr'];
        } else {
            $selected = 0;
        }

        $data = array(
            'type' => 'rating',
            'sort_order' => $this->info['filter']['rating']['sort_order'],
            'collapse' => $this->info['filter']['rating']['collapse'],
            'values' => array()
        );

        foreach (range(1, 5) as $rate) {
            $data['values'][$rate] = array(
                'rating' => $rate,
                'selected' => ($rate == $selected) ? true : false,
                'total' => 0,
                'status' => false
            );
        }

        $filter_param = $this->filter_param;
        unset($filter_param['filter_min_rating']);

        // Get post filtered rating
        $post_filter_rating = array();

        if ($filter_param) {
            $filter_data                = $filter_param;
            $filter_data['field_total'] = $this->info['count_product'];
            $ratings                    = $this->model_extension_module_mz_filter->getRatings($this->filter_product_table ?: $this->common_product_table, $filter_data);

            foreach ($ratings as $rating) {
                $post_filter_rating[(int) $rating['rating']] = $rating;
            }
        }


        // Get rating before filter
        $ratings = $this->common_cache_key ? $this->cache->get($this->common_cache_key . '.rating') : array();

        if (empty($ratings)) {
            $ratings = $this->model_extension_module_mz_filter->getRatings($this->common_product_table, ['field_total' => $this->info['count_product']]);

            if ($this->common_cache_key) {
                $this->cache->set($this->common_cache_key . '.rating', $ratings);
            }
        }

        foreach ($ratings as $rating) {
            if (isset($post_filter_rating[(int) $rating['rating']])) {
                $status = true;
                $total  = $this->info['count_product'] ? $post_filter_rating[(int) $rating['rating']]['total'] : null;
            } elseif ($filter_param) {
                $status = false;
                $total  = $this->info['count_product'] ? 0 : null;
            } else {
                $status = true;
                $total  = $this->info['count_product'] ? $rating['total'] : null;
            }

            $data['values'][(int) $rating['rating']] = array(
                'rating' => $rating['rating'],
                'selected' => ($rating['rating'] == $selected) ? true : false,
                'total' => $total,
                'status' => $status
            );
        }

        // Add total products in decending rating
        if ($this->info['count_product']) {
            foreach (range(1, 5) as $rate) {
                $data['values'][$rate]['total'] = array_sum(array_slice(array_column($data['values'], 'total'), $rate - 1));
            }
        }
        unset($data['values'][5]); // Remove 5 star rating because there is no products above 5 star

        $data['values'] = array_reverse($data['values']);

        return $data;
    }

    /**
     * Filter filter
     * @return array
     */
    private function getFilterFilter(): array {
        if ($this->info['filter']['filter']['require_category'] && !$this->category_id) {
            return array(); // Can not get filter without category
        }

        // User selected
        if (!empty($this->request->get['mz_ff'])) {
            $selected = explode('.', $this->request->get['mz_ff']);
        } else {
            $selected = array();
        }

        $filter_param = $this->filter_param;
        unset($filter_param['filter_filter']);

        // Get Post filtered filters
        $post_filter_filter = array();

        if ($filter_param) {
            $filter_data                = $filter_param;
            $filter_data['field_total'] = $this->info['count_product'];

            if ($this->info['filter']['filter']['require_category']) {
                $filter_category_id = $this->category_id;
            } else {
                $filter_category_id = 0;
            }

            $filters = $this->model_extension_module_mz_filter->getFilters($this->filter_product_table ?: $this->common_product_table, $filter_category_id, $filter_data);

            foreach ($filters as $filter) {
                $post_filter_filter[$filter['filter_id']] = $filter;
            }
        }

        // Get filters list before apply filter
        $filters = $this->common_cache_key ? $this->cache->get($this->common_cache_key . '.filter') : array();

        if (empty($filters)) {
            $filter_data                        = array();
            $filter_data['field_total']         = $this->info['count_product'];
            $filter_data['filter_sub_category'] = $this->sub_category;

            if ($this->info['filter']['filter']['require_category']) {
                $filter_category_id = $this->category_id;
            } else {
                $filter_category_id = 0;
            }

            $filters = $this->model_extension_module_mz_filter->getFilters($this->common_product_table, $filter_category_id, $filter_data);

            if ($this->common_cache_key) {
                $this->cache->set($this->common_cache_key . '.filter', $filters);
            }
        }


        // Organise filter values by filter group
        $filter_group_values = array();

        foreach ($filters as $filter) {

            if (isset($post_filter_filter[$filter['filter_id']])) {
                $status = true;
                $total  = $this->info['count_product'] ? $post_filter_filter[$filter['filter_id']]['total'] : null;
            } elseif ($filter_param) {
                $status = false;
                $total  = $this->info['count_product'] ? 0 : null;
            } else {
                $status = true;
                $total  = $this->info['count_product'] ? $filter['total'] : null;
            }

            $filter_group_values[$filter['filter_group_id']]['values'][] = array(
                'filter_id' => $filter['filter_id'],
                'name' => $filter['name'],
                'selected' => in_array($filter['filter_id'], $selected),
                'total' => $total,
                'sort_order' => $filter['sort_order'],
                'status' => $status
            );
        }

        unset($filters); // free memorary

        $filter_group_ids = array();

        foreach ($filter_group_values as $filter_group_id => $filter_group) {
            $filter_group_ids[] = $filter_group_id;
        }

        $filter_groups = $this->model_extension_module_mz_filter->getFilterGroups($filter_group_ids);

        foreach ($filter_groups as $key => &$filter_group) {
            if (isset($filter_group_values[$filter_group['filter_group_id']])) {

                // Sort values
                if ($this->info['count_product'] && $this->info['sort_by'] == 'product') {
                    array_multisort(array_column($filter_group_values[$filter_group['filter_group_id']]['values'], 'total'), SORT_DESC, SORT_NUMERIC, array_column($filter_group_values[$filter_group['filter_group_id']]['values'], 'name'), $filter_group_values[$filter_group['filter_group_id']]['values']);
                } else {
                    array_multisort(array_column($filter_group_values[$filter_group['filter_group_id']]['values'], 'sort_order'), SORT_ASC, SORT_NUMERIC, array_column($filter_group_values[$filter_group['filter_group_id']]['values'], 'name'), $filter_group_values[$filter_group['filter_group_id']]['values']);
                }

                $filter_group['type']     = 'filter';
                $filter_group['collapse'] = $this->info['filter']['filter']['collapse'];
                $filter_group['values']   = $filter_group_values[$filter_group['filter_group_id']]['values'];
                $filter_group['status']   = in_array(true, array_column($filter_group['values'], 'status'));

                // Search status
                if ($this->info['filter']['filter']['search'] == 1 || ($this->info['filter']['filter']['search'] == -1 && $this->info['search_on_limit'] < count($filter_group['values']))) {
                    $filter_group['search'] = true;
                } else {
                    $filter_group['search'] = false;
                }

                unset($filter_group_values[$filter_group['filter_group_id']]); // Free memorary
            } else {
                unset($filter_groups[$key]);
            }
        }

        return array_values($filter_groups);
    }

    /**
     * Custom filter
     * @return array
     */
    private function getCustomFilter(): array {
        if ($this->info['filter']['custom']['require_category'] && !$this->category_id) {
            return array(); // Can not get filter without category
        }

        // User selected
        if (!empty($this->request->get['mz_fc'])) {
            $selected = array();

            foreach (explode('c', $this->request->get['mz_fc']) as $custom_group) {
                $selected = array_merge($selected, explode('.', $custom_group));
            }
        } else {
            $selected = array();
        }

        $filter_values = $this->common_cache_key ? $this->cache->get($this->common_cache_key . '.custom') : array();

        if (empty($filter_values)) {
            $filter_data                        = array();
            $filter_data['field_total']         = $this->info['count_product'];
            $filter_data['filter_sub_category'] = $this->sub_category;

            if ($this->info['filter']['custom']['require_category']) {
                $filter_category_id = $this->category_id;
            } else {
                $filter_category_id = 0;
            }

            $filter_values = $this->model_extension_module_mz_filter->getCustomFilterValues($this->common_product_table, $filter_category_id, $filter_data);

            if ($this->common_cache_key) {
                $this->cache->set($this->common_cache_key . '.custom', $filter_values);
            }
        }


        // Organise filter values by filter
        $filters = array();

        foreach ($filter_values as $filter_value) {

            if ($this->filter_param) {
                $total  = $this->info['count_product'] ? 0 : null;
                $status = false;
            } else {
                $total  = $this->info['count_product'] ? $filter_value['total'] : null;
                $status = true;
            }

            $filters[$filter_value['filter_id']]['values'][] = array(
                'value_id' => $filter_value['value_id'],
                'name' => $filter_value['name'],
                'image' => $filter_value['image'],
                'sort_order' => $filter_value['sort_order'],
                'total' => $total,
                'status' => $status,
                'selected' => in_array($filter_value['value_id'], $selected)
            );
        }

        unset($filter_values); // free memorary

        $filter_ids = array(); // List of filter ids

        foreach ($filters as $filter_id => $filter) {
            $filter_ids[] = $filter_id;

            // Get post filtered status of values
            if (!$this->filter_param) {
                continue;
            }

            $filter_data                        = $this->filter_param;
            $filter_data['field_total']         = $this->info['count_product'];
            $filter_data['filter_sub_category'] = $this->sub_category;
            $filter_data['filter_filter_id']    = $filter_id;

            if (!empty($filter_data['filter_custom'])) { // Remove current filter value ids
                unset($filter_data['filter_custom'][$filter_id]);
            }

            $filter_value_data = array();

            if ($this->info['filter']['custom']['require_category']) {
                $filter_category_id = $this->category_id;
            } else {
                $filter_category_id = 0;
            }

            $filter_values = $this->model_extension_module_mz_filter->getCustomFilterValues($this->filter_product_table ?: $this->common_product_table, $filter_category_id, $filter_data);

            foreach ($filter_values as $filter_value) {
                $filter_value_data[$filter_value['value_id']] = $filter_value;
            }

            foreach ($filter['values'] as $key => $value) {
                if (isset($filter_value_data[$value['value_id']])) {
                    $value['status'] = true;
                    $value['total']  = $this->info['count_product'] ? $filter_value_data[$value['value_id']]['total'] : null;
                }

                $filter['values'][$key] = $value;
            }

            $filters[$filter_id] = $filter;
        }

        $custom_filters = $this->model_extension_module_mz_filter->getCustomFiltersByIds($filter_ids);

        $data = array();

        foreach ($custom_filters as $key => $custom_filter) {

            if (isset($filters[$custom_filter['filter_id']])) {
                $values = array();

                // Resize image
                if ($custom_filter['setting']['list_type'] !== 'text') {
                    foreach ($filters[$custom_filter['filter_id']]['values'] as $value) {

                        if (is_file(DIR_IMAGE . $value['image'])) {
                            $image = $this->model_tool_image->resize($value['image'], $custom_filter['setting']['value_image_width'] ?: 30, $custom_filter['setting']['value_image_height'] ?: 30);
                        } else {
                            $image = $this->model_tool_image->resize('no_image.png', $custom_filter['setting']['value_image_width'] ?: 30, $custom_filter['setting']['value_image_height'] ?: 30);
                        }

                        $value['image'] = $image;
                        $values[]       = $value;
                    }
                } else {
                    $values = $filters[$custom_filter['filter_id']]['values'];
                }

                // Search status
                if ($this->info['filter']['custom']['search'] == 1 || ($this->info['filter']['custom']['search'] == -1 && $this->info['search_on_limit'] < count($values))) {
                    $search = true;
                } else {
                    $search = false;
                }

                // Sort values
                if ($this->info['count_product'] && $this->info['sort_by'] == 'product') {
                    array_multisort(array_column($values, 'total'), SORT_DESC, SORT_NUMERIC, array_column($values, 'name'), $values);
                } else {
                    array_multisort(array_column($values, 'sort_order'), SORT_ASC, SORT_NUMERIC, array_column($values, 'name'), $values);
                }

                $data[] = array(
                    'filter_id' => $custom_filter['filter_id'],
                    'type' => 'custom',
                    'status' => in_array(true, array_column($values, 'status')),
                    'sort_order' => $custom_filter['sort_order'],
                    'name' => $custom_filter['name'],
                    'collapse' => $custom_filter['setting']['collapse'],
                    'input_type' => $custom_filter['setting']['input_type'],
                    'input_shape' => $custom_filter['setting']['input_shape'],
                    'list_type' => $custom_filter['setting']['list_type'],
                    'image_width' => $custom_filter['setting']['value_image_width'] ?: 30,
                    'image_height' => $custom_filter['setting']['value_image_height'] ?: 30,
                    'search' => $search,
                    'values' => $values
                );

                unset($filters[$custom_filter['filter_id']]); // Free memorary
            } else {
                unset($custom_filters[$key]);
            }
        }

        return $data;
    }

    /**
     * Create temporary table for products
     * and generate filter values
     */
    private function loadData(): void {
        $this->common_param = $this->commonParam();
        $this->filter_param = $this->param();


        // Create cache key
        if ($this->info && $this->info['cache'] && empty($this->common_param['search']) && empty($this->common_param['tag'])) {
            $this->common_cache_key = md5(serialize($this->common_param));
        }

        // Create temporary table for common filter
        $filter_param     = array_merge($this->common_param, $this->filter_param);
        $additional_field = array();

        if (($this->info && $this->info['filter']['price']['status']) || isset($filter_param['filter_min_price']) || isset($filter_param['filter_max_price']) || isset($filter_param['filter_special'])) {
            $additional_field[] = 'price';
        }
        if (($this->info && empty($this->request->get['manufacturer_id']) && $this->info['filter']['manufacturer']['status']) || isset($this->request->get['_f_manufacturer']) || isset($filter_param['filter_manufacturer_id'])) {
            $additional_field[] = 'p.manufacturer_id';
        }
        if (($this->info && $this->info['filter']['availability']['status'] && $this->info['filter']['availability']['stock_status']) || isset($this->request->get['_f_stock_status']) || isset($filter_param['filter_stock_status'])) {
            $additional_field[] = 'p.stock_status_id';
            $additional_field[] = 'p.quantity';
        }
        if (($this->info && $this->info['filter']['rating']['status']) || isset($this->request->get['_f_rating']) || isset($filter_param['filter_min_rating'])) {
            $additional_field[] = 'rating';
        }
        if (($this->info && $this->info['filter']['availability']['status']) || isset($this->request->get['_f_availability']) || isset($filter_param['filter_in_stock'])) {
            $additional_field[] = 'p.quantity';
        }
        if (($this->info && $this->info['filter']['discount']['status']) || isset($this->request->get['_f_discount']) || isset($filter_param['filter_min_special_perc'])) {
            $additional_field[] = 'special_perc';
        }

        $this->model_extension_maza_catalog_product->createTempTable($this->common_product_table, $additional_field, $this->common_param);

        // Create temporary table for filter scaler parameter
        if (!empty($this->filter_param['filter_min_price']) || !empty($this->filter_param['filter_max_price']) || !empty($this->filter_param['filter_name'])) {
            $filter_data = $this->common_param;

            if (!empty($this->filter_param['filter_min_price'])) {
                $filter_data['filter_min_price'] = $this->filter_param['filter_min_price'];
            }

            if (!empty($this->filter_param['filter_max_price'])) {
                $filter_data['filter_max_price'] = $this->filter_param['filter_max_price'];
            }

            if (!empty($this->filter_param['filter_name'])) {
                $filter_data['filter_name'] = $this->filter_param['filter_name'];
            }

            if (($key = array_search('p.price', $additional_field)) !== false) { // price field not require
                unset($additional_field[$key]);
            }

            $this->model_extension_maza_catalog_product->createTempTable('mz_temp_filter_product', $additional_field, $filter_data);

            $this->filter_product_table = 'mz_temp_filter_product';
        }

        // Get custom filter id base on user selected filter values
        if (!empty($this->filter_param['filter_custom'])) {
            $filter_value_ids = array();

            foreach ($this->filter_param['filter_custom'] as $value_ids) {
                $filter_value_ids = array_merge($filter_value_ids, $value_ids);
            }

            $filter_custom = array();

            $custom_filter_ids = $this->model_extension_module_mz_filter->getCustomFiltersIdByValue($filter_value_ids);

            foreach ($custom_filter_ids as $filter_data) {
                foreach ($this->filter_param['filter_custom'] as $value_ids) {
                    if (in_array($filter_data['value_id'], $value_ids)) {
                        $filter_custom[$filter_data['filter_id']] = $value_ids;
                        break(1);
                    }
                }
            }

            $this->filter_param['filter_custom'] = $filter_custom;
        }
    }

    private function dropData(): void {
        if ($this->filter_product_table) {
            $this->model_extension_maza_catalog_product->dropTempTable($this->filter_product_table);
        }

        $this->model_extension_maza_catalog_product->dropTempTable($this->common_product_table);
    }

    public function product(): void {
        $json = array();

        if ($this->request->get['target_route'] == 'product/manufacturer/info') {
            $this->load->language('product/manufacturer');
        } else {
            $this->load->language($this->request->get['target_route']);
        }

        $this->load->model('extension/module/mz_filter');
        $this->load->model('catalog/product');

        $this->loadData();

        $filter_data = array();

        if ($this->category_id) {
            $filter_data['filter_category_id'] = $this->category_id;
        }

        if ($this->sub_category) {
            $filter_data['filter_sub_category'] = $this->sub_category;
        }

        if (isset($this->request->get['search'])) {
            $filter_data['filter_name'] = $this->request->get['search'];
        }

        if (isset($this->request->get['tag'])) {
            $filter_data['filter_tag'] = $this->request->get['tag'];
        }

        if (isset($this->request->get['description'])) {
            $filter_data['filter_description'] = $this->request->get['description'];
        }

        if (isset($this->request->get['filter'])) {
            $filter_data['filter_filter'] = $this->request->get['filter'];
        }

        if (isset($this->request->get['manufacturer_id'])) {
            $filter_data['filter_manufacturer_id'] = (int) $this->request->get['manufacturer_id'];
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.sort_order';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (!empty($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (!empty($this->request->get['limit'])) {
            $limit = (int) $this->request->get['limit'];
        } else {
            $limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
        }

        $filter_data['sort']  = $sort;
        $filter_data['order'] = $order;
        $filter_data['start'] = ($page - 1) * $limit;
        $filter_data['limit'] = $limit;

        $filter_data = $this->filter_data($filter_data);

        $product_total = $this->model_extension_maza_catalog_product->getTotalProducts($filter_data);

        $results = $this->model_extension_maza_catalog_product->getProducts($filter_data);

        $data['products'] = array();

        foreach ($results as $result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }

            if ((float) $result['special']) {
                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $special = false;
            }

            if ($this->config->get('config_tax')) {
                $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
            } else {
                $tax = false;
            }

            if ($this->config->get('config_review_status')) {
                $rating = $result['rating'];
            } else {
                $rating = false;
            }

            $data['products'][] = array(
                'product_id' => $result['product_id'],
                'thumb' => $image,
                'name' => $result['name'],
                'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                'price' => $price,
                'special' => $special,
                'tax' => $tax,
                'rating' => $rating,
                'href' => $this->url->link('product/product', 'product_id=' . $result['product_id'])
            );
        }

        // Add extra meta data to product list
        $this->mz_hook->fetch('catalog_product_data_list', [$results, &$data['products']]);

        $url = '';

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['path'])) {
            $url .= '&path=' . $this->request->get['path'];
        }

        if (isset($this->request->get['search'])) {
            $url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['tag'])) {
            $url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['description'])) {
            $url .= '&description=' . $this->request->get['description'];
        }

        if (isset($this->request->get['category_id'])) {
            $url .= '&category_id=' . (int) $this->request->get['category_id'];
        }

        if (isset($this->request->get['manufacturer_id'])) {
            $url .= '&manufacturer_id=' . (int) $this->request->get['manufacturer_id'];
        }

        if (isset($this->request->get['sub_category'])) {
            $url .= '&sub_category=' . $this->request->get['sub_category'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $url = $this->url($url);

        $pagination        = new Pagination();
        $pagination->total = $product_total;
        $pagination->page  = $page;
        $pagination->limit = $limit;
        $pagination->url   = $this->url->link($this->request->get['target_route'], $url . '&page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

        $json['products'] = $this->mz_load->view($this->productTemplate(), $data, 'extension/module/mz_filter/product');

        $json['pagination'] = trim($this->mz_load->view($this->paginationTemplate(), $data, 'extension/module/mz_filter/product'));

        $json['filter'] = $this->getPostFilterValues();

        $this->dropData();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function productTemplate(): string {
        $templete = $this->getCache('mz_filter.product.' . $this->request->get['target_route'] . '.' . (int) $this->request->get['module_id']); // get static cache

        if ($templete) {
            return $templete;
        }

        $this->load->model('extension/maza/layout_builder');

        $setting = $this->model_extension_maza_layout_builder->getEntrySetting($this->request->get['product_entry_id']);

        if (!$setting) {
            return '';
        }

        $setting['entry_id'] = $setting['mz_suffix'] = (int) $this->request->get['product_entry_id'];

        $type = 'category';

        switch ($this->request->get['target_route']) {
            case 'product/category':
                $type = 'category';
                break;
            case 'product/special':
                $type = 'special';
                break;
            case 'product/search':
                $type = 'search';
                break;
            case 'product/manufacturer/info':
                $type = 'manufacturer_info';
                break;
        }

        $template = $this->load->controller('extension/mz_content/' . $type . '/products', $setting);

        $this->setCache('mz_filter.product.' . $this->request->get['target_route'] . '.' . (int) $this->request->get['module_id'], $template, false); //set static cache of templare

        return $template;
    }

    private function paginationTemplate(): string {
        $templete = $this->getCache('mz_filter.pagination.' . $this->request->get['target_route'] . '.' . (int) $this->request->get['module_id']); // get static cache

        if ($templete) {
            return $templete;
        }

        $this->load->model('extension/maza/layout_builder');

        $setting = $this->model_extension_maza_layout_builder->getEntrySetting($this->request->get['pagination_entry_id']);

        if (!$setting) {
            return '';
        }

        $setting['entry_id'] = $setting['mz_suffix'] = (int) $this->request->get['pagination_entry_id'];

        $type = 'category';

        switch ($this->request->get['target_route']) {
            case 'product/category':
                $type = 'category';
                break;
            case 'product/special':
                $type = 'special';
                break;
            case 'product/search':
                $type = 'search';
                break;
            case 'product/manufacturer/info':
                $type = 'manufacturer_info';
                break;
        }

        $template = $this->load->controller('extension/mz_content/' . $type . '/pagination', $setting);

        $this->setCache('mz_filter.pagination.' . $this->request->get['target_route'] . '.' . (int) $this->request->get['module_id'], $template, false); //set static cache of templare

        return $template;
    }

    private function getPostFilterValues(): array {
        $data = array();

        if (isset($this->request->get['_count_product'])) {
            $count_product = true;
        } else {
            $count_product = false;
        }

        // Manufacturer
        $data['manufacturer'] = array();

        if (isset($this->request->get['_f_manufacturer'])) {
            $filter_data                = $this->filter_param;
            $filter_data['field_total'] = $count_product;
            // unset($filter_data['filter_manufacturer_id']);

            $manufacturers = $this->model_extension_module_mz_filter->getManufacturers($this->filter_product_table ?: $this->common_product_table, $filter_data);

            foreach ($manufacturers as $manufacturer) {
                $data['manufacturer'][] = array(
                    'manufacturer_id' => $manufacturer['manufacturer_id'],
                    'total' => $count_product ? $manufacturer['total'] : null,
                );
            }
        }

        // Sub categories
        $data['sub_category'] = array();

        if (isset($this->request->get['_f_sub_category'])) {
            $filter_data                = $this->filter_param;
            $filter_data['field_total'] = $count_product;

            $sub_categories = $this->model_extension_module_mz_filter->getSubCategories($this->filter_product_table ?: $this->common_product_table, $this->category_id, $filter_data);

            foreach ($sub_categories as $sub_category) {
                $data['sub_category'][] = array(
                    'category_id' => $sub_category['category_id'],
                    'total' => $count_product ? $sub_category['total'] : null,
                );
            }
        }


        // Avaibility
        $data['availability'] = array();

        if (isset($this->request->get['_f_availability'])) {
            $filter_data = $this->filter_param;
            // unset($filter_data['filter_in_stock']);

            $data['availability']['in_stock']     = $this->model_extension_module_mz_filter->getTotalProductsByStock($this->filter_product_table ?: $this->common_product_table, true, $filter_data);
            $data['availability']['out_of_stock'] = $this->model_extension_module_mz_filter->getTotalProductsByStock($this->filter_product_table ?: $this->common_product_table, false, $filter_data);
        }

        // Stock status
        $data['stock_status'] = array();

        if (isset($this->request->get['_f_stock_status'])) {
            $filter_data                = $this->filter_param;
            $filter_data['field_total'] = $count_product;
            // unset($filter_data['filter_stock_status']);

            $stock_statuses = $this->model_extension_module_mz_filter->getStockStatuses($this->filter_product_table ?: $this->common_product_table, $filter_data);

            foreach ($stock_statuses as $stock_status) {
                $data['stock_status'][] = array(
                    'stock_status_id' => $stock_status['stock_status_id'],
                    'total' => $count_product ? $stock_status['total'] : null,
                );
            }

            $total_in_stock = $this->model_extension_module_mz_filter->getTotalProductsByStock($this->filter_product_table ?: $this->common_product_table, true, $filter_data);
            if ($total_in_stock > 0) {
                array_unshift($data['stock_status'], array(
                    'stock_status_id' => -1,
                    'total' => $total_in_stock,
                ));
            }
        }

        // Rating
        $data['rating'] = array();

        if (isset($this->request->get['_f_rating'])) {
            $filter_data                = $this->filter_param;
            $filter_data['field_total'] = $count_product;

            $ratings = $this->model_extension_module_mz_filter->getRatings($this->filter_product_table ?: $this->common_product_table, $filter_data);

            if ($ratings) {
                $max_rating = max(array_column($ratings, 'rating'));

                foreach (range(1, $max_rating) as $rate) {
                    if ($count_product) {
                        $total = array_reduce($ratings, function ($total, $rating) use ($rate) {
                            if ($rating['rating'] >= $rate) {
                                return $total + $rating['total'];
                            }
                            return $total;
                        }, 0);
                    } else {
                        $total = null;
                    }

                    $data['rating'][$rate] = array(
                        'rating' => $rate,
                        'total' => $total,
                    );
                }

                unset($data['rating'][5]); // Remove 5 star rating because there is no products above 5 star
            }
        }

        // Discount
        $data['discount'] = array();

        if (isset($this->request->get['_f_discount'])) {
            $filter_data                   = $this->filter_param;
            $filter_data['field_total']    = $count_product;
            $filter_data['discount_group'] = array_reverse(range(10, 50, 10));

            $discounts = $this->model_extension_module_mz_filter->getDiscounts($this->filter_product_table ?: $this->common_product_table, $filter_data);

            foreach ($discounts as $discount) {
                $data['discount'][] = array(
                    'value' => $discount['discount'],
                    'total' => $count_product ? $discount['total'] : null,
                );
            }
        }

        // Filter
        $data['filter'] = array();

        if (isset($this->request->get['_f_filter'])) {
            $filter_data                        = $this->filter_param;
            $filter_data['field_total']         = $count_product;
            $filter_data['filter_sub_category'] = $this->sub_category;

            if (isset($this->request->get['_filter_require_category'])) {
                $filter_category_id = $this->category_id;
            } else {
                $filter_category_id = 0;
            }

            $filters = $this->model_extension_module_mz_filter->getFilters($this->filter_product_table ?: $this->common_product_table, $filter_category_id, $filter_data);

            foreach ($filters as $filter) {
                $data['filter'][] = array(
                    'filter_id' => $filter['filter_id'],
                    'total' => $count_product ? $filter['total'] : null,
                );
            }
        }


        // custom
        $data['custom'] = array();

        if (isset($this->request->get['_f_custom'])) {
            $filter_data                        = $this->filter_param;
            $filter_data['field_total']         = $count_product;
            $filter_data['filter_sub_category'] = $this->sub_category;

            if (isset($this->request->get['_custom_require_category'])) {
                $filter_category_id = $this->category_id;
            } else {
                $filter_category_id = 0;
            }

            $filters = $this->model_extension_module_mz_filter->getCustomFilters($this->filter_product_table ?: $this->common_product_table, $filter_category_id, $filter_data);

            foreach ($filters as $filter) {
                $filter_data['filter_filter_id'] = $filter['filter_id'];

                if (isset($this->filter_param['filter_custom'])) {
                    $filter_data['filter_custom'] = $this->filter_param['filter_custom'];
                }

                if (isset($filter_data['filter_custom'][$filter['filter_id']])) {
                    unset($filter_data['filter_custom'][$filter['filter_id']]);
                }

                $filter_values = $this->model_extension_module_mz_filter->getCustomFilterValues($this->filter_product_table ?: $this->common_product_table, $filter_category_id, $filter_data);

                foreach ($filter_values as $filter_value) {
                    $data['custom'][] = array(
                        'value_id' => $filter_value['value_id'],
                        'total' => $count_product ? $filter_value['total'] : null,
                    );
                }
            }
        }

        return $data;
    }

    private function commonParam(): array {
        $filter_data = array();

        if ($this->category_id) {
            $filter_data['filter_category_id'] = $this->category_id;
        }

        if ($this->sub_category) {
            $filter_data['filter_sub_category'] = $this->sub_category;
        }

        if (isset($this->request->get['search'])) {
            $filter_data['filter_name'] = $this->request->get['search'];
        }

        if (isset($this->request->get['tag'])) {
            $filter_data['filter_tag'] = $this->request->get['tag'];
        }

        if (isset($this->request->get['description'])) {
            $filter_data['filter_description'] = $this->request->get['description'];
        }

        if (isset($this->request->get['filter'])) {
            $filter_data['filter_filter'] = $this->request->get['filter'];
        }

        if (isset($this->request->get['manufacturer_id'])) {
            $filter_data['filter_manufacturer_id'] = (int) $this->request->get['manufacturer_id'];
        }

        if ($this->route == 'product/special') { // Special page
            $filter_data['filter_special'] = 1;
        }

        return $filter_data;
    }

    /**
     * Get active filter parameter from URL
     */
    private function param(array $filter_data = array()): array {
        // Filter Price
        if (!empty($this->request->get['mz_fp'])) {
            $price = explode('p', $this->request->get['mz_fp']);

            if (isset($price[0])) { // Minimum price
                $filter_data['filter_min_price'] = (float) $price[0] / $this->currency->getValue($this->session->data['currency']);
            }

            if (isset($price[1])) { // Maximum price
                $filter_data['filter_max_price'] = (float) $price[1] / $this->currency->getValue($this->session->data['currency']);
            }
        }

        // Filter Manufacturer
        if (!empty($this->request->get['mz_fm'])) {
            $filter_data['filter_manufacturer_id'] = explode('.', $this->request->get['mz_fm']);
        }

        // Filter sub category
        if (!empty($this->request->get['mz_fsc'])) {
            $filter_data['filter_category_id']  = explode('.', $this->request->get['mz_fsc']);
            $filter_data['filter_sub_category'] = $this->sub_category;
        }

        // Filter search
        if (!empty($this->request->get['mz_fq'])) {
            $filter_data['filter_name'] = $this->request->get['mz_fq'];
        }

        // Filter availability
        if (isset($this->request->get['mz_fs'])) {
            $filter_data['filter_in_stock'] = $this->request->get['mz_fs'];

            if (isset($this->request->get['description'])) {
                $filter_data['filter_description'] = $this->request->get['description'];
            }
        }

        // Filter stock status
        if (!empty($this->request->get['mz_fss'])) {
            $filter_data['filter_stock_status'] = explode('.', $this->request->get['mz_fss']);
        }

        // Filter rating
        if (!empty($this->request->get['mz_fr'])) {
            $filter_data['filter_min_rating'] = $this->request->get['mz_fr'];
        }

        // Filter discount
        if (!empty($this->request->get['mz_fd'])) {
            $filter_data['filter_min_special_perc'] = $this->request->get['mz_fd'];
        }

        // Filter Filter
        if (!empty($this->request->get['mz_ff'])) {
            $filter_data['filter_filter'] = explode('.', $this->request->get['mz_ff']);
        }

        // Filter custom
        if (!empty($this->request->get['mz_fc'])) {
            $filter_data['filter_custom'] = array_map(function ($values) {
                return explode('.', $values);
            }, explode('c', $this->request->get['mz_fc']));
        }

        // For special product page
        if ($this->route == 'product/special') {
            $filter_data['filter_special'] = 1;
        }

        return $filter_data;
    }

    public function filter_data(array $filter_data = array()): array {
        if (!isset($filter_data['filter_sub_category']) && $this->mz_skin_config->get('catalog_sub_category_product')) {
            $filter_data['filter_sub_category'] = true;
        }

        // For special product page
        if ($this->route == 'product/special' && $filter_data['sort'] == 'ps.price') {
            $filter_data['sort'] = 'p.price';
        }

        return $this->param($filter_data);
    }

    /**
     * Add filter parameter in URL
     */
    public function url(string $url = ''): string {

        // Filter Price
        if (!empty($this->request->get['mz_fp'])) {
            $url .= '&mz_fp=' . $this->request->get['mz_fp'];
        }

        // Filter Manufacturer
        if (!empty($this->request->get['mz_fm'])) {
            $url .= '&mz_fm=' . $this->request->get['mz_fm'];
        }

        // Filter sub category
        if (!empty($this->request->get['mz_fsc'])) {
            $url .= '&mz_fsc=' . $this->request->get['mz_fsc'];
        }

        // Filter search
        if (!empty($this->request->get['mz_fq'])) {
            $url .= '&mz_fq=' . urlencode(html_entity_decode($this->request->get['mz_fq'], ENT_QUOTES, 'UTF-8'));
        }

        // Filter availability
        if (isset($this->request->get['mz_fs']) && $this->request->get['mz_fs'] !== '') {
            $url .= '&mz_fs=' . $this->request->get['mz_fs'];
        }

        // Filter stock status
        if (!empty($this->request->get['mz_fss'])) {
            $url .= '&mz_fss=' . $this->request->get['mz_fss'];
        }

        // Filter rating
        if (!empty($this->request->get['mz_fr'])) {
            $url .= '&mz_fr=' . $this->request->get['mz_fr'];
        }

        // Filter discount
        if (!empty($this->request->get['mz_fd'])) {
            $url .= '&mz_fd=' . $this->request->get['mz_fd'];
        }

        // Filter filter
        if (!empty($this->request->get['mz_ff'])) {
            $url .= '&mz_ff=' . $this->request->get['mz_ff'];
        }

        // Filter custom
        if (!empty($this->request->get['mz_fc'])) {
            $url .= '&mz_fc=' . $this->request->get['mz_fc'];
        }

        return $url;
    }

    private function translate(): void {
        // Heading title
        $this->language->set('heading_title', maza\getOfLanguage($this->info['title']));

        // Price
        if ($this->info['filter']['price']['title'] && !empty($this->info['filter']['price']['title'][$this->config->get('config_language_id')])) {
            $this->language->set('text_price', $this->info['filter']['price']['title'][$this->config->get('config_language_id')]);
        }

        // sub category
        if ($this->info['filter']['sub_category']['title'] && !empty($this->info['filter']['sub_category']['title'][$this->config->get('config_language_id')])) {
            $this->language->set('text_sub_category', $this->info['filter']['sub_category']['title'][$this->config->get('config_language_id')]);
        }

        // Manufacturer
        if ($this->info['filter']['manufacturer']['title'] && !empty($this->info['filter']['manufacturer']['title'][$this->config->get('config_language_id')])) {
            $this->language->set('text_manufacturer', $this->info['filter']['manufacturer']['title'][$this->config->get('config_language_id')]);
        }

        // Search
        if ($this->info['filter']['search']['title'] && !empty($this->info['filter']['search']['title'][$this->config->get('config_language_id')])) {
            $this->language->set('text_search', $this->info['filter']['search']['title'][$this->config->get('config_language_id')]);
        }
        if ($this->info['filter']['search']['placeholder'] && !empty($this->info['filter']['search']['placeholder'][$this->config->get('config_language_id')])) {
            $this->language->set('text_search_placeholder', $this->info['filter']['search']['placeholder'][$this->config->get('config_language_id')]);
        }

        // Availability
        if ($this->info['filter']['availability']['title'] && !empty($this->info['filter']['availability']['title'][$this->config->get('config_language_id')])) {
            $this->language->set('text_availability', $this->info['filter']['availability']['title'][$this->config->get('config_language_id')]);
        }

        // Discount
        if ($this->info['filter']['discount']['title'] && !empty($this->info['filter']['discount']['title'][$this->config->get('config_language_id')])) {
            $this->language->set('text_discount', $this->info['filter']['discount']['title'][$this->config->get('config_language_id')]);
        }

        // Rating
        if ($this->info['filter']['rating']['title'] && !empty($this->info['filter']['rating']['title'][$this->config->get('config_language_id')])) {
            $this->language->set('text_rating', $this->info['filter']['rating']['title'][$this->config->get('config_language_id')]);
        }
    }

}