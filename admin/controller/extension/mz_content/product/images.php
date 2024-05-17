<?php
class ControllerExtensionMzContentProductImages extends maza\layout\Content {
    public function index(): void {
        $this->load->language('extension/mz_content/product/images');

        $setting = array(
            'content_status' => 0,
            'content_lazy_loading' => 1,
            'content_carousel_status' => 1,
            'content_carousel_pagination' => 1,
            'content_column_xs' => 3,
            'content_column_sm' => 3,
            'content_column_md' => 3,
            'content_column_lg' => 4,
            'content_column_xl' => 4,
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/images', $data));
    }
}
