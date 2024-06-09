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
}
