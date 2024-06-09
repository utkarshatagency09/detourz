<?php
class ControllerExtensionMzDesignTabs extends maza\layout\Design {
    private static $instance_count = 0;

	public function index(array $setting): string {
        $data = array();
        
        // Heading title
        $data['heading_title'] = maza\getOfLanguage($setting['design_title']);
        
        $data['show'] = $setting['design_show'];
        $data['fade'] = $setting['design_fade'];
        $data['hook'] = $setting['design_hook'];
        $data['icon_position'] = $setting['design_icon_position'];
        $data['icon_width']  = $setting['design_icon_width'];
        $data['icon_height'] = $setting['design_icon_height'];
        
        // Tabs
        $data['tabs']  =  array();
        
        foreach($setting['design_tabs']??[] as $key => $tab){
            if(!$tab['status']) continue;
            
            // Nsme
            $name = maza\getOfLanguage($tab['name']);
            
            // Icon
            // font icon
            if($setting['design_show'] !== 'text'){
                $icon_font = maza\getOfLanguage($tab['icon_font']);
            } else {
                $icon_font = false;
            }

            // svg image
            $icon_svg = maza\getOfLanguage($tab['icon_svg']);
            if($setting['design_show'] !== 'text' && $icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)){
                $icon_svg = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
            } else {
                $icon_svg = false;
            }
            
            $image_width = $setting['design_icon_width'];
            $image_height = $setting['design_icon_height'];

            // Image
            $icon_image = maza\getOfLanguage($tab['icon_image']);
            if($setting['design_show'] !== 'text' && $icon_image && is_file(DIR_IMAGE . $icon_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $image_width, $image_height);
                $icon_image = $this->model_tool_image->resize($icon_image, $image_width, $image_height);
            } else {
                $icon_image = false;
            }
            
            // Content
            $content = $condition = null;
            
            // HTML
            if($tab['type'] == 'html'){
                $content = maza\getOfLanguage($tab['html']);
            }

            // Module
            if($tab['type'] == 'module' && $tab['module']){
                $content = $this->entryModule($tab['module'], $key, $setting['mz_suffix']??self::$instance_count);
            }
            
            // widget
            if($tab['type'] == 'widget' && $tab['widget'] && !empty($tab['widget_data'])){
                $content = $this->entryWidget($tab['widget'], $tab['widget_data'], $key, $setting['mz_suffix']??self::$instance_count);
            }
            
            // Content
            if($tab['type'] == 'content' && $tab['content'] && !empty($tab['content_data'])){
                $content = $this->entryContent($tab['content'], $tab['content_data'], $key, $setting['mz_suffix']??self::$instance_count);

                $code = explode('.', $tab['content'])[1];

                switch($code){
                    case 'description': $condition = 'description';
                        break;
                    case 'specification': $condition = 'attribute_groups';
                        break;
                }
            }

            // Content
            if($tab['type'] == 'content_builder' && $tab['content_builder_id']){
                $content  = $this->layout_builder(['group' => 'content_builder', 'group_owner' => $tab['content_builder_id'], 'suffix' => $setting['mz_suffix']??self::$instance_count]);
            }
            
            if($content){
                $data['tabs'][] = array(
                    'name' => $name,
                    'sort_order' => $tab['sort_order'],
                    'icon_font' => $icon_font,
                    'icon_svg' => $icon_svg,
                    'icon_image' => $icon_image,
                    'image_width' => $image_width,
                    'image_height' => $image_height,
                    'content'   => $content,
                    'condition' => $condition,
                );
            }
        }
        
        $data['mz_suffix']   = $setting['mz_suffix']??self::$instance_count++;
        
        // Sort tabs
        if($data['tabs']){
            array_multisort(array_column($data['tabs'], 'sort_order'), SORT_ASC, SORT_NUMERIC, $data['tabs']);
        }

        return $this->load->view('extension/mz_design/tabs', $data);
	}
}
