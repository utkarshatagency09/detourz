<?php
class ControllerExtensionMzDesignDropDown extends maza\layout\Design {
    public function index(array $setting): string {
        $data = array();
    
        $data['heading_title']  = maza\getOfLanguage($setting['design_title']);
        $data['title_icon_position'] = $setting['design_title_icon_position'];
        $data['title_icon_width']    = $setting['design_title_icon_width'];
        $data['title_icon_height']   = $setting['design_title_icon_height'];
        $data['title_icon_size']     = $setting['design_title_icon_size'];
        $data['title_show']          = $setting['design_title_show'];
        
        $data['type']                = $setting['design_type'];
        $data['direction']           = $setting['design_direction'];
        $data['alignment']           = $setting['design_alignment'];

        $data['btn_color']           = $setting['design_btn_color'];
        $data['btn_size']            = $setting['design_btn_size'];
        $data['btn_outline']         = $setting['design_btn_outline'];

        // Font icon
        if($setting['design_title_show'] !== 'text'){
            $data['title_icon_font'] = maza\getOfLanguage($setting['design_title_icon_font']);
        } else {
            $data['title_icon_font'] = false;
        }
        
        // svg icon
        $icon_svg = maza\getOfLanguage($setting['design_title_icon_svg']);
        if($setting['design_title_show'] !== 'text' && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)){
            $data['title_icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
        } else {
            $data['title_icon_svg'] = false;
        }
        
        // Image icon
        $icon_image = maza\getOfLanguage($setting['design_title_icon_image']);
        if($setting['design_title_show'] !== 'text' && is_file(DIR_IMAGE . $icon_image)){
            list($width, $height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $setting['design_title_icon_width'], $setting['design_title_icon_height']);
            $data['title_icon_image'] = $this->model_tool_image->resize($icon_image, $width, $height);
            
            $data['title_icon_width'] = $width;
            $data['title_icon_height'] = $height;
        } else {
            $data['title_icon_image'] = false;
        }
        
        // -- Item ----------------
        $data['show']           = $setting['design_show'];
        $data['icon_position']  = $setting['design_icon_position'];
        $data['icon_width']     = $setting['design_icon_width'];
        $data['icon_height']    = $setting['design_icon_height'];

        
        // -- Items -----------------
        $data['items']  =  $this->getItems($setting);
        
        if($data['items']){
            return $this->load->view('extension/mz_design/dropdown', $data);
        } else {
            return '';
        }
    }
    
    /**
     * Get list of items
     * @param array $setting setting
     * @return array items
     */
    private function getItems(array $setting): array {
        $items = array();
        
        foreach($setting['design_items']??[] as $design_item){
            if(!$design_item['status']) continue;
            
            // Name
            $name = maza\getOfLanguage($design_item['name']);
            
            // Description
            $description = maza\getOfLanguage($design_item['description']);
            
            // Url
            $url = array();
            if ($design_item['url_link_code']) {
                $url = $this->model_extension_maza_common->createLink($design_item['url_link_code']);
            }
            
            // Icon
            // font icon
            $font = maza\getOfLanguage($design_item['icon_font']);
            if($setting['design_show'] !== 'text' && $font){
                $icon_font = $font;
            } else {
                $icon_font = false;
            }

            // svg image
            $file = maza\getOfLanguage($design_item['icon_svg']);
            if($setting['design_show'] !== 'text' && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $file)){
                $icon_svg = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $file);
            } else {
                $icon_svg = false;
            }
            
            $image_width = $setting['design_icon_width'];
            $image_height = $setting['design_icon_height'];

            // Image
            $file = maza\getOfLanguage($design_item['icon_image']);
            if($setting['design_show'] !== 'text' && is_file(DIR_IMAGE . $file)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($file, $setting['design_icon_width'], $setting['design_icon_height']);
                $icon_image = $this->model_tool_image->resize($file, $image_width, $image_height);
            } else {
                $icon_image = false;
            }
            
            $items[] = array(
                'name' => $name,
                'description' => $description,
                'url'  => $url,
                'sort_order' => $design_item['sort_order'],
                'icon_font' => $icon_font,
                'icon_svg' => $icon_svg,
                'icon_image' => $icon_image,
                'image_width' => $image_width,
                'image_height' => $image_height,
            );
        }
        
        if($items){
            // Sort data
            array_multisort(array_column($items, 'sort_order'), SORT_ASC, SORT_NUMERIC, $items);
        }
        
        return $items;
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
