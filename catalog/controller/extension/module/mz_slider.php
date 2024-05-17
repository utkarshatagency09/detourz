<?php
class ControllerExtensionModuleMzSlider extends maza\layout\Module {
    private static $instance_count = 0;

    private static $mz_suffix;
        
    public function index(array $module_setting) {
        // Extension will not work without maza engine
        if(!$this->config->get('maza_status') || empty($module_setting['module_id'])){
            return;
        }
        
        $this->load->model('extension/maza/module');
        $this->load->model('extension/maza/extension');
        
        // Setting
        $setting = $this->model_extension_maza_module->getSetting($module_setting['module_id']);
        
        if(!$setting || !$setting['status']){
            return; // Exit if disabled
        }
        
        if($this->config->get('maza_cdn')){
            $this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
            $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.js', 'footer');
        } else {
            $this->document->addStyle('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
            $this->document->addScript('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.js', 'footer');
        }
        
        if($setting['lazy_loading']){
            $this->document->addScript('//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js', 'footer');
            $this->document->addScript('//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.plugins.min.js', 'footer');
        }

        self::$mz_suffix = $module_setting['mz_suffix']??self::$instance_count++;
        
        $data = array();
        
        $data['heading_title'] = maza\getOfLanguage($setting['title']);
        
        $data['slide_effect']        = $setting['slide_effect'];
        $data['auto_height']         = (int)$setting['auto_height'];
        $data['speed']               = (int)$setting['speed'];
        $data['loop']                = $setting['loop'];
        $data['lazy_loading']        = $setting['lazy_loading'];
        $data['slide_per_column']    = $setting['slide_per_column'];
        $data['space_between_slide'] = $setting['space_between_slide'];
        $data['navigation']          = $setting['navigation'];
        $data['pagination']          = $setting['pagination'];
        $data['pagination_type']     = $setting['pagination_type'];
        $data['autoplay_status']     = $setting['autoplay_status'];
        $data['autoplay_delay']      = (int)$setting['autoplay_delay'];
        $data['keyboard_control']    = $setting['keyboard_control'];
        $data['mousewheel_control']  = $setting['mousewheel_control'];
        $data['simulate_touch']      = $setting['simulate_touch'];
        $data['url_target']          = $setting['url_target'];
        $data['slide_image_width']   = (int)$setting['slide_image_width'];
        $data['slide_image_height']  = (int)$setting['slide_image_height'];
        $data['column_xs']           = $setting['column_xs'];
        $data['column_sm']           = $setting['column_sm'];
        $data['column_md']           = $setting['column_md'];
        $data['column_lg']           = $setting['column_lg'];
        $data['column_xl']           = $setting['column_xl'];
        
        // Srcset size
        $data['srcset_sizes'] = $this->model_extension_maza_image->getSrcSetSize($setting['slide_image_srcset'], $setting['slide_image_width']);
        
        // Style
        $data['breakpoint_sm']        =  $this->mz_skin_config->get('style_breakpoints')['sm'];
        $data['breakpoint_md']        =  $this->mz_skin_config->get('style_breakpoints')['md'];
        $data['breakpoint_lg']        =  $this->mz_skin_config->get('style_breakpoints')['lg'];
        $data['breakpoint_xl']        =  $this->mz_skin_config->get('style_breakpoints')['xl'];
        
        // Slides
        $data['slides']              = $this->getSlides($setting);
        
        // Navigation icon
        $data['nav_icon_width'] = $setting['nav_icon_width'];
        $data['nav_icon_height']= $setting['nav_icon_height'];
        $data['nav_icon_size']  = $setting['nav_icon_size'];
        $data['nav_icon_font']  = false;
        $data['nav_icon_svg']   = false;
        $data['nav_icon_image'] = false;
        
        $data['nav_icon_font'] = maza\getOfLanguage($setting['nav_icon_font']);

        $nav_icon_svg = maza\getOfLanguage($setting['nav_icon_svg']);
        if($nav_icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $nav_icon_svg)){
            $data['nav_icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $nav_icon_svg);
        }
        
        $nav_icon_image = maza\getOfLanguage($setting['nav_icon_image']);
        if($nav_icon_image && is_file(DIR_IMAGE . $nav_icon_image)){
            list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($nav_icon_image, $setting['nav_icon_width'], $setting['nav_icon_height']);
            
            $data['nav_icon_width'] = $image_width;
            $data['nav_icon_height'] = $image_height;
            
            $data['nav_icon_image'] = $this->model_tool_image->resize($nav_icon_image, $image_width, $image_height);
        }
        
        if($setting['lazy_loading'] && $setting['slide_image_width'] && $setting['slide_image_height']){
            $data['transparent']  =  $this->model_extension_maza_image->transparent($setting['slide_image_width'], $setting['slide_image_height']);
        }
        
        $data['mz_suffix'] = self::$mz_suffix;
        
        if($data['slides']){
            return $this->load->view('extension/module/mz_slider', $data);
        }
    }
    
    /**
     * Get slides list
     * @param array $setting module setting
     * @return array slide list
     */
    private function getSlides(array $setting): array {
        $slides = array();
        
        foreach($setting['slides'] as $key => $slide){
            if(!$slide['status']){
                continue;
            }
            
            // Image
            if($slide['type'] == 'image'){
                // caption
                $caption = maza\getOfLanguage($slide['caption']);
                if($caption){
                    $image['caption'] = html_entity_decode($caption);
                } else {
                    $image['caption'] = '';
                }

                // alt
                $image['alt'] = maza\getOfLanguage($slide['alt']);
                
                $image['width'] = $setting['slide_image_width'];
                $image['height'] = $setting['slide_image_height'];

                // svg image
                $slide_svg = maza\getOfLanguage($slide['slide_svg']);
                if($slide_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $slide_svg)){
                    $image['svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $slide_svg;
                } else {
                    $image['svg'] = false;
                }

                // bitmap Image
                $slide_image = maza\getOfLanguage($slide['slide_image']);
                if(is_file(DIR_IMAGE . $slide_image) || $setting['slide_image_width'] || $setting['slide_image_height']){
                    list($width, $height) = $this->model_extension_maza_image->getEstimatedSize($slide_image, $setting['slide_image_width'], $setting['slide_image_height']);
                    
                    $image['width'] = $width;
                    $image['height'] = $height;

                    $image['image'] = $this->model_tool_image->resize($slide_image, $width, $height);
                    $image['srcset'] = $this->model_extension_maza_image->getSrcSet($setting['slide_image_srcset'], $slide_image, $width, $height);
                } else {
                    $image['image'] = false;
                }

                // Url
                if ($slide['url_link_code']) {
                    $image['url'] = $this->model_extension_maza_common->createLink($slide['url_link_code']);
                } else {
                    $image['url'] = array();
                }
            } else {
                $image = array();
            }

            // Content
            $content = '';

            // HTML
            if($slide['type'] == 'html'){
                $content = html_entity_decode(maza\getOfLanguage($slide['html']));
            }

            // Module
            if($slide['type'] == 'module' && $slide['module']){
                $content = $this->model_extension_maza_extension->getModuleOutput($slide['module'], self::$mz_suffix . $key);
            }

            // Content
            if($slide['type'] == 'content' && $slide['content_id']){
                $content = $this->load->controller('extension/maza/layout_builder', ['group' => 'content_builder', 'group_owner' => $slide['content_id'], 'suffix' => 'mz_slider.' . self::$instance_count]);
            }

            if($image || $content){
                $slides[] = array(
                    'sort_order' => $slide['sort_order'],
                    'image'      => $image,
                    'content'    => $content
                );
            }
        }
        
        // Sort slide
        array_multisort(array_column($slides, 'sort_order'), SORT_ASC, SORT_NUMERIC, $slides);
        
        return $slides;
    }
}