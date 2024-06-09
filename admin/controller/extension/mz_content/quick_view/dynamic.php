<?php
class ControllerExtensionMzContentQuickViewDynamic extends maza\layout\Content {
    public function index(): void {
        $this->load->language('extension/mz_content/quick_view/dynamic');
        $this->load->model('extension/maza/opencart');
        $this->load->model('extension/maza/extension');
        $this->load->model('extension/maza/content_builder');

        $setting = array();
        $setting['content_status']          = 0;
        $setting['content_type']            = 'content';
        $setting['content_content']         = '';
        $setting['content_content_data']    = '';
        $setting['content_design']          = '';
        $setting['content_design_data']     = '';
        $setting['content_widget']          = '';
        $setting['content_widget_data']     = '';
        $setting['content_module']          = '';
        $setting['content_content_builder_id'] = '';
        $setting['content_opposite']        = '0';

        $setting['content_condition_price_min'] = '';
        $setting['content_condition_price_max'] = '';
        $setting['content_condition_quantity_min'] = '';
        $setting['content_condition_quantity_max'] = '';
        $setting['content_condition_rating_min'] = '';
        $setting['content_condition_rating_max'] = '';
        $setting['content_condition_stock_status'] = '';
        $setting['content_condition_special'] = '';
        $setting['content_condition_reward'] = '';
        $setting['content_condition_points'] = '';
        $setting['content_condition_tax_class_id'] = '';
        $setting['content_condition_product'] = [];
        $setting['content_condition_manufacturer'] = [];
        
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }

        $content_builder_info =  $this->model_extension_maza_content_builder->getContent($data['content_content_builder_id']);
        
        if($content_builder_info){
            $data['content_builder_name'] =  $content_builder_info['name'];
        } else {
            $data['content_builder_name'] =  '';
        }

        // Types
        $data['types'] = array(
            array('code' => 'content', 'text' => $this->language->get('text_content')),
            array('code' => 'design', 'text' => $this->language->get('text_design')),
            array('code' => 'widget', 'text' => $this->language->get('text_widget')),
            array('code' => 'module', 'text' => $this->language->get('text_module')),
            array('code' => 'content_builder', 'text' => $this->language->get('text_content_builder')),
        );

        // Content
        $data['contents'] = $this->model_extension_maza_extension->getContentsOfType('quick_view');
                
        // widgets
        $data['widgets'] = $this->model_extension_maza_extension->getWidgets();
        
        // Designs
        $data['designs'] = $this->model_extension_maza_extension->getDesigns();
        
        // Modules
        $data['extensions'] = array();

        // Get a list of installed modules
        $extensions = $this->model_extension_maza_opencart->getInstalled('module');

        // Add all the modules which have multiple settings for each module
        foreach ($extensions as $code) {
            $this->load->language('extension/module/' . $code, 'extension');

            if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                $heading_title = $this->language->get('heading_title');
            } else {
                $heading_title = $this->language->get('extension')->get('heading_title');
            }

            $module_data = array();

            $modules = $this->model_extension_maza_opencart->getModulesByCode($code);

            foreach ($modules as $module) {
                $module_data[] = array(
                    'name' => strip_tags($module['name']),
                    'code' => $code . '.' .  $module['module_id']
                );
            }

            if ($this->config->has('module_' . $code . '_status') || $this->config->has($code . '_status') || $module_data) {
                $data['extensions'][] = array(
                    'name'   => strip_tags($heading_title),
                    'code'   => $code,
                    'module' => $module_data
                );
            }
        }

        $this->load->model('localisation/stock_status');
		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

        $this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        $this->load->model('catalog/product');

        $data['data_products'] = array();

        foreach ($data['content_condition_product'] as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
                $data['data_products'][] = array(
                    'product_id' => $product_info['product_id'],
                    'name'       => $product_info['name'],
                );
            }
        }

        $this->load->model('catalog/manufacturer');

        $data['data_manufacturers'] = array();

        foreach ($data['content_condition_manufacturer'] as $manufacturer_id) {
            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);

            if ($manufacturer_info) {
                $data['data_manufacturers'][] = array(
                    'manufacturer_id' => $manufacturer_info['manufacturer_id'],
                    'name'       => $manufacturer_info['name'],
                );
            }
        }
        
        $data['user_token']  = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_content/quick_view/dynamic', $data));
    }

    /**
     * Merger Event
     */
    public function merger(String $route, array $param): void {
        list($code, $data) = $param;

        // Unregister event to stop infinite loop
        $this->event->unregister('model/extension/maza/install/merger/content/after', 'extension/mz_content/quick_view/dynamic/merger');
        $this->event->unregister('model/extension/maza/install/merger/design/after', 'extension/mz_content/quick_view/dynamic/merger');

        $type = array_slice(explode('/', $route), -1)[0]; // get last element from array

        $this->model_extension_maza_install_merger->content('quick_view.dynamic', function($setting) use($type, $code, $data) {
            $isEdited = false;

            if($type == 'content' && $setting['content_content'] === $code){
                $content_data = array();
                parse_str(html_entity_decode($setting['content_content_data']), $content_data);

                $setting['content_content_data'] = htmlentities(http_build_query(\maza\array_merge_subsequence($data, $content_data)));

                $isEdited = true;
            }

            if($type == 'design' && $setting['content_design'] === $code){
                $design_data = array();
                parse_str(html_entity_decode($setting['content_design_data']), $design_data);

                if(is_callable($data)){
                    $result = $data($design_data);
                } else {
                    $result = \maza\array_merge_subsequence($data, $design_data);
                }

                if($result){
                    $setting['content_design_data'] = htmlentities(http_build_query($result));

                    $isEdited = true;
                }
                
            }

            if($isEdited){
                return $setting;
            }
            return false;
        });

        // Restore event
        $this->event->register('model/extension/maza/install/merger/content/after', new Action('extension/mz_content/quick_view/dynamic/merger'));
        $this->event->register('model/extension/maza/install/merger/design/after', new Action('extension/mz_content/quick_view/dynamic/merger'));
    }
}
