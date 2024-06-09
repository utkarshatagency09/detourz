<?php
class ControllerExtensionMzWidgetBanner extends maza\layout\Widget {
	public function index($setting) {
        $data = array();
        
        $data['heading_title'] = maza\getOfLanguage($setting['widget_title']);
        
        $data['alt'] = maza\getOfLanguage($setting['widget_alt']);
        
        $data['widget_banner_effect'] = $setting['widget_banner_effect'];
        $data['banner_width']      = $setting['widget_banner_width'];
        $data['banner_height']     = $setting['widget_banner_height'];
        $data['lazy_loading']      = $setting['widget_lazy_loading'];
        
        // Caption
        $data['banner_caption'] = maza\getOfLanguage($setting['widget_banner_caption']);
        
        $data['banner_image'] = false;
        $data['banner_srcset'] = null;
        $data['srcset_sizes'] = null;
        
        // Image
        $widget_banner_image = maza\getOfLanguage($setting['widget_banner_image']);
        if($setting['widget_banner_width'] || $setting['widget_banner_height'] || is_file(DIR_IMAGE . $widget_banner_image)){
            
            list($banner_width, $banner_height) = $this->model_extension_maza_image->getEstimatedSize($widget_banner_image, $setting['widget_banner_width'], $setting['widget_banner_height']);
            
            $data['banner_image'] = $this->model_tool_image->resize($widget_banner_image?:'placeholder', $banner_width, $banner_height);
            
            $data['banner_width'] = $banner_width;
            $data['banner_height'] = $banner_height;
            
            // Srcset
            $data['banner_srcset'] = $this->model_extension_maza_image->getSrcSet($setting['widget_banner_srcset'], $widget_banner_image, $banner_width, $banner_height);
            $data['srcset_sizes'] = $this->model_extension_maza_image->getSrcSetSize($setting['widget_banner_srcset'], $banner_width);
        }
        
        $data['url_target'] = $setting['widget_url_target'];

        if ($setting['widget_url_link_code']) {
            $data['url'] = $this->model_extension_maza_common->createLink($setting['widget_url_link_code']);
        } else {
            $data['url'] = array();
        }
        
        if($data['banner_image']){
            return $this->load->view('extension/mz_widget/banner', $data);
        }
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
