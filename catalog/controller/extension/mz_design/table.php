<?php
class ControllerExtensionMzDesignTable extends maza\layout\Design {
	public function index(array $setting): string {
                
        $data = array();
        
        #title
        $data['heading_title'] = maza\getOfLanguage($setting['design_title']);
        
        #Caption
        $data['caption'] = maza\getOfLanguage($setting['design_caption']);
        
        $data['size']     = $setting['design_size'];
        
        # Head
        $data['head_status']    =  $setting['design_head_status'];
        $data['head_style']     =  $setting['design_head_style'];
        
        # Style
        $data['table_striped']     =  $setting['design_style_striped'];
        $data['table_dark']        =  $setting['design_style_dark'];
        $data['table_bordered']    =  $setting['design_style_bordered'];
        $data['table_borderless']  =  $setting['design_style_borderless'];
        $data['table_hover']       =  $setting['design_style_hover'];
        
        # Data
        $data['table'] = array();
        
        foreach(explode("\n", maza\getOfLanguage($setting['design_data'])) as $row){
            $data['table'][] = array_map('trim', str_getcsv($row));
        }
        
        if($data['table']){
            return $this->load->view('extension/mz_design/table', $data);
        } else {
            return '';
        }
	}
}
