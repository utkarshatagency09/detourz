<?php
class ControllerExtensionMzWidgetContactForm extends maza\layout\Widget {
        public function index(array $setting): string {
                $data = array();
                
                $this->load->language('extension/mz_widget/contact_form');
                
                // Form title
                $data['heading_title'] = maza\getOfLanguage($setting['widget_title']);
                
                // Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
		} else {
			$data['captcha'] = '';
		}
                
                // Field text
                if(!empty($setting['widget_field_name_text'][$this->config->get('config_language_id')])){
                    $this->language->set('text_name', $setting['widget_field_name_text'][$this->config->get('config_language_id')]);
                }
                if(!empty($setting['widget_field_email_text'][$this->config->get('config_language_id')])){
                    $this->language->set('text_email', $setting['widget_field_email_text'][$this->config->get('config_language_id')]);
                }
                if(!empty($setting['widget_field_subject_text'][$this->config->get('config_language_id')])){
                    $this->language->set('text_subject', $setting['widget_field_subject_text'][$this->config->get('config_language_id')]);
                }
                if(!empty($setting['widget_field_message_text'][$this->config->get('config_language_id')])){
                    $this->language->set('text_message', $setting['widget_field_message_text'][$this->config->get('config_language_id')]);
                }
                if(!empty($setting['widget_field_submit_text'][$this->config->get('config_language_id')])){
                    $this->language->set('text_submit', $setting['widget_field_submit_text'][$this->config->get('config_language_id')]);
                }
                
                // Layout
                $data['size']       = $setting['widget_size'];
                $data['color']      = $setting['widget_color'];
                
                // Action
                $data['action']     = $this->url->link('extension/mz_widget/contact_form/submit');
                
                return $this->load->view('extension/mz_widget/contact_form', $data);
	}
        
        /**
         * Submit contact form
         */
        public function submit(){
                $this->load->language('extension/mz_widget/contact_form');
                $this->load->model('extension/maza/layout_builder');
                
                $json = array();
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
                                $json['error']['name'] = $this->language->get('error_name');
                        }
                        
                        if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
                                $json['error']['email'] = $this->language->get('error_email');
                        }
                        
                        if ((utf8_strlen($this->request->post['subject']) < 3) || (utf8_strlen($this->request->post['subject']) > 32)) {
                                $json['error']['subject'] = $this->language->get('error_subject');
                        }
                        
                        if ((utf8_strlen($this->request->post['message']) < 10) || (utf8_strlen($this->request->post['message']) > 3000)) {
                                $json['error']['message'] = $this->language->get('error_message');
                        }
                        
                        // Captcha
                        if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
                                $captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

                                if ($captcha) {
                                        $json['error']['captcha'] = $captcha;
                                }
                        }
                        
                        if(empty($json['error'])){
                                if (version_compare(VERSION, '3.0.0.0') < 0) {
                                        $mail = new Mail();
                                        $mail->protocol = $this->config->get('config_mail_protocol');
                                        $mail->parameter = $this->config->get('config_mail_parameter');
                                        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                                        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                                        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                                        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                                        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
                                } else {
                                        $mail = new Mail($this->config->get('config_mail_engine'));
                                        $mail->parameter = $this->config->get('config_mail_parameter');
                                        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                                        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                                        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                                        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                                        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
                                }

                                $mail->setTo($this->config->get('config_email'));
                                $mail->setFrom($this->config->get('config_email'));
                                $mail->setReplyTo($this->request->post['email']);
                                $mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
                                $mail->setSubject(html_entity_decode($this->request->post['subject'], ENT_QUOTES, 'UTF-8'));
                                $mail->setText($this->request->post['message']);
                                $mail->send();
                                
                                $json['success'] = $this->language->get('text_success');
                        }
                }
                
                $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
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
