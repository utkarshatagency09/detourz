<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaForm extends Controller {
    public function submit(): void {
        $this->load->model('extension/maza/form');
        $this->load->model('catalog/information');
        $this->load->model('extension/maza/common');
        $this->load->language('extension/maza/form');
        
        if (isset($this->request->post['mz_form_id'])) {
            $form_info = $this->model_extension_maza_form->getForm($this->request->post['mz_form_id']);
        } else {
            $form_info = array();
        }
        
        $json = array();

        if($form_info && $this->request->server['REQUEST_METHOD'] == 'POST') {
            // Captcha
            if ($this->config->get('captcha_' . $form_info['captcha'] . '_status') && $form_info['captcha'] && !$this->customer->isLogged()) {
                $captcha = $this->load->controller('extension/captcha/' . $form_info['captcha'] . '/validate');

                if ($captcha) {
                    $json['error']['captcha'] = $captcha;
                }
            }

            // Policy
            if($form_info['information_id']){
                $information_info = $this->model_catalog_information->getInformation($form_info['information_id']);
            } else {
                $information_info = array();
            }
            
            if($information_info && empty($this->request->post['policy'])){
                $json['error']['policy'] = sprintf($this->language->get('error_policy'), $information_info['title']);
            }

            $customer_email = $this->customer->getEmail();
            $customer_subject = '';

            $field_data = array();

            // Validate fields
            $fields = $this->model_extension_maza_form->getFields($this->request->post['mz_form_id']);
            
            foreach($fields as $field){
                if($field['type'] !== 'file' && isset($this->request->post[$field['name']])){
                    if(is_array($this->request->post[$field['name']])){
                        $value = array_map('trim', $this->request->post[$field['name']]);
                    } else {
                        $value = trim($this->request->post[$field['name']]);
                    }
                } else if($field['type'] == 'file' && !empty($this->request->files[$field['name']]['name']) && is_file($this->request->files[$field['name']]['tmp_name'])){
                    $value = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files[$field['name']]['name'], ENT_QUOTES, 'UTF-8')));
                } else {
                    $value = null;
                }

                if($field['is_required'] && empty($value) && $value !== '0'){
                    $json['error'][$field['name']] = $field['error']?:$this->language->get('error_something');

                    if(in_array($field['type'], ['radio', 'checkbox'])){
                        $json['alert'][$field['name']] = $field['error']?:$this->language->get('error_something');
                    }
                    continue;
                } else if($value === ''){
                    continue; // Skin optional field if value is empty
                }

                // Number
                if($field['type'] == 'number' && ((!is_null($field['max']) && $value > $field['max']) || (!is_null($field['min']) && $value < $field['min']))){
                    $json['error'][$field['name']] = $field['error']?:$this->language->get('error_something');
                }

                // Validation regex
                if(in_array($field['type'], ['text', 'textarea', 'tel', 'date', 'time', 'datetime']) && !empty($field['validation']) && !filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $field['validation'])))){
                    $json['error'][$field['name']] = $field['error']?:$this->language->get('error_something');
                }

                // Email
                if($field['type'] == 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $json['error'][$field['name']] = $this->language->get('error_email');
                }

                // URL
                if($field['type'] == 'url' && !filter_var($value, FILTER_VALIDATE_URL)){
                    $json['error'][$field['name']] = $this->language->get('error_url');
                }

                // Choose
                if(in_array($field['type'], ['select', 'radio', 'checkbox'])){
                    $values = $this->model_extension_maza_form->getFieldValues($field['form_field_id']);

                    if(is_array($value)){
                        foreach($value as $i){
                            if(!in_array($i, $values)){
                                $json['error'][$field['name']] = $this->language->get('error_something');
                                break;
                            }
                        }
                    } else if(!in_array($value, $values)) {
                        $json['error'][$field['name']] = $this->language->get('error_something');
                    }
                }

                // File upload
                if($field['type'] == 'file'){
                    $filename = $value;

                    // Allowed file extension types
                    $allowed = array_map('trim', explode("\n", preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'))));

                    if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
                        $json['error'] = $this->language->get('error_filetype');
                    }

                    // Allowed file mime types
                    $allowed = array_map('trim', explode("\n", preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'))));

                    if (!in_array($this->request->files[$field['name']]['type'], $allowed)) {
                        $json['error'] = $this->language->get('error_filetype');
                    }

                    // Check to see if any PHP files are trying to be uploaded
                    $content = file_get_contents($this->request->files[$field['name']]['tmp_name']);

                    if (preg_match('/\<\?php/i', $content)) {
                        $json['error'] = $this->language->get('error_filetype');
                    }

                    // Return any upload error
                    if ($this->request->files[$field['name']]['error'] != UPLOAD_ERR_OK) {
                        $json['error'] = $this->language->get('error_upload_' . $this->request->files[$field['name']]['error']);
                    }
                }
                
                if(!$json){
                    if($field['type'] == 'file'){
                        $file = $filename . '.' . token(32);

                        move_uploaded_file($this->request->files[$field['name']]['tmp_name'], DIR_UPLOAD . $file);

                        // Hide the uploaded file name so people can not link to it directly.
                        $this->load->model('tool/upload');
                        $code = $this->model_tool_upload->addUpload($filename, $file);

                        $value = ['name' => $filename, 'code' => $code];
                    }

                    $field_data[$field['name']] = $value;

                    // Email field
                    if($form_info['email_field_id'] == $field['form_field_id']){
                        $customer_email = $value;
                    }

                    // Subject field
                    if($form_info['subject_field_id'] == $field['form_field_id']){
                        $customer_subject = $value;
                    }
                }
            }

            // After successfull validation
            if(!$json){
                // Check for spam
                $is_spam = false;

                $spam_keywords = array_filter(array_map('trim', explode(',', $form_info['spam_keywords'])), 'strlen');
                foreach($fields as $field){
                    if(in_array($field['type'], ['text', 'textarea']) && !empty($field_data[$field['name']])){
                        foreach ($spam_keywords as $keyword) {
                            if (stripos($field_data[$field['name']], $keyword) !== false) {
                                $is_spam = true;
                                break(2);
                            }
                        }
                    }
                }

                // Honey-pot field to stop spam
                if(!isset($this->request->post['mz_email']) || !empty($this->request->post['mz_email'])){
                    $is_spam = true;
                }

                // add form record
                if($form_info['record'] && !$is_spam){
                    $this->model_extension_maza_form->addRecord($form_info['form_id'], $field_data);
                }

                // Send mail to admin
                if($form_info['mail_admin_status'] && !$is_spam){
                    $to = $form_info['mail_admin_to']?:$this->config->get('config_email');

                    $subject = $form_info['name'];
                    if($customer_subject){
                        $subject .= ' - ' . $customer_subject;
                    }

                    if($this->customer->isLogged()){
                        $option['sender'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
                    }
                    
                    $option['replyTo'] = $customer_email;
                    
                    $this->model_extension_maza_common->sendMail($to, $subject, $this->mailTemplate($form_info, $field_data), $option);
                }

                // Send mail to customer
                if($form_info['mail_customer_status'] && $customer_email && !$is_spam){
                    $option = array();

                    if ($form_info['mail_admin_to']) {
                        $option['replyTo'] = $form_info['mail_admin_to'];
                    }

                    $this->model_extension_maza_common->sendMail($customer_email, $form_info['mail_customer_subject'], $this->shortcode($form_info['mail_customer_message'], $field_data), $option);
                }

                $json['title']      = $form_info['name'];
                $json['success']    = $form_info['success'];
            }
        }

        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    /**
     * Admin Email template
     */
    private function mailTemplate(array $form_info, array $field_data): string {
        $data = array();

        $data['title']  = $form_info['name'];
        $data['language']  = $this->language->get('code');
        $data['currency']  = $this->session->data['currency'];
        $data['store']['name'] = $this->config->get('config_name');
        $data['store']['url'] = $this->config->get('mz_store_url');
        $data['ip_address']  = getenv('REMOTE_ADDR');
        $data['page_url'] = $this->config->get('mz_store_url') . ltrim($this->request->post['mz_page_url'], '/');

        // Fields
        foreach($field_data as $name => $value){
            // File
            if(is_array($value) && isset($value['code']) && isset($value['name'])){
                $data['fields'][$name] = $value['name'];
            } else {
                $data['fields'][$name] = $value;
            }
        }

        if($this->customer->isLogged()){
            $this->load->model('account/customer_group');

            $data['customer']['firstname'] = $this->customer->getFirstName();
            $data['customer']['lastname'] = $this->customer->getLastName();
            $data['customer']['email'] = $this->customer->getEmail();
            $data['customer']['telephone'] = $this->customer->getTelephone();

            $customer_group = $this->model_account_customer_group->getCustomerGroup($this->customer->getGroupId());

            if($customer_group){
                $data['customer']['customer_group'] = $customer_group['name'];
            }
        }

        return $this->load->view('mail/form_submit', $data);
    }

    /**
     * Resolve shortcode for customer email
     */
    private function shortcode(string $template, array $field_data): string {
        $shortcode = array();

        // Page info
        $shortcode['page_title'] = '';

        if (isset($this->request->post['mz_product_id'])) {
            $this->load->model('catalog/product');

            $product_info = $this->model_catalog_product->getProduct($this->request->post['mz_product_id']);

            if ($product_info) {
                $shortcode['page_title'] = $product_info['name'];
            }
        }

        if (isset($this->request->post['mz_category_id'])) {
            $this->load->model('catalog/category');

            $category_info = $this->model_catalog_category->getCategory($this->request->post['mz_category_id']);

            if ($category_info) {
                $shortcode['page_title'] = $category_info['name'];
            }
        }

        if (isset($this->request->post['mz_manufacturer_id'])) {
            $this->load->model('catalog/manufacturer');

            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->post['mz_manufacturer_id']);

            if ($manufacturer_info) {
                $shortcode['page_title'] = $manufacturer_info['name'];
            }
        }

        $shortcode['page_url'] = $this->config->get('mz_store_url') . ltrim($this->request->post['mz_page_url'], '/');

        // Customer information
        if ($this->customer->isLogged()) {
            $shortcode['firstname'] = $shortcode['name'] = $this->customer->getFirstName();
            $shortcode['lastname'] = $this->customer->getLastName();
            $shortcode['email'] = $this->customer->getEmail();
        } else {
            $shortcode['firstname'] = $shortcode['lastname'] = $shortcode['email'] = '';
        }

        $shortcode = array_merge($shortcode, $field_data);

        // Replace shortcode to value in template
        foreach ($shortcode as $code => $value) {
            if (is_string($value)) {
                $template = str_replace('{' . $code . '}', $value, $template);
            }
        }

        return $template;
    }
}
