<?php
class ControllerExtensionMzDesignParallax extends maza\layout\Design {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_design/parallax');
        
        $this->load->model('tool/image');
        $this->load->model('extension/maza/common');
        
        $data = array();
        
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }
        
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        
        // Parallax
        if(isset($this->request->post['design_parallax_image'])){
            $data['design_parallax_image'] = $this->request->post['design_parallax_image'];
        } else {
            $data['design_parallax_image'] =  array();
        }
        
        // image svg
        if(isset($this->request->post['design_parallax_svg'])){
            $data['design_parallax_svg'] = $this->request->post['design_parallax_svg'];
        } else {
            $data['design_parallax_svg'] =  array();
        }
        
        
        // Parallax
        $data['thumb_parallax_image'] = array();
        
        if (isset($this->request->post['design_parallax_image'])){
            foreach ($this->request->post['design_parallax_image'] as $language_id => $parallax_image) {
                if($parallax_image){
                    $data['thumb_parallax_image'][$language_id] = $this->model_tool_image->resize($parallax_image, 100, 100);
                } else {
                    $data['thumb_parallax_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        
        // image svg
        $data['thumb_parallax_svg'] = array();
        
        if (isset($this->request->post['design_parallax_svg'])){
            foreach ($this->request->post['design_parallax_svg'] as $language_id => $parallax_svg) {
                if($parallax_svg){
                    $data['thumb_parallax_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $parallax_svg;
                } else {
                    $data['thumb_parallax_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        if(isset($this->request->post['design_parallax_height'])){
            $data['design_parallax_height'] = $this->request->post['design_parallax_height'];
        } else {
            $data['design_parallax_height'] =  100;
        }
        
        if(isset($this->request->post['design_parallax_caption'])){
            $data['design_parallax_caption'] = $this->request->post['design_parallax_caption'];
        } else {
            $data['design_parallax_caption'] =  '';
        }
        
        // Data
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/parallax', $data));
    }
    
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_design/parallax')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
