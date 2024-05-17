<?php
class ControllerExtensionMzContentProductButton extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/product/button');
        
        $setting = array(
            'content_status' => 0,
            'content_cart' => 1,
            'content_buynow' => 0,
            'content_wishlist' => 0,
            'content_compare' => 0,
            'content_stock_status' => 1,
            'content_color' => 'primary',
            'content_outline' => 0,
            'content_show' => 'both',
            'content_size' => 'md',
            'content_block' => 0,
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }
        
        $this->load->model('extension/maza/asset');
        
        $data['colors'] = array();

        foreach($this->model_extension_maza_asset->getColorTypes() as $color){
            $data['colors'][] = array('code' => $color, 'text' => ucfirst($color));
        }
        
        $data['list_show'] = array(
            array('code' => 'icon', 'text' => $this->language->get('text_icon')),
            array('code' => 'text', 'text' => $this->language->get('text_text')),
            array('code' => 'both', 'text' => $this->language->get('text_both'))
        );

        $data['button_sizes'] = array(
            array('code' => 'sm', 'text' => $this->language->get('text_small')),
            array('code' => 'md', 'text' => $this->language->get('text_medium')),
            array('code' => 'lg', 'text' => $this->language->get('text_large'))
        );
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/button', $data));
    }
}
