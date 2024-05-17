<?php
class ControllerExtensionMzWidgetForm extends maza\layout\Widget {
    private static $instance_count = 0;

	public function index(array $setting): string {
        $data = array();

        $this->load->language('extension/maza/form');
        $this->load->model('extension/maza/form');
        
        $data['color']         = $setting['widget_color'];
        $data['size']          = $setting['widget_size'];
        $data['submit_align']  = $setting['widget_submit_align'];
        $data['form_id']       = $setting['widget_form_id'];

        $data['action']        = $this->url->link('extension/maza/form/submit');
        $data['page_url']      = $this->request->server['REQUEST_URI'];
        $data['mz_suffix']     = $setting['mz_suffix']??self::$instance_count++;

        // Page
        if(!empty($this->request->get['product_id'])){
            $data['product_id'] = (int)$this->request->get['product_id'];
        }
        if(!empty($this->request->get['manufacturer_id'])){
            $data['manufacturer_id'] = (int)$this->request->get['manufacturer_id'];
        }
        if(!empty($this->request->get['path'])){
            $parts = explode('_', (string)$this->request->get['path']);

            $data['category_id'] = (int)array_pop($parts);
        }

        // Form
        $form_info = $this->model_extension_maza_form->getForm((int)$setting['widget_form_id']);
        
        if($form_info){
            if($setting['widget_title']){
                $data['form_title']  =  $form_info['name'];
            }
            
            // Captcha
			if ($form_info['captcha'] && !$this->customer->isLogged() && $this->config->get('captcha_' . $form_info['captcha'] . '_status')) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $form_info['captcha']);
			} else {
				$data['captcha'] = '';
			}

            // Policy
            if($form_info['information_id']){
                $this->load->model('catalog/information');

                $information_info = $this->model_catalog_information->getInformation($form_info['information_id']);

                if($information_info){
                    $link = $this->url->link('information/information', 'information_id=' . $information_info['information_id']);

                    $data['policy'] = sprintf($this->language->get('text_policy'), $link, $information_info['title']);
                }
            }

            // Submit text
            if($form_info['submit_text']){
                $data['button_submit']  =  $form_info['submit_text'];
            }

            // Fields
            $data['fields'] = array();

            $fields = $this->model_extension_maza_form->getFields((int)$setting['widget_form_id']);
            
            foreach($fields as $field){
                // Get options for field types select, radio, checkbox
                if(in_array($field['type'], ['select', 'radio', 'checkbox'])){
                    $values = $this->model_extension_maza_form->getFieldValues($field['form_field_id']);
                } else {
                    $values = [];
                }
                
                // Number type step
                if($field['decimal'] > 0){
                    $step = '0.' . str_repeat('0', $field['decimal'] - 1) . '1';
                } else {
                    $step = '1';
                }

                if(in_array($field['type'], ['date', 'time', 'datetime'])){
                    if($this->config->get('maza_cdn')){
                        $this->document->addScript('https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', 'footer');
                        $this->document->addScript('https://cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js', 'footer');
                        $this->document->addScript('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', 'footer');
                        $this->document->addStyle('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.css', 'stylesheet', 'all', 'footer');
                    } else {
                        $this->document->addScript('catalog/view/javascript/maza/javascript/daterangepicker/moment.min.js', 'footer');
                        $this->document->addScript('catalog/view/javascript/maza/javascript/daterangepicker/moment-with-locales.min.js', 'footer');
                        $this->document->addScript('catalog/view/javascript/maza/javascript/daterangepicker/daterangepicker.js', 'footer');
                        $this->document->addStyle('catalog/view/javascript/maza/javascript/daterangepicker/daterangepicker.css', 'stylesheet', 'all', 'footer');
                    }
                }

                $label_position = $setting['widget_label'];

                if ($label_position == 'floating' && in_array($field['type'], ['datetime', 'time', 'file'])) {
                    $label_position = '';
                }

                if ((!$label_position || $label_position == 'floating') && in_array($field['type'], ['radio', 'checkbox', 'date'])) {
                    $label_position = 'top';
                }
                
                $data['fields'][] = array(
                    'label'       => $field['label'],
                    'label_position' => $label_position,
                    'placeholder' => $field['placeholder'],
                    'help'        => $field['help'],
                    'is_required' => $field['is_required'],
                    'column'      => $field['column'],
                    'type'        => $field['type'],
                    'name'        => $field['name'],
                    'value'       => $field['value'],
                    'min'         => $field['min'],
                    'max'         => $field['max'],
                    'step'        => $step,
                    'values'      => $values,
                );
            }
            
            if($data['fields']){
                return $this->load->view('extension/mz_widget/form', $data);
            }
        }
        
        return '';
	}
}
