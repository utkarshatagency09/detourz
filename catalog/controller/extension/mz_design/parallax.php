<?php
class ControllerExtensionMzDesignParallax extends maza\layout\Design {
	public function index(array $setting): string {
        $data = array();
        
        $data['parallax_height']     = $setting['design_parallax_height'];
        
        // Caption
        $data['parallax_caption'] = maza\getOfLanguage($setting['design_parallax_caption']);
        
        // parallax svg image
        $design_parallax_svg = maza\getOfLanguage($setting['design_parallax_svg']);
        if($design_parallax_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $design_parallax_svg)){
            $data['parallax_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $design_parallax_svg;
        } else {
            $data['parallax_svg'] = false;
        }
        
        // parallax bitmap image
        $design_parallax_image = maza\getOfLanguage($setting['design_parallax_image']);
        if(!empty($design_parallax_image) && is_file(DIR_IMAGE . $design_parallax_image)){
            $data['parallax_image'] = $this->config->get('mz_store_url') . 'image/' . $design_parallax_image;
        } else {
            $data['parallax_image'] = false;
        }
        
        if($data['parallax_caption'] || $data['parallax_svg'] || $data['parallax_image']){
            return $this->load->view('extension/mz_design/parallax', $data);
        } else {
            return '';
        }
	}
}
