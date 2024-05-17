<?php
class ControllerExtensionMzContentBlogArticleDescription extends maza\layout\Content {
        public function index($setting) {
                $data['collapsed'] = $setting['content_collapsed'];
                
                return $this->load->view('extension/maza/blog/article/description', $data);
        }
}
