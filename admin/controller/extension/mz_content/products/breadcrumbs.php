<?php
class ControllerExtensionMzContentProductsBreadcrumbs extends maza\layout\Content {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_content/products/breadcrumbs');

                $data = array();
                

                if(isset($this->request->post['content_status'])){
                    $data['content_status'] = $this->request->post['content_status'];
                } else {
                    $data['content_status'] =  0;
                }
                
                
                $this->response->setOutput($this->load->view('extension/mz_content/products/breadcrumbs', $data));
        }
        
        protected function validateForm() {
            if (!$this->user->hasPermission('modify', 'extension/mz_content/products/breadcrumbs')) {
                $this->error['warning'] = $this->language->get('error_permission');
            }

            return !$this->error;
        }
}
