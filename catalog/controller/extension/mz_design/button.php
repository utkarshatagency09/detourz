<?php
class ControllerExtensionMzDesignButton extends maza\layout\Design {
        
    public function index(array $setting): string {
        $data = array();
        
        $data['color']            = $setting['design_color'];
        $data['outline']          = $setting['design_outline'];
        $data['size']             = $setting['design_size'];
        $data['width']            = $setting['design_width'];
        
        $data['url_target']       = $setting['design_url_target'];

        if ($setting['design_url_link_code']) {
            $data['url'] = $this->model_extension_maza_common->createLink($setting['design_url_link_code']);
        } else {
            $data['url'] = array();
        }
        
        // Button
        // Button name
        $data['name'] = maza\getOfLanguage($setting['design_name']);

        // Button icon
        $data['icon_width']     = $setting['design_icon_width'];
        $data['icon_height']    = $setting['design_icon_height'];
        $data['icon_size']      = $setting['design_icon_size'];
        $data['icon_position']  = $setting['design_icon_position'];
        $data['icon_font']      = false;
        $data['icon_svg']       = false;
        $data['icon_image']     = false;
        
        // font icon
        $data['icon_font'] = maza\getOfLanguage($setting['design_icon_font']);

        // svg image
        $design_icon_svg = maza\getOfLanguage($setting['design_icon_svg']);
        if($design_icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $design_icon_svg)){
            $data['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $design_icon_svg);
        }

        // Image
        $design_icon_image = maza\getOfLanguage($setting['design_icon_image']);
        if($design_icon_image && is_file(DIR_IMAGE . $design_icon_image)){
            list($icon_width, $icon_height) = $this->model_extension_maza_image->getEstimatedSize($design_icon_image, $setting['design_icon_width'], $setting['design_icon_height']);
            $data['icon_image'] = $this->model_tool_image->resize($design_icon_image, $icon_width, $icon_height);
        }
        
        return $this->load->view('extension/mz_design/button', $data);
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
