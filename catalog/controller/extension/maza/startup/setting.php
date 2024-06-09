<?php
class ControllerExtensionMazaStartupSetting extends Controller {
	public function index(): void {
        // Layout
        $this->load->model('design/layout');
        
        $route = $this->mz_document->getRoute();

        $layout_id = 0;

        if ($route == 'product/category' && isset($this->request->get['path'])) {
            $this->load->model('catalog/category');

            $path = explode('_', (string)$this->request->get['path']);

            $layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
        }

        if ($route == 'product/product' && isset($this->request->get['product_id'])) {
            $this->load->model('catalog/product');

            $layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
        }

        if ($route == 'product/manufacturer/info' && isset($this->request->get['manufacturer_id'])) {
            $this->load->model('extension/maza/catalog/manufacturer');

            $layout_id = $this->model_extension_maza_catalog_manufacturer->getManufacturerLayoutId($this->request->get['manufacturer_id']);
        }

        if ($route == 'information/information' && isset($this->request->get['information_id'])) {
            $this->load->model('catalog/information');

            $layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
        }

        if (!$layout_id) {
            $layout_id = $this->model_design_layout->getLayout($route);
        }

        if (!$layout_id) {
            $layout_id = $this->config->get('config_layout_id');
        }
        
        $this->load->model('extension/maza/layout_builder');
        
        $layout_info = $this->model_extension_maza_layout_builder->getLayout($layout_id);
        
        if($layout_info){
            $this->config->set('mz_layout_type', $layout_info['mz_layout_type']);

            if ($layout_info['mz_override_skin_id']) {
                $this->mz_theme_config->set('skin_id', $layout_info['mz_override_skin_id']);
            }
        }
        
        $this->config->set('mz_layout_id', $layout_id);

        // Page builder skin
        if ($this->mz_document->isRoute('extension/maza/page') && isset($this->request->get['page_id'])) {
            $this->load->model('extension/maza/page');

            $page_skin_id = $this->model_extension_maza_page->getPageSkinId($this->request->get['page_id']);

            if ($page_skin_id) {
                $this->mz_theme_config->set('skin_id', $page_skin_id);
            }
        }

        // Override skin
        if(isset($this->request->get['mz_skin_id']) && (isset($this->session->data['user_id']) || $this->config->get('maza_developer_mode'))){
            $this->mz_theme_config->set('skin_id', (int)$this->request->get['mz_skin_id']);
        }
        
        // Website domain
        if ($this->request->server['HTTPS']) {
            $this->config->set('mz_store_url', $this->config->get('config_ssl'));
        } else {
            $this->config->set('mz_store_url', $this->config->get('config_url'));
        }

        // Webp
        if (!$this->mz_browser->isSupportedWebp()) {
            $this->config->set('maza_webp', false);
        }

        // set Local
        $language_data = $this->cache->get('catalog.language');
        if(isset($language_data[$this->session->data['language']])){
            setlocale(LC_TIME, explode(',', $language_data[$this->session->data['language']]['locale']));
        }

        // Skin
        $this->load->model('extension/maza/skin');
        
        $skin_info = $this->model_extension_maza_skin->getSkin($this->mz_theme_config->get('skin_id'));
        
        if($skin_info){
            if($skin_info['parent_skin_id']){
                $parent_skin_info = $this->model_extension_maza_skin->getSkin($skin_info['parent_skin_id']);
                $skin_info['skin_code'] = $parent_skin_info['skin_code'];
            }
            
            $skin_setting = $this->model_extension_maza_skin->getSetting($this->mz_theme_config->get('skin_id'));
            
            $this->mz_skin_config->set('skin_id', $skin_info['skin_id']);
            $this->mz_skin_config->set('skin_code', $skin_info['skin_code']);
        } else {
            throw new Exception('Please install skin');
        }
        
        // Header
        $header_info = $this->model_extension_maza_skin->getHeader($skin_setting['skin_header_id']);
        
        if($header_info){
            if($header_info['parent_header_id']){
                $parent_header_info = $this->model_extension_maza_skin->getHeader($header_info['parent_header_id']);
            } else {
                $parent_header_info = array();
            }
            
            if($parent_header_info){
                $this->mz_skin_config->set('skin_header_code', $parent_header_info['code']);
            } else {
                $this->mz_skin_config->set('skin_header_code', $header_info['code']);
            }
        } else {
            throw new Exception('Missing skin header');
        }
        
        // Footer
        $footer_info = $this->model_extension_maza_skin->getFooter($skin_setting['skin_footer_id']);
        
        if($footer_info){
            if($footer_info['parent_footer_id']){
                $parent_footer_info = $this->model_extension_maza_skin->getFooter($footer_info['parent_footer_id']);
            } else {
                $parent_footer_info = array();
            }
            
            if($parent_footer_info){
                $this->mz_skin_config->set('skin_footer_code', $parent_footer_info['code']);
            } else {
                $this->mz_skin_config->set('skin_footer_code', $footer_info['code']);
            }
        } else {
            throw new Exception('Missing skin footer');
        }

        // Load config file
        MZ_CONFIG::load();
        
        // Skin setting
        $this->mz_skin_config->load('skin/' . $skin_info['skin_code']);
        
        foreach ($skin_setting as $key => $value) {
            $this->mz_skin_config->set($key, $value);
        }
        
        ## Overwrite global setting ##
        
        // Category
        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_category_width', $this->mz_skin_config->get('catalog_category_image_width'));
        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_category_height', $this->mz_skin_config->get('catalog_category_image_height'));
        
        // Product image
        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_thumb_width', $this->mz_skin_config->get('catalog_thumb_image_width'));
        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_thumb_height', $this->mz_skin_config->get('catalog_thumb_image_height'));

        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_popup_width', $this->mz_skin_config->get('catalog_popup_image_width'));
        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_popup_height', $this->mz_skin_config->get('catalog_popup_image_height'));

        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_additional_width', $this->mz_skin_config->get('catalog_thumb_image_width') / $this->mz_skin_config->get('catalog_additional_image_slides'));
        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_additional_height', $this->mz_skin_config->get('catalog_thumb_image_height') / $this->mz_skin_config->get('catalog_additional_image_slides'));

        $catalog_additional_image_srcset = array();
        foreach($this->mz_skin_config->get('catalog_thumb_image_srcset') as $key => $value){
            if($value){
                $catalog_additional_image_srcset[$key] = $value / $this->mz_skin_config->get('catalog_additional_image_slides');
            } else {
                $catalog_additional_image_srcset[$key] = $value;
            }
        }
        $this->mz_skin_config->set('catalog_additional_image_srcset', $catalog_additional_image_srcset);

        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_related_width', $this->mz_skin_config->get('catalog_related_image_width'));
        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_related_height', $this->mz_skin_config->get('catalog_related_image_height'));
        
        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_product_width', $this->mz_skin_config->get('catalog_grid_image_width'));
        $this->config->set('theme_' . $this->config->get('config_theme') . '_image_product_height', $this->mz_skin_config->get('catalog_grid_image_height'));
	}
}
