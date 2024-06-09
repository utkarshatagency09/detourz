<?php
class ControllerExtensionModuleMzTags extends maza\layout\Module {
        private $info = array();

        public function index(array $setting) {
                // Extension will not work without maza engine
                if(!$this->config->get('maza_status') || empty($setting['module_id'])){
                    return null;
                }
                
                $this->load->model('extension/maza/module');
                $this->load->model('extension/module/mz_tags');
                
                // Setting
                $this->info = $this->model_extension_maza_module->getSetting($setting['module_id']);
                
                if(!$this->info || !$this->info['status']){
                    return null;
                }
                
                $data = array();
                
                $data['heading_title'] = maza\getOfLanguage($this->info['title']);
                
                if($this->info['tags_source'] == 'product'){
                    $data['tags']   = $this->getProductTags();
                } elseif($this->info['tags_source'] == 'blog'){
                    $data['tags']   = $this->getBlogTags();
                } else {
                    $data['tags']   = array();
                }
                
                $data['tag_color']   = $this->info['tag_color'];
                
                if($data['tags']){
                    return $this->load->view('extension/module/mz_tags', $data);
                }
        }
        
        /**
         * Get product tags list
         * @return array tags
         */
        private function getProductTags() {
                $tags = array();
                
                // tag type
                if($this->info['product_tags_type'] == 'featured'){
                    $product_featured_tags = maza\getOfLanguage($this->info['product_featured_tags']);
                    $feature_tags = $product_featured_tags?explode(',', $product_featured_tags):array();
                    
                    foreach($feature_tags as $tag){
                        $tag_info = $this->model_extension_module_mz_tags->getProductTag(trim($tag));
                        
                        if($tag_info){
                            $tags[] = $tag_info;
                        }
                    }
                } else {
                    // Filter
                    $filter_data = array();
                    $filter_data['filter_category_id']      = !empty($this->info['product_filter_category'])?$this->info['product_filter_category']:array();
                    $filter_data['filter_sub_category']     = $this->info['product_filter_sub_category'];
                    $filter_data['filter_manufacturer_id']  = !empty($this->info['product_filter_manufacturer'])?$this->info['product_filter_manufacturer']:array();
                    $filter_data['filter_filter']           = !empty($this->info['product_filter_filter'])?$this->info['product_filter_filter']:array();
                    $filter_data['start']                   = 0;
                    $filter_data['limit']                   = $this->info['product_filter_limit'];

                    // Auto filter
                    if($this->info['product_filter_auto_filter']){
                        if(isset($this->request->get['category_id'])){
                            $filter_data['filter_category'] = $this->request->get['category_id'];
                        } elseif(isset($this->request->get['path'])){
                            $path = explode('_', (string)$this->request->get['path']);
                            $filter_data['filter_category'] = (int)array_pop($path);
                        }

                        if(isset($this->request->get['manufacturer_id'])){
                            $filter_data['filter_manufacturer_id'] = $this->request->get['manufacturer_id'];
                        }
                    }

                    if($this->info['product_tags_type'] == 'most_viewed') {
                        $filter_data['sort'] = 'viewed';
                    } else {
                        $filter_data['sort'] = 'used';
                    }
                    
                    $filter_data['order'] = 'DESC';
                    
                    $tags = $this->model_extension_module_mz_tags->getProductTags($filter_data);
                }
                
                $data = array();
                
                foreach ($tags as $tag) {
                    $data[] = array(
                        'tag_id'  => $tag['tag_id'],
                        'name'    => $tag['name'],
                        'viewed'    => $tag['viewed'],
                        'viewed'    => $tag['viewed'],
                        'href'    => $this->url->link('product/search', 'tag=' . urlencode(html_entity_decode($tag['name'], ENT_QUOTES, 'UTF-8')))
                    );
                }
                
                return $data;
        }
        
        /**
         * Get blog article tags list
         * @return array tags
         */
        private function getBlogTags() {
                $tags = array();
                
                // tag type
                if($this->info['blog_tags_type'] == 'featured'){
                    $blog_featured_tags = maza\getOfLanguage($this->info['blog_featured_tags']);
                    $feature_tags = $blog_featured_tags?explode(',', $blog_featured_tags):array();
                    
                    foreach($feature_tags as $tag){
                        $tag_info = $this->model_extension_module_mz_tags->getBlogTag(trim($tag));
                        
                        if($tag_info){
                            $tags[] = $tag_info;
                        }
                    }
                } else {
                    // Filter
                    $filter_data = array();
                    $filter_data['filter_category_id']      = !empty($this->info['blog_filter_category'])?$this->info['blog_filter_category']:array();
                    $filter_data['filter_sub_category']     = $this->info['product_filter_sub_category'];
                    $filter_data['filter_author_id']        = !empty($this->info['blog_filter_author'])?$this->info['blog_filter_author']:array();
                    $filter_data['filter_filter']           = !empty($this->info['blog_filter_filter'])?$this->info['blog_filter_filter']:array();
                    $filter_data['start']                   = 0;
                    $filter_data['limit']                   = $this->info['blog_filter_limit'];

                    // Auto filter
                    if($this->info['blog_filter_auto_filter']){
                        if(isset($this->request->get['category_id'])){
                            $filter_data['filter_category'] = $this->request->get['category_id'];
                        } elseif(isset($this->request->get['path'])){
                            $path = explode('_', (string)$this->request->get['path']);
                            $filter_data['filter_category'] = (int)array_pop($path);
                        }

                        if(isset($this->request->get['author_id'])){
                            $filter_data['filter_author_id'] = $this->request->get['author_id'];
                        }
                    }

                    if($this->info['blog_tags_type'] == 'most_viewed') {
                        $filter_data['sort'] = 'viewed';
                    } else {
                        $filter_data['sort'] = 'used';
                    }
                    $filter_data['sort'] = 'DESC';
                    
                    $tags = $this->model_extension_module_mz_tags->getBlogTags($filter_data);
                }
                
                $data = array();
                
                foreach ($tags as $tag) {
                    $data[] = array(
                        'tag_id'  => $tag['tag_id'],
                        'name'    => $tag['name'],
                        'href'    => $this->url->link('extension/maza/blog/search', 'tag=' . urlencode(html_entity_decode($tag['name'], ENT_QUOTES, 'UTF-8')))
                    );
                }
                
                return $data;
        }
        
        /**
         * Generate product and blog tags 
         */
        public function generateTags(){
                $this->load->model('extension/module/mz_tags');
                $this->load->model('catalog/product');
                $this->load->model('localisation/language');
                
                $languages = $this->model_localisation_language->getLanguages();
                
                // products tags
                foreach($languages as $language){
                    $limit = 50;
                    $start = 0;
                    
                    while(true){
                        $query = $this->db->query("SELECT p.product_id, pd.tag FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . $language['language_id'] . "' GROUP BY p.product_id ORDER BY p.date_modified ASC LIMIT $start,$limit");
                        
                        if($query->num_rows){
                            foreach($query->rows as $product){
                                $this->model_extension_module_mz_tags->addProductTags($product['product_id'], explode(',', $product['tag']), $language['language_id']);
                            }
                        } else {
                            break;
                        }
                        
                        $start += $limit;
                    }
                }
                
                // Delete garbag tags
                $this->db->query("DELETE t FROM `" . DB_PREFIX . "mz_product_tags` t LEFT JOIN `" . DB_PREFIX . "mz_product_to_tags` p2t ON (t.tag_id = p2t.tag_id) WHERE p2t.tag_id IS NULL");
                
                // Count of use of tags
                $this->db->query("UPDATE `" . DB_PREFIX . "mz_product_tags` t SET used = (SELECT COUNT(*) FROM `" . DB_PREFIX . "mz_product_to_tags` p2t WHERE p2t.tag_id = t.tag_id)");
                
                // blog article tags
                foreach($languages as $language){
                    $limit = 50;
                    $start = 0;
                    
                    while(true){
                        $query = $this->db->query("SELECT a.article_id, ad.tag FROM `" . DB_PREFIX . "mz_blog_article` a LEFT JOIN `" . DB_PREFIX . "mz_blog_article_description` ad ON (a.article_id = ad.article_id) WHERE ad.language_id = '" . $language['language_id'] . "' GROUP BY a.article_id ORDER BY a.date_modified ASC LIMIT $start,$limit");
                        
                        if($query->num_rows){
                            foreach($query->rows as $article){
                                $this->model_extension_module_mz_tags->addBlogTags($article['article_id'], explode(',', $article['tag']), $language['language_id']);
                            }
                        } else {
                            break;
                        }
                        
                        $start += $limit;
                    }
                }
                
                // Delete garbag tags
                $this->db->query("DELETE t FROM `" . DB_PREFIX . "mz_blog_article_tags` t LEFT JOIN `" . DB_PREFIX . "mz_blog_article_to_tags` a2t ON (t.tag_id = a2t.tag_id) WHERE a2t.tag_id IS NULL");
                
                // Count of use of tags
                $this->db->query("UPDATE `" . DB_PREFIX . "mz_blog_article_tags` t SET used = (SELECT COUNT(*) FROM `" . DB_PREFIX . "mz_blog_article_to_tags` a2t WHERE a2t.tag_id = t.tag_id)");
        }
}