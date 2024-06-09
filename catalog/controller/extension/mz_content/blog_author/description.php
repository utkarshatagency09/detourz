<?php
class ControllerExtensionMzContentBlogAuthorDescription extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('extension/maza/blog/common/description', ['limit' => $setting['content_limit']]);
        }
}
