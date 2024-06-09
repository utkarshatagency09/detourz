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
            
        if(!empty($data['reply_to'])){
            $sql .= ", `reply_to` = '" . $this->db->escape($data['reply_to']) . "'";
        }
        
        if(!empty($data['sender'])){
            $sql .= ", `sender` = '" . $this->db->escape($data['sender']) . "'";
        }
        
        $this->db->query($sql);
    }
}
