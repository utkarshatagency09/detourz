<?php
class ModelExtensionMazaNewsletter extends Model {
    /**
     * Get subscriber by email id
     * @param string $email_id
     * @return string detail
     */
    public function getSubscriber($email_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_newsletter` WHERE email_id = '" . $this->db->escape($email_id) . "' LIMIT 1");

        return $query->row;
    }

    /**
     * Get subscriber by token
     * @param string $token
     * @return string detail
     */
    public function getSubscriberByToken($token) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_newsletter` WHERE `token` = '" . $this->db->escape($token) . "' LIMIT 1");

        return $query->row;
    }

    /**
     * Add subscriber
     * @param string $email_id subscriber email id
     * @return int subscriber id
     */
    public function addSubscriber($email_id, $is_confirmed = 0) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_newsletter` SET email_id = '" . $this->db->escape($email_id) . "', `token` = '" . $this->db->escape(token(32)) . "', `status` = '" . !$this->mz_skin_config->get('newsletter_required_approval') . "', date_added = NOW(), is_confirmed = '" . (int) $is_confirmed . "'");

        if ($this->customer->isLogged()) {
            $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET newsletter = 1 WHERE customer_id = '" . (int)$this->customer->getId() . "' AND email = '" . $this->db->escape($email_id) . "'");
        }

        return $this->db->getLastId();
    }

    /**
     * Confirm subscriber by token
     * @param string $token unique token
     * @return null
     */
    public function confirmedSubscriber($token) {
        $this->db->query("UPDATE `" . DB_PREFIX . "mz_newsletter` SET is_confirmed = 1 WHERE `token` = '" . $this->db->escape($token) . "'");
    }

    /**
     * delete subscriber by token
     * @param string $token unique token
     * @return null
     */
    public function deleteSubscriber($token) {
        if ($token) {
            $subscriber = $this->getSubscriberByToken($token);

            $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_newsletter` WHERE `token` = '" . $this->db->escape($token) . "'");

            if ($this->customer->isLogged()) {
                $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET newsletter = 0 WHERE customer_id = '" . (int)$this->customer->getId() . "' AND email = '" . $this->db->escape($subscriber['email']) . "'");
            }
        }
    }

    /**
     * Send confirmation mail of subscribe
     * @param string $email_id
     * @return null
     */
    public function sendSubscribeConfirmMail($email_id) {

        // Template
        if (isset($this->mz_skin_config->get('newsletter_confirm_subscribe_template')[$this->config->get('config_language_id')])) {
            $template = $this->mz_skin_config->get('newsletter_confirm_subscribe_template')[$this->config->get('config_language_id')];
        } else {
            $template = array();
        }
        if (empty($template['subject'])) {
            $template['subject'] = $this->language->get('help_mail_subject');
        }
        if (empty($template['message'])) {
            $template['message'] = $this->language->get('help_mail_message');
        }

        $subscribe_info = $this->getSubscriber($email_id);

        if ($subscribe_info) {
            // Add confirm link in message
            $confirm_link        = $this->url->link('extension/maza/newsletter/confirm_subscribe', 'subscribe_token=' . $subscribe_info['token']);
            $template['message'] = str_replace('[subscribe]', $confirm_link, $template['message']);

            // Send mail
            $this->load->model('extension/maza/common');
            $this->model_extension_maza_common->sendMail($email_id, $template['subject'], $template['message']);
        }
    }

    /**
     * Send confirmation mail of unsubscribe
     * @param string $email_id
     * @return null
     */
    public function sendUnsubscribeConfirmMail($email_id) {

        // Template
        if (isset($this->mz_skin_config->get('newsletter_confirm_unsubscribe_template')[$this->config->get('config_language_id')])) {
            $template = $this->mz_skin_config->get('newsletter_confirm_unsubscribe_template')[$this->config->get('config_language_id')];
        } else {
            $template = array();
        }
        if (empty($template['subject'])) {
            $template['subject'] = $this->language->get('help_mail_subject');
        }
        if (empty($template['message'])) {
            $template['message'] = $this->language->get('help_mail_message');
        }

        $subscribe_info = $this->getSubscriber($email_id);

        if ($subscribe_info) {
            // Add confirm link in message
            $confirm_link        = $this->url->link('extension/maza/newsletter/confirm_unsubscribe', 'subscribe_token=' . $subscribe_info['token']);
            $template['message'] = str_replace('[unsubscribe]', $confirm_link, $template['message']);

            // Send mail
            $this->load->model('extension/maza/common');
            $this->model_extension_maza_common->sendMail($email_id, $template['subject'], $template['message']);
        }
    }

    /**
     * Send welcome mail to new subscriber
     * @param string $email_id
     * @return null
     */
    public function sendWelcomeMail($email_id) {

        // Template
        if (isset($this->mz_skin_config->get('newsletter_welcome_mail_template')[$this->config->get('config_language_id')])) {
            $template = $this->mz_skin_config->get('newsletter_welcome_mail_template')[$this->config->get('config_language_id')];
        } else {
            $template = array();
        }
        if (empty($template['subject'])) {
            $template['subject'] = $this->language->get('help_mail_subject');
        }
        if (empty($template['message'])) {
            $template['message'] = $this->language->get('help_mail_message');
        }

        // Send mail
        $this->load->model('extension/maza/common');
        $this->model_extension_maza_common->sendMail($email_id, $template['subject'], $template['message']);
    }

    public function getChannel(int $customer_id, int $channel_id): array {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_notification_channel_subscribe` WHERE customer_id = '" . (int)$customer_id . "' AND channel_id = '" . (int)$channel_id . "'");

        if ($query->row) {
            $query->row['methods'] = explode(',', $query->row['methods']);
        }

        return $query->row;
    }

    public function addChannel(int $customer_id, int $channel_id): void {
        $channel_info = $this->getChannel($customer_id, $channel_id);

        if ($channel_info) {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_notification_channel_subscribe` WHERE customer_id = '" . (int)$customer_id . "' AND channel_id = '" . (int)$channel_id . "'");

            if (!in_array('email', $channel_info['methods'])) {
                $channel_info['methods'][] = 'email';
            }
        } else {
            $channel_info = ['methods' => ['email']];
        }

        $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_notification_channel_subscribe` SET customer_id = '" . (int)$customer_id . "', channel_id = '" . (int)$channel_id . "', methods = '" . $this->db->escape(implode(',', $channel_info['methods'])) . "'");
    }
}