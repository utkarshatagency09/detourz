<?php
class ControllerExtensionMzContentProductBrand extends maza\layout\Content {
    public function index(): void {
        $this->load->language('extension/mz_content/product/brand');

        $data = array();
        

        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
        }
        
        if(isset($this->request->post['content_caption'])){
            $data['content_caption'] = $this->request->post['content_caption'];
        } else {
            $data['content_caption'] =  1;
        }
        
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/brand', $data));
    }
}
