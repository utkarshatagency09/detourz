<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaToolMail extends model {
    public function addMail(array $data): void {
        $sql = "INSERT INTO " . DB_PREFIX . "mz_mail_queue SET `to` = '" . $this->db->escape($data['to']) . "', `from` = '" . $this->db->escape($data['from']) . "', `subject` = '" . $this->db->escape($data['subject']) . "', `body` = '" . $this->db->escape($data['body']) . "', date_added = NOW()";

        if (!empty($data['reply_to'])) {
            $sql .= ", reply_to = '" . $this->db->escape($data['reply_to']) . "'";
        }

        if (!empty($data['sender'])) {
            $sql .= ", `sender` = '" . $this->db->escape($data['sender']) . "'";
        }

        $this->db->query($sql);
    }

    public function flush(int $limit = 50, bool $recursive = true): void {
        $reserved_id = token(8);

        $this->db->query("UPDATE `" . DB_PREFIX . "mz_mail_queue` SET reserved_id = '" . $this->db->escape($reserved_id) . "' WHERE reserved_id IS NULL LIMIT " . (int) $limit);

        $mail                = new Mail($this->config->get('config_mail_engine'));
        $mail->parameter     = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port     = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_mail_queue WHERE reserved_id = '" . $this->db->escape($reserved_id) . "'");

        if ($query->num_rows) {
            try {
                foreach ($query->rows as $value) {
                    $mail->setTo($value['to']);
                    $mail->setFrom($value['from']);
                    $mail->setSubject(html_entity_decode($value['subject'], ENT_QUOTES, 'UTF-8'));
                    $mail->setHtml($value['body']);

                    if (!empty($value['reply_to'])) {
                        $mail->setReplyTo($value['reply_to']);
                    }
                    if (!empty($value['sender'])) {
                        $mail->setSender(html_entity_decode($value['sender'], ENT_QUOTES, 'UTF-8'));
                    }

                    $mail->send();
                }

                $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_mail_queue` WHERE reserved_id = '" . $this->db->escape($reserved_id) . "'");

                if($recursive) $this->flush($limit);
            } catch (\Throwable $th) {
                $this->db->query("UPDATE `" . DB_PREFIX . "mz_mail_queue` SET reserved_id = NULL WHERE reserved_id = '" . $this->db->escape($reserved_id) . "'");

                throw $th;
            }
        }
    }
}