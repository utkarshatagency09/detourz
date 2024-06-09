<?php
class ControllerExtensionMzDesignCKEditor extends maza\layout\Design {
	public function index($setting) {
            return maza\getOfLanguage($setting['design_html']);	
	}
}
