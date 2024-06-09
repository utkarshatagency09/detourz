<?php
class ControllerExtensionMzContentQuickViewButton extends maza\layout\Content {
	private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/quick_view/button');
                
                $this->load->model('extension/maza/asset');

                $data = array();
                
                
                // Status
                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_cart'])){
                    $data['content_cart'] = $this->request->post['content_cart'];
                } else {
                    $data['content_cart'] =  1;
                }
                
                if(isset($this->request->post['content_buynow'])){
                    $data['content_buynow'] = $this->request->post['content_buynow'];
                } else {
                    $data['content_buynow'] =  0;
                }
                
                if(isset($this->request->post['content_wishlist'])){
                    $data['content_wishlist'] = $this->request->post['content_wishlist'];
                } else {
                    $data['content_wishlist'] =  0;
                }
                
                if(isset($this->request->post['content_compare'])){
                    $data['content_compare'] = $this->request->post['content_compare'];
                } else {
                    $data['content_compare'] =  0;
                }
                
                // content
                if(isset($this->request->post['content_color'])){
                    $data['content_color'] = $this->request->post['content_color'];
                } else {
                    $data['content_color'] =  'primary';
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
                
                if(isset($this->request->post['content_show'])){
                    $data['content_show'] = $this->request->post['content_show'];
                } else {
                    $data['content_show'] =  'both';
                }
                
                $data['list_show'] = array(
                    array('code' => 'icon', 'text' => $this->language->get('text_icon')),
                    array('code' => 'text', 'text' => $this->language->get('text_text')),
                    array('code' => 'both', 'text' => $this->language->get('text_both'))
                );
                
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
                
                if(isset($this->request->post['content_block'])){
                    $data['content_block'] = $this->request->post['content_block'];
                } else {
                    $data['content_block'] =  0;
                }
                
                $data['user_token'] = $this->session->data['user_token'];
                
                $this->response->setOutput($this->load->view('extension/mz_content/quick_view/button', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/quick_view/button')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
