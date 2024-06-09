<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright           Copyright (c) 2021 Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza\layout;
/**
* Design class
*/
abstract class Design extends \Controller{
    use mixin\EntryMerger;
    
    /**
     * Default settings
     */
    public function getSettings(): array {
        $screen_sizes = array('xl', 'lg', 'md', 'sm', 'xs');
        
        $setting = array();
        $setting['design_custom_class'] = '';
        $setting['design_condition']    = '';
        
        $setting['design_status_customer'] = 'all';
        $setting['design_status_customer_group'] = 0;
        $setting['design_status_date_start']   = '';
        $setting['design_status_date_end']     = '';
        
        // Style
        $setting['xl'] = $setting['lg'] = $setting['md'] = 
        $setting['sm'] = $setting['xs'] = array(
            'design_margin_top' => '',
            'design_margin_bottom' => '',
            'design_margin_left' => '',
            'design_margin_right' => '',
            'design_padding_top' => '',
            'design_padding_bottom' => '',
            'design_padding_left' => '',
            'design_padding_right' => '',
            'design_border_top' => '',
            'design_border_bottom' => '',
            'design_border_left' => '',
            'design_border_right' => '',
            'design_flex_grow' => 1,
            'design_flex_shrink' => 1,
            'design_align_self' => 'auto',
            'design_text_color' => '',
            'design_text_size' => '',
            'design_border_color' => '',
            'design_background_color' => ''
        );
        
        foreach($screen_sizes as $size){
            for($layer = 1; $layer <= 3; $layer++){
                $setting[$size]['design_background_image']['layer_' . $layer] = array(
                        'status'            =>  'none',
                        'image'             =>  array(),
                        'thumb'             =>  array(),
                        'image_position'    =>  'left_top',
                        'image_repeat'      =>  'repeat',
                        'image_attachment'  =>  'initial',
                        'image_size'        =>  'initial',
                        'overlay_pattern'   =>  'default',
                );
            }
        }
        
        return $setting;
    }
    
    public function edit(): void {
        $this->load->controller('extension/maza/layout_builder/edit_design', $this->getSettings());
    }
}
