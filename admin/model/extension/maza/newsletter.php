<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaNewsLetter extends model {
    /**
     *  Get list of subscribers
     *  @param array $data filter subscribers
     *  @return array
     */
    public function getSubscribers(array $data = array()): array {
        $sql = "SELECT * FROM `" . DB_PREFIX . "mz_newsletter` WHERE 1";

        // Email
        if (!empty($data['filter_email'])) {
            $sql .= " AND email_id LIKE '" . $this->db->escape($data['filter_email']) . "%'";
        }

        // date start
        if (!empty($data['filter_start_date_added'])) {
            $sql .= " AND date_added >= '" . $this->db->escape($data['filter_start_date_added']) . "'";
        }

        // date end
        if (!empty($data['filter_end_date_added'])) {
            $sql .= " AND date_added <= '" . $this->db->escape($data['filter_end_date_added']) . "'";
        }

        // Status(Approval)
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND status = '" . (int) $data['filter_status'] . "'";
        }

        // Is comfirm
        if (isset($data['filter_is_confirmed']) && !is_null($data['filter_is_confirmed'])) {
            $sql .= " AND is_confirmed = '" . (int) $data['filter_is_confirmed'] . "'";
        }

        // Sort and limit
        $sort_data = array('date_added', 'email_id', 'is_confirmed', 'status');

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY date_added";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    /**
     *  Get total subscribers
     *  @param array $data filter subscribers
     *  @return int total
     */
    public function getTotalSubscribers(array $data = array()): int {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "mz_newsletter` WHERE 1";

        // Email
        if (!empty($data['filter_email'])) {
            $sql .= " AND email_id LIKE '" . $this->db->escape($data['filter_email']) . "%'";
        }

        // date start
        if (!empty($data['filter_start_date_added'])) {
            $sql .= " AND date_added >= '" . $this->db->escape($data['filter_start_date_added']) . "'";
        }

        // date end
        if (!empty($data['filter_end_date_added'])) {
            $sql .= " AND date_added <= '" . $this->db->escape($data['filter_end_date_added']) . "'";
        }

        // Status(Approval)
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND status = '" . (int) $data['filter_status'] . "'";
        }

        // Is comfirm
        if (isset($data['filter_is_confirmed']) && !is_null($data['filter_is_confirmed'])) {
            $sql .= " AND is_confirmed = '" . (int) $data['filter_is_confirmed'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    /**
     * Delete subscriber by id
     * @param int $subscriber_id
     * @return null
     */
    public function deleteSubscriber(int $subscriber_id): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_newsletter` WHERE subscriber_id = '" . (int) $subscriber_id . "'");
    }

    /**
     * approve subscriber by id
     * @param int $subscriber_id
     * @return null
     */
    public function approveSubscriber(int $subscriber_id): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "mz_newsletter` SET status = 1 WHERE subscriber_id = '" . (int) $subscriber_id . "' LIMIT 1");
    }

    /**
     * disapprove subscriber by id
     * @param int $subscriber_id
     * @return null
     */
    public function disapproveSubscriber(int $subscriber_id): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "mz_newsletter` SET status = 0 WHERE subscriber_id = '" . (int) $subscriber_id . "' LIMIT 1");
    }

    public function sendNewsletter(array $data): void {
        $sql = "INSERT INTO `" . DB_PREFIX . "mz_mail_queue` (`to`, `from`, `sender`, `subject`, `body`) SELECT email_id, '" . $this->db->escape($data['from']) . "', '" . $this->db->escape($data['sender']) . "', '" . $this->db->escape($data['subject']) . "', '" . $this->db->escape($data['body']) . "' FROM `" . DB_PREFIX . "mz_newsletter` WHERE 1";

        // Status(Approval)
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND status = '" . (int) $data['filter_status'] . "'";
        }

        // Is comfirm
        if (isset($data['filter_is_confirmed']) && !is_null($data['filter_is_confirmed'])) {
            $sql .= " AND is_confirmed = '" . (int) $data['filter_is_confirmed'] . "'";
        }

        $this->db->query($sql);
    }
}