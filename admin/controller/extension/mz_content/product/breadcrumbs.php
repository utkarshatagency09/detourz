<?php
class ControllerExtensionMzContentProductBreadcrumbs extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/product/breadcrumbs');

        $data = array();

        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/breadcrumbs', $data));
    }
}
