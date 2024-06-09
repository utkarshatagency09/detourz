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
abstract class Component{
        /**
         * Default settings
         */
        public static function getSettings(){
                $screen_sizes = array('xl', 'lg', 'md', 'sm', 'xs');
                
                $setting = array();
                $setting['component_custom_class']  = '';
                $setting['component_condition']     = '';
                $setting['component_type']          =  'drawer';
                $setting['component_unique_id']     =  'mz-component-' . mt_rand();
                $setting['component_title']         =  array();
                $setting['component_color']         =  'primary';
                
                $setting['component_status']        = 0;
                $setting['component_status_customer'] = 'all';
                $setting['component_status_customer_group'] = 0;
                $setting['component_status_date_start']   = '';
                $setting['component_status_date_end']     = '';
                
                // Drawer
                $setting['component_drawer_open_from'] =  'start';
                $setting['component_drawer_size']     =  '';
                
                // Popup
                $setting['component_popup_size']              =  'lg';
                $setting['component_popup_show_only_once']    =  0;
                $setting['component_popup_cookie_id']         =  'mz_popup_' . mt_rand();
                $setting['component_popup_close_button']      =  1;
                $setting['component_popup_auto_start_status'] =  1;
                $setting['component_popup_auto_start_delay']  =  3;
                $setting['component_popup_auto_close_status'] =  0;
                $setting['component_popup_auto_close_delay']  =  6;
                $setting['component_popup_do_not_show_again'] =  array('status' => 0, 'text' => array());
                
                // Sticky
                $setting['component_sticky_position']       =  'bottom';
                $setting['component_sticky_show_only_once'] =  0;
                $setting['component_sticky_cookie_id']      =  'mz_sticky_' . mt_rand();
                $setting['component_sticky_collapsible']    =  1;
                $setting['component_sticky_collapsed']      =  0;
                
                // Style
                $setting['xl'] = $setting['lg'] = $setting['md'] = 
                $setting['sm'] = $setting['xs'] = array(
                    'component_padding_top' => '',
                    'component_padding_bottom' => '',
                    'component_padding_left' => '',
                    'component_padding_right' => '',
                    'component_border_top' => '',
                    'component_border_bottom' => '',
                    'component_border_left' => '',
                    'component_border_right' => '',
                    'component_text_align' => 'default',
                    'component_flex_direction' => 'column',
                    'component_justify_content' => 'start',
                    'component_align_items' => 'stretch',
                    'component_flex_wrap' => 'nowrap',
                    'component_text_color' => '',
                    'component_text_size' => '',
                    'component_background_color' => '',
                    'component_border_color' => ''
                );
                
                foreach($screen_sizes as $size){
                    for($layer = 1; $layer <= 3; $layer++){
                        $setting[$size]['component_background_image']['layer_' . $layer] = array(
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
