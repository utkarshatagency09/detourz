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
abstract class Widget extends \Controller{
        /**
         * Get cache of widget
         * @param string $key cache key
         * @return mixed
         */
        protected function getCache(string $key){
            return $this->mz_cache->get($this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '.' . $this->config->get('config_store_id') . '.widget.' . $key . $this->session->data['currency'] . $this->config->get('config_language_id') . (int)$this->mz_browser->isSupportedWebp());
        }
        
        /**
         * Set cache of widget
         * @param string $key cache key
         * @param mixed $expire expire timestamp
         * @return null
         */
        protected function setCache(string $key, $value, $expire = true){
            $this->mz_cache->set($this->mz_theme_config->get('theme_code') . '.' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '.' . $this->config->get('config_store_id') . '.widget.' . $key . $this->session->data['currency'] . $this->config->get('config_language_id') . (int)$this->mz_browser->isSupportedWebp(), $value, $expire);
        }
        
        /**
         * Default settings
         */
        public function getSettings(): array {
                $screen_sizes = array('xl', 'lg', 'md', 'sm', 'xs');
                
                $setting = array();
                $setting['widget_custom_class'] = '';
                $setting['widget_condition']    = '';
                
                $setting['widget_status_customer'] = 'all';
                $setting['widget_status_customer_group'] = 0;
                $setting['widget_status_date_start']   = '';
                $setting['widget_status_date_end']     = '';
                $setting['widget_cache']               = 0;
                
                
                // Style
                $setting['xl'] = $setting['lg'] = $setting['md'] = 
                $setting['sm'] = $setting['xs'] = array(
                    'widget_margin_top' => '',
                    'widget_margin_bottom' => '',
                    'widget_margin_left' => '',
                    'widget_margin_right' => '',
                    'widget_padding_top' => '',
                    'widget_padding_bottom' => '',
                    'widget_padding_left' => '',
                    'widget_padding_right' => '',
                    'widget_border_top' => '',
                    'widget_border_bottom' => '',
                    'widget_border_left' => '',
                    'widget_border_right' => '',
                    'widget_text_color' => '',
                    'widget_text_size' => '',
                    'widget_border_color' => '',
                    'widget_background_color' => '',
                    'widget_flex_grow' => 1,
                    'widget_flex_shrink' => 1,
                    'widget_align_self' => 'auto'
                );
                
                foreach($screen_sizes as $size){
                    for($layer = 1; $layer <= 3; $layer++){
                        $setting[$size]['widget_background_image']['layer_' . $layer] = array(
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
        
        /**
         * @admin
         * Edit widget
         */
        public function edit(): void {
                $this->load->controller('extension/maza/layout_builder/edit_widget', $this->getSettings());
        }

        /**
         * Output of widget to reload content of it in webpage
         */
        public function reload(): void {
            $this->load->model('extension/maza/layout_builder');
                
            $setting = $this->model_extension_maza_layout_builder->getEntrySetting($this->request->get['entry_id']);

            if ($setting && $setting['widget_status']) {
                $this->response->setOutput($this->index($setting));
            }
        }
}
