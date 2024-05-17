<?php
class ControllerExtensionMzWidgetGallery extends maza\layout\Widget {
	private static $instance_count = 0;

	public function index(array $setting): string {
		$data = array();

        $this->load->model('extension/maza/gallery');
		$this->load->model('tool/image');
        
        $gallery_info = $this->model_extension_maza_gallery->getGallery((int)$setting['widget_gallery_id']);

		if ($gallery_info) {
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			
			$data['galleries'] = array();

			$image_width = $setting['widget_image_width']?:100;
			$image_height = $setting['widget_image_height']?:100;

			if (isset($gallery_info['images'])) {
				foreach ($gallery_info['images'] as $image) {
					if (is_file(DIR_IMAGE . $image['image'])) {
						$thumb = $this->model_tool_image->resize($image['image'], $image_width, $image_height);
					} else {
						continue;
					}

					$data['galleries'][] = array(
						'title' => $image['title'],
						'thumb' => $thumb,
						'popup' => maza\getImageURL($image['image']),
						'sort_order' => $image['sort_order'],
						'type'  => 'image',
					);
				}
			}

			if (isset($gallery_info['videos'])) {
				foreach ($gallery_info['videos'] as $video) {
					if (is_file(DIR_IMAGE . $video['image'])) {
						$thumb = $this->model_tool_image->resize($video['image'], $image_width, $image_height);
					} else {
						$thumb = $this->model_tool_image->resize('catalog/maza/asset/play.png', $image_width, $image_height);
					}

					$data['galleries'][] = array(
						'url' => $video['url'],
						'thumb' => $thumb,
						'sort_order' => $video['sort_order'],
						'type'  => 'video',
					);
				}
			}

			// Sort
			$sort_order = array_column($data['galleries'], 'sort_order');
			array_multisort($sort_order, SORT_ASC, $data['galleries']);

			$data['heading_title'] 	= maza\getOfLanguage($setting['widget_title']);
			$data['image_width']	= $image_width;
			$data['image_height']	= $image_height;
			$data['gutter']			= $setting['widget_gutter'];
			$data['mz_suffix']     	= $setting['mz_suffix']??self::$instance_count++;

			return $this->load->view('extension/mz_widget/gallery', $data);
		}

		return '';
	}

	/**
	 * Change default setting
	 */
	public function getSettings(): array {
		$setting = parent::getSettings();
		
		$setting['widget_cache'] = 'hard';
		
		return $setting;
	}
}
