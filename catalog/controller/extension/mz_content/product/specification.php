<?php
class ControllerExtensionMzContentProductSpecification extends maza\layout\Content {
    public function index(array $setting): string {
        $data = array();

        $data['mz_suffix']              =  $setting['mz_suffix'];

        $data['design']                 =  $setting['content_design'];
        
        $data['size']                   =  $setting['content_size'];
        $data['table_head']             =  $setting['content_style_head'];
        $data['table_striped']          =  $setting['content_style_striped'];
        $data['table_dark']             =  $setting['content_style_dark'];
        $data['table_bordered']         =  $setting['content_style_bordered'];
        $data['table_borderless']       =  $setting['content_style_borderless'];
        $data['table_hover']            =  $setting['content_style_hover'];

        $data['accordion_auto_close']   =  $setting['content_accordion_auto_close'];
        $data['tab_fade_effect']        =  $setting['content_tab_fade_effect'];
        $data['pill_orientation']       =  $setting['content_pill_orientation'];
        
        return $this->load->view('product/product/specification', $data);
    }
}
