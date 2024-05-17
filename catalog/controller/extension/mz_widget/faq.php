<?php
class ControllerExtensionMzWidgetFaq extends maza\layout\Widget {
    private static $instance_count = 0;

	public function index(array $setting) {
        $data = array();
        
        // Heading title
        $data['heading_title'] = maza\getOfLanguage($setting['widget_title']);
        
        $data['accordion'] = $setting['widget_accordion'];
        $data['icon_width']  = $setting['widget_icon_width'];
        $data['icon_height'] = $setting['widget_icon_height'];
        
        // Tabs
        $data['faq']  =  array();
        
        if (isset($setting['widget_faq'])) {
            foreach($setting['widget_faq'] as $key => $panel){
                if(!$panel['status']) continue;
                
                // Nsme
                $question = maza\getOfLanguage($panel['name']);
                
                // Icon
                // font icon
                $icon_font = maza\getOfLanguage($panel['icon_font']);
    
                // svg image
                $icon_svg = maza\getOfLanguage($panel['icon_svg']);
                if(!empty($icon_svg) && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)){
                    $icon_svg = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
                } else {
                    $icon_svg = false;
                }
                
                $image_width = $setting['widget_icon_width'];
                $image_height = $setting['widget_icon_height'];
    
                // Image
                $icon_image = maza\getOfLanguage($panel['icon_image']);
                if($icon_image && is_file(DIR_IMAGE . $icon_image)){
                    list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $image_width, $image_height);
                    $icon_image = $this->model_tool_image->resize($icon_image, $image_width, $image_height);
                } else {
                    $icon_image = false;
                }
                
                // answer
                $answer = '';
                
                // Text
                $text = maza\getOfLanguage($panel['text']);
                if($panel['type'] == 'text' && $text){
                    $answer = '<p>' . $text . '</p>';
                }
                
                // HTML
                if($panel['type'] == 'html'){
                    $answer = maza\getOfLanguage($panel['html']);
                }
    
                if($answer){
                    if ($this->config->get('maza_schema')) {
                        $this->mz_schema->addFAQ($question, $answer);
                    }

                    $data['faq'][] = array(
                        'question'      => $question,
                        'answer'        => $answer,
                        'sort_order'    => $panel['sort_order'],
                        'icon_font'     => $icon_font,
                        'icon_svg'      => $icon_svg,
                        'icon_image'    => $icon_image,
                        'image_width'   => $image_width,
                        'image_height'  => $image_height
                    );
                }
            }
        }
        
        // Hook
        if($setting['widget_hook']){
            foreach($this->mz_hook->fetch('faq') as $key => $panel){
                if($panel['content']){
                    if ($this->config->get('maza_schema')) {
                        $this->mz_schema->addFAQ($panel['title'], $panel['content']);
                    }
                    
                    $data['faq'][] = array(
                        'question' => $panel['title'],
                        'answer'   => $panel['content'],
                        'sort_order' => $panel['sort_order'],
                        'icon_font' => $panel['icon_font'],
                        'icon_svg' => $panel['icon_svg'],
                        'icon_image' => $panel['icon_image'],
                        'image_width' => $panel['image_width'],
                        'image_height' => $panel['image_height']
                    );
                }
            }
        }

        $data['mz_suffix'] = $setting['mz_suffix']??self::$instance_count++;
        $data['column'] = 'col-' . 12/$setting['widget_column_xs'];
        if($setting['widget_column_xs'] !== $setting['widget_column_sm']){
            $data['column'] .= ' col-sm-' . 12/$setting['widget_column_sm'];
        }
        if($setting['widget_column_sm'] !== $setting['widget_column_md']){
            $data['column'] .= ' col-md-' . 12/$setting['widget_column_md'];
        }
        if($setting['widget_column_md'] !== $setting['widget_column_lg']){
            $data['column'] .= ' col-lg-' . 12/$setting['widget_column_lg'];
        }
        if($setting['widget_column_lg'] !== $setting['widget_column_xl']){
            $data['column'] .= ' col-xl-' . 12/$setting['widget_column_xl'];
        }
        
        if($data['faq']){
            // Sort faq
            array_multisort(array_column($data['faq'], 'sort_order'), SORT_ASC, SORT_NUMERIC, $data['faq']);
            
            return $this->load->view('extension/mz_widget/faq', $data);
        }
	}
}
