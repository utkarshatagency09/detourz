<?php
class ControllerExtensionMzDesignImage extends maza\layout\Design {
	public function index(array $setting): string {
        $data = array();
        
        #Alt
        $data['alt'] = maza\getOfLanguage($setting['design_alt']);
        
        #Caption
        $data['caption'] = maza\getOfLanguage($setting['design_caption']);
        
        #Style
        $data['style_class']     = $setting['design_style'];
        
        #Image
        $data['icon_width']      = $setting['design_icon_width'];
        $data['icon_height']     = $setting['design_icon_height'];
        $data['icon_size']       = $setting['design_icon_size'];
        $data['icon_font']       = false;
        $data['icon_svg']        = false;
        $data['icon_image']      = false;
        
        // font icon
        $data['icon_font'] = maza\getOfLanguage($setting['design_icon_font']);
            
        // svg image
        $icon_svg = maza\getOfLanguage($setting['design_icon_svg']);
        if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)){
            $data['icon_svg'] = $this->config->get('mz_store_url') . 'image/' . substr(MZ_CONFIG::$DIR_SVG_IMAGE, strlen(DIR_IMAGE)) . $icon_svg;
        }

        // Image
        $icon_image = maza\getOfLanguage($setting['design_icon_image']);
        if($icon_image && (is_file(DIR_IMAGE . $icon_image) || $setting['design_icon_width'] || $setting['design_icon_height'])){
            list($width, $height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $setting['design_icon_width'], $setting['design_icon_height']);
            
            $data['icon_width']     = $width;
            $data['icon_height']    = $height;
            
            $data['icon_image'] = $this->model_tool_image->resize($icon_image, $width, $height);
        }
        
        #URL
        $data['url_target'] = $setting['design_url_target'];
        
        if ($setting['design_url_link_code']) {
            $data['url'] = $this->model_extension_maza_common->createLink($setting['design_url_link_code']);
        } else {
            $data['url'] = array();
        }

        $data['lazy_loading'] = $setting['design_lazy_loading'];
        
        if($data['icon_font'] || $data['icon_svg'] || $data['icon_image']){
            return $this->load->view('extension/mz_design/image', $data);
        } else {
            return '';
        }
    }
}
