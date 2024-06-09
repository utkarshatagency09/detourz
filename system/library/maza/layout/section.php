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
* Module class
*/
abstract class Section{
        /**
         * Default settings
         */
        public static function getSettings(){
                $screen_sizes = array('xl', 'lg', 'md', 'sm', 'xs');
                
                $setting = array();
                $setting['section_type'] = 'fixed_content';
                $setting['section_sticky'] = '0';
                $setting['section_custom_class'] = '';
                $setting['section_condition']    = '';
                
                $setting['section_status'] = 1;
                $setting['section_status_customer'] = 'all';
                $setting['section_status_customer_group'] = 0;
                $setting['section_status_date_start']   = '';
                $setting['section_status_date_end']     = '';
                
                $setting['section_unique_id'] = 'section-' . mt_rand();
                $setting['section_collapsible'] = 0;
                $setting['section_collapsible_default'] = 1;
                
                // Style
                $setting['xl'] = $setting['lg'] = $setting['md'] = 
                $setting['sm'] = $setting['xs'] = array(
                    'section_margin_top' => '',
                    'section_margin_bottom' => '',
                    'section_padding_top' => '',
                    'section_padding_bottom' => '',
                    'section_border_top' => '',
                    'section_border_bottom' => '',
                    'section_border_left' => '',
                    'section_border_right' => '',
                    'section_min_height' => '',
                    'section_text_align' => 'default',
                    'section_flex_direction' => 'column',
                    'section_justify_content' => 'start',
                    'section_align_items' => 'stretch',
                    'section_flex_wrap' => 'nowrap',
                    'section_text_color' => '',
                    'section_text_size' => '',
                    'section_background_color' => '',
                    'section_border_color' => ''
                );
                
                foreach($screen_sizes as $size){
                    for($layer = 1; $layer <= 3; $layer++){
                        $setting[$size]['section_background_image']['layer_' . $layer] = array(
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
}
