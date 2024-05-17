<?php
class ControllerExtensionMzDesignLink extends maza\layout\Design {
    public function index(array $setting): string {
        $data = array();
        
        $data['url_target']     = $setting['design_url_target'];
        $data['url_nofollow']   = $setting['design_url_nofollow'];

        if ($setting['design_url_link_code']) {
            $data['url'] = $this->model_extension_maza_common->createLink($setting['design_url_link_code']);
        } else {
            $data['url'] = array();
        }
        
        # Link
        // Link name
        $data['name'] = maza\getOfLanguage($setting['design_name']);

        // Link icon
        $data['icon_width']     = $setting['design_icon_width'];
        $data['icon_height']    = $setting['design_icon_height'];
        $data['icon_size']      = $setting['design_icon_size'];
        $data['icon_position']  = $setting['design_icon_position'];
        $data['icon_font']      = false;
        $data['icon_svg']       = false;
        $data['icon_image']     = false;
        
        // font icon
        $data['icon_font'] =  maza\getOfLanguage($setting['design_icon_font']);

        // svg image
        $icon_svg = maza\getOfLanguage($setting['design_icon_svg']);
        if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)){
            $data['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
        }

        // Image
        $icon_image = maza\getOfLanguage($setting['design_icon_image']);
        if(is_file(DIR_IMAGE . $icon_image)){
            list($icon_width, $icon_height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $setting['design_icon_width'], $setting['design_icon_height']);
            
            $data['image_width']     = $icon_width;
            $data['image_height']    = $icon_height;

            $data['icon_image'] = $this->model_tool_image->resize($icon_image, $icon_width, $icon_height);
        }
        
        $data['show']      = $setting['design_show'];
        
        return $this->load->view('extension/mz_design/link', $data);
    }
    
    /**
     * Change default setting
     */
    public function getSettings(): array {
        $setting['xl'] = $setting['lg'] = $setting['md'] = 
        $setting['sm'] = $setting['xs'] = array(
            'design_flex_grow' => 0,
            'design_flex_shrink' => 0,
        );
        
        return \maza\array_merge_subsequence(parent::getSettings(), $setting);
    }
}
