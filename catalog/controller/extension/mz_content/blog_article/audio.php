<?php
class ControllerExtensionMzContentBlogArticleAudio extends maza\layout\Content {
        public function index($setting) {
                $data['mz_suffix'] = $setting['mz_suffix'];

                return $this->load->view('extension/maza/blog/article/audio', $data);
        }
}
