<?php
class ControllerExtensionMzDesignCarousel extends maza\layout\Design {
    public function index(): void {
        $this->load->language('extension/mz_design/carousel');
        
        $this->load->model('localisation/language');
        $this->load->model('tool/image');
        
        $data = array();
        
        // Status
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        
        // Image size
        if(isset($this->request->post['design_image_width'])){
            $data['design_image_width'] = $this->request->post['design_image_width'];
        } else {
            $data['design_image_width'] = '';
        }
        if(isset($this->request->post['design_image_height'])){
            $data['design_image_height'] = $this->request->post['design_image_height'];
        } else {
            $data['design_image_height'] = '';
        }
        if(isset($this->request->post['design_image_srcset'])){
            $data['design_image_srcset'] =  $this->request->post['design_image_srcset'];
        } else {
            $data['design_image_srcset'] =  array('lg' => '', 'md' => '', 'sm' => '', 'xs' => '');
        }

        // Fade
        if(isset($this->request->post['design_fade'])){
            $data['design_fade'] = $this->request->post['design_fade'];
        } else {
            $data['design_fade'] = 0;
        }
        
        // interval
        if(isset($this->request->post['design_interval'])){
            $data['design_interval'] = $this->request->post['design_interval'];
        } else {
            $data['design_interval'] = 5000;
        }
        
        // loop
        if(isset($this->request->post['design_loop'])){
            $data['design_loop'] = $this->request->post['design_loop'];
        } else {
            $data['design_loop'] = 1;
        }
        
        // pagination
        if(isset($this->request->post['design_pagination'])){
            $data['design_pagination'] = $this->request->post['design_pagination'];
        } else {
            $data['design_pagination'] = 1;
        }
        
        // navigation
        if(isset($this->request->post['design_navigation'])){
            $data['design_navigation'] = $this->request->post['design_navigation'];
        } else {
            $data['design_navigation'] = 1;
        }
        
        // pause
        if(isset($this->request->post['design_pause'])){
            $data['design_pause'] = $this->request->post['design_pause'];
        } else {
            $data['design_pause'] = 'hover';
        }
        
        // keyboard
        if(isset($this->request->post['design_keyboard'])){
            $data['design_keyboard'] = $this->request->post['design_keyboard'];
        } else {
            $data['design_keyboard'] = 1;
        }
        
        // Lazy loading
        if(isset($this->request->post['design_lazy_loading'])){
            $data['design_lazy_loading'] = $this->request->post['design_lazy_loading'];
        } else {
            $data['design_lazy_loading'] = 1;
        }
        
        // Carousel
        if(isset($this->request->post['design_slides'])){
            $design_slides = $this->request->post['design_slides'];
        } else {
            $design_slides = array();
        }
        
        $data['design_slides'] = array();
        
        foreach ($design_slides as $design_slide) {
            // Slide thumb Image
            $design_slide['thumb_slide_image'] = array();

            foreach ($design_slide['slide_image'] as $language_id => $image) {
                if($image){
                    $design_slide['thumb_slide_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
                } else {
                    $design_slide['thumb_slide_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }

            // Slide thumb svg
            $design_slide['thumb_slide_svg'] = array();

            foreach ($design_slide['slide_svg'] as $language_id => $image_svg) {
                if($image_svg){
                    $design_slide['thumb_slide_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
                } else {
                    $design_slide['thumb_slide_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }

            // Link
            if(!empty($design_slide['url_link_code'])){
                $design_slide['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $design_slide['url_link_code']);
            } else {
                $design_slide['link_info'] = '';
            }

            $data['design_slides'][] = $design_slide;
        }
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['language_id'] = $this->config->get('config_language_id');
        
        // Image
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        $data['user_token']  = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/carousel', $data));
    }
}
