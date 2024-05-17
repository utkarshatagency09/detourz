<?php
class ControllerExtensionMzWidgetForm extends maza\layout\Widget {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_widget/form');
        
        $this->load->model('extension/maza/asset');
        $this->load->model('localisation/language');
        $this->load->model('extension/maza/form');

        $setting = array(
            'widget_status' => 0,
            'widget_title' => 1,
            'widget_label' => 'top',
            'widget_color' => 'primary',
            'widget_size' => 'md',
            'widget_submit_align' => 'left',
            'widget_form_id' => 0,
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }

        $form_info = $this->model_extension_maza_form->getForm($data['widget_form_id']);

        if($form_info){
            $data['widget_form'] = $form_info['name'];
        }
        
        $data['list_label'] = array(
            array('code' => '', 'text' => $this->language->get('text_disabled')),
            array('code' => 'top', 'text' => $this->language->get('text_top')),
            array('code' => 'left', 'text' => $this->language->get('text_left')),
            array('code' => 'floating', 'text' => $this->language->get('text_floating'))
        );

        $data['sizes'] = array(
            array('code' => 'sm', 'text' => $this->language->get('text_small')),
            array('code' => 'md', 'text' => $this->language->get('text_medium')),
            array('code' => 'lg', 'text' => $this->language->get('text_large'))
        );

        $data['list_submit_align'] = array(
            array('code' => 'left', 'text' => $this->language->get('text_left')),
            array('code' => 'center', 'text' => $this->language->get('text_center')),
            array('code' => 'right', 'text' => $this->language->get('text_right')),
            array('code' => 'full', 'text' => $this->language->get('text_full'))
        );
        
        $color_types = $this->model_extension_maza_asset->getColorTypes();
        
        $data['colors'] = array();
        foreach($color_types as $color_type){
            $data['colors'][] = array('code' => $color_type, 'text' => ucfirst($color_type));
        }

        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_widget/form', $data));
    }
}
