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
abstract class Row{
        /**
         * Default settings
         */
        public static function getSettings(){
                $screen_sizes = array('xl', 'lg', 'md', 'sm', 'xs');
                
                $setting = array();
                $setting['row_unique_id'] = 'row-' . mt_rand();
                $setting['row_collapsible'] = 0;
                $setting['row_collapsible_default'] = 1;
                $setting['row_no_gutters'] = 0;
                $setting['row_custom_class'] = '';
                $setting['row_condition'] = '';
                
                $setting['row_status'] = 1;
                $setting['row_status_customer'] = 'all';
                $setting['row_status_customer_group'] = 0;
                $setting['row_status_date_start']   = '';
                $setting['row_status_date_end']     = '';
                
                // Style
                $setting['xl'] = $setting['lg'] = $setting['md'] = 
                $setting['sm'] = $setting['xs'] = array(
                    'row_margin_top' => '',
                    'row_margin_bottom' => '',
                    'row_padding_top' => '',
                    'row_padding_bottom' => '',
                    'row_padding_left' => '',
                    'row_padding_right' => '',
                    'row_border_top' => '',
                    'row_border_bottom' => '',
                    'row_border_left' => '',
                    'row_border_right' => '',
                    'row_text_align' => 'default',
                    'row_align_items' => 'stretch',
                    'row_min_height' => '',
                    'row_text_color' => '',
                    'row_text_size' => '',
                    'row_background_color' => '',
                    'row_border_color' => ''
                );
                
                foreach($screen_sizes as $size){
                    for($layer = 1; $layer <= 3; $layer++){
                        $setting[$size]['row_background_image']['layer_' . $layer] = array(
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
