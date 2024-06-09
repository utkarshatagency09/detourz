<?php
class ControllerExtensionMzContentProductSpecification extends maza\layout\Content {
    public function index(): void {
        $this->load->language('extension/mz_content/product/specification');

        $data = array();
        
        # General
        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
        }

        if(isset($this->request->post['content_design'])){
            $data['content_design'] = $this->request->post['content_design'];
        } else {
            $data['content_design'] =  'table';
        }

        $data['list_design'] = array(
            array('id' => 'table', 'name' => $this->language->get('text_table')),
            array('id' => 'pill', 'name' => $this->language->get('text_pill')),
            array('id' => 'tab', 'name' => $this->language->get('text_tab')),
            array('id' => 'accordion', 'name' => $this->language->get('text_accordion')),
            array('id' => 'card', 'name' => $this->language->get('text_card')),
        );

        # table
        if(isset($this->request->post['content_size'])){
            $data['content_size'] = $this->request->post['content_size'];
        } else {
            $data['content_size'] =  'md';
        }

        if(isset($this->request->post['content_style_head'])){
            $data['content_style_head'] = $this->request->post['content_style_head'];
        } else {
            $data['content_style_head'] =  'light';
        }

        if(isset($this->request->post['content_style_dark'])){
            $data['content_style_dark'] = $this->request->post['content_style_dark'];
        } else {
            $data['content_style_dark'] =  0;
        }
        
        if(isset($this->request->post['content_style_striped'])){
            $data['content_style_striped'] = $this->request->post['content_style_striped'];
        } else {
            $data['content_style_striped'] =  0;
        }
        
        if(isset($this->request->post['content_style_bordered'])){
            $data['content_style_bordered'] = $this->request->post['content_style_bordered'];
        } else {
            $data['content_style_bordered'] =  1;
        }
        
        if(isset($this->request->post['content_style_borderless'])){
            $data['content_style_borderless'] = $this->request->post['content_style_borderless'];
        } else {
            $data['content_style_borderless'] =  0;
        }
        
        if(isset($this->request->post['content_style_hover'])){
            $data['content_style_hover'] = $this->request->post['content_style_hover'];
        } else {
            $data['content_style_hover'] =  0;
        }

        $data['sizes'] = array(
            array('code' => 'sm', 'text' => $this->language->get('text_small')),
            array('code' => 'md', 'text' => $this->language->get('text_regular'))
        );

        $data['head_styles'] = array(
            array('code' => 'default', 'text' => $this->language->get('text_default')),
            array('code' => 'dark', 'text' => $this->language->get('text_dark')),
            array('code' => 'light', 'text' => $this->language->get('text_light'))
        );

        // accordion
        if(isset($this->request->post['content_accordion_auto_close'])){
            $data['content_accordion_auto_close'] = $this->request->post['content_accordion_auto_close'];
        } else {
            $data['content_accordion_auto_close'] =  1;
        }

        // tab
        if(isset($this->request->post['content_tab_fade_effect'])){
            $data['content_tab_fade_effect'] = $this->request->post['content_tab_fade_effect'];
        } else {
            $data['content_tab_fade_effect'] =  1;
        }

        // pill
        if(isset($this->request->post['content_pill_orientation'])){
            $data['content_pill_orientation'] = $this->request->post['content_pill_orientation'];
        } else {
            $data['content_pill_orientation'] =  'horizontal';
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/specification', $data));
    }
}
