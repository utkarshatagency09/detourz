<?php
class ControllerExtensionMzWidgetVideo extends maza\layout\Widget {
	private static $instance_count = 0;

	public function index(array $setting): string {
		$data = array();

		$this->load->model('tool/image');
        
		if ($setting['widget_url']) {
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');

			$data['heading_title'] 	= maza\getOfLanguage($setting['widget_title']);

			// Video
			$parseVideoURL = maza\parseVideoURL($setting['widget_url']);
            if ($parseVideoURL) {
                $data['video_url'] = $parseVideoURL['url'];
            } else {
                $data['video_url'] = $setting['widget_url'];
            }

			// Video thumb
			$widget_image = maza\getOfLanguage($setting['widget_image']);
			if(is_file(DIR_IMAGE . $widget_image)){
				list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($widget_image, $setting['widget_image_width'], $setting['widget_image_height']);
				
				$data['thumb'] = $this->model_tool_image->resize($widget_image, $image_width, $image_height);

				$data['width'] = $image_width;
				$data['height'] = $image_height;
			} elseif($parseVideoURL) {
				$data['thumb'] = $parseVideoURL['thumb'];
			} else {
				$data['thumb'] = '';
			}

			// Image caption
			$data['image_caption'] = maza\getOfLanguage($setting['widget_image_caption']);

			$data['mz_suffix']     	= $setting['mz_suffix']??self::$instance_count++;

			return $this->load->view('extension/mz_widget/video', $data);
		}

		return '';
	}
}
