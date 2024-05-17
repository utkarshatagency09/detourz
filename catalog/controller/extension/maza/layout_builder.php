<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazalayoutBuilder extends Controller {
    protected $cache_expire = false;
    protected $asset_data = array('captured' => array());
    protected $suffix;
        
    /**
     * Get entry of specific group and group owner after shortcode resolve
     * @param array $layout group and group owner
     * @return string HTML layout of specific group and group owner
     */
    public function index(array $layout): string {
        list($shortcode_data, $entry_layout, $asset_data) = $this->getLayoutData($layout);
        
        if($shortcode_data){
            $output = $this->resolveShortcode($shortcode_data, $entry_layout);
            
            // shortcode Assets
            $asset_data_cache = $this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '.' . $this->config->get('config_store_id')
                . '.layout.' . $layout['group'] . '.' . $layout['group_owner'] . '_g' . $this->customer->getGroupId() . '.shortcode_data_assets' . $this->suffix;
            
            $shortcode_asset_data = unserialize($this->mz_cache->get($asset_data_cache))?:array();
            
            if(!empty($this->asset_data['captured'])){
                $shortcode_asset_data = array_merge($shortcode_asset_data, $this->asset_data['captured']);
                
                $this->mz_cache->set($asset_data_cache, serialize($shortcode_asset_data));
            }
            
            $asset_data = array_merge($asset_data, $shortcode_asset_data);
        } else {
            $output = $entry_layout;
        }
        
        // Restore assets to main flow
        $this->_asset_restore($asset_data);
        
        return $output;
    }
        
    /**
     * Get entry of specific group and group owner without shortcode resolve
     * @param array $layout group and group owner
     * @return array shortcode and html layout
     */
    public function getLayoutData(array $layout): array{
        $data = array();
        
        if(isset($layout['suffix'])){
            $this->suffix = $layout['suffix'];
        }
        
        $this->load->model('extension/maza/layout_builder');
        
        $shortcode_data_cache = $this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '.' . $this->config->get('config_store_id') . '.layout.' . $layout['group'] . '.' . $layout['group_owner'] . '_g' . $this->customer->getGroupId() . '.shortcode_data' . $this->suffix;
        $entry_layout_cache = $this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '.' . $this->config->get('config_store_id') . '.layout.' . $layout['group'] . '.' . $layout['group_owner'] . '_g' . $this->customer->getGroupId() . '.entry_layout-l' . $this->config->get('config_language_id') . $this->session->data['currency'] . (int)$this->mz_browser->isSupportedWebp() . $this->suffix;
        $asset_data_cache = $this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '.' . $this->config->get('config_store_id') . '.layout.' . $layout['group'] . '.' . $layout['group_owner'] . '_g' . $this->customer->getGroupId() . '.assets' . $this->suffix;
        
        // Get static cache of layout
        $shortcode_data = $this->mz_cache->get($shortcode_data_cache)?:array();
        $entry_layout = $this->mz_cache->get($entry_layout_cache);
        $asset_data = unserialize($this->mz_cache->get($asset_data_cache))?:array();
        
        if(empty($entry_layout) || $this->mz_skin_config->get('flag_compile_route_asset')){
            $shortcode_data = array();
            $entry_layout = $this->getEntryLayout($this->model_extension_maza_layout_builder->getLayoutEntries($layout['group'], $layout['group_owner']), $shortcode_data);
            
            if($this->cache_expire){
                $expire = $this->cache_expire - time();
            } else {
                $expire = false;
            }
            // Set static cache of layout
            $this->mz_cache->set($shortcode_data_cache, $shortcode_data, $expire);
            $this->mz_cache->set($entry_layout_cache, $entry_layout, $expire);
            
            if(!empty($this->asset_data['captured'])){
                $asset_data = $this->asset_data['captured'];
                $this->mz_cache->set($asset_data_cache, serialize($asset_data), $expire);
                unset($this->asset_data['captured']);
            }
        }
        
        $data[0] = $shortcode_data;
        $data[1] = $entry_layout;
        $data[2] = $asset_data;
        
        return $data;
    }
        
    /**
     * Get html layout of entry
     * @param array $entries entry list
     * @return string html content of entry
     */
    protected function getEntryLayout(array $entries = array(), array &$shortcode_data = array()): string {
        $html = '';
        
        foreach($entries as $entry){
            
            $data = array();
            $data['entry_id']       = $entry['entry_id'];
            $data['type']           = $entry['type'];
            $data['code']           = $entry['code'];
            $data['suffix']         = $this->suffix;
            
            if(empty($entry['setting'])){
                switch($entry['type']){
                    case 'design': $entry['setting'] = $this->load->controller('extension/mz_design/' . $entry['code'] . '/getSettings');
                        break;
                    case 'widget': $entry['setting'] = $this->load->controller('extension/mz_widget/' . $entry['code'] . '/getSettings');
                        break;
                    case 'content': $entry['setting'] = $this->load->controller('extension/mz_content/' . str_replace('.', '/', $entry['code']) . '/getSettings');
                        break;
                    case 'module': $entry['setting'] = maza\layout\Module::getSettings();
                        break;
                    case 'row': $entry['setting'] = maza\layout\Row::getSettings();
                        break;
                    case 'col': $entry['setting'] = maza\layout\Column::getSettings();
                        break;
                    case 'section': $entry['setting'] = maza\layout\Section::getSettings();
                        break;
                    case 'component': $entry['setting'] = maza\layout\Component::getSettings();
                        break;
                }
            }
            
            // Status
            if(isset($entry['setting'][$entry['type'] . '_status']) && !$entry['setting'][$entry['type'] . '_status']){
                continue;
            }

            // Get entry css
            $this->mz_document->addCSSCode($this->getEntryCSS($entry));

            // Child entry
            $data['child_entry']    = $this->getEntryLayout($entry['child_entry'], $shortcode_data);

            // Status customer
            if(isset($entry['setting'][$entry['type'] . '_status_customer']) && $entry['setting'][$entry['type'] . '_status_customer'] !== 'all'){
                if(($entry['setting'][$entry['type'] . '_status_customer'] === 'logged' && !$this->customer->isLogged()) || ($entry['setting'][$entry['type'] . '_status_customer'] === 'guest' && $this->customer->isLogged())){
                    continue;
                }
            }
            // Status customer group
            if(!empty($entry['setting'][$entry['type'] . '_status_customer_group']) && $entry['setting'][$entry['type'] . '_status_customer_group'] !== $this->customer->getGroupId()){
                continue;
            }
            // Status date start
            if(!empty($entry['setting'][$entry['type'] . '_status_date_start'])){
                $start_timestamp = DateTime::createFromFormat("Y-m-d H:i", $entry['setting'][$entry['type'] . '_status_date_start'])->getTimestamp();
            } else {
                $start_timestamp = false;
            }
            
            if($start_timestamp && $start_timestamp > time()){
                if(!$this->cache_expire || $this->cache_expire > $start_timestamp){
                    $this->cache_expire = $start_timestamp;
                }
                continue;
            }
            
            // Status date end
            if(!empty($entry['setting'][$entry['type'] . '_status_date_end'])){
                $end_timestamp = DateTime::createFromFormat("Y-m-d H:i", $entry['setting'][$entry['type'] . '_status_date_end'])->getTimestamp();
                
                if($end_timestamp < time()){
                    continue;
                } elseif(!$this->cache_expire || $this->cache_expire > $end_timestamp){
                    $this->cache_expire = $end_timestamp;
                }
            }
            
            // Utility classes
            $data['class']          = $this->getUtilityClasses($entry);
            
            // Custom class
            if(isset($entry['setting'][$entry['type'] . '_custom_class'])){
                $data['custom_class'] = $entry['setting'][$entry['type'] . '_custom_class'];
            }

            // condition
            if(isset($entry['setting'][$entry['type'] . '_condition'])){
                $data['condition'] = $entry['setting'][$entry['type'] . '_condition'];
            }
            
            // Section entry
            if($entry['type'] === 'section'){
                // Setting
                $data['section_type']       = $entry['setting']['section_type'];
                $data['unique_id']          = $entry['setting']['section_unique_id'];
                $data['collapsible_status'] = $entry['setting']['section_collapsible'];
                $data['collapsible_default']= $entry['setting']['section_collapsible_default'];

                $breakpoints = $this->mz_skin_config->get('style_breakpoints');
                if($entry['setting']['section_sticky'] && isset($breakpoints[$entry['setting']['section_sticky']])){
                    $data['section_sticky'] = $breakpoints[$entry['setting']['section_sticky']];
                } else {
                    $data['section_sticky'] = $entry['setting']['section_sticky'];
                }
            }
            
            // Row entry
            if($entry['type'] === 'row'){
                // Setting
                $data['unique_id']          = $entry['setting']['row_unique_id'];
                $data['collapsible_status'] = $entry['setting']['row_collapsible'];
                $data['collapsible_default']= $entry['setting']['row_collapsible_default'];

                if($entry['setting']['row_no_gutters']){ // Space between columm
                    $data['class'] .= ' g-0';
                }
            }
            
            // Col entry
            // if($entry['type'] === 'col'){
                
            // }
                
            // Module entry
            if($entry['type'] === 'module'){
                if($this->config->get('maza_cache_partial')){
                    $cache = $entry['setting']['module_cache'];
                } else {
                    $cache = false;
                }
                
                if($cache === 'hard'){
                    $this->_asset_capture_start();
                    $data['child_entry'] = $this->getModuleOutput($entry['code'], $entry['entry_id'], $this->suffix);     
                    $this->_asset_capture_end($entry['code']);
                } else {
                    // shortcode
                    $data['child_entry'] = '{mz_module.' . $entry['code'] . '.' . $entry['entry_id'] . $this->suffix . '}';
                    $shortcode_data['module'][] = array(
                        'entry_id'  => $entry['entry_id'],
                        'suffix'    => $this->suffix,
                        'code'      => $entry['code'],
                        'cache'     => (bool)$cache
                    );
                }
                
                
                $data['code'] = explode('.', $entry['code'])[0];
            }
            
            // Widget entry
            if($entry['type'] === 'widget'){
                if (!empty($entry['widget_data']) && $entry['widget_data']['widget_status']) {
                    if($this->config->get('maza_cache_partial')){
                        $cache = $entry['setting']['widget_cache'];
                    } else {
                        $cache = false;
                    }
                    
                    if($cache === 'hard'){
                        $this->_asset_capture_start();
                        
                        // $entry['widget_data']['entry_id'] = $entry['entry_id'];
                        $entry['widget_data']['mz_suffix'] = $entry['entry_id'] . $this->suffix;
                        $data['child_entry'] = $this->load->controller('extension/mz_widget/' . $entry['code'], $entry['widget_data']);
                        
                        $this->_asset_capture_end($entry['code'] . $entry['entry_id']);
                    } else {
                        $data['child_entry'] = '{mz_widget.' . $entry['code'] . '.' . $entry['entry_id'] . $this->suffix . '}';

                        $shortcode_data['widget'][] = array(
                            'entry_id'  => $entry['entry_id'],
                            'suffix'    => $this->suffix,
                            'code'      => $entry['code'],
                            'data'      => $entry['widget_data'],
                            'cache'     => (bool)$cache
                        );
                    }
                    
                } else {
                    continue;
                }
            }

            $entry_shortcodes = [];
            
            // Design entry
            if($entry['type'] === 'design'){
                if (!empty($entry['design_data']) && $entry['design_data']['design_status']) {
                    $this->_asset_capture_start();
                    
                    $entry['design_data']['entry_id'] = $entry['entry_id'];
                    $entry['design_data']['mz_suffix'] = $entry['entry_id'] . $this->suffix;
                    
                    list($data['child_entry'], $entry_shortcodes, $asset_data) = $this->load->controller('extension/mz_design/' . $entry['code'] . '/output', $entry['design_data']);
                    
                    if($asset_data){
                        $this->asset_data['captured'] = array_merge($this->asset_data['captured'], $asset_data);
                    }
                    
                    $this->_asset_capture_end($entry['code'] . $entry['entry_id']);
                } else {
                    continue;
                }
            }
            
            if($entry['type'] === 'content'){
                if (!empty($entry['content_data']) && $entry['content_data']['content_status']) {
                    $entry['content_data']['entry_id']  = $entry['entry_id'];
                    $entry['content_data']['mz_suffix'] = $entry['entry_id'] . $this->suffix;

                    list($data['child_entry'], $entry_shortcodes, $asset_data) = $this->load->controller('extension/mz_content/' . str_replace('.', '/', $entry['code']) . '/output', $entry['content_data']);

                    if($asset_data){
                        $this->asset_data['captured'] = array_merge($this->asset_data['captured'], $asset_data);
                    }
                } else {
                    continue;
                }
            }

            if($entry_shortcodes){
                foreach($entry_shortcodes as $key => $entry_shortcode_data){
                    if(isset($shortcode_data[$key])){
                        $shortcode_data[$key] = array_merge($shortcode_data[$key], $entry_shortcode_data);
                    } else {
                        $shortcode_data[$key] = $entry_shortcode_data;
                    }
                }
            }
            
            // Component entry
            if($entry['type'] === 'component'){
                // Setting
                if($entry['setting']['component_status']){
                    $entry['setting']['component_title'] = maza\getOfLanguage($entry['setting']['component_title']??[]);

                    if($entry['setting']['component_type'] == 'popup'){
                        if(!$entry['setting']['component_popup_cookie_id']){
                            $entry['setting']['component_popup_cookie_id']  = 'mz_popup_' . $entry['entry_id'];
                        }
                        if(!empty($entry['setting']['component_popup_do_not_show_again']['text'][$this->config->get('config_language_id')])){
                            $entry['setting']['component_popup_do_not_show_again']['text']  = $entry['setting']['component_popup_do_not_show_again']['text'][$this->config->get('config_language_id')];
                        } else {
                            $entry['setting']['component_popup_do_not_show_again']['text']  = $this->language->get('text_do_not_show_again');
                        }
                    }
                    
                    $data = array_merge($data, $entry['setting']);
                } else {
                    continue;
                }
            }
            
            
            $html .= trim($this->load->view('extension/maza/layout_builder', $data));
        }
        
        return $html;
    }
        
    /**
     * Resolve shortcodes of entry layout
     * @param array $shortcode_data data of shortcode
     * @param string $entry_layout entries layout
     * @return string entries layout after resolve shortcode
     */
    protected function resolveShortcode(array $shortcode_data, string $entry_layout): string {
        // Resolve entry shortcode
        $data = array();
        
        foreach($shortcode_data as $type => $type_values){
            // Module
            if($type == 'module'){
                foreach($type_values as $module){
                    $shortcode = '{mz_module.' . $module['code'] . '.' . $module['entry_id'] . $module['suffix'] .'}';
                    
                    $data[$shortcode] = null;
                    
                    $cache_key = $this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '.' . $this->config->get('config_store_id') . '.module.layout_builder.' . $module['code'] . '.e' . $module['entry_id'] . $module['suffix'] . '-l' . $this->config->get('config_language_id') . $this->session->data['currency'] . (int)$this->mz_browser->isSupportedWebp();
                            
                    if($module['cache']){
                        $data[$shortcode] = $this->mz_cache->get($cache_key);
                    }
                    
                    if(empty($data[$shortcode])){
                        if($module['cache']){
                            $this->_asset_capture_start();
                        }
                        
                        $data[$shortcode] = $this->getModuleOutput($module['code'], $module['entry_id'], $module['suffix']);
                        
                        if($module['cache']){
                            $this->mz_cache->set($cache_key, $data[$shortcode]);
                            
                            $this->_asset_capture_end($module['code']);
                        }
                    }
                    
                }
            }
            
            // Widget
            if($type == 'widget'){
                foreach($type_values as $widget){
                    $shortcode = '{mz_widget.' . $widget['code'] . '.' . $widget['entry_id'] . $widget['suffix'] . '}';
                    
                    $data[$shortcode] = null;
                    
                    $cache_key = $this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '.' . $this->config->get('config_store_id') . '.widget.layout_builder.' . $widget['code'] . '.e' . $widget['entry_id'] . '-l' . $this->config->get('config_language_id') . $this->session->data['currency'] . (int)$this->mz_browser->isSupportedWebp();
                            
                    if($widget['cache']){
                        $data[$shortcode] = $this->mz_cache->get($cache_key);
                    }
                    
                    if(empty($data[$shortcode])){
                        if($widget['cache']){
                            $this->_asset_capture_start();
                        } 
                        
                        $widget['data']['mz_suffix'] = $widget['entry_id'] . $widget['suffix'];
                        $data[$shortcode] = $this->load->controller('extension/mz_widget/' . $widget['code'], $widget['data']);
                        
                        if($widget['cache']){
                            $this->mz_cache->set($cache_key, $data[$shortcode]);
                            
                            $this->_asset_capture_end($widget['code'] . $widget['entry_id']);
                        }
                    }
                }
            }
        }
        
        return str_replace(array_keys($data), array_values($data), $entry_layout);
    }
        
    /**
     * Get css classes base on entry data
     * @param array $entry entry data
     * @return string css classes
     */
    private function getUtilityClasses(array $entry): string {
        $class = array();
        
        // Hidden
        $is_prev_device_hidden = 0;
        $display = 'block';
        if(in_array($entry['type'], array('row', 'col', 'section', 'component'))){ // Flex container
            $display = 'flex';
        }
        foreach($entry['device_hidden'] as $device => $is_hidden){
            if($is_hidden != $is_prev_device_hidden){
                if($is_hidden){
                    $class[] = 'd-' . ($device !== 'xs'? ($device . '-') : '') . 'none';
                } else {
                    $class[] = 'd-' . ($device !== 'xs'? ($device . '-') : '') . $display;
                }
                $is_prev_device_hidden = $is_hidden;
            }
        }

        // Column size
        if(!empty($entry['device_size'])){
            $prev_device_size = 0;
            foreach($entry['device_size'] as $device => $size){
                if($device == 'xs'){
                    $class[] = 'col-' . $size;
                } elseif($size != $prev_device_size){
                    $class[] = 'col-' . $device . '-' . $size;
                }
                $prev_device_size = $size;
            }
        }
        
        // Column order
        if(!empty($entry['device_order'])){
            $prev_device_order = 0;
            foreach($entry['device_order'] as $device => $order){
                --$order; // Substract by 1 and start from 0
                
                if($order > -1 && $order != $prev_device_order){
                    if($device == 'xs'){
                        $class[] = 'order-' . $order;
                    } else {
                        $class[] = 'order-' . $device . '-' . $order;
                    }
                    $prev_device_order = $order;
                }
            }
        } 
        
        // Styles
        $screen_sizes = array('xs', 'sm', 'md', 'lg', 'xl');
        
        // Flex direction
        $prev_breakpoint_flex_direction = 'row'; // Previous breakpoint value or default value
        if(in_array($entry['type'], array('section', 'component'))){
            $prev_breakpoint_flex_direction = 'column';
        }
        foreach ($screen_sizes as $breakpoint) {
            if(!empty($entry['setting'][$breakpoint][$entry['type'] . '_flex_direction']) && $entry['setting'][$breakpoint][$entry['type'] . '_flex_direction'] !== $prev_breakpoint_flex_direction){
                $class[] = 'flex-' . (($breakpoint !== 'xs')?$breakpoint . '-' : '') . $entry['setting'][$breakpoint][$entry['type'] . '_flex_direction'];
                $prev_breakpoint_flex_direction = $entry['setting'][$breakpoint][$entry['type'] . '_flex_direction'];
            }
        }
            
        
        // justify content
        $prev_breakpoint_justify_content = 'start'; // Previous breakpoint value or default value
        foreach ($screen_sizes as $breakpoint) {
            if(!empty($entry['setting'][$breakpoint][$entry['type'] . '_justify_content']) && $entry['setting'][$breakpoint][$entry['type'] . '_justify_content'] !== $prev_breakpoint_justify_content){
                
                $class[] = 'justify-content-' . (($breakpoint !== 'xs')?$breakpoint . '-' : '') . $entry['setting'][$breakpoint][$entry['type'] . '_justify_content'];
                $prev_breakpoint_justify_content = $entry['setting'][$breakpoint][$entry['type'] . '_justify_content'];
                
            }
        }
        
        // align items
        $prev_breakpoint_align_items = 'stretch'; // Previous breakpoint value or default value
        foreach ($screen_sizes as $breakpoint) {
            if(!empty($entry['setting'][$breakpoint][$entry['type'] . '_align_items']) && $entry['setting'][$breakpoint][$entry['type'] . '_align_items'] !== $prev_breakpoint_align_items){
                
                $class[] = 'align-items-' . (($breakpoint !== 'xs')?$breakpoint . '-' : '') . $entry['setting'][$breakpoint][$entry['type'] . '_align_items'];
                $prev_breakpoint_align_items = $entry['setting'][$breakpoint][$entry['type'] . '_align_items'];
                
            }
        }
        
        // flex wrap
        $prev_breakpoint_flex_wrap = 'nowrap'; // Previous breakpoint value or default value
        foreach ($screen_sizes as $breakpoint) {
            if(!empty($entry['setting'][$breakpoint][$entry['type'] . '_flex_wrap']) && $entry['setting'][$breakpoint][$entry['type'] . '_flex_wrap'] !== $prev_breakpoint_flex_wrap){
                
                $class[] = 'flex-' . (($breakpoint !== 'xs')?$breakpoint . '-' : '') . $entry['setting'][$breakpoint][$entry['type'] . '_flex_wrap'];
                $prev_breakpoint_flex_wrap = $entry['setting'][$breakpoint][$entry['type'] . '_flex_wrap'];
                
            }   
        }   
            
        // Flex grow
        $prev_breakpoint_flex_grow = '1'; // Previous breakpoint value or default value
        foreach ($screen_sizes as $breakpoint) {
            if(isset($entry['setting'][$breakpoint][$entry['type'] . '_flex_grow']) && $entry['setting'][$breakpoint][$entry['type'] . '_flex_grow'] != $prev_breakpoint_flex_grow){

                $class[] = 'flex-' . (($breakpoint !== 'xs')?$breakpoint . '-' : '') . 'grow-' . $entry['setting'][$breakpoint][$entry['type'] . '_flex_grow'];
                $prev_breakpoint_flex_grow = $entry['setting'][$breakpoint][$entry['type'] . '_flex_grow'];

            }
        }
        
        // Flex shrink
        $prev_breakpoint_flex_shrink = '1'; // Previous breakpoint value or default value
        foreach ($screen_sizes as $breakpoint) {
            if(isset($entry['setting'][$breakpoint][$entry['type'] . '_flex_shrink']) && $entry['setting'][$breakpoint][$entry['type'] . '_flex_shrink'] != $prev_breakpoint_flex_shrink){
                
                $class[] = 'flex-' . (($breakpoint !== 'xs')?$breakpoint . '-' : '') . 'shrink-' . $entry['setting'][$breakpoint][$entry['type'] . '_flex_shrink'];
                $prev_breakpoint_flex_shrink = $entry['setting'][$breakpoint][$entry['type'] . '_flex_shrink'];
                
            }
        }
        
        // Align self
        $prev_breakpoint_align_self = 'auto'; // Previous breakpoint value or default value
        foreach ($screen_sizes as $breakpoint) { 
            if(!empty($entry['setting'][$breakpoint][$entry['type'] . '_align_self']) && $entry['setting'][$breakpoint][$entry['type'] . '_align_self'] !== $prev_breakpoint_align_self){
                
                $class[] = 'align-self-' . (($breakpoint !== 'xs')?$breakpoint . '-' : '') . $entry['setting'][$breakpoint][$entry['type'] . '_align_self'];
                $prev_breakpoint_align_self = $entry['setting'][$breakpoint][$entry['type'] . '_align_self'];
                
            }
        }
        
        // Text align
        $prev_breakpoint_text_align = 'default';
        foreach ($screen_sizes as $breakpoint) {
            if(!empty($entry['setting'][$breakpoint][$entry['type'] . '_text_align']) && !in_array($entry['setting'][$breakpoint][$entry['type'] . '_text_align'], array('default', $prev_breakpoint_text_align))){
                $class[] = 'text-' . (($breakpoint !== 'xs')?$breakpoint . '-' : '') . $entry['setting'][$breakpoint][$entry['type'] . '_text_align'];
                $prev_breakpoint_text_align = $entry['setting'][$breakpoint][$entry['type'] . '_text_align'];
            }
        }
        
        return implode(' ', $class);
    }
        
    /**
     * Get css of entry
     * @param array $entry entry data
     * @return string entry css or sass
     */
    private function getEntryCSS(array $entry): string {
        $scss = '';
        
        $screen_sizes = array('xs', 'sm', 'md', 'lg', 'xl');
        
        foreach($screen_sizes as $screen_size){
            $data = array();
            
            if(isset($entry['setting'][$screen_size])){
                
                foreach($entry['setting'][$screen_size] as $key => $value){
                    $data['style'][substr($key, strlen($entry['type'])+1)] = $value;
                }
                
                // Get layer wise list of background image
                $data['style']['background'] = array();
                foreach ($entry['setting'][$screen_size][$entry['type'] . '_background_image'] as $layer => $background) {
                    $bg_data = array();

                    // Background image
                    $image = maza\getOfLanguage($background['image']);
                    if($background['status'] == 'image' && $image && is_file(DIR_IMAGE . $image)){
                        if ($this->config->get('maza_webp')) {
                            list($width, $height) = getimagesize(DIR_IMAGE . $image);
                            $bg_data[] = 'url("' . $this->model_tool_image->resize($image, $width, $height) . '")';
                        } else {
                            $bg_data[] = 'url("' . $this->config->get('mz_store_url') . 'image/' . $image . '")';
                        }
                        
                        $image_position_and_size = str_replace('_', ' ', $background['image_position']);
                        if($background['image_size'] !== 'initial'){
                            $image_position_and_size .= '/' . $background['image_size'];
                        }
                        
                        $bg_data[] = $image_position_and_size;
                        $bg_data[] = $background['image_repeat'];
                        
                        if($background['image_attachment'] !== 'initial'){
                            $bg_data[] = $background['image_attachment'];
                        }
                    }

                    // Background patter
                    elseif($background['status'] == 'pattern' && !empty($background['overlay_pattern'])){
                        $overlay_pattern = $this->model_extension_maza_asset->overlayPatterns($background['overlay_pattern']);

                        if($overlay_pattern){
                            $bg_data[] = 'url("' . $this->config->get('mz_store_url') . 'image/' . $overlay_pattern['image'] . '")';
                        }
                    }

                    if($bg_data){
                        if(!empty($entry['setting'][$screen_size][$entry['type'] . '_background_color'])){
                            array_unshift($bg_data, $entry['setting'][$screen_size][$entry['type'] . '_background_color']);
                        }

                        $data['style']['background'][] = implode(' ', $bg_data);
                    }
                }
                $data['style']['background'] = implode(', ', $data['style']['background']);

            }
            
            if($data){
                $data['entry_type'] = $entry['type'];
                $style = $this->load->view('extension/maza/layout_builder/sass', $data);
                
                if($style){
                    $scss .= $this->load->view('extension/maza/layout_builder/sass_media', ['entry_id' => $entry['entry_id'], 'suffix' => $this->suffix, 'type' => $entry['type'], 'screen_size' => $screen_size, 'style' => $style]);
                }
            }
        }
        
        return $scss;
    }
        
    /**
     * Get output of module
     * @param string $code
     * @return mixed
     */
    private function getModuleOutput($code, $entry_id, $suffix){
        $part = explode('.', $code);

        if (!isset($part[1]) && ($this->config->get('module_' . $part[0] . '_status') || $this->config->get($part[0] . '_status'))) {
            return $this->load->controller('extension/module/' . $part[0]);
        }

        if (isset($part[1])) {
            $setting_info = $this->model_extension_maza_opencart->getModule($part[1]);

            if ($setting_info && $setting_info['status']) {
                $setting_info['mz_suffix'] = $part[1] . $entry_id . $suffix;
                
                return $this->load->controller('extension/module/' . $part[0], $setting_info);
            }
        }
        
        return null;
    }
        
    private function _asset_capture_start(){
        $this->asset_data['orignal'] = array();
        
        // Document;
        $this->asset_data['orignal']['document'] = $this->document;
        $this->document = new Document();
        
        // Maza Document
        $this->asset_data['orignal']['mz_document'] = $this->mz_document;
        $this->mz_document = new Maza\Document();
    }
        
    private function _asset_capture_end($key){
        // Document
        if($this->document != new Document()){
            $this->asset_data['captured'][$key]['document'] = $this->document;
        }
        $this->document = $this->asset_data['orignal']['document'];
        
        // Maza Document
        $this->asset_data['orignal']['mz_document']->addCSSCode($this->mz_document->getCSSCode());
        $this->asset_data['orignal']['mz_document']->addJSCode($this->mz_document->getJSCode());
        if($this->mz_document->getSVG()){
            $this->asset_data['captured'][$key]['mz_document_svg'] = $this->mz_document->getSVG();
        }
        $this->mz_document = $this->asset_data['orignal']['mz_document'];
    }
        
    private function _asset_restore($captured_data){
        foreach($captured_data as $key => $asset){
            // Documents
            if(isset($asset['document'])){
                foreach($asset['document']->getLinks() as $link){
                    $this->document->addLink($link['href'], $link['rel']);
                }
                foreach($asset['document']->getStyles('header') as $style){
                    $this->document->addStyle($style['href'], $style['rel'], $style['media'], 'header');
                }
                foreach($asset['document']->getStyles('footer') as $style){
                    $this->document->addStyle($style['href'], $style['rel'], $style['media'], 'footer');
                }
                foreach($asset['document']->getScripts('header') as $href){
                    $this->document->addScript($href, 'header');
                }
                foreach($asset['document']->getScripts('footer') as $href){
                    $this->document->addScript($href, 'footer');
                }
            }
            
            // Maza Document SVG
            if(!empty($asset['mz_document_svg'])){
                foreach($asset['mz_document_svg'] as $svg){
                    $this->mz_document->addSVG($svg);
                }
            }
        }
    }
}
