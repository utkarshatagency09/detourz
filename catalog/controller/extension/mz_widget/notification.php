<?php
class ControllerExtensionMzWidgetNotification extends maza\layout\Widget {
	public function index(array $setting): string {
		if (!$this->customer->isLogged()) return '';

		$data = array();
		
		// layout setting
		$data['title'] = maza\getOfLanguage($setting['widget_title']);
		
		$data['icon_width']     = $setting['widget_icon_width'];
		$data['icon_height']    = $setting['widget_icon_height'];
		$data['icon_size']      = $setting['widget_icon_size'];
		$data['icon_font']      = false;
		$data['icon_svg']       = false;
		$data['icon_image']     = false;
		
		$icon_width = $setting['widget_icon_width'];
		$icon_height = $setting['widget_icon_height'];
		
		// font icon
		$data['icon_font'] = maza\getOfLanguage($setting['widget_icon_font']);

		// svg image
		$icon_svg = maza\getOfLanguage($setting['widget_icon_svg']);
		if(is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)){
			$data['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
		}

		// Image
		$icon_image = maza\getOfLanguage($setting['widget_icon_image']);
		if(is_file(DIR_IMAGE . $icon_image)){
			list($icon_width, $icon_height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $icon_width, $icon_height);

			$data['icon_width']     = $icon_width;
			$data['icon_height']    = $icon_height;

			$this->load->model('tool/image');

			$data['icon_image'] = $this->model_tool_image->resize($icon_image, $icon_width, $icon_height);
		}

		// Notification
		$this->load->model('extension/maza/notification');

		$data['notification_total'] = $this->model_extension_maza_notification->getTotalNotifications();
		$data['notification_url'] = $this->url->link('extension/maza/notification');
		
		return $this->load->view('extension/mz_widget/notification', $data);			
	}
	
	/**
	 * Change default setting
	 */
	public function getSettings(): array {
		$setting['xl'] = $setting['lg'] = $setting['md'] = 
		$setting['sm'] = $setting['xs'] = array(
			'widget_flex_grow' => 0,
			'widget_flex_shrink' => 0,
		);
		
		return \maza\array_merge_subsequence(parent::getSettings(), $setting);
	}
}
