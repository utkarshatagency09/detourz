<?php
class ControllerExtensionMzContentBlogArticleCommentList extends maza\layout\Content {
        public function index($setting) {
                if($this->mz_skin_config->get('blog_comment_status')){
                    return $this->load->view('extension/maza/blog/article/comment_list');
                }
        }
}
