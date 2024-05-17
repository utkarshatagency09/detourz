<?php
class ControllerExtensionMzContentBlogSearchTitle extends maza\layout\Content {
        public function index($setting) {
                $data['size']       =   $setting['content_size'];
                
                return $this->load->view('extension/maza/blog/common/title', $data);
        }
}
