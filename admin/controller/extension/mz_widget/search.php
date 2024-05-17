<?php
class ControllerExtensionMzWidgetSearch extends maza\layout\Widget {
    public function index() {
        $this->load->language('extension/mz_widget/search');

        $setting = array(
            'widget_status' => 1,
            'widget_search_route' => 'auto_switch',
            'widget_autocomplete_status' => 1,
            'widget_autocomplete_limit' => 5,
            'widget_search_button_type' => 'icon',
            'widget_product_category_status' => 0,
            'widget_product_category_depth' => 1,
            'widget_product_placeholder' => array(),
            'widget_blog_category_status' => 0,
            'widget_blog_category_depth' => 1,
            'widget_blog_placeholder' => array(),
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }
        
        $data['list_search_button_type'] = array(
            array('id' => 'icon', 'name' => $this->language->get('text_icon')),
            array('id' => 'text', 'name' => $this->language->get('text_text')),
            array('id' => 'both', 'name' => $this->language->get('text_both'))
        );

        $data['list_search_route'] = array(
            array('id' => 'auto_switch', 'name' => $this->language->get('text_auto_switch')),
            array('id' => 'product', 'name' => $this->language->get('text_product')),
            array('id' => 'blog', 'name' => $this->language->get('text_blog'))
        );
        
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        $this->response->setOutput($this->load->view('extension/mz_widget/search', $data));
    }
}
