<?php
class ControllerExtensionMzDesignHorizontalLine extends maza\layout\Design {
	public function index(array $setting): string {
                $data = array();
                
                // Title
                $data['heading_title'] = maza\getOfLanguage($setting['design_title']);
                
                return $this->load->view('extension/mz_design/horizontal_line', $data);
	}
}
