<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaNewsletter extends Controller {
    public function subscribe() {
        $json = array();
        
        // Language
        $this->load->language('extension/maza/newsletter');
        $this->translate();
        
        $this->load->model('extension/maza/newsletter');
        
        // validate email id
        if(isset($this->request->post['newsletter_email'])){
            $email_id = trim($this->request->post['newsletter_email']);
        } else {
            $email_id = false;
        }
        if(!filter_var($email_id, FILTER_VALIDATE_EMAIL)){
            $json['error'] = $this->language->get('error_invalid_email');
        }

        // Honey-pot field to stop spam
        if(!isset($this->request->post['newsletter_email_id']) || !empty($this->request->post['newsletter_email_id'])){
            $json['success'] = $this->language->get('text_subscribe_success');
        }
        
        // check avaiability
        if(!$json){
            $subscriber_info = $this->model_extension_maza_newsletter->getSubscriber($email_id);
            
            if($subscriber_info){
                $json['success'] = $this->language->get('text_subscribe_success');
            }

            // Notification channel
            $this->load->model('account/customer');

            $customer_info = $this->model_account_customer->getCustomerByEmail($email_id);

            if ($customer_info && isset($this->request->post['notification_channel'])) {
                foreach ($this->request->post['notification_channel'] as $channel_id) {
                    $this->model_extension_maza_newsletter->addChannel($customer_info['customer_id'], $channel_id);
                }
            }
        }
        
        // Add subscriber to newsletter
        if(!$json && !$subscriber_info){
            $this->model_extension_maza_newsletter->addSubscriber($email_id);
            $subscriber_info = $this->model_extension_maza_newsletter->getSubscriber($email_id);
        }
        
        // Send confirmation
        if(!$json){
            if($this->mz_skin_config->get('newsletter_confirm_subscribe_status') && !$subscriber_info['is_confirmed']){
                // Send confirmation mail
                $this->model_extension_maza_newsletter->sendSubscribeConfirmMail($email_id);
                
                $json['success'] = $this->language->get('text_subscribe_confirm_mail_sent_success');
            } else {
                $this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '1' WHERE email = '" . $this->db->escape($email_id) . "'");

                $json['success'] = $this->language->get('text_subscribe_success');
                
                // Send welcome mail
                if($this->mz_skin_config->get('newsletter_welcome_mail_status')) $this->model_extension_maza_newsletter->sendWelcomeMail($email_id);
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
        
    public function unsubscribe() {
        $json = array();
        
        // Language
        $this->load->language('extension/maza/newsletter');
        $this->translate();
        
        $this->load->model('extension/maza/newsletter');
        
        // validate email id
        if(isset($this->request->post['newsletter_email'])){
            $email_id = trim($this->request->post['newsletter_email']);
        } else {
            $email_id = false;
        }
        if(!filter_var($email_id, FILTER_VALIDATE_EMAIL)){
            $json['error'] = $this->language->get('error_invalid_email');
        }
        
        // check avaiability
        if(!$json){
            $subscriber_info = $this->model_extension_maza_newsletter->getSubscriber($email_id);
            
            if(!$subscriber_info){
                $json['error'] = $this->language->get('error_email_not_found');
            }
        }
        
        // remove subscriber from newsletter
        if(!$json && $subscriber_info){
            
            // send confirmation if require
            if($this->mz_skin_config->get('newsletter_confirm_unsubscribe_status')){
                // Send confirmation mail
                $this->model_extension_maza_newsletter->sendUnsubscribeConfirmMail($email_id);
                
                $json['success'] = $this->language->get('text_unsubscribe_confirm_mail_sent_success');
            } else {
                $this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '0' WHERE email = '" . $this->db->escape($email_id) . "'");

                $this->model_extension_maza_newsletter->deleteSubscriber($subscriber_info['token']);
                
                $json['success'] = $this->language->get('text_unsubscribe_success');
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
        
    /**
     * Confirm subscription
     */
    public function confirm_subscribe(){
        $this->load->language('extension/maza/newsletter');
        $this->translate();

        $this->load->model('extension/maza/newsletter');
        
        if(isset($this->request->get['subscribe_token'])){
            $subscriber_info = $this->model_extension_maza_newsletter->getSubscriberByToken($this->request->get['subscribe_token']);

            if($subscriber_info){
                $this->model_extension_maza_newsletter->confirmedSubscriber($this->request->get['subscribe_token']);

                $this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '1' WHERE email = '" . $this->db->escape($subscriber_info['email_id']) . "'");
            }

            // Send greeting
            if($this->mz_skin_config->get('newsletter_welcome_mail_status') && $subscriber_info && !$subscriber_info['is_confirmed']){
                $this->model_extension_maza_newsletter->sendWelcomeMail($subscriber_info['email_id']);
            }
        }
        
        $data['heading_title'] = $this->language->get('text_confirm_subscribe_page_title');
        $data['text_message'] = $this->language->get('text_confirm_subscribe_page_message');
        
        $data['continue'] = $this->url->link('common/home');
        
        $data['class'] = 'extension-maza-newsletter-confirm-subscribe';
        
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('common/success', $data));
    }
        
    /**
     * Confirm unsubscribe
     */
    public function confirm_unsubscribe(){
        $this->load->language('extension/maza/newsletter');
        $this->translate();

        $this->load->model('extension/maza/newsletter');
        
        if(isset($this->request->get['subscribe_token'])){
            $subscriber_info = $this->model_extension_maza_newsletter->getSubscriberByToken($this->request->get['subscribe_token']);

            if ($subscriber_info) {
                $this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '0' WHERE email = '" . $this->db->escape($subscriber_info['email_id']) . "'");
            }

            $this->model_extension_maza_newsletter->deleteSubscriber($this->request->get['subscribe_token']);
        }
        
        $data['heading_title'] = $this->language->get('text_confirm_unsubscribe_page_title');
        $data['text_message'] = $this->language->get('text_confirm_unsubscribe_page_message');
        
        $data['continue'] = $this->url->link('common/home');
        
        $data['class'] = 'extension-maza-newsletter-confirm-unsubscribe';
        
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('common/success', $data));
    }
        
    private function translate(){
        if(!empty($this->mz_skin_config->get('newsletter_translate')[$this->config->get('config_language_id')])){
            $translate = $this->mz_skin_config->get('newsletter_translate')[$this->config->get('config_language_id')];
        } else {
            $translate = array();
        }
        
        // Invalid email
        if(!empty($translate['invalid_email'])){
            $this->language->set('error_invalid_email', $translate['invalid_email']);
        }
        
        // email exist
        if(!empty($translate['email_exist'])){
            $this->language->set('error_email_exist', $translate['email_exist']);
        }
        
        // Subscribe success
        if(!empty($translate['subscribe_success'])){
            $this->language->set('text_subscribe_success', $translate['subscribe_success']);
        }
        
        // Unsubscribe success
        if(!empty($translate['unsubscribe_success'])){
            $this->language->set('text_unsubscribe_success', $translate['unsubscribe_success']);
        }
        
        // Subscribe confirm mail sent
        if(!empty($translate['subscribe_confirm_mail_sent_success'])){
            $this->language->set('text_subscribe_confirm_mail_sent_success', $translate['subscribe_confirm_mail_sent_success']);
        }
        
        // Unsubscribe confirm mail sent
        if(!empty($translate['unsubscribe_confirm_mail_sent_success'])){
            $this->language->set('text_unsubscribe_confirm_mail_sent_success', $translate['unsubscribe_confirm_mail_sent_success']);
        }
        
        // Confirm mail sent
        if(!empty($translate['email_not_found'])){
            $this->language->set('error_email_not_found', $translate['email_not_found']);
        }
    }
}
