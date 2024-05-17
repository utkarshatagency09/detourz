<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaToolSMS extends model {
    public function addSMS(array $data): void {
        $this->db->query("INSERT INTO " . DB_PREFIX . "mz_sms_queue SET `telephone` = '" . $this->db->escape($data['telephone']) . "', message = '" . $this->db->escape($data['message']) . "', date_added = NOW()");
    }

    public function flush(int $limit = 50, bool $recursive = true): void {
        $reserved_id = token(8);

        $this->db->query("UPDATE `" . DB_PREFIX . "mz_sms_queue` SET reserved_id = '" . $this->db->escape($reserved_id) . "' WHERE reserved_id IS NULL LIMIT " . (int)$limit);

        $sms = new maza\SMS();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_sms_queue WHERE reserved_id = '" . $this->db->escape($reserved_id) . "'");

        If($query->num_rows){
            try {
                foreach ($query->rows as $value) {
                    $sms->setRecipients([$value['telephone']]);
                    $sms->setMessage(htmlspecialchars_decode($value['message']));
                    $sms->send();
                }
    
                $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_sms_queue` WHERE reserved_id = '" . $this->db->escape($reserved_id) . "'");
    
                if($recursive) $this->flush($limit);
            } catch (\Throwable $th) {
                $this->db->query("UPDATE `" . DB_PREFIX . "mz_sms_queue` SET reserved_id = NULL WHERE reserved_id = '" . $this->db->escape($reserved_id) . "'");

                throw $th;
            }
        }
    }
}
