<?php
class ControllerExtensionMzDesignMenu extends maza\layout\Design {
        
    public function index(array $setting): string {
        $data = array();
        
        $data['heading_title']  = maza\getOfLanguage($setting['design_title']);
        
        $data['orientation']    = $setting['design_orientation'];
        $data['show']           = $setting['design_show'];
        $data['icon_position']  = $setting['design_icon_position'];
        $data['icon_width']     = $setting['design_icon_width'];
        $data['icon_height']    = $setting['design_icon_height'];
        $data['image_position'] = $setting['design_image_position'];
        $data['image_width']    = $setting['design_image_width'];
        $data['image_height']   = $setting['design_image_height'];
        $data['image_size']     = $setting['design_image_size'];
        
        // font image
        $data['image_font'] = maza\getOfLanguage($setting['design_image_font']);
        
        // svg image
        $image_svg = maza\getOfLanguage($setting['design_image_svg']);
        if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $image_svg)){
            $data['image_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $image_svg);
        } else {
            $data['image_svg'] = false;
        }
        
        // Image
        $image_image = maza\getOfLanguage($setting['design_image_image']);
        if(is_file(DIR_IMAGE . $image_image)){
            list($width, $height) = $this->model_extension_maza_image->getEstimatedSize($image_image, $setting['design_image_width'], $setting['design_image_height']);
            $data['image_image'] = $this->model_tool_image->resize($image_image, $width, $height);
            
            $data['image_width'] = $width;
            $data['image_height'] = $height;
        } else {
            $data['image_image'] = false;
        }

        // Title url
        if ($setting['design_title_url_link_code']) {
            $data['title_url'] = $this->model_extension_maza_common->createLink($setting['design_title_url_link_code']);
        } else {
            $data['title_url'] = array();
        }
        
        $data['items']  =  $this->getItems($setting);
        
        if($data['items']){
            return $this->load->view('extension/mz_design/menu', $data);
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
                'url_target' => $design_item['url_target'],
                'nofollow' => $design_item['nofollow'],
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
}
