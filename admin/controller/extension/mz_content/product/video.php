<?php
class ControllerExtensionMzContentProductVideo extends maza\layout\Content {
    public function index(): void {
        $this->load->language('extension/mz_content/product/video');

        $setting = array(
            'content_status' => 0,
            'content_carousel_status' => 0,
            'content_carousel_pagination' => 1,
            'content_column_xs' => 2,
            'content_column_sm' => 2,
            'content_column_md' => 2,
            'content_column_lg' => 3,
            'content_column_xl' => 3,
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/video', $data));
    }
}
