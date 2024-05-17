<?php
class ControllerExtensionModuleMzArticleListing extends maza\layout\Module {
    private static $instance_count = 0;

    public function index(array $module_setting) {
        // Extension will not work without maza engine
        if (!$this->config->get('maza_status') || empty($module_setting['module_id'])) {
            return null;
        }

        $this->load->language('extension/module/mz_article_listing');

        $this->load->model('extension/maza/module');
        $this->load->model('extension/maza/blog/article');


        // Setting
        $setting = $this->model_extension_maza_module->getSetting($module_setting['module_id']);
        if (!$setting || !$setting['status']) {
            return null;
        }
        $setting['module_id'] = $module_setting['module_id'];

        // Add assets
        if ($setting['carousel_status']) {
            if ($this->config->get('maza_cdn')) {
                $this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
                $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/Swiper/7.4.1/swiper-bundle.min.js', 'footer');
            } else {
                $this->document->addStyle('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.css', 'stylesheet', 'all', 'footer');
                $this->document->addScript('catalog/view/javascript/maza/javascript/swiperjs/swiper-bundle.min.js', 'footer');
            }
        }

        // Tabs
        $data['tabs'] = $this->getTabs($setting);

        $data['mz_suffix'] = $module_setting['mz_suffix'] ?? self::$instance_count++;

        if ($data['tabs']) {
            return $this->mz_load->view($this->template($setting, $data), $data, 'extension/module/mz_article_listing');
        }
    }

    private function template(array $setting, array $data = array()): string {
        $carousel_nav_icon_svg = maza\getOfLanguage($setting['carousel_nav_icon_svg']);
        if (empty(maza\getOfLanguage($setting['carousel_nav_icon_font'])) && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg)) {
            $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg);
        }

        $templete = $this->getCache('mz_article_listing.' . $setting['module_id']); // get static cache

        if ($templete) {
            return $templete;
        }

        // Module Title
        $data['heading_title'] = maza\getOfLanguage($setting['title']);

        // Image
        $data['article_image_width']    = $setting['article_image_width'];
        $data['article_image_height']   = $setting['article_image_height'];
        $data['article_image_position'] = $setting['article_image_position'];

        // Banner
        $data['banner_width']  = $setting['banner_width'];
        $data['banner_height'] = $setting['banner_height'];

        if ($setting['banner_status']) {
            // svg image
            $banner_svg = maza\getOfLanguage($setting['banner_svg']);
            if ($banner_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $banner_svg)) {
                $data['banner_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $banner_svg);
            } else {
                $data['banner_svg'] = false;
            }

            // static Image
            $banner_image = maza\getOfLanguage($setting['banner_image']);
            if ($banner_image && is_file(DIR_IMAGE . $banner_image)) {
                list($banner_width, $banner_height) = $this->model_extension_maza_image->getEstimatedSize($banner_image, $setting['banner_width'], $setting['banner_height']);
                $data['banner_image']               = $this->model_tool_image->resize($banner_image, $banner_width, $banner_height);
            } else {
                $data['banner_image'] = false;
            }

            // banner link
            if ($setting['banner_link_code']) {
                $data['banner_url'] = $this->model_extension_maza_common->createLink($setting['banner_link_code']);
            } else {
                $data['banner_url'] = array();
            }
        }

        // Tabs icon
        $data['tab_icon_width']    = $setting['tab_icon_width'];
        $data['tab_icon_height']   = $setting['tab_icon_height'];
        $data['tab_icon_position'] = $setting['tab_icon_position'];

        // Column
        $data['column_xs'] = $setting['column_xs'];
        $data['column_sm'] = $setting['column_sm'];
        $data['column_md'] = $setting['column_md'];
        $data['column_lg'] = $setting['column_lg'];
        $data['column_xl'] = $setting['column_xl'];

        // carousel
        $data['carousel_status']     = $setting['carousel_status'];
        $data['carousel_autoplay']   = $setting['carousel_autoplay'];
        $data['carousel_pagination'] = $setting['carousel_pagination'];
        //$data['carousel_loop']       = $setting['carousel_loop'];
        $data['carousel_row'] = $setting['carousel_row'];

        // carousel navigation icon
        $data['carousel_nav_icon_width']  = $setting['carousel_nav_icon_width'];
        $data['carousel_nav_icon_height'] = $setting['carousel_nav_icon_height'];
        $data['carousel_nav_icon_size']   = $setting['carousel_nav_icon_size'];
        $data['carousel_nav_icon_font']   = false;
        $data['carousel_nav_icon_svg']    = false;
        $data['carousel_nav_icon_image']  = false;

        $data['carousel_nav_icon_font'] = maza\getOfLanguage($setting['carousel_nav_icon_font']);

        $carousel_nav_icon_svg = maza\getOfLanguage($setting['carousel_nav_icon_svg']);
        if ($carousel_nav_icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg)) {
            $data['carousel_nav_icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $carousel_nav_icon_svg);
        }

        $carousel_nav_icon_image = maza\getOfLanguage($setting['carousel_nav_icon_image']);
        if ($carousel_nav_icon_image && is_file(DIR_IMAGE . $carousel_nav_icon_image)) {
            list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($carousel_nav_icon_image, $setting['carousel_nav_icon_width'], $setting['carousel_nav_icon_height']);

            $data['carousel_nav_icon_width']  = $image_width;
            $data['carousel_nav_icon_height'] = $image_height;

            $data['carousel_nav_icon_image'] = $this->model_tool_image->resize($carousel_nav_icon_image, $image_width, $image_height);
        }

        // Element
        $data['article_grid_comment_count']      = $setting['article_grid_comment_count'];
        $data['article_grid_viewed_count']       = $setting['article_grid_viewed_count'];
        $data['article_grid_author_status']      = $setting['article_grid_author_status'];
        $data['article_grid_category_status']    = $setting['article_grid_category_status'];
        $data['article_grid_timestamp_status']   = $setting['article_grid_timestamp_status'];
        $data['article_grid_readmore_status']    = $setting['article_grid_readmore_status'];
        $data['article_grid_description_status'] = $setting['article_grid_description_status'];

        // Image lazy loading
        $data['image_lazy_loading'] = $this->mz_skin_config->get('blog_article_grid_image_lazy_loading');

        $data['transparent'] = $this->model_extension_maza_image->transparent($setting['article_image_width'], $setting['article_image_height']);


        // layout
        $data['tab_status'] = $setting['tab_status'];
        $data['url_target'] = $setting['url_target'];

        // Style
        $data['gutter_width']  = $this->mz_skin_config->get('style_gutter_width');
        $data['breakpoint_sm'] = $this->mz_skin_config->get('style_breakpoints')['sm'];
        $data['breakpoint_md'] = $this->mz_skin_config->get('style_breakpoints')['md'];
        $data['breakpoint_lg'] = $this->mz_skin_config->get('style_breakpoints')['lg'];
        $data['breakpoint_xl'] = $this->mz_skin_config->get('style_breakpoints')['xl'];

        $template = $this->load->view('extension/module/mz_article_listing', $data);

        $this->setCache('mz_article_listing.' . $setting['module_id'], $template, false); //set static cache of templare

        return $template;
    }

    /**
     * Get list of tabs with articles
     * @param array $setting module setting
     * @return array tab list
     */
    private function getTabs(array $setting): array {
        $tab_data = $sort_order = array();

        foreach ($setting['tabs'] as $tab) {
            $tab_info = array();

            // tab name
            $tab_info['name'] = maza\getOfLanguage($tab['name']);

            // skin step
            if (!$tab['status'] || !$tab_info['name']) {
                continue;
            }

            // Get articles
            $tab_info['articles'] = array();

            foreach ($this->getTabArticles($tab, $setting['article_limit']) as $result) {

                $thumb        = $this->model_tool_image->resize($result['image'], $setting['article_image_width'], $setting['article_image_height']);
                $thumb_srcset = $this->model_extension_maza_image->getSrcSet($setting['article_image_srcset'], $result['image'], $setting['article_image_width'], $setting['article_image_height']);

                // Additional images
                if ($setting['article_grid_additional_image'] > 0 || ($setting['article_grid_additional_image'] && $this->mz_skin_config->get('blog_article_grid_additional_image'))) {
                    $additional_images = $this->model_extension_maza_blog_article->getArticleImages($result['article_id']);
                } else {
                    $additional_images = array();
                }
                $images = array();
                foreach ($additional_images as $image_result) {
                    $images[] = array(
                        'image' => $this->model_tool_image->resize($image_result['image'], $setting['article_image_width'], $setting['article_image_height']),
                        'srcset' => $this->model_extension_maza_image->getSrcSet($setting['article_image_srcset'], $image_result['image'], $setting['article_image_width'], $setting['article_image_height']),
                    );
                }

                // category
                if ($result['category_id']) {
                    $category = implode(' > ', array_column($this->model_extension_maza_blog_category->getCategoryPath($result['category_id']), 'name'));
                } else {
                    $category = '';
                }


                $tab_info['articles'][] = array(
                    'article_id' => $result['article_id'],
                    'thumb' => $thumb,
                    'srcset' => $thumb_srcset,
                    'images' => $images,
                    'name' => $result['name'],
                    'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $setting['article_grid_description_limit'] ?: $this->mz_skin_config->get('blog_article_grid_description_limit')) . '..',
                    'author' => $result['author'],
                    'author_href' => $this->url->link('extension/maza/blog/author', 'author_id=' . $result['author_id']),
                    'category' => $category,
                    'category_href' => $this->url->link('extension/maza/blog/category', 'path=' . $result['category_id']),
                    'comments' => (int) $result['comments'],
                    'viewed' => (int) $result['viewed'],
                    'timestamp' => strftime('%e %b %Y', strtotime($result['date_available'] ?: $result['date_added'])),
                    'href' => $this->url->link('extension/maza/blog/article', 'article_id=' . $result['article_id'])
                );
            }


            if (empty($tab_info['articles'])) {
                continue;
            }

            // Tab icon
            // font icon
            $tab_info['icon_font'] = maza\getOfLanguage($tab['icon_font']);

            // svg image
            $icon_svg = maza\getOfLanguage($tab['icon_svg']);
            if ($icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)) {
                $tab_info['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
            } else {
                $tab_info['icon_svg'] = false;
            }

            // Image
            $icon_image = maza\getOfLanguage($tab['icon_image']);
            if ($icon_image && is_file(DIR_IMAGE . $icon_image)) {
                list($icon_width, $icon_height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $setting['tab_icon_width'], $setting['tab_icon_height']);
                $tab_info['icon_image']         = $this->model_tool_image->resize($icon_image, $icon_width, $icon_height);
            } else {
                $tab_info['icon_image'] = false;
            }

            // Sort order of tab
            $sort_order[] = $tab['sort_order'];

            $tab_data[] = $tab_info;
        }

        // Sort tab
        array_multisort($sort_order, SORT_ASC, SORT_NUMERIC, $tab_data);

        return $tab_data;
    }

    /**
     * Get articles of tab
     * @param array $tab tab data
     * @param int $article_limit article limit
     * @return array list of articles
     */
    private function getTabArticles(array $tab, int $article_limit): array {
        // Auto filter
        $auto_filter = array();

        if ($tab['auto_filter']) {
            if (isset($this->request->get['category_id'])) {
                $auto_filter['filter_category_id'] = $this->request->get['category_id'];
            } elseif (isset($this->request->get['path'])) {
                $path                              = explode('_', (string) $this->request->get['path']);
                $auto_filter['filter_category_id'] = (int) array_pop($path);
            }

            if (isset($this->request->get['author_id'])) {
                $auto_filter['filter_author_id'] = $this->request->get['author_id'];
            }
        }

        // Get custom articles
        if ($tab['source'] === 'article') {
            $articles = array();
            foreach ($tab['custom_article'] ?? [] as $article_id) {
                if ($article = $this->model_extension_maza_blog_article->getArticle($article_id)) {
                    $articles[] = $article;
                }
            }
            return $articles;
        }

        // Get featured articles
        if ($tab['source'] === 'featured') {
            return $this->model_extension_maza_blog_article->getFeaturedArticles($article_limit);
        }

        // Get recent viewed articles
        if ($tab['source'] === 'recent_viewed') {
            $articles = array();
            foreach (array_slice($this->session->data['mz_recent_viewed_article'] ?? [], 0, $article_limit) as $article_id) {
                if ($article = $this->model_extension_maza_blog_article->getArticle($article_id)) {
                    $articles[] = $article;
                }
            }
            return $articles;
        }

        // Get related articles
        if ($tab['source'] === 'related') {
            if (isset($this->request->get['article_id'])) {
                return $this->model_extension_maza_blog_article->getArticleRelated($this->request->get['article_id']);
            } else {
                return [];
            }
        }

        // Get articles by filter
        if ($tab['source'] === 'filter') {
            return $this->getArticlesByFilter($tab, $auto_filter, $article_limit);
        }

        // Filter
        $filter          = array();
        $filter['start'] = 0;
        $filter['limit'] = $article_limit;

        // Get latest articles
        if ($tab['source'] === 'latest') {
            $filter['sort_order'] = array(
                array('sort' => 'a.date_available', 'order' => 'DESC'),
                array('sort' => 'a.date_added', 'order' => 'DESC'),
            );
        }

        // Get most viewed articles
        if ($tab['source'] === 'most_viewed') {
            $filter['sort_order'] = array(
                array('sort' => 'a.viewed', 'order' => 'ASC'),
                array('sort' => 'a.date_added', 'order' => 'ASC')
            );
        }

        // Get Random articles
        if ($tab['source'] === 'random') {
            $filter['sort'] = 'random';
        }

        return $this->model_extension_maza_blog_article->getArticles(array_merge($filter, $auto_filter));
    }

    /**
     * Get articles by filter
     * @param array $data article filter
     * @param array $auto_filter Auto filter
     * @param int $article_limit article limit
     * @return array list of filtered articles
     */
    private function getArticlesByFilter(array $data, array $auto_filter, int $article_limit): array {
        $filter = array();

        // Filter category
        if (isset($auto_filter['filter_category_id'])) {
            $filter['filter_category_id'] = $auto_filter['filter_category_id'];
        } elseif (isset($data['filter_category'])) {
            $filter['filter_category_id'] = $data['filter_category'];
        } else {
            $filter['filter_category_id'] = array();
        }


        // Include or exclude sub category
        $filter['filter_sub_category'] = $data['filter_sub_category'];

        // Depth of sub category
        $filter['filter_sub_category_depth'] = $data['filter_sub_category_depth'];

        // Filter author
        if (isset($auto_filter['filter_author_id'])) {
            $filter['filter_author_id'] = $auto_filter['filter_author_id'];
        } elseif (isset($data['filter_author'])) {
            $filter['filter_author_id'] = $data['filter_author'];
        } else {
            $filter['filter_author_id'] = array();
        }


        // Filter article filter
        $filter['filter_filter'] = isset($data['filter_article_filter']) ? $data['filter_article_filter'] : array();

        // Filter date added
        if ($data['filter_date_add_start']) {
            $filter['filter_date_add_start'] = $data['filter_date_add_start'];
        }
        if ($data['filter_date_add_end']) {
            $filter['filter_date_add_end'] = $data['filter_date_add_end'];
        }

        // Sort and order
        $filter['sort'] = $data['filter_sort_by'];
        switch ($filter['sort']) {
            case 'name':
                $filter['sort'] = 'ad.name';
                break;
            case 'viewed':
                $filter['sort'] = 'a.viewed';
                break;
            case 'sort_order':
                $filter['sort'] = 'a.sort_order';
                break;
            case 'date_added':
                $filter['sort'] = 'a.date_added';
                break;
            default:
                break;
        }

        $filter['order'] = $data['filter_sort_direction'];

        // article limit
        $filter['start'] = 0;
        $filter['limit'] = $article_limit;

        return $this->model_extension_maza_blog_article->getArticles($filter);
    }
}