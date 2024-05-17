<?php
class ControllerExtensionMzContentProductTags extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/product/tags');
        $this->load->model('extension/maza/asset');

        $data = array();
        

        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
        }
        
        if(isset($this->request->post['content_color'])){
            $data['content_color'] = $this->request->post['content_color'];
        } else {
            $data['content_color'] =  'primary';
        }
        
        $data['colors'] = array();
        foreach($this->model_extension_maza_asset->getColorTypes() as $color){
            $data['colors'][] = array('code' => $color, 'text' => ucfirst($color));
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/tags', $data));
    }
}
