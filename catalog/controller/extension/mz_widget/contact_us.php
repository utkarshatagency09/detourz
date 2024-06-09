<?php
class ControllerExtensionMzWidgetContactUs extends maza\layout\Widget {
    public function index($setting) {
        $data = array();
        
        $this->load->language('extension/mz_widget/contact_us');
        
        // Form title
        $data['heading_title'] = maza\getOfLanguage($setting['widget_title']);
        
        // Icon
        $data['icon_width']     = $setting['widget_icon_width'];
        $data['icon_height']    = $setting['widget_icon_height'];
        
        // Image
        $data['widget_image_status'] = $setting['widget_image_status'];
        
        if($setting['widget_image_status']){
            $data['image_alt']       = $setting['widget_image_alt'];
            $data['image_width']     = $setting['widget_image_width'];
            $data['image_height']    = $setting['widget_image_height'];
            $data['image_size']      = $setting['widget_image_size'];

            $data['image_font'] = maza\getOfLanguage($setting['widget_image_font']);

            $widget_image_svg = maza\getOfLanguage($setting['widget_image_svg']);
            if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_image_svg)){
                $data['image_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_image_svg);
            } else {
                $data['image_svg'] = false;
            }

            $widget_image_image = maza\getOfLanguage($setting['widget_image_image']);
            if(is_file(DIR_IMAGE . $widget_image_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($widget_image_image, $setting['widget_image_width'], $setting['widget_image_height']);
                
                $data['image_width'] = $image_width;
                $data['image_height'] = $image_height;

                $data['image_image'] = $this->model_tool_image->resize($widget_image_image, $image_width, $image_height);
            } else {
                $data['image_image'] = false;
            }
        }
        
        // Contact details
        $data['contact'] = array();
        
        // Address
        if($setting['widget_address_status'] && $this->config->get('config_address')){
            $data['contact']['address'] = array(
                'sort_order' => $setting['widget_address_sort_order'],
                'data'       => $this->config->get('config_address')
            );
            
            // Icon
            $data['contact']['address']['icon_font'] = maza\getOfLanguage($setting['widget_address_icon_font']);
            
            $widget_address_icon_svg = maza\getOfLanguage($setting['widget_address_icon_svg']);
            if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_address_icon_svg)){
                $data['contact']['address']['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_address_icon_svg);
            } else {
                $data['contact']['address']['icon_svg'] = false;
            }
            
            $widget_address_icon_image = maza\getOfLanguage($setting['widget_address_icon_image']);
            if(is_file(DIR_IMAGE . $widget_address_icon_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($widget_address_icon_image, $setting['widget_icon_width'], $setting['widget_icon_height']);

                $data['contact']['address']['image_width'] = $image_width;
                $data['contact']['address']['image_height'] = $image_height;

                $data['contact']['address']['icon_image'] = $this->model_tool_image->resize($widget_address_icon_image, $image_width, $image_height);
            } else {
                $data['contact']['address']['icon_image'] = false;
            }
        }
        
        // Geocode
        if($setting['widget_geocode_status'] && $this->config->get('config_geocode')){
            $data['geocode_hl'] = $this->config->get('config_language');
            
            $data['contact']['geocode'] = array(
                'sort_order' => $setting['widget_geocode_sort_order'],
                'data'       => $this->config->get('config_geocode')
            );
            
            // Icon
            $data['contact']['geocode']['icon_font'] = maza\getOfLanguage($setting['widget_geocode_icon_font']);
            
            $widget_geocode_icon_svg = maza\getOfLanguage($setting['widget_geocode_icon_svg']);
            if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_geocode_icon_svg)){
                $data['contact']['geocode']['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_geocode_icon_svg);
            } else {
                $data['contact']['geocode']['icon_svg'] = false;
            }
            
            $widget_geocode_icon_image = maza\getOfLanguage($setting['widget_geocode_icon_image']);
            if(is_file(DIR_IMAGE . $widget_geocode_icon_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($widget_geocode_icon_image, $setting['widget_icon_width'], $setting['widget_icon_height']);
                
                $data['contact']['geocode']['image_width'] = $image_width;
                $data['contact']['geocode']['image_height'] = $image_height;

                $data['contact']['geocode']['icon_image'] = $this->model_tool_image->resize($widget_geocode_icon_image, $image_width, $image_height);
            } else {
                $data['contact']['geocode']['icon_image'] = false;
            }
        }
        
        // Telephone
        if($setting['widget_telephone_status'] && $this->config->get('config_telephone')){
            $data['contact']['telephone'] = array(
                'sort_order' => $setting['widget_telephone_sort_order'],
                'data'       => sprintf($this->language->get('entry_tel_%'), $this->config->get('config_telephone'))
            );
            
            // Icon
            $data['contact']['telephone']['icon_font'] = maza\getOfLanguage($setting['widget_telephone_icon_font']);
            
            $widget_telephone_icon_svg = maza\getOfLanguage($setting['widget_telephone_icon_svg']);
            if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_telephone_icon_svg)){
                $data['contact']['telephone']['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_telephone_icon_svg);
            } else {
                $data['contact']['telephone']['icon_svg'] = false;
            }
            
            $widget_telephone_icon_image = maza\getOfLanguage($setting['widget_telephone_icon_image']);
            if(is_file(DIR_IMAGE . $widget_telephone_icon_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($widget_telephone_icon_image, $setting['widget_icon_width'], $setting['widget_icon_height']);

                $data['contact']['telephone']['image_width'] = $image_width;
                $data['contact']['telephone']['image_height'] = $image_height;

                $data['contact']['telephone']['icon_image'] = $this->model_tool_image->resize($widget_telephone_icon_image, $image_width, $image_height);
            } else {
                $data['contact']['telephone']['icon_image'] = false;
            }
        }
        
        // Email
        if($setting['widget_email_status'] && $this->config->get('config_email')){
            $data['contact']['email'] = array(
                'sort_order' => $setting['widget_email_sort_order'],
                'data'       => $this->config->get('config_email')
            );
            
            // Icon
            $data['contact']['email']['icon_font'] = maza\getOfLanguage($setting['widget_email_icon_font']);
            
            $widget_email_icon_svg = maza\getOfLanguage($setting['widget_email_icon_svg']);
            if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_email_icon_svg)){
                $data['contact']['email']['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_email_icon_svg);
            } else {
                $data['contact']['email']['icon_svg'] = false;
            }
            
            $widget_email_icon_image = maza\getOfLanguage($setting['widget_email_icon_image']);
            if(is_file(DIR_IMAGE . $widget_email_icon_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($widget_email_icon_image, $setting['widget_icon_width'], $setting['widget_icon_height']);

                $data['contact']['email']['image_width'] = $image_width;
                $data['contact']['email']['image_height'] = $image_height;

                $data['contact']['email']['icon_image'] = $this->model_tool_image->resize($widget_email_icon_image, $image_width, $image_height);
            } else {
                $data['contact']['email']['icon_image'] = false;
            }
        }
        
        // Fax
        if($setting['widget_fax_status'] && $this->config->get('config_fax')){
            $data['contact']['fax'] = array(
                'sort_order' => $setting['widget_fax_sort_order'],
                'data'       => sprintf($this->language->get('entry_fax_%'), $this->config->get('config_fax'))
            );
            
            // Icon
            $data['contact']['fax']['icon_font'] = maza\getOfLanguage($setting['widget_fax_icon_font']);
            
            $widget_fax_icon_svg = maza\getOfLanguage($setting['widget_fax_icon_svg']);
            if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_fax_icon_svg)){
                $data['contact']['fax']['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_fax_icon_svg);
            } else {
                $data['contact']['fax']['icon_svg'] = false;
            }
            
            $widget_fax_icon_image = maza\getOfLanguage($setting['widget_fax_icon_image']);
            if(is_file(DIR_IMAGE . $widget_fax_icon_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($widget_fax_icon_image, $setting['widget_icon_width'], $setting['widget_icon_height']);

                $data['contact']['fax']['image_width'] = $image_width;
                $data['contact']['fax']['image_height'] = $image_height;

                $data['contact']['fax']['icon_image'] = $this->model_tool_image->resize($widget_fax_icon_image, $image_width, $image_height);
            } else {
                $data['contact']['fax']['icon_image'] = false;
            }
        }
        
        // Times
        if($setting['widget_times_status'] && $this->config->get('config_open')){
            $data['contact']['times'] = array(
                'sort_order' => $setting['widget_times_sort_order'],
                'data'       => $this->config->get('config_open')
            );
            
            // Icon
            $data['contact']['times']['icon_font'] = maza\getOfLanguage($setting['widget_times_icon_font']);
            
            $widget_times_icon_svg = maza\getOfLanguage($setting['widget_times_icon_svg']);
            if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_times_icon_svg)){
                $data['contact']['times']['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $widget_times_icon_svg);
            } else {
                $data['contact']['times']['icon_svg'] = false;
            }
            
            $widget_times_icon_image = maza\getOfLanguage($setting['widget_times_icon_image']);
            if(is_file(DIR_IMAGE . $widget_times_icon_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($widget_times_icon_image, $setting['widget_icon_width'], $setting['widget_icon_height']);

                $data['contact']['times']['image_width'] = $image_width;
                $data['contact']['times']['image_height'] = $image_height;

                $data['contact']['times']['icon_image'] = $this->model_tool_image->resize($widget_times_icon_image, $image_width, $image_height);
            } else {
                $data['contact']['times']['icon_image'] = false;
            }
        }
        
        // Sort data
        array_multisort(array_column($data['contact'], 'sort_order'), SORT_ASC, SORT_NUMERIC, $data['contact']);
        
        return $this->load->view('extension/mz_widget/contact_us', $data);
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
