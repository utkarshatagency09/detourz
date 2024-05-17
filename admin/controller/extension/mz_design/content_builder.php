<?php
class ControllerExtensionMzDesignContentBuilder extends maza\layout\Design {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_design/content_builder');
        
        $this->load->model('extension/maza/content_builder');
        
        $data = array();
        
        if(isset($this->request->post['design_status'])){
            $data['design_status'] = $this->request->post['design_status'];
        } else {
            $data['design_status'] =  0;
        }

        // content_builder
        if(isset($this->request->post['design_content_builder_id'])){
            $data['design_content_builder_id'] = $this->request->post['design_content_builder_id'];
        } else {
            $data['design_content_builder_id'] = 0;
        }

        $content_builder_info = $this->model_extension_maza_content_builder->getContent($data['design_content_builder_id']);

        if($content_builder_info){
            $data['design_content_builder'] = $content_builder_info['name'];
        }
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_design/content_builder', $data));
    }
}
