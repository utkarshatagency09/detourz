<?php
class ControllerExtensionMzContentBlogArticleCommentForm extends maza\layout\Content {
        public function index($setting) {
                $data['field_email']    = $setting['content_field_email'];
                $data['field_website']  = $setting['content_field_website'];
                $data['size']           = $setting['content_size'];
                $data['color']          = $setting['content_color'];
                
                if($this->mz_skin_config->get('blog_comment_status')){
                    return $this->load->view('extension/maza/blog/article/comment_form', $data);
                }
        }
}
