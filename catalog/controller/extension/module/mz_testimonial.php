<?php
class ControllerExtensionModuleMzTestimonial extends maza\layout\Module {
    private static $instance_count = 0;

    private $setting = array();

    public function index(array $module_setting) {
        // Extension will not work without maza engine
        if(!$this->config->get('maza_status') || empty($module_setting['module_id'])){
            return null;
        }
        
        $this->load->language('extension/module/mz_testimonial');
        $this->load->model('extension/maza/module');
        $this->load->model('extension/maza/testimonial');
        
        // Setting
        $this->setting = $this->model_extension_maza_module->getSetting($module_setting['module_id']);
        
        if(!$this->setting || !$this->setting['status']){
            return null;
        }
        
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
        
        $data['testimonials']   = $this->getTestimonials();
        
        $data['mz_suffix'] = $module_setting['mz_suffix']??self::$instance_count++;
        
        if($data['testimonials']){
            return $this->mz_load->view($this->template($module_setting), $data, 'extension/module/mz_testimonial');
        }
    }
    
    /**
     * Get layout template based on configuration
     * @param array $module_setting module setting
     */
    private function template(array $module_setting): string {
        $carousel_nav_icon_svg = maza\getOfLanguage($this->setting['carousel_nav_icon_svg']);
        if(empty(maza\getOfLanguage($this->setting['carousel_nav_icon_font'])) && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg)){
            $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg);
        }

        $testimonial_quote_icon_svg = maza\getOfLanguage($this->mz_skin_config->get('testimonial_quote_icon_svg'));
        if(empty(maza\getOfLanguage($this->mz_skin_config->get('testimonial_quote_icon_font'))) && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $testimonial_quote_icon_svg)){
            $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $testimonial_quote_icon_svg);
        }
        
        // Cache file of layout template
        $templete = $this->getCache('mz_testimonial.' . $module_setting['module_id']); // get static cache
        
        if($templete){
            return $templete;
        }
    
        // Heading Title
        $data['heading_title'] = maza\getOfLanguage($this->setting['title']);
        
        // Data
        if($this->setting['button_add_status']){
            $data['add_testimonial'] = $this->url->link('extension/maza/testimonial');
        }
        if($this->setting['button_view_status']){
            $data['view_testimonial'] = $this->url->link('extension/maza/testimonial');
        }
        
        // layout
        $data['column_xs']           = $this->setting['column_xs'];
        $data['column_sm']           = $this->setting['column_sm'];
        $data['column_md']           = $this->setting['column_md'];
        $data['column_lg']           = $this->setting['column_lg'];
        $data['column_xl']           = $this->setting['column_xl'];
        $data['image_width']         = $this->setting['image_width'];
        $data['image_height']        = $this->setting['image_height'];
        $data['collapsed']           = $this->setting['collapsed'];
        $data['lazy_loading']        = $this->setting['lazy_loading'];
        
        // Style
        $data['gutter_width']         =  $this->mz_skin_config->get('style_gutter_width');
        $data['breakpoint_sm']        =  $this->mz_skin_config->get('style_breakpoints')['sm'];
        $data['breakpoint_md']        =  $this->mz_skin_config->get('style_breakpoints')['md'];
        $data['breakpoint_lg']        =  $this->mz_skin_config->get('style_breakpoints')['lg'];
        $data['breakpoint_xl']        =  $this->mz_skin_config->get('style_breakpoints')['xl'];
        
        // Carousel
        $data['carousel_status']     = $this->setting['carousel_status'];
        $data['carousel_autoplay']   = $this->setting['carousel_autoplay'];
        $data['carousel_pagination'] = $this->setting['carousel_pagination'];
        $data['carousel_loop']       = $this->setting['carousel_loop'];
        $data['carousel_row']        = $this->setting['carousel_row'];
        $data['carousel_nav_status'] = $this->setting['carousel_nav_status'];
        
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
        
        // Quote icon
        $data['testimonial_quote_icon_width']   = $this->mz_skin_config->get('testimonial_quote_icon_width');
        $data['testimonial_quote_icon_height']  = $this->mz_skin_config->get('testimonial_quote_icon_height');
        $data['testimonial_quote_icon_size']    = $this->mz_skin_config->get('testimonial_quote_icon_size');
        $data['testimonial_quote_icon_font']    = false;
        $data['testimonial_quote_icon_svg']     = false;
        $data['testimonial_quote_icon_image']   = false;
        
        $data['testimonial_quote_icon_font']    = maza\getOfLanguage($this->mz_skin_config->get('testimonial_quote_icon_font'));

        $testimonial_quote_icon_svg = maza\getOfLanguage($this->mz_skin_config->get('testimonial_quote_icon_svg'));
        if($testimonial_quote_icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $testimonial_quote_icon_svg)){
            $data['testimonial_quote_icon_svg'] =  $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $testimonial_quote_icon_svg);
            
        }
        
        $testimonial_quote_icon_image = maza\getOfLanguage($this->mz_skin_config->get('testimonial_quote_icon_image'));
        if($testimonial_quote_icon_image && is_file(DIR_IMAGE . $testimonial_quote_icon_image)){
            list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($testimonial_quote_icon_image, $this->mz_skin_config->get('testimonial_quote_icon_width'), $this->mz_skin_config->get('testimonial_quote_icon_height'));
            
            $data['testimonial_quote_icon_width'] = $image_width;
            $data['testimonial_quote_icon_height'] = $image_height;
            
            $data['testimonial_quote_icon_image'] = $this->model_tool_image->resize($testimonial_quote_icon_image, $image_width, $image_height);
        }
        
        // Setting
        $data['testimonial_rating']     = $this->mz_skin_config->get('testimonial_list_rating');
        $data['testimonial_image']      = $this->mz_skin_config->get('testimonial_list_image');
        $data['testimonial_extra']      = $this->mz_skin_config->get('testimonial_list_extra');
        $data['testimonial_timestamp']  = $this->mz_skin_config->get('testimonial_list_timestamp');
        
        if($this->setting['lazy_loading']){
            $data['transparent']  =  $this->model_extension_maza_image->transparent($this->setting['image_width'], $this->setting['image_height']);
        }
        
        $template = $this->load->view('extension/module/mz_testimonial', $data);
        
        $this->setCache('mz_testimonial.' . $module_setting['module_id'], $template, false); //set static cache of templare
        
        return $template;
    }
    
    /**
     * Get testimonial list
     * @return array testimonials
     */
    private function getTestimonials(): array {
        $testimonials = array();
        
        // Featured source
        if($this->setting['testimonial_source'] == 'featured'){
            $this->setting['featured_testimonial'] = array_slice($this->setting['featured_testimonial'], 0, $this->setting['limit']);
            
            foreach($this->setting['featured_testimonial'] as $testimonial_id){
                $testimonial_info = $this->model_extension_maza_testimonial->getTestimonial($testimonial_id);
                
                if($testimonial_info){
                    $testimonials[] = $testimonial_info;
                }
            }
        }
        // Latest testimonial
        elseif($this->setting['testimonial_source'] == 'latest'){
            $filter = array();
            
            $filter['sort'] = 't.date_added';
            $filter['order'] = 'DESC';
            $filter['start'] = 0;
            $filter['limit'] = $this->setting['limit'];
            
            $testimonials = $this->model_extension_maza_testimonial->getTestimonials($filter);
        }
        // filter testimonial
        elseif($this->setting['testimonial_source'] == 'filter'){
            $filter = array();
            
            $filter['filter_min_rating'] = $this->setting['filter_min_rating'];
            $filter['sort'] = $this->setting['filter_sort'];
            $filter['order'] = $this->setting['filter_order'];
            $filter['start'] = 0;
            $filter['limit'] = $this->setting['limit'];
            
            $testimonials = $this->model_extension_maza_testimonial->getTestimonials($filter);
        }
        
        $data = array();
        
        foreach ($testimonials as $testimonial_info) {
            if(is_file(DIR_IMAGE . $testimonial_info['image'])){
                $image = $this->model_tool_image->resize($testimonial_info['image'], $this->setting['image_width'], $this->setting['image_height']);
            } else {
                $image = $this->url->link('extension/maza/tool/image/profile', 'letter=' . $testimonial_info['name'][0] . '&width=' . $this->setting['image_width'] . '&height=' . $this->setting['image_height']);
            }
            
            $data[] = array(
                    'testimonial_id'  => $testimonial_info['testimonial_id'],
                    'thumb'       => $image,
                    'author'      => $testimonial_info['name'],
                    'extra'       => $testimonial_info['extra'],
                    'rating'      => $testimonial_info['rating'],
                    'description' => trim(strip_tags(html_entity_decode($testimonial_info['description'], ENT_QUOTES, 'UTF-8'))),
                    'timestamp'   => date('d M Y', strtotime($testimonial_info['date_added'])),
            );
        }
        
        return $data;
    }
        
}