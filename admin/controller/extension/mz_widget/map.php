<?php
class ControllerExtensionMzWidgetMap extends maza\layout\Widget {
    public function index() {
        $this->load->language('extension/mz_widget/map');
        
        $this->load->model('localisation/language');

        $data = array();
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
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
        
        if(isset($this->request->post['widget_longitude'])){
            $data['widget_longitude'] = $this->request->post['widget_longitude'];
        } else {
            $data['widget_longitude'] =  0;
        }
        
        if(isset($this->request->post['widget_latitude'])){
            $data['widget_latitude'] = $this->request->post['widget_latitude'];
        } else {
            $data['widget_latitude'] =  0;
        }
        
        if(isset($this->request->post['widget_zoom'])){
            $data['widget_zoom'] = $this->request->post['widget_zoom'];
        } else {
            $data['widget_zoom'] =  8;
        }
        
        if(isset($this->request->post['widget_height'])){
            $data['widget_height'] = $this->request->post['widget_height'];
        } else {
            $data['widget_height'] =  300;
        }
        
        if(isset($this->request->post['widget_marker'])){
            $data['widget_marker'] = $this->request->post['widget_marker'];
        } else {
            $data['widget_marker'] =  1;
        }
        
        if(isset($this->request->post['widget_marker_icon'])){
            $data['widget_marker_icon'] = $this->request->post['widget_marker_icon'];
        } else {
            $data['widget_marker_icon'] =  '';
        }
        
        if(isset($this->request->post['widget_control_zoom'])){
            $data['widget_control_zoom'] = $this->request->post['widget_control_zoom'];
        } else {
            $data['widget_control_zoom'] =  1;
        }
        
        if(isset($this->request->post['widget_control_maptype'])){
            $data['widget_control_maptype'] = $this->request->post['widget_control_maptype'];
        } else {
            $data['widget_control_maptype'] =  1;
        }
        
        if(isset($this->request->post['widget_control_scale'])){
            $data['widget_control_scale'] = $this->request->post['widget_control_scale'];
        } else {
            $data['widget_control_scale'] =  1;
        }
        
        if(isset($this->request->post['widget_control_streetview'])){
            $data['widget_control_streetview'] = $this->request->post['widget_control_streetview'];
        } else {
            $data['widget_control_streetview'] =  1;
        }
        
        if(isset($this->request->post['widget_control_rotate'])){
            $data['widget_control_rotate'] = $this->request->post['widget_control_rotate'];
        } else {
            $data['widget_control_rotate'] =  1;
        }
        
        if(isset($this->request->post['widget_control_fullscreen'])){
            $data['widget_control_fullscreen'] = $this->request->post['widget_control_fullscreen'];
        } else {
            $data['widget_control_fullscreen'] =  1;
        }
        
        $this->load->model('tool/image');

        if (isset($this->request->post['widget_marker_icon']) && is_file(DIR_IMAGE . $this->request->post['widget_marker_icon'])) {
            $data['thumb_marker_icon'] = $this->model_tool_image->resize($this->request->post['widget_marker_icon'], 100, 100);
        }  else {
            $data['thumb_marker_icon'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
        
        if(!$this->config->get('maza_api_google_map_key')){
            $data['warning'] = sprintf($this->language->get('error_api_key'), $this->url->link('extension/maza/system', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        
        $this->response->setOutput($this->load->view('extension/mz_widget/map', $data));
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
