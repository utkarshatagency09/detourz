<?php
class ControllerExtensionMzWidgetHTML extends maza\layout\Widget {
	public function index(array $setting): string {
		if($setting['widget_type'] == 'path'){
			return $this->load->controller($setting['widget_path']);	
		} else {
			return maza\getOfLanguage($setting['widget_html']);	
		}
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
