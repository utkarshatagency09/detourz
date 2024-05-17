<?php
class ControllerExtensionMzDesignAccordion extends maza\layout\Design {
    private static $instance_count = 0;

	public function index(array $setting): string {
        $data = array();
        
        // Heading title
        $data['heading_title'] = maza\getOfLanguage($setting['design_title']);
        
        $data['auto_close']  = $setting['design_auto_close'];
        $data['collapsed']   = $setting['design_collapsed'];
        $data['icon_width']  = $setting['design_icon_width'];
        $data['icon_height'] = $setting['design_icon_height'];
        $data['hook']        = $setting['design_hook'];
        
        // Tabs
        $data['accordion']  =  array();
        
        foreach($setting['design_accordion']??[] as $key => $panel){
            if(!$panel['status']) continue;
            
            // name
            $name = maza\getOfLanguage($panel['name']);
            
            // Icon
            // font icon
            $icon_font = maza\getOfLanguage($panel['icon_font']);

            // svg image
            $icon_svg = maza\getOfLanguage($panel['icon_svg']);
            if($icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)){
                $icon_svg = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
            } else {
                $icon_svg = false;
            }
            
            $image_width = $setting['design_icon_width'];
            $image_height = $setting['design_icon_height'];

            // Image
            $icon_image = maza\getOfLanguage($panel['icon_image']);
            if($icon_image && is_file(DIR_IMAGE . $icon_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $image_width, $image_height);
                $icon_image = $this->model_tool_image->resize($icon_image, $image_width, $image_height);
            } else {
                $icon_image = false;
            }
            
            // Content
            $content = '';
            
            // HTML
            if($panel['type'] == 'html'){
                $content = maza\getOfLanguage($panel['html']);
            }

            // Module
            if($panel['type'] == 'module' && $panel['module']){
                $content = $this->entryModule($panel['module'], $key, $setting['mz_suffix']??self::$instance_count);
            }
            
            // widget
            if($panel['type'] == 'widget' && $panel['widget'] && !empty($panel['widget_data'])){
                $content = $this->entryWidget($panel['widget'], $panel['widget_data'], $key, $setting['mz_suffix']??self::$instance_count);
            }
            
            // Content
            if($panel['type'] == 'content' && $panel['content'] && !empty($panel['content_data'])){
                $content = $this->entryContent($panel['content'], $panel['content_data'], $key, $setting['mz_suffix']??self::$instance_count);
            }

            // Content
            if($panel['type'] == 'content_builder' && $panel['content_builder_id']){
                $content  = $this->layout_builder(['group' => 'content_builder', 'group_owner' => $panel['content_builder_id'], 'suffix' => $setting['mz_suffix']??self::$instance_count]);
            }
            
            if($content){
                $data['accordion'][] = array(
                    'name' => $name,
                    'sort_order' => $panel['sort_order'],
                    'icon_font' => $icon_font,
                    'icon_svg' => $icon_svg,
                    'icon_image' => $icon_image,
                    'image_width' => $image_width,
                    'image_height' => $image_height,
                    'content'   => $content
                );
            }
        }
        
        $data['mz_suffix']   = $setting['mz_suffix']??self::$instance_count++;
        
        // Sort accordion
        if($data['accordion']){
            array_multisort(array_column($data['accordion'], 'sort_order'), SORT_ASC, SORT_NUMERIC, $data['accordion']);
        }

        return $this->load->view('extension/mz_design/accordion', $data);
	}
}
