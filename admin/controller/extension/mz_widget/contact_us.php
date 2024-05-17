<?php
class ControllerExtensionMzWidgetContactUs extends maza\layout\Widget {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_widget/contact_us');
        
        $this->load->model('localisation/language');
        $this->load->model('tool/image');

        $data = array();
        
        // Status
        if(isset($this->request->post['widget_status'])){
            $data['widget_status'] = $this->request->post['widget_status'];
        } else {
            $data['widget_status'] =  0;
        }
        
        // Title
        if(isset($this->request->post['widget_title'])){
            $data['widget_title'] = $this->request->post['widget_title'];
        } else {
            $data['widget_title'] = array();
        }
        
        // Icon size
        if(isset($this->request->post['widget_icon_width'])){
            $data['widget_icon_width'] = $this->request->post['widget_icon_width'];
        } else {
            $data['widget_icon_width'] =  20;
        }
        
        if(isset($this->request->post['widget_icon_height'])){
            $data['widget_icon_height'] = $this->request->post['widget_icon_height'];
        } else {
            $data['widget_icon_height'] =  20;
        }
        
        // Image
        if(isset($this->request->post['widget_image_alt'])){
            $data['widget_image_alt'] = $this->request->post['widget_image_alt'];
        } else {
            $data['widget_image_alt'] = array();
        }
        
        if(isset($this->request->post['widget_image_image'])){
            $data['widget_image_image'] = $this->request->post['widget_image_image'];
        } else {
            $data['widget_image_image'] =  array();
        }
        if(isset($this->request->post['widget_image_svg'])){
            $data['widget_image_svg'] = $this->request->post['widget_image_svg'];
        } else {
            $data['widget_image_svg'] =  array();
        }
        if(isset($this->request->post['widget_image_font'])){
            $data['widget_image_font'] = $this->request->post['widget_image_font'];
        } else {
            $data['widget_image_font'] =  array();
        }
        if(isset($this->request->post['widget_image_width'])){
            $data['widget_image_width'] = $this->request->post['widget_image_width'];
        } else {
            $data['widget_image_width'] =  '';
        }
        if(isset($this->request->post['widget_image_height'])){
            $data['widget_image_height'] = $this->request->post['widget_image_height'];
        } else {
            $data['widget_image_height'] =  '';
        }
        if(isset($this->request->post['widget_image_size'])){
            $data['widget_image_size'] = $this->request->post['widget_image_size'];
        } else {
            $data['widget_image_size'] =  '';
        }
        
        // Thumb
        $data['thumb_image_image'] = array();
        if (isset($this->request->post['widget_image_image'])){
            foreach ($this->request->post['widget_image_image'] as $language_id => $image_image) {
                if($image_image){
                    $data['thumb_image_image'][$language_id] = $this->model_tool_image->resize($image_image, 100, 100);
                } else {
                    $data['thumb_image_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        $data['thumb_image_svg'] = array();
        if (isset($this->request->post['widget_image_svg'])){
            foreach ($this->request->post['widget_image_svg'] as $language_id => $image_svg) {
                if($image_svg){
                    $data['thumb_image_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
                } else {
                    $data['thumb_image_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        // Address
        if(isset($this->request->post['widget_address_status'])){
            $data['widget_address_status'] = $this->request->post['widget_address_status'];
        } else {
            $data['widget_address_status'] =  1;
        }
        
        if(isset($this->request->post['widget_address_sort_order'])){
            $data['widget_address_sort_order'] = $this->request->post['widget_address_sort_order'];
        } else {
            $data['widget_address_sort_order'] =  0;
        }
        
        if(isset($this->request->post['widget_address_icon_image'])){
            $data['widget_address_icon_image'] = $this->request->post['widget_address_icon_image'];
        } else {
            $data['widget_address_icon_image'] =  array();
        }
        if(isset($this->request->post['widget_address_icon_svg'])){
            $data['widget_address_icon_svg'] = $this->request->post['widget_address_icon_svg'];
        } else {
            $data['widget_address_icon_svg'] =  array();
        }
        if(isset($this->request->post['widget_address_icon_font'])){
            $data['widget_address_icon_font'] = $this->request->post['widget_address_icon_font'];
        } else {
            $data['widget_address_icon_font'] =  array();
        }
        $data['thumb_address_icon_image'] = array();
        if (isset($this->request->post['widget_address_icon_image'])){
            foreach ($this->request->post['widget_address_icon_image'] as $language_id => $icon_image) {
                if($icon_image){
                    $data['thumb_address_icon_image'][$language_id] = $this->model_tool_image->resize($icon_image, 100, 100);
                } else {
                    $data['thumb_address_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        $data['thumb_address_icon_svg'] = array();
        if (isset($this->request->post['widget_address_icon_svg'])){
            foreach ($this->request->post['widget_address_icon_svg'] as $language_id => $icon_svg) {
                if($icon_svg){
                    $data['thumb_address_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $icon_svg;
                } else {
                    $data['thumb_address_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        // Geocode
        if(isset($this->request->post['widget_geocode_status'])){
            $data['widget_geocode_status'] = $this->request->post['widget_geocode_status'];
        } else {
            $data['widget_geocode_status'] =  1;
        }
        
        if(isset($this->request->post['widget_geocode_sort_order'])){
            $data['widget_geocode_sort_order'] = $this->request->post['widget_geocode_sort_order'];
        } else {
            $data['widget_geocode_sort_order'] =  0;
        }
        
        if(isset($this->request->post['widget_geocode_icon_image'])){
            $data['widget_geocode_icon_image'] = $this->request->post['widget_geocode_icon_image'];
        } else {
            $data['widget_geocode_icon_image'] =  array();
        }
        if(isset($this->request->post['widget_geocode_icon_svg'])){
            $data['widget_geocode_icon_svg'] = $this->request->post['widget_geocode_icon_svg'];
        } else {
            $data['widget_geocode_icon_svg'] =  array();
        }
        if(isset($this->request->post['widget_geocode_icon_font'])){
            $data['widget_geocode_icon_font'] = $this->request->post['widget_geocode_icon_font'];
        } else {
            $data['widget_geocode_icon_font'] =  array();
        }
        $data['thumb_geocode_icon_image'] = array();
        if (isset($this->request->post['widget_geocode_icon_image'])){
            foreach ($this->request->post['widget_geocode_icon_image'] as $language_id => $icon_image) {
                if($icon_image){
                    $data['thumb_geocode_icon_image'][$language_id] = $this->model_tool_image->resize($icon_image, 100, 100);
                } else {
                    $data['thumb_geocode_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        $data['thumb_geocode_icon_svg'] = array();
        if (isset($this->request->post['widget_geocode_icon_svg'])){
            foreach ($this->request->post['widget_geocode_icon_svg'] as $language_id => $icon_svg) {
                if($icon_svg){
                    $data['thumb_geocode_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $icon_svg;
                } else {
                    $data['thumb_geocode_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        // Email
        if(isset($this->request->post['widget_email_status'])){
            $data['widget_email_status'] = $this->request->post['widget_email_status'];
        } else {
            $data['widget_email_status'] =  1;
        }
        
        if(isset($this->request->post['widget_email_sort_order'])){
            $data['widget_email_sort_order'] = $this->request->post['widget_email_sort_order'];
        } else {
            $data['widget_email_sort_order'] =  0;
        }
        
        if(isset($this->request->post['widget_email_icon_image'])){
            $data['widget_email_icon_image'] = $this->request->post['widget_email_icon_image'];
        } else {
            $data['widget_email_icon_image'] =  array();
        }
        if(isset($this->request->post['widget_email_icon_svg'])){
            $data['widget_email_icon_svg'] = $this->request->post['widget_email_icon_svg'];
        } else {
            $data['widget_email_icon_svg'] =  array();
        }
        if(isset($this->request->post['widget_email_icon_font'])){
            $data['widget_email_icon_font'] = $this->request->post['widget_email_icon_font'];
        } else {
            $data['widget_email_icon_font'] =  array();
        }
        $data['thumb_email_icon_image'] = array();
        if (isset($this->request->post['widget_email_icon_image'])){
            foreach ($this->request->post['widget_email_icon_image'] as $language_id => $icon_image) {
                if($icon_image){
                    $data['thumb_email_icon_image'][$language_id] = $this->model_tool_image->resize($icon_image, 100, 100);
                } else {
                    $data['thumb_email_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        $data['thumb_email_icon_svg'] = array();
        if (isset($this->request->post['widget_email_icon_svg'])){
            foreach ($this->request->post['widget_email_icon_svg'] as $language_id => $icon_svg) {
                if($icon_svg){
                    $data['thumb_email_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $icon_svg;
                } else {
                    $data['thumb_email_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        // Telephone
        if(isset($this->request->post['widget_telephone_status'])){
            $data['widget_telephone_status'] = $this->request->post['widget_telephone_status'];
        } else {
            $data['widget_telephone_status'] =  1;
        }
        
        if(isset($this->request->post['widget_telephone_sort_order'])){
            $data['widget_telephone_sort_order'] = $this->request->post['widget_telephone_sort_order'];
        } else {
            $data['widget_telephone_sort_order'] =  0;
        }
        
        if(isset($this->request->post['widget_telephone_icon_image'])){
            $data['widget_telephone_icon_image'] = $this->request->post['widget_telephone_icon_image'];
        } else {
            $data['widget_telephone_icon_image'] =  array();
        }
        if(isset($this->request->post['widget_telephone_icon_svg'])){
            $data['widget_telephone_icon_svg'] = $this->request->post['widget_telephone_icon_svg'];
        } else {
            $data['widget_telephone_icon_svg'] =  array();
        }
        if(isset($this->request->post['widget_telephone_icon_font'])){
            $data['widget_telephone_icon_font'] = $this->request->post['widget_telephone_icon_font'];
        } else {
            $data['widget_telephone_icon_font'] =  array();
        }
        $data['thumb_telephone_icon_image'] = array();
        if (isset($this->request->post['widget_telephone_icon_image'])){
            foreach ($this->request->post['widget_telephone_icon_image'] as $language_id => $icon_image) {
                if($icon_image){
                    $data['thumb_telephone_icon_image'][$language_id] = $this->model_tool_image->resize($icon_image, 100, 100);
                } else {
                    $data['thumb_telephone_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        $data['thumb_telephone_icon_svg'] = array();
        if (isset($this->request->post['widget_telephone_icon_svg'])){
            foreach ($this->request->post['widget_telephone_icon_svg'] as $language_id => $icon_svg) {
                if($icon_svg){
                    $data['thumb_telephone_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $icon_svg;
                } else {
                    $data['thumb_telephone_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        // Fax
        if(isset($this->request->post['widget_fax_status'])){
            $data['widget_fax_status'] = $this->request->post['widget_fax_status'];
        } else {
            $data['widget_fax_status'] =  1;
        }
        
        if(isset($this->request->post['widget_fax_sort_order'])){
            $data['widget_fax_sort_order'] = $this->request->post['widget_fax_sort_order'];
        } else {
            $data['widget_fax_sort_order'] =  0;
        }
        
        if(isset($this->request->post['widget_fax_icon_image'])){
            $data['widget_fax_icon_image'] = $this->request->post['widget_fax_icon_image'];
        } else {
            $data['widget_fax_icon_image'] =  array();
        }
        if(isset($this->request->post['widget_fax_icon_svg'])){
            $data['widget_fax_icon_svg'] = $this->request->post['widget_fax_icon_svg'];
        } else {
            $data['widget_fax_icon_svg'] =  array();
        }
        if(isset($this->request->post['widget_fax_icon_font'])){
            $data['widget_fax_icon_font'] = $this->request->post['widget_fax_icon_font'];
        } else {
            $data['widget_fax_icon_font'] =  array();
        }
        $data['thumb_fax_icon_image'] = array();
        if (isset($this->request->post['widget_fax_icon_image'])){
            foreach ($this->request->post['widget_fax_icon_image'] as $language_id => $icon_image) {
                if($icon_image){
                    $data['thumb_fax_icon_image'][$language_id] = $this->model_tool_image->resize($icon_image, 100, 100);
                } else {
                    $data['thumb_fax_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        $data['thumb_fax_icon_svg'] = array();
        if (isset($this->request->post['widget_fax_icon_svg'])){
            foreach ($this->request->post['widget_fax_icon_svg'] as $language_id => $icon_svg) {
                if($icon_svg){
                    $data['thumb_fax_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $icon_svg;
                } else {
                    $data['thumb_fax_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        // Times
        if(isset($this->request->post['widget_times_status'])){
            $data['widget_times_status'] = $this->request->post['widget_times_status'];
        } else {
            $data['widget_times_status'] =  1;
        }
        
        if(isset($this->request->post['widget_times_sort_order'])){
            $data['widget_times_sort_order'] = $this->request->post['widget_times_sort_order'];
        } else {
            $data['widget_times_sort_order'] =  0;
        }
        
        if(isset($this->request->post['widget_times_icon_image'])){
            $data['widget_times_icon_image'] = $this->request->post['widget_times_icon_image'];
        } else {
            $data['widget_times_icon_image'] =  array();
        }
        if(isset($this->request->post['widget_times_icon_svg'])){
            $data['widget_times_icon_svg'] = $this->request->post['widget_times_icon_svg'];
        } else {
            $data['widget_times_icon_svg'] =  array();
        }
        if(isset($this->request->post['widget_times_icon_font'])){
            $data['widget_times_icon_font'] = $this->request->post['widget_times_icon_font'];
        } else {
            $data['widget_times_icon_font'] =  array();
        }
        $data['thumb_times_icon_image'] = array();
        if (isset($this->request->post['widget_times_icon_image'])){
            foreach ($this->request->post['widget_times_icon_image'] as $language_id => $icon_image) {
                if($icon_image){
                    $data['thumb_times_icon_image'][$language_id] = $this->model_tool_image->resize($icon_image, 100, 100);
                } else {
                    $data['thumb_times_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        $data['thumb_times_icon_svg'] = array();
        if (isset($this->request->post['widget_times_icon_svg'])){
            foreach ($this->request->post['widget_times_icon_svg'] as $language_id => $icon_svg) {
                if($icon_svg){
                    $data['thumb_times_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $icon_svg;
                } else {
                    $data['thumb_times_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        // Image
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        $this->response->setOutput($this->load->view('extension/mz_widget/contact_us', $data));
    }
    
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_widget/contact_us')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
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
