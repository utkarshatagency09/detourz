<?php
class ControllerExtensionMzContentProductImage extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/product/image');

        $data = array();
        

        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
        }

        if(isset($this->request->post['content_additional_image_status'])){
            $data['content_additional_image_status'] = $this->request->post['content_additional_image_status'];
        } else {
            $data['content_additional_image_status'] =  '1';
        }
        
        if(isset($this->request->post['content_additional_image_position'])){
            $data['content_additional_image_position'] = $this->request->post['content_additional_image_position'];
        } else {
            $data['content_additional_image_position'] =  'left';
        }
        
        $data['additional_image_positions'] = array(
            array('code' => 'top', 'text' => $this->language->get('text_top')),
            array('code' => 'bottom', 'text' => $this->language->get('text_bottom')),
            array('code' => 'left', 'text' => $this->language->get('text_left')),
            array('code' => 'right', 'text' => $this->language->get('text_right')),
        );

        if(isset($this->request->post['content_video_status'])){
            $data['content_video_status'] = $this->request->post['content_video_status'];
        } else {
            $data['content_video_status'] =  '1';
        }

        if(isset($this->request->post['content_video_position'])){
            $data['content_video_position'] = $this->request->post['content_video_position'];
        } else {
            $data['content_video_position'] =  'start';
        }
        
        if(isset($this->request->post['content_wishlist_status'])){
            $data['content_wishlist_status'] = $this->request->post['content_wishlist_status'];
        } else {
            $data['content_wishlist_status'] =  1;
        }

        if(isset($this->request->post['content_audio_status'])){
            $data['content_audio_status'] = $this->request->post['content_audio_status'];
        } else {
            $data['content_audio_status'] =  '1';
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/image', $data));
    }
}
