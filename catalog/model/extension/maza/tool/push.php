<?php
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaToolPush extends model {
    public function addSubscriber(array $data): void {
        $this->db->query("INSERT INTO " . DB_PREFIX . "mz_push_notification_subscriber SET `customer_id` = '" . (int)$this->customer->getId() . "', `endpoint` = '" . $this->db->escape($data['endpoint']) . "', key_auth = '" . $this->db->escape($data['keys']['auth']) . "', key_p256dh = '" . $this->db->escape($data['keys']['p256dh']) . "', date_expire = '" . $this->db->escape(!empty($data['expirationTime']) ? date('Y-m-d H:i:s', $data['expirationTime'] / 1000) : '') . "', date_added = NOW()");
    }

    public function editSubscriber(int $subscriber_id, array $data): void {
        $this->db->query("UPDATE " . DB_PREFIX . "mz_push_notification_subscriber SET `endpoint` = '" . $this->db->escape($data['endpoint']) . "', key_auth = '" . $this->db->escape($data['keys']['auth']) . "', key_p256dh = '" . $this->db->escape($data['keys']['p256dh']) . "', date_expire = '" . $this->db->escape(!empty($data['expirationTime']) ? date('Y-m-d H:i:s', $data['expirationTime'] / 1000) : '') . "' WHERE subscriber_id = '" . (int)$subscriber_id . "'");
    }

    public function getSubscriber(string $endpoint): array {
        $data_subscriber = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_push_notification_subscriber WHERE `endpoint` = '" . $this->db->escape($endpoint) . "' AND customer_id = '" . (int)$this->customer->getId() . "'");

        if ($query->row) {
            $data_subscriber['subscriber_id']   = $query->row['subscriber_id'];
            $data_subscriber['customer_id']     = $query->row['customer_id'];
            $data_subscriber['endpoint']        = $query->row['endpoint'];
            $data_subscriber['keys']            = ['auth' => $query->row['key_auth'], 'p256dh' => $query->row['key_p256dh']];

            if ($query->row['date_expire']) {
                $data_subscriber['expirationTime'] = DateTime::createFromFormat('Y-m-d H:i:s', $query->row['date_expire'])->getTimestamp() * 1000;
            }
        }

        return $data_subscriber;
    }

    public function getSubscriptions(int $customer_id): array {
        $data_subscriptions = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_push_notification_subscriber WHERE customer_id = '" . (int)$customer_id . "'");

        foreach ($query->rows as $value) {
            $data_subscriptions[] = [
                'subscriber_id' => $value['subscriber_id'],
                'endpoint'      => $value['endpoint'],
                'keys'          => ['auth' => $value['key_auth'], 'p256dh' => $value['key_p256dh']],
            ];
        }

        return $data_subscriptions;
    }

    public function send(int $customer_id, array $payload): void {
        $auth = array(
            'VAPID' => array(
                'subject' => 'mailto:' . $this->config->get('config_email'),
                'publicKey' => $this->config->get('maza_notification_push_public_key'),
                'privateKey' => $this->config->get('maza_notification_push_private_key'),
            ),
        );

        $webPush = new WebPush($auth);

        $payload = array_merge([
            'icon'                  => maza\getImageURL($this->config->get('config_icon')),
            'requireInteraction'    => true,
            'vibrate'               => [300, 100, 400],
        ], $payload);

        foreach ($this->getSubscriptions($customer_id) as $subscription) {
            $webPush->queueNotification(
                Subscription::create($subscription),
                json_encode($payload),
            );
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
        
            if (!$report->isSuccess()) {
                $this->log->write("[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
            }
        }
    }
    
    public function flush(int $limit = 20, bool $recursive = true): void {
        $reserved_id = token(8);

        $this->db->query("UPDATE `" . DB_PREFIX . "mz_push_notification_queue` SET reserved_id = '" . $this->db->escape($reserved_id) . "' WHERE reserved_id IS NULL LIMIT " . (int)$limit);

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_push_notification_queue` WHERE reserved_id = '" . $this->db->escape($reserved_id) . "'");

        if ($query->num_rows > 0) {
            try {
                $auth = array(
                    'VAPID' => array(
                        'subject' => 'mailto:' . $this->config->get('config_email'),
                        'publicKey' => $this->config->get('maza_notification_push_public_key'),
                        'privateKey' => $this->config->get('maza_notification_push_private_key'),
                    ),
                );
        
                $webPush = new WebPush($auth);
    
                foreach ($query->rows as $notification) {
                    $subscription = Subscription::create([
                        'endpoint'  => $notification['endpoint'],
                        'keys'      => ['auth' => $notification['key_auth'], 'p256dh' => $notification['key_p256dh']],
                    ]);
    
                    $payload = [
                        'title' => $notification['title'],
                        'body'  => $notification['message'],
                        'data'  => ['url' => $notification['url']],
                        'icon'  => maza\getImageURL($this->config->get('config_icon')),
                        'requireInteraction' => true,
                        'timestamp' => strtotime($notification['date_added']) * 1000,
                        'vibrate' => [300, 100, 400]
                    ];
    
                    if ($notification['image']) {
                        $payload['image'] = maza\getImageURL($notification['image']);
                    }
    
                    $webPush->queueNotification(
                        $subscription,
                        json_encode($payload),
                    );
                }
    
                foreach ($webPush->flush() as $report) {
                    $endpoint = $report->getRequest()->getUri()->__toString();
                
                    if (!$report->isSuccess()) {
                        $this->log->write("[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
                    }
                }

                $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_push_notification_queue` WHERE reserved_id = '" . $this->db->escape($reserved_id) . "'");

                if($recursive) $this->flush($limit);
            } catch (\Throwable $th) {
                $this->db->query("UPDATE `" . DB_PREFIX . "mz_push_notification_queue` SET reserved_id = NULL WHERE reserved_id = '" . $this->db->escape($reserved_id) . "'");

                throw $th;
            }
        }
    }
}
