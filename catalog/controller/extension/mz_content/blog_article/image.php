<?php
class ControllerExtensionMzContentBlogArticleImage extends maza\layout\Content {
        public function index($setting) {
                $data['additional_image_position'] = $setting['content_additional_image_position'];
                $data['suffix'] = $setting['mz_suffix'];
                $data['srcset_sizes'] = $this->model_extension_maza_image->getSrcSetSize($this->mz_skin_config->get('blog_article_image_srcset'), $this->mz_skin_config->get('blog_article_image_width'));
                
                return $this->load->view('extension/maza/blog/article/image', $data);
        }
}
