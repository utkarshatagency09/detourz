<?php
class ControllerExtensionMzContentCategoryDescription extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/category/description');

        $data = array();
        
        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
        }
        
        if(isset($this->request->post['content_category_image'])){
            $data['content_category_image'] = $this->request->post['content_category_image'];
        } else {
            $data['content_category_image'] =  0;
        }
        
        if(isset($this->request->post['content_limit'])){
            $data['content_limit'] = $this->request->post['content_limit'];
        } else {
            $data['content_limit'] =  0;
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/category/description', $data));
    }
}
