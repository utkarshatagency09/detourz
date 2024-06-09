<?php
class ControllerExtensionMzContentBlogArticleExtra extends maza\layout\Content {
        public function index($setting) {
                $data['author'] = $setting['content_author'];
                $data['timestamp'] = $setting['content_timestamp'];
                $data['viewed'] = $setting['content_viewed'];
                $data['comments'] = $setting['content_comments'];

                return $this->load->view('extension/maza/blog/article/extra', $data);
        }
}
