<?php
class ControllerExtensionModuleMzBrand extends maza\layout\Module {
    private static $instance_count = 0;

    private $setting = array();

    public function index(array $module_setting) {
        // Extension will not work without maza engine
        if(!$this->config->get('maza_status') || empty($module_setting['module_id'])){
            return;
        }
        
        $this->load->model('extension/maza/module');
        $this->load->model('extension/maza/catalog/manufacturer');
        $this->load->model('catalog/manufacturer');
        
        // Setting
        $this->setting = $this->model_extension_maza_module->getSetting($module_setting['module_id']);
        
        if(!$this->setting || !$this->setting['status']){
            return;
        }
        $this->setting['module_id'] = $module_setting['module_id'];
        
        if($this->setting['carousel_status']){
            if($this->config->get('maza_cdn')){
                $this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
                $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.js', 'footer');
            } else {
                $this->document->addStyle('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
                $this->document->addScript('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.js', 'footer');
            }
        }
        
        if($this->setting['lazy_loading']){
            $this->document->addScript('//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js', 'footer');
            $this->document->addScript('//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.plugins.min.js', 'footer');
        }
        
        $data = array();
        
        // Manufacturers
        $data['manufacturers']   = $this->getManufacturers();
        
        $data['mz_suffix'] = $module_setting['mz_suffix']??self::$instance_count++;
        
        if($data['manufacturers']){
            return $this->mz_load->view($this->template($data), $data, 'extension/module/mz_brand');
        }
    }
        
    private function template($data = array()){
        $carousel_nav_icon_svg = maza\getOfLanguage($this->setting['carousel_nav_icon_svg']);
        if(empty(maza\getOfLanguage($this->setting['carousel_nav_icon_font'])) && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg)){
            $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg);
        }
        
        $templete = $this->getCache('mz_brand.' . $this->setting['module_id']); // get static cache
        
        if($templete){
            return $templete;
        }
        
        // Settings
        $data['heading_title'] = maza\getOfLanguage($this->setting['title']);
        
        $data['show_content']        = $this->setting['show_content'];
        $data['image_position']      = $this->setting['image_position'];
        $data['carousel_status']     = $this->setting['carousel_status'];
        $data['carousel_autoplay']   = $this->setting['carousel_autoplay'];
        $data['carousel_pagination'] = $this->setting['carousel_pagination'];
        $data['carousel_loop']       = $this->setting['carousel_loop'];
        $data['carousel_row']        = $this->setting['carousel_row'];
        $data['column_xs']           = $this->setting['column_xs'];
        $data['column_sm']           = $this->setting['column_sm'];
        $data['column_md']           = $this->setting['column_md'];
        $data['column_lg']           = $this->setting['column_lg'];
        $data['column_xl']           = $this->setting['column_xl'];
        $data['image_width']         = $this->setting['image_width'];
        $data['image_height']        = $this->setting['image_height'];
        $data['lazy_loading']        = $this->setting['lazy_loading'];
        
        // Carousel navigation icon
        $data['carousel_nav_icon_width'] = $this->setting['carousel_nav_icon_width'];
        $data['carousel_nav_icon_height'] = $this->setting['carousel_nav_icon_height'];
        $data['carousel_nav_icon_size'] = $this->setting['carousel_nav_icon_size'];
        $data['carousel_nav_icon_font'] = false;
        $data['carousel_nav_icon_svg']  = false;
        $data['carousel_nav_icon_image'] = false;

        $data['carousel_nav_icon_font'] = maza\getOfLanguage($this->setting['carousel_nav_icon_font']);
        
        $carousel_nav_icon_svg = maza\getOfLanguage($this->setting['carousel_nav_icon_svg']);
        if($carousel_nav_icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg)){
            $data['carousel_nav_icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg);
            
        }
        
        $carousel_nav_icon_image = maza\getOfLanguage($this->setting['carousel_nav_icon_image']);
        if($carousel_nav_icon_image && is_file(DIR_IMAGE . $carousel_nav_icon_image)){
            list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($carousel_nav_icon_image, $this->setting['carousel_nav_icon_width'], $this->setting['carousel_nav_icon_height']);
            
            $data['carousel_nav_icon_width'] = $image_width;
            $data['carousel_nav_icon_height'] = $image_height;
            
            $data['carousel_nav_icon_image'] = $this->model_tool_image->resize($carousel_nav_icon_image, $image_width, $image_height);
        }
        
        // Style
        $data['gutter_width']         =  $this->mz_skin_config->get('style_gutter_width');
        $data['breakpoint_sm']        =  $this->mz_skin_config->get('style_breakpoints')['sm'];
        $data['breakpoint_md']        =  $this->mz_skin_config->get('style_breakpoints')['md'];
        $data['breakpoint_lg']        =  $this->mz_skin_config->get('style_breakpoints')['lg'];
        $data['breakpoint_xl']        =  $this->mz_skin_config->get('style_breakpoints')['xl'];
        
        if($this->setting['lazy_loading']){
            $data['transparent']      =  $this->model_extension_maza_image->transparent($this->setting['image_width'], $this->setting['image_height']);
        }
        
        $template = $this->load->view('extension/module/mz_brand', $data);
        
        $this->setCache('mz_brand.' . $this->setting['module_id'], $template, false); //set static cache of templare
        
        return $template;
    }
        
    /**
     * Get manufacturer list
     * @return array manufacturers
     */
    private function getManufacturers() {
        $manufacturers = array();
        
        // Selected source
        if($this->setting['brand_source'] == 'selected'){
            foreach($this->setting['selected_manufacturer'] as $menufacturer_id){
                $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($menufacturer_id);
                
                if($manufacturer_info){
                    $manufacturers[] = $manufacturer_info;
                }
            }
        }

        // Featured
        if($this->setting['brand_source'] == 'featured'){
            $manufacturers = $this->model_extension_maza_catalog_manufacturer->getFeaturedManufacturers(20);
        }

        // filter sorce
        elseif($this->setting['brand_source'] == 'filter'){
            $filter = array();
            
            if($this->setting['filter_auto_filter'] && isset($this->request->get['category_id'])){
                $filter['filter_category_id'] = $this->request->get['category_id'];
            } elseif(isset($this->setting['filter_category'])){
                $filter['filter_category_id'] = $this->setting['filter_category'];
            }
            
            $filter['filter_sub_category'] = $this->setting['filter_sub_category'];
            
            if(isset($this->setting['filter_filter'])){
                $filter['filter_filter'] = $this->setting['filter_filter'];
            }
            
            $filter['sort'] = $this->setting['filter_sort'];
            $filter['order'] = $this->setting['filter_order'];
            $filter['start'] = 0;
            $filter['limit'] = $this->setting['filter_limit'];
            
            $manufacturers = $this->model_extension_maza_catalog_manufacturer->getManufacturers($filter);
        }
        
        $data = array();
        
        foreach ($manufacturers as $manufacturer_info) {
            if(is_file(DIR_IMAGE . $manufacturer_info['image'])){
                $image = $this->model_tool_image->resize($manufacturer_info['image'], $this->setting['image_width'], $this->setting['image_height']);
            } else {
                $image = $this->model_tool_image->resize('no_image.png', $this->setting['image_width'], $this->setting['image_height']);
            }
            
            $data[] = array(
                'manufacturer_id'   => $manufacturer_info['manufacturer_id'],
                'name'              => $manufacturer_info['name'],
                'image'             => $image,
                'href'               => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_info['manufacturer_id'])
            );
        }
        
        return $data;
    }
        
}