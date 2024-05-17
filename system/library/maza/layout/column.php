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
abstract class Column{
        /**
         * Default settings
         */
        public static function getSettings(){
                $screen_sizes = array('xl', 'lg', 'md', 'sm', 'xs');
                
                $setting = array();
                $setting['col_custom_class'] = '';
                
                $setting['col_status'] = 1;
                $setting['col_status_customer'] = 'all';
                $setting['col_status_customer_group'] = 0;
                $setting['col_status_date_start']   = '';
                $setting['col_status_date_end']     = '';
                
                // Style
                $setting['xl'] = $setting['lg'] = $setting['md'] = 
                $setting['sm'] = $setting['xs'] = array(
                    'col_margin_top' => '',
                    'col_margin_bottom' => '',
                    'col_border_top' => '',
                    'col_border_bottom' => '',
                    'col_border_left' => '',
                    'col_border_right' => '',
                    'col_padding_top' => '',
                    'col_padding_bottom' => '',
                    'col_padding_left' => '',
                    'col_padding_right' => '',
                    'col_min_height' => '',
                    'col_flex_direction' => 'row',
                    'col_justify_content' => 'start',
                    'col_align_items' => 'stretch',
                    'col_flex_wrap' => 'nowrap',
                    'col_text_align' => 'default',
                    'col_text_color' => '',
                    'col_text_size' => '',
                    'col_border_color' => '',
                    'col_background_color' => ''
                );
                
                foreach($screen_sizes as $size){
                    for($layer = 1; $layer <= 3; $layer++){
                        $setting[$size]['col_background_image']['layer_' . $layer] = array(
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
