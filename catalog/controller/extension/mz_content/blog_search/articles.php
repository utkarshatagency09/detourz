<?php
class ControllerExtensionMzContentBlogSearchArticles extends maza\layout\Content {
        public function index($setting) {
                $data = array();
                $data['mz_suffix']                = $setting['mz_suffix'];
                
                $data['list_grid']                = $setting['content_list_grid'];
                
                $data['article_column_xs']        = $setting['content_column_xs'];
                $data['article_column_sm']        = $setting['content_column_sm'];
                $data['article_column_md']        = $setting['content_column_md'];
                $data['article_column_lg']        = $setting['content_column_lg'];
                $data['article_column_xl']        = $setting['content_column_xl'];
                      
                $data['comment_count_status']     = $setting['content_comment_count'];
                $data['viewed_count_status']      = $setting['content_viewed_count'];
                $data['author_status']            = $setting['content_author'];
                $data['category_status']          = $setting['content_category'];
                $data['timestamp_status']         = $setting['content_timestamp'];
                $data['readmore_status']          = $setting['content_readmore'];
                $data['description_status']       = $setting['content_description'];
                
                $data['lazy_loading']             = $this->mz_skin_config->get('blog_article_grid_image_lazy_loading');
                $data['transparent']              = $this->model_extension_maza_image->transparent($this->mz_skin_config->get('blog_article_grid_image_width'), $this->mz_skin_config->get('blog_article_grid_image_height'));
                
                $data['srcset_sizes'] = $this->model_extension_maza_image->getSrcSetSize($this->mz_skin_config->get('blog_article_grid_image_srcset'), $this->mz_skin_config->get('blog_article_grid_image_width'));
                
                return $this->load->view('extension/maza/blog/common/articles', $data);
        }
}
