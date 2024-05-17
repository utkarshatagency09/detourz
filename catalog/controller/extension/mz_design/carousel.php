<?php
class ControllerExtensionMzDesignCarousel extends maza\layout\Design {
    private static $instance_count = 0;

	public function index(array $setting) {
        $data = array();
        
        #carousel
        $data['fade']       = (int)$setting['design_fade'];
        $data['interval']   = (int)$setting['design_interval'];
        $data['loop']       = (int)$setting['design_loop'];
        $data['pagination'] = (int)$setting['design_pagination'];
        $data['navigation'] = (int)$setting['design_navigation'];
        $data['pause']      = $setting['design_pause'];
        $data['keyboard']   = (int)$setting['design_keyboard'];
        $data['lazy_loading'] = $setting['design_lazy_loading'];
        
        // slides
        $data['slides']  =  array();
        
        foreach($setting['design_slides'] as $slide){
            if(!$slide['status']) continue;
            
            // caption
            $caption = maza\getOfLanguage($slide['caption']);

            // alt
            $alt = maza\getOfLanguage($slide['alt']);
            
            $width = $setting['design_image_width'];
            $height = $setting['design_image_height'];

            // svg image
            $slide_svg = maza\getOfLanguage($slide['slide_svg']);
            if($slide_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $slide_svg)){
                $svg = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $slide_svg;
            } else {
                $svg = false;
            }

            // bitmap Image
            $slide_image = maza\getOfLanguage($slide['slide_image']);
            if(!$svg && (is_file(DIR_IMAGE . $slide_image) || $width || $height)){
                list($width, $height) = $this->model_extension_maza_image->getEstimatedSize($slide_image, $width, $height);
                $image = $this->model_tool_image->resize($slide_image, $width, $height);
                
                // Srcset
                $image_srcset = $this->model_extension_maza_image->getSrcSet($setting['design_image_srcset'], $slide_image, $width, $height);
                $image_srcset_sizes = $this->model_extension_maza_image->getSrcSetSize($setting['design_image_srcset'], $width);
            } else {
                $image = $image_srcset = $image_srcset_sizes = false;
            }

            // Url
            $url = array();

            if ($slide['url_link_code']) {
                $url = $this->model_extension_maza_common->createLink($slide['url_link_code']);
            }
            
            $data['slides'][] = array(
                'image'              => $image,
                'image_srcset'       => $image_srcset,
                'image_srcset_sizes' => $image_srcset_sizes,
                'svg'                => $svg,
                'width'              => $width,
                'height'             => $height,
                'alt'                => $alt,
                'caption'            => $caption,
                'url'                => $url,
                'sort_order'         => $slide['sort_order']
            );
        }
        
        $data['mz_suffix']   = $setting['mz_suffix']??self::$instance_count++;
        
        if($data['slides']){
            // Sort carousel
            array_multisort(array_column($data['slides'], 'sort_order'), SORT_ASC, SORT_NUMERIC, $data['slides']);
            
            return $this->load->view('extension/mz_design/carousel', $data);
        }
	}
}
