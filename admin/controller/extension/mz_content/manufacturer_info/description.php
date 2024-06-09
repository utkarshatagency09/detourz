<?php
class ControllerExtensionMzContentManufacturerInfoDescription extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/manufacturer_info/description');

        $data = array();
        
        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
        }
        
        if(isset($this->request->post['content_manufacturer_image'])){
            $data['content_manufacturer_image'] = $this->request->post['content_manufacturer_image'];
        } else {
            $data['content_manufacturer_image'] =  0;
        }
        
        if(isset($this->request->post['content_limit'])){
            $data['content_limit'] = $this->request->post['content_limit'];
        } else {
            $data['content_limit'] =  0;
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/manufacturer_info/description', $data));
    }
}
