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
* Widget class
*/
abstract class Content extends \Controller{
    use mixin\EntryMerger;

	/**
     * Default settings
     */
    public function getSettings(): array {
        $screen_sizes = array('xl', 'lg', 'md', 'sm', 'xs');
        
        $setting = array();
        $setting['content_custom_class'] = '';
        $setting['content_condition']    = '';
        
        $setting['content_status'] = 1;
        $setting['content_status_customer'] = 'all';
        $setting['content_status_customer_group'] = 0;
        $setting['content_status_date_start']   = '';
        $setting['content_status_date_end']     = '';
        
        // Style
        $setting['xl'] = $setting['lg'] = $setting['md'] = 
        $setting['sm'] = $setting['xs'] = array(
            'content_margin_top' => '',
            'content_margin_bottom' => '',
            'content_margin_left' => '',
            'content_margin_right' => '',
            'content_padding_top' => '',
            'content_padding_bottom' => '',
            'content_padding_left' => '',
            'content_padding_right' => '',
            'content_border_top' => '',
            'content_border_bottom' => '',
            'content_border_left' => '',
            'content_border_right' => '',
            'content_flex_grow' => 1,
            'content_flex_shrink' => 1,
            'content_align_self' => 'auto',
            'content_text_color' => '',
            'content_text_size' => '',
            'content_border_color' => '',
            'content_background_color' => ''
        );
        
        foreach($screen_sizes as $size){
            for($layer = 1; $layer <= 3; $layer++){
                $setting[$size]['content_background_image']['layer_' . $layer] = array(
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
        $this->load->controller('extension/maza/layout_builder/edit_content', $this->getSettings());
    }
}
