<?php
class ControllerExtensionMzWidgetNavbar extends maza\layout\Widget {
    private static $instance_count = 0;
    
	public function index(array $setting) {
        $data = array();
        
        $data['orientation']    = $setting['widget_orientation'];
        $data['collapsible']    = $setting['widget_collapsible'];
        $data['hoverable']      = $setting['widget_hoverable'];
        $data['collapse']       = $setting['widget_collapse'];
        
        if($setting['widget_collapse'] < 0 && $this->mz_document->getRoute() === 'common/home'){
            $data['collapse'] = 0;
        } elseif($setting['widget_collapse'] < 0){
            $data['collapse'] = 1;
        }
        
        if($setting['widget_expand']){
            $data['expand'] = $setting['widget_expand'] == 'xs' ? 'navbar-expand' : 'navbar-expand-' . $setting['widget_expand'];
        } else {
            $data['expand'] = null;
        }
        $data['expand_breakpoint'] = $setting['widget_expand'] == 'xs' ? null : $setting['widget_expand'];
        
        
        
        // Color
        $data['bg_color']       = $setting['widget_bg_color'];
        $data['text_color']         = $setting['widget_text_color'];
        
        // Brand
        $data['brand_title']        = maza\getOfLanguage($setting['widget_brand']);
        
        // Brand icon
        $data['brand_icon_width']    = $setting['widget_brand_icon_width'];
        $data['brand_icon_height']   = $setting['widget_brand_icon_height'];
        $data['brand_icon_size']     = $setting['widget_brand_icon_size'];
        
        // font image
        $data['brand_icon_font']    = maza\getOfLanguage($setting['widget_brand_icon_font']);
        
        // svg image
        $brand_icon_svg     =   maza\getOfLanguage($setting['widget_brand_icon_svg']);
        if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $brand_icon_svg)){
            $data['brand_icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $brand_icon_svg);
        } else {
            $data['brand_icon_svg'] = false;
        }
        
        // Image
        $brand_icon_image = maza\getOfLanguage($setting['widget_brand_icon_image']);
        if(is_file(DIR_IMAGE . $brand_icon_image)){
            list($width, $height) = $this->model_extension_maza_image->getEstimatedSize($brand_icon_image, $setting['widget_brand_icon_width'], $setting['widget_brand_icon_height']);
            $data['brand_icon_image'] = $this->model_tool_image->resize($brand_icon_image, $width, $height);
            
            // Overwrite default(200x200)
            $data['brand_icon_width'] = $width;
            $data['brand_icon_height'] = $height;
        } else {
            $data['brand_icon_image'] = false;
        }

        if ($setting['widget_brand_url_link_code']) {
            $data['brand_url'] = $this->model_extension_maza_common->createLink($setting['widget_brand_url_link_code']);
        } else {
            $data['brand_url'] = array();
        }
        
        // Menu
        if($setting['widget_menu_id']){
            $data['menu'] = $this->load->controller('extension/maza/menu', $setting['widget_menu_id']);
        } else {
            $data['menu'] = false;
        }
        
        $data['mz_suffix'] = $setting['mz_suffix']??self::$instance_count++;
        
        if($data['menu']){
            return $this->load->view('extension/mz_widget/navbar', $data);
        }
	}
        
}
