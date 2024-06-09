<?php
class ModelExtensionMazaCatalogProduct extends Model {
    public function editProduct(int $product_id, array $data): void {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET mz_featured = '" . (int) $data['mz_featured'] . "', date_modified = NOW() WHERE product_id = '" . (int) $product_id . "'");

        // Video
        $this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_video_description WHERE product_id = '" . (int) $product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_video WHERE product_id = '" . (int) $product_id . "'");

        if (isset($data['product_video'])) {
            foreach ($data['product_video'] as $product_video) {
                if ($product_video['url']) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_video SET product_id = '" . (int) $product_id . "', url = '" . $this->db->escape($product_video['url']) . "', image = '" . $this->db->escape($product_video['image']) . "', sort_order = '" . (int) $product_video['sort_order'] . "'");

                    $product_video_id = $this->db->getLastId();

                    foreach ($product_video['description'] as $language_id => $value) {
                        if (!empty($value['title'])) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_video_description SET product_video_id = '" . (int) $product_video_id . "', language_id = '" . (int) $language_id . "', product_id = '" . (int) $product_id . "', title = '" . $this->db->escape($value['title']) . "'");
                        }
                    }
                }
            }
        }

        // Audio
        $this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_audio_description WHERE product_id = '" . (int) $product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_audio WHERE product_id = '" . (int) $product_id . "'");

        if (isset($data['product_audio'])) {
            foreach ($data['product_audio'] as $product_audio) {
                if ($product_audio['url']) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_audio SET product_id = '" . (int) $product_id . "', url = '" . $this->db->escape($product_audio['url']) . "', sort_order = '" . (int) $product_audio['sort_order'] . "'");

                    $product_audio_id = $this->db->getLastId();

                    foreach ($product_audio['description'] as $language_id => $value) {
                        if (!empty($value['title'])) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_audio_description SET product_audio_id = '" . (int) $product_audio_id . "', language_id = '" . (int) $language_id . "', product_id = '" . (int) $product_id . "', title = '" . $this->db->escape($value['title']) . "'");
                        }
                    }
                }
            }
        }
    }

    public function getTotalOrderQuantityOfProduct(int $product_id, array $data = array()): int {
        $sql = "SELECT DISTINCT SUM(op.quantity) as total FROM " . DB_PREFIX . "order_product op";

        if ($data) {
            $sql .= " LEFT JOIN `" . DB_PREFIX . "order` o ON op.order_id = o.order_id";
        }

        $sql .= " WHERE op.product_id = '" . (int) $product_id . "'";

        if (isset($data['store_id'])) {
            $sql .= " AND o.store_id = '" . (int) $data['store_id'] . "'";
        }

        if (isset($data['customer_group_id'])) {
            $sql .= " AND o.customer_group_id = '" . (int) $data['customer_group_id'] . "'";
        }

        if (isset($data['order_status'])) {
            $sql .= " AND o.order_status_id IN (" . implode(',', array_map('intval', $data['order_status'])) . ")";
        }

        if (isset($data['date_start'])) {
            $sql .= " AND (o.date_added > '" . $this->db->escape($data['date_start']) . "')";
        }

        if (isset($data['date_end'])) {
            $sql .= " AND (o.date_added < '" . $this->db->escape($data['date_end']) . "')";
        }

        $sql .= " GROUP BY op.product_id";

        $query = $this->db->query($sql);

        return $query->row['total'] ?? 0;
    }

    public function getProductVideos(int $product_id): array {
        $video_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_product_video WHERE product_id = '" . (int) $product_id . "'");

        foreach ($query->rows as $result) {
            $result['description'] = $this->getProductVideoDescriptions($result['product_video_id']);
            $video_data[]          = $result;
        }

        return $video_data;
    }

    public function getProductVideoDescriptions(int $product_video_id): array {
        $video_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_product_video_description WHERE product_video_id = '" . (int) $product_video_id . "'");

        foreach ($query->rows as $result) {
            $video_description_data[$result['language_id']] = array(
                'title' => $result['title'],
            );
        }

        return $video_description_data;
    }

    public function getProductAudios(int $product_id): array {
        $audio_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_product_audio WHERE product_id = '" . (int) $product_id . "'");

        foreach ($query->rows as $result) {
            $result['description'] = $this->getProductAudioDescriptions($result['product_audio_id']);

            $audio_data[] = $result;
        }

        return $audio_data;
    }

    public function getProductAudioDescriptions(int $product_audio_id): array {
        $audio_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_product_audio_description WHERE product_audio_id = '" . (int) $product_audio_id . "'");

        foreach ($query->rows as $result) {
            $audio_description_data[$result['language_id']] = array(
                'title' => $result['title'],
            );
        }

        return $audio_description_data;
    }
}