<?php
class ModelExtensionMazaGallery extends Model {
	public function getGallery(int $gallery_id): array {
		$data = array();

		$query = $this->db->query("SELECT image, video FROM " . DB_PREFIX . "mz_gallery WHERE gallery_id = '" . (int)$gallery_id . "' AND status = 1");
                
		if($query->row){
			$images = json_decode($query->row['image'], true);

			if ($images) {
				foreach ($images as $image){
					$data['images'][] = array(
						'image' => $image['image'],
						'title' => maza\getOfLanguage($image['title']),
						'sort_order' => $image['sort_order'],
					);
				}
			}

			$data['videos'] = json_decode($query->row['video'], true);
		}

		return $data;
	}
}