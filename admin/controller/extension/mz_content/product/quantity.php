<?php
class ControllerExtensionMzContentProductQuantity extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/product/quantity');
        
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
            $data['content_color'] =  'light';
        }
        
        $data['colors'] = array();
        foreach($this->model_extension_maza_asset->getColorTypes() as $color){
            $data['colors'][] = array('code' => $color, 'text' => ucfirst($color));
        }
        
        if(isset($this->request->post['content_outline'])){
            $data['content_outline'] = $this->request->post['content_outline'];
        } else {
            $data['content_outline'] =  0;
        }
        
        if(isset($this->request->post['content_size'])){
            $data['content_size'] = $this->request->post['content_size'];
        } else {
            $data['content_size'] =  'md';
        }
        
        $data['button_sizes'] = array(
            array('code' => 'sm', 'text' => $this->language->get('text_small')),
            array('code' => 'md', 'text' => $this->language->get('text_medium')),
            array('code' => 'lg', 'text' => $this->language->get('text_large'))
        );
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/quantity', $data));
    }
}
