<?php
class ControllerExtensionMzContentBlogArticleTags extends maza\layout\Content {
        public function index($setting) {
                $data['color'] = $setting['content_color'];
                
                return $this->load->view('extension/maza/blog/article/tags', $data);
        }
}
