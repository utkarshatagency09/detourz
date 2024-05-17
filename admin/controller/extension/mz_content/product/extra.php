<?php
class ControllerExtensionMzContentProductExtra extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/product/extra');

        $data = array();
        
        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
        }
        
        if(isset($this->request->post['content_brand'])){
            $data['content_brand'] = $this->request->post['content_brand'];
        } else {
            $data['content_brand'] =  1;
        }
        
        if(isset($this->request->post['content_model'])){
            $data['content_model'] = $this->request->post['content_model'];
        } else {
            $data['content_model'] =  1;
        }
        
        if(isset($this->request->post['content_reward'])){
            $data['content_reward'] = $this->request->post['content_reward'];
        } else {
            $data['content_reward'] =  1;
        }
        
        if(isset($this->request->post['content_stock'])){
            $data['content_stock'] = $this->request->post['content_stock'];
        } else {
            $data['content_stock'] =  1;
        }
        
        if(isset($this->request->post['content_viewed'])){
            $data['content_viewed'] = $this->request->post['content_viewed'];
        } else {
            $data['content_viewed'] =  0;
        }

        if(isset($this->request->post['content_sold'])){
            $data['content_sold'] = $this->request->post['content_sold'];
        } else {
            $data['content_sold'] =  0;
        }
        
        if(isset($this->request->post['content_sku'])){
            $data['content_sku'] = $this->request->post['content_sku'];
        } else {
            $data['content_sku'] =  0;
        }
        
        if(isset($this->request->post['content_upc'])){
            $data['content_upc'] = $this->request->post['content_upc'];
        } else {
            $data['content_upc'] =  0;
        }
        
        if(isset($this->request->post['content_ean'])){
            $data['content_ean'] = $this->request->post['content_ean'];
        } else {
            $data['content_ean'] =  0;
        }
        
        if(isset($this->request->post['content_jan'])){
            $data['content_jan'] = $this->request->post['content_jan'];
        } else {
            $data['content_jan'] =  0;
        }
        
        if(isset($this->request->post['content_isbn'])){
            $data['content_isbn'] = $this->request->post['content_isbn'];
        } else {
            $data['content_isbn'] =  0;
        }
        
        if(isset($this->request->post['content_mpn'])){
            $data['content_mpn'] = $this->request->post['content_mpn'];
        } else {
            $data['content_mpn'] =  0;
        }
        
        if(isset($this->request->post['content_date_modified'])){
            $data['content_date_modified'] = $this->request->post['content_date_modified'];
        } else {
            $data['content_date_modified'] =  0;
        }
        
        if(isset($this->request->post['content_date_available'])){
            $data['content_date_available'] = $this->request->post['content_date_available'];
        } else {
            $data['content_date_available'] =  0;
        }
        
        if(isset($this->request->post['content_weight'])){
            $data['content_weight'] = $this->request->post['content_weight'];
        } else {
            $data['content_weight'] =  0;
        }
        
        if(isset($this->request->post['content_length'])){
            $data['content_length'] = $this->request->post['content_length'];
        } else {
            $data['content_length'] =  0;
        }
        
        if(isset($this->request->post['content_width'])){
            $data['content_width'] = $this->request->post['content_width'];
        } else {
            $data['content_width'] =  0;
        }
        
        if(isset($this->request->post['content_height'])){
            $data['content_height'] = $this->request->post['content_height'];
        } else {
            $data['content_height'] =  0;
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/extra', $data));
    }
}
