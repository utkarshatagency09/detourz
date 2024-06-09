<?php
class ControllerExtensionMzWidgetFaq extends maza\layout\Widget {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_widget/faq');
        
        $this->load->model('localisation/language');
        $this->load->model('tool/image');
        
        $data = array();
        
        // Status
        if(isset($this->request->post['widget_status'])){
            $data['widget_status'] = $this->request->post['widget_status'];
        } else {
            $data['widget_status'] =  0;
        }
        
        // Title
        if(isset($this->request->post['widget_title'])){
            $data['widget_title'] = $this->request->post['widget_title'];
        } else {
            $data['widget_title'] = array();
        }
        
        // Icon size
        if(isset($this->request->post['widget_icon_width'])){
            $data['widget_icon_width'] = $this->request->post['widget_icon_width'];
        } else {
            $data['widget_icon_width'] = '';
        }
        if(isset($this->request->post['widget_icon_height'])){
            $data['widget_icon_height'] = $this->request->post['widget_icon_height'];
        } else {
            $data['widget_icon_height'] = '';
        }

        // Hook
        if(isset($this->request->post['widget_hook'])){
            $data['widget_hook'] = $this->request->post['widget_hook'];
        } else {
            $data['widget_hook'] = 1;
        }
        
        // accordion
        if(isset($this->request->post['widget_accordion'])){
            $data['widget_accordion'] = $this->request->post['widget_accordion'];
        } else {
            $data['widget_accordion'] = 1;
        }
        
        // Faq
        if(isset($this->request->post['widget_faq'])){
            $widget_faq = $this->request->post['widget_faq'];
        } else {
            $widget_faq = array();
        }
        
        $data['widget_faq'] = array();
        
        foreach ($widget_faq as $widget_panel) {
            // Icon thumb Image
            $widget_panel['thumb_icon_image'] = array();

            foreach ($widget_panel['icon_image'] as $language_id => $image) {
                if($image){
                    $widget_panel['thumb_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
                } else {
                    $widget_panel['thumb_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }

            // Icon thumb svg
            $widget_panel['thumb_icon_svg'] = array();

            foreach ($widget_panel['icon_svg'] as $language_id => $image_svg) {
                if($image_svg){
                    $widget_panel['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
                } else {
                    $widget_panel['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }

            $data['widget_faq'][] = $widget_panel;
        }
        
        // Types
        $data['types'] = array(
            array('code' => 'text', 'text' => $this->language->get('text_text')),
            array('code' => 'html', 'text' => $this->language->get('text_html'))
        );
        
        // Grid size
        if(isset($this->request->post['widget_column_xs'])){
            $data['widget_column_xs'] = $this->request->post['widget_column_xs'];
        } else {
            $data['widget_column_xs'] =  1;
        }
        
        if(isset($this->request->post['widget_column_sm'])){
            $data['widget_column_sm'] = $this->request->post['widget_column_sm'];
        } else {
            $data['widget_column_sm'] =  1;
        }
        
        if(isset($this->request->post['widget_column_md'])){
            $data['widget_column_md'] = $this->request->post['widget_column_md'];
        } else {
            $data['widget_column_md'] =  1;
        }
        
        if(isset($this->request->post['widget_column_lg'])){
            $data['widget_column_lg'] = $this->request->post['widget_column_lg'];
        } else {
            $data['widget_column_lg'] =  1;
        }
        
        if(isset($this->request->post['widget_column_xl'])){
            $data['widget_column_xl'] = $this->request->post['widget_column_xl'];
        } else {
            $data['widget_column_xl'] =  1;
        }
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['language_id'] = $this->config->get('config_language_id');
        
        // Image
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        $data['user_token']  = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_widget/faq', $data));
    }
    
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_widget/faq')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
