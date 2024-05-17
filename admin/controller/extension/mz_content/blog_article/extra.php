<?php
class ControllerExtensionMzContentBlogArticleExtra extends maza\layout\Content {
    public function index() {
        $this->load->language('extension/mz_content/blog_article/extra');

        $setting = array(
            'content_status' => 0,
            'content_author' => 1,
            'content_timestamp' => 1,
            'content_viewed' => 1,
            'content_comments' => 1,
        );

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $data = array_merge($setting, $this->request->post);
        } else {
            $data = $setting;
        }

        $this->response->setOutput($this->load->view('extension/mz_content/blog_article/extra', $data));
    }
}
