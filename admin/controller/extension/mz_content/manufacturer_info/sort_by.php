<?php
class ControllerExtensionMzContentManufacturerInfoSortBy extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/manufacturer_info/sort_by');

        $data = array();
        

        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
        }
        
        if(isset($this->request->post['content_label'])){
            $data['content_label'] = $this->request->post['content_label'];
        } else {
            $data['content_label'] =  1;
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/manufacturer_info/sort_by', $data));
    }
}
