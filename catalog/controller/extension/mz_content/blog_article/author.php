<?php
class ControllerExtensionMzContentBlogArticleAuthor extends maza\layout\Content {
        public function index($setting) {
                $data['description_status'] = $setting['content_description'];
                $data['collapsed']          = $setting['content_collapsed'];
                        
                return $this->load->view('extension/maza/blog/article/author', $data);
        }
}
