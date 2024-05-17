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
abstract class Module extends \Controller{
	abstract public function index(array $setting);
        
    /**
     * Get cache of module
     * @param string $key cache key
     * @param string $static static or not
     * @return mixed
     */
    protected function getCache(string $key){
        return $this->mz_cache->get($this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '.' . $this->config->get('config_store_id') . '.module.' . $key . $this->session->data['currency'] . $this->config->get('config_language_id') . (int)$this->mz_browser->isSupportedWebp());
    }
    
    /**
     * Set cache of module
     * @param string $key cache key
     * @param mixed $expire expire timestamp
     * @return null
     */
    protected function setCache(string $key, $value, $expire = true): void {
        $this->mz_cache->set($this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '.' . $this->config->get('config_store_id') . '.module.' . $key . $this->session->data['currency'] . $this->config->get('config_language_id') . (int)$this->mz_browser->isSupportedWebp(), $value, $expire);
    }
    
    /**
     * Default settings
     */
    public static function getSettings(): array {
        $screen_sizes = array('xl', 'lg', 'md', 'sm', 'xs');
        
        $setting = array();
        $setting['module_custom_class'] = '';
        $setting['module_condition']    = '';
        
        $setting['module_status'] = 1;
        $setting['module_status_customer'] = 'all';
        $setting['module_status_customer_group'] = 0;
        $setting['module_status_date_start']   = '';
        $setting['module_status_date_end']     = '';
        $setting['module_cache']               = 0;
        
        // Style
        $setting['xl'] = $setting['lg'] = $setting['md'] = 
        $setting['sm'] = $setting['xs'] = array(
            'module_margin_top' => '',
            'module_margin_bottom' => '',
            'module_margin_left' => '',
            'module_margin_right' => '',
            'module_padding_top' => '',
            'module_padding_bottom' => '',
            'module_padding_left' => '',
            'module_padding_right' => '',
            'module_border_top' => '',
            'module_border_bottom' => '',
            'module_border_left' => '',
            'module_border_right' => '',
            'module_flex_grow' => 1,
            'module_flex_shrink' => 1,
            'module_align_self' => 'auto',
            'module_text_color' => '',
            'module_text_size' => '',
            'module_border_color' => '',
            'module_background_color' => ''
        );
        
        foreach($screen_sizes as $size){
            for($layer = 1; $layer <= 3; $layer++){
                $setting[$size]['module_background_image']['layer_' . $layer] = array(
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
