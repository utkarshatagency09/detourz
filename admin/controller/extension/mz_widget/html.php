<?php
class ControllerExtensionMzWidgetHTML extends maza\layout\Widget {
        public function index() {
                $this->load->language('extension/mz_widget/html');

                $data = array();
                
                // Status
                if(isset($this->request->post['widget_status'])){
                    $data['widget_status'] = $this->request->post['widget_status'];
                } else {
                    $data['widget_status'] =  0;
                }

                // Type
                if(isset($this->request->post['widget_type'])){
                        $data['widget_type'] = $this->request->post['widget_type'];
                } else {
                        $data['widget_type'] =  'html';
                }

                $data['list_type'] = array(
                        array('id' => 'html', 'text' => $this->language->get('text_html')),
                        array('id' => 'path', 'text' => $this->language->get('text_php')),
                );
                
                // HTML
                if (isset($this->request->post['widget_html'])) {
			$data['widget_html'] = $this->request->post['widget_html'];
		} else {
			$data['widget_html'] = array();
		}

                // PHP - Controller path
                if (isset($this->request->post['widget_path'])) {
			$data['widget_path'] = $this->request->post['widget_path'];
		} else {
			$data['widget_path'] = '';
		}
                
                $this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
                
                $this->response->setOutput($this->load->view('extension/mz_widget/html', $data));
        }
        
        /**
         * Change default setting
         */
        public function getSettings(): array {
            $setting = parent::getSettings();
            
            $setting['widget_cache'] = 'hard';
            
            return $setting;
        }
}
