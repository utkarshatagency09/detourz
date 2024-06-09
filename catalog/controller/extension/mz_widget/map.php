<?php
class ControllerExtensionMzWidgetMap extends maza\layout\Widget {
        private static $instance_count = 0;
        
        public function index(array $setting) {
                $this->load->language('extension/mz_widget/map');
                
                // Title
                $data['heading_title']      =   maza\getOfLanguage($setting['widget_title']);
                
                $data['longitude']          =   $setting['widget_longitude'];
                $data['latitude']           =   $setting['widget_latitude'];
                $data['zoom']               =   $setting['widget_zoom'];
                $data['height']             =   $setting['widget_height'];
                $data['marker']             =   $setting['widget_marker'];
                $data['marker_icon']        =   maza\getImageURL($setting['widget_marker_icon']);
                $data['control_zoom']       =   $setting['widget_control_zoom'];
                $data['control_maptype']    =   $setting['widget_control_maptype'];
                $data['control_scale']      =   $setting['widget_control_scale'];
                $data['control_streetview'] =   $setting['widget_control_streetview'];
                $data['control_rotate']     =   $setting['widget_control_rotate'];
                $data['control_fullscreen'] =   $setting['widget_control_fullscreen'];
                $data['api_key']            =   $this->config->get('maza_api_google_map_key');
                $data['language_code']      =   $this->session->data['language'];

                // Google js should only run onetime when muliple map widget added
                $data['include_js']         =   self::$instance_count == 0;
                self::$instance_count++;
                
                return $this->load->view('extension/mz_widget/map', $data);			
	}

        /**
         * Change default setting
         */
        public function getSettings(): array {
                $setting = parent::getSettings();
                
                $setting['widget_cache'] = 'hard';
                
                return $setting;
        }
}
