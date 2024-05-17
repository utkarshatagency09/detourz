<?php
class ControllerExtensionMzDesignListGroup extends maza\layout\Design {
        
	public function index(array $setting): string {
        $data = array();
        
        $data['heading_title'] = maza\getOfLanguage($setting['design_title']);
        
        $data['icon_width']  = $setting['design_icon_width'];
        $data['icon_height'] = $setting['design_icon_height'];
        
        $data['items']  =  $this->getItems($setting);
        
        if($data['items']){
            return $this->load->view('extension/mz_design/list_group', $data);
        } else {
            return '';
        }
	}
        
    /**
     * Get items of list
     * @param array $setting setting
     * @return array items
     */
    private function getItems(array $setting): array {
        $items = array();
        
        foreach($setting['design_list']??[] as $list_item){
            if(!$list_item['status']) continue;
            
            // Nsme
            $name = maza\getOfLanguage($list_item['name']);
            
            // Description
            $description = maza\getOfLanguage($list_item['description']);
            
            // Url
            $url = array();
            if ($list_item['url_link_code']) {
                $url = $this->model_extension_maza_common->createLink($list_item['url_link_code']);
            }
            
            // Icon
            // font icon
            $icon_font = maza\getOfLanguage($list_item['icon_font']);

            // svg image
            $icon_svg = maza\getOfLanguage($list_item['icon_svg']);
            if($icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)){
                $icon_svg = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
            } else {
                $icon_svg = false;
            }
            
            $image_width = $setting['design_icon_width'];
            $image_height = $setting['design_icon_height'];

            // Image
            $icon_image = maza\getOfLanguage($list_item['icon_image']);
            if($icon_image && is_file(DIR_IMAGE . $icon_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $image_width, $image_height);
                $icon_image = $this->model_tool_image->resize($icon_image, $image_width, $image_height);
            } else {
                $icon_image = false;
            }
            
            $items[] = array(
                'name' => $name,
                'description' => $description,
                'url'  => $url,
                'sort_order' => $list_item['sort_order'],
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
