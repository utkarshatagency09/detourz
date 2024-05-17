<?php
class ControllerExtensionMzContentQuickViewOptions extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/quick_view/options');

                $data = array();
                
                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                if(isset($this->request->post['content_option_name'])){
                    $data['content_option_name'] = $this->request->post['content_option_name'];
                } else {
                    $data['content_option_name'] =  1;
                }
                
                if(isset($this->request->post['content_radio_style'])){
                    $data['content_radio_style'] = $this->request->post['content_radio_style'];
                } else {
                    $data['content_radio_style'] =  'button';
                }
                
                if(isset($this->request->post['content_checkbox_style'])){
                    $data['content_checkbox_style'] = $this->request->post['content_checkbox_style'];
                } else {
                    $data['content_checkbox_style'] =  'button';
                }
                
                $data['option_styles'] = array(
                        array('code' => 'default', 'text' => $this->language->get('text_default')),
                        array('code' => 'button', 'text' => $this->language->get('text_button')),
                );
                
                // Column
                if(isset($this->request->post['content_column_xl'])){
                    $data['content_column_xl'] = $this->request->post['content_column_xl'];
                } else {
                    $data['content_column_xl'] =  1;
                }
                if(isset($this->request->post['content_column_lg'])){
                    $data['content_column_lg'] = $this->request->post['content_column_lg'];
                } else {
                    $data['content_column_lg'] =  1;
                }
                if(isset($this->request->post['content_column_md'])){
                    $data['content_column_md'] = $this->request->post['content_column_md'];
                } else {
                    $data['content_column_md'] =  1;
                }
                if(isset($this->request->post['content_column_sm'])){
                    $data['content_column_sm'] = $this->request->post['content_column_sm'];
                } else {
                    $data['content_column_sm'] =  1;
                }
                if(isset($this->request->post['content_column_xs'])){
                    $data['content_column_xs'] = $this->request->post['content_column_xs'];
                } else {
                    $data['content_column_xs'] =  1;
                }
                
                $this->response->setOutput($this->load->view('extension/mz_content/quick_view/options', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/quick_view/options')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
