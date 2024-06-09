<?php
class ControllerExtensionMzDesignAlert extends maza\layout\Design {
	public function index(array $setting): string {
        $data = array();
        
        $data['dismissible'] = $setting['design_dismissible'];
        $data['color'] = $setting['design_color'];
        $data['title_size'] = $setting['design_title_size'];
        $data['heading_title'] = maza\getOfLanguage($setting['design_title']);
        $data['content'] = maza\getOfLanguage($setting['design_html']);
        
        if($data['content']){
            return $this->load->view('extension/mz_design/alert', $data);
        } else {
            return '';
        }
	}
}
