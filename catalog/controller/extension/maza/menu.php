<?php
class ControllerExtensionMazaMenu extends Controller {
    static $suffix = 0;

    public function __construct($registry) {
        parent::__construct($registry);
        self::$suffix++;
    }

    /**
     * Get menu items
     * @param int $menu_id
     * @return string
     */
    public function index(int $menu_id): string {
        $this->load->model('extension/maza/menu');
        $this->load->model('catalog/category');
        $this->load->model('account/wishlist');
        $this->load->model('extension/maza/notification');

        $menu_info = $this->model_extension_maza_menu->getMenu($menu_id);

        if ($menu_info) {
            return $this->getItems($menu_id, 0, true);
        }
        return '';
    }

    /**
     * Get all Items html
     * @param int $menu_id
     * @param int $parent_item_id
     * @param bool $top
     * @return string
     */
    private function getItems(int $menu_id, int $parent_item_id = 0, bool $top = false): string {
        $menu_items = $this->model_extension_maza_menu->getItems($menu_id, $parent_item_id);

        $data = '';

        foreach ($menu_items as $menu_item) {
            $data .= $this->getItem($menu_item, $top);
        }

        return $data;
    }

    /**
     * Get Item html
     * @param array $item_info
     * @param bool $top
     * @return string
     */
    private function getItem(array $item_info, bool $top = false): string {
        $data['item_id']       = $item_info['item_id'];
        $data['type']          = $item_info['type'];
        $data['has_dropdown']  = false;
        $data['child_content'] = $this->getItems($item_info['menu_id'], $item_info['item_id']);
        $data['top']           = $top;

        // title
        $data['title'] = maza\getOfLanguage($item_info['setting']['title']);

        // description
        $data['description'] = maza\getOfLanguage($item_info['setting']['description']);

        // label
        $data['label'] = maza\getOfLanguage($item_info['setting']['label']);

        // icon
        $data['icon_width']  = $item_info['setting']['icon_width'];
        $data['icon_height'] = $item_info['setting']['icon_height'];
        $data['icon_size']   = $item_info['setting']['icon_size'];

        // font icon
        $data['icon_font'] = maza\getOfLanguage($item_info['setting']['icon_font']);

        // svg image
        $icon_svg = maza\getOfLanguage($item_info['setting']['icon_svg']);
        if ($icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)) {
            $data['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
        } else {
            $data['icon_svg'] = false;
        }

        // Image
        $icon_image = maza\getOfLanguage($item_info['setting']['icon_image']);
        if ($icon_image && is_file(DIR_IMAGE . $icon_image)) {
            list($width, $height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $item_info['setting']['icon_width'], $item_info['setting']['icon_height']);

            $data['icon_image'] = $this->model_tool_image->resize($icon_image, $width, $height);
        } else {
            $data['icon_image'] = false;
        }


        // Category type
        if ($item_info['type'] == 'category') {
            $path = $this->model_extension_maza_catalog_category->getCategoryPath($item_info['setting']['category_top_id']);

            $data['link'] = $this->url->link('product/category', 'path=' . $path);

            // Categories
            if ($item_info['setting']['category_column']) {
                $top_category_info = $this->model_catalog_category->getCategory($item_info['setting']['category_top_id']);

                if ($top_category_info) {
                    $data['category_column'] = $top_category_info['column'];
                    $data['categories']      = $this->getCategories($item_info['setting']['category_top_id'], 1, $path);
                }
            } else {
                $data['categories'] = $this->getCategories($item_info['setting']['category_top_id'], $item_info['setting']['category_depth'], $path);
            }

            if ($data['categories']) {
                $data['has_dropdown'] = true;
            }
        }


        // System type
        if ($item_info['type'] == 'system') {
            $data['system_item'] = $item_info['setting']['system_item'];

            // Language
            if ($item_info['setting']['system_item'] == 'language') {
                $data['has_dropdown'] = true;

                $data['languages'] = array();

                $results = $this->model_localisation_language->getLanguages();
                if (count($results) <= 1)
                    return ''; // Minimum 2 languages required to display language option

                foreach ($results as $result) {
                    if ($result['status']) {
                        $data['languages'][$result['code']] = array(
                            'name' => $result['name'],
                            'code' => $result['code']
                        );
                    }
                }

                // Current language
                if (isset($data['languages'][$this->session->data['language']])) {
                    $data['language_code'] = $data['languages'][$this->session->data['language']]['code'];
                    $data['language_name'] = $data['languages'][$this->session->data['language']]['name'];
                }

            }

            // Currency
            if ($item_info['setting']['system_item'] == 'currency') {
                $data['has_dropdown'] = true;

                $data['currencies'] = array();

                $results = $this->model_localisation_currency->getCurrencies();
                if (count($results) <= 1)
                    return ''; // Minimum 2 currencies required to display currencies option
                foreach ($results as $result) {
                    if ($result['status']) {
                        $data['currencies'][$result['code']] = array(
                            'title' => $result['title'],
                            'code' => $result['code'],
                            'symbol_left' => $result['symbol_left'],
                            'symbol_right' => $result['symbol_right']
                        );
                    }
                }

                // Current currency
                if (isset($data['currencies'][$this->session->data['currency']])) {
                    $data['currency_title']        = $data['currencies'][$this->session->data['currency']]['title'];
                    $data['currency_code']         = $data['currencies'][$this->session->data['currency']]['code'];
                    $data['currency_symbol_left']  = $data['currencies'][$this->session->data['currency']]['symbol_left'];
                    $data['currency_symbol_right'] = $data['currencies'][$this->session->data['currency']]['symbol_right'];
                }
            }

            if ($item_info['setting']['system_item'] == 'currency' || $item_info['setting']['system_item'] == 'language') {
                if (!isset($this->request->get['route'])) {
                    $data['redirect'] = $this->url->link('common/home');
                } else {
                    $url_data = $this->request->get;

                    unset($url_data['_route_']);

                    $route = $url_data['route'];

                    unset($url_data['route']);

                    $url = '';

                    if ($url_data) {
                        $url = '&' . urldecode(http_build_query($url_data, '', '&'));
                    }

                    $data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
                }
            }

            // Wishlist
            if ($item_info['setting']['system_item'] == 'wishlist') {
                if ($this->customer->isLogged()) {
                    $data['wishlist_total'] = $this->model_account_wishlist->getTotalWishlist();
                } else {
                    $data['wishlist_total'] = isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0;
                }
                $data['link'] = $this->url->link('account/wishlist');
            }

            // Compare
            if ($item_info['setting']['system_item'] == 'compare') {
                $data['compare_total'] = isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0;
                $data['link']          = $this->url->link('product/compare');
            }

            // Notification
            if ($item_info['setting']['system_item'] == 'notification') {
                if ($this->customer->isLogged()) {
                    $data['notification_total'] = $this->model_extension_maza_notification->getTotalNotifications();
                    $data['link']               = $this->url->link('extension/maza/notification');
                } else {
                    return '';
                }
            }

            // customer greeting
            if ($item_info['setting']['system_item'] == 'customer') {
                if ($this->customer->isLogged()) {
                    $data['title'] = sprintf($this->language->get('text_greeting'), $this->customer->getFirstname());
                    $data['link']  = $this->url->link('account/account');
                } else {
                    $data['title'] = $this->language->get('text_login');
                    $data['link']  = $this->url->link('account/login');
                }
            }

            // Telephone
            if ($item_info['setting']['system_item'] == 'tel') {
                $data['tel']  = $this->config->get('config_telephone');
                $data['link'] = 'tel:' . $this->config->get('config_telephone');
            }

            // Fax
            if ($item_info['setting']['system_item'] == 'fax') {
                $data['fax'] = $this->config->get('config_fax');
            }

            // Email
            if ($item_info['setting']['system_item'] == 'email') {
                $data['email'] = $this->config->get('config_email');
                $data['link']  = 'mailto:' . $this->config->get('config_email');
            }
        }


        // Link type
        if ($item_info['type'] == 'link' || $item_info['type'] == 'menu' || $item_info['type'] == 'content') {
            if ($item_info['setting']['link_code']) {
                $link = $this->model_extension_maza_common->createLink($item_info['setting']['link_code']);
            } else {
                $link = array();
            }
            if ($link) {
                $data['link']      = $link['href'];
                $data['link_attr'] = $link['attr'] ?? '';

                if ($item_info['setting']['link_url_target'] != '_self') {
                    $data['link_attr'] .= ' target="' . $item_info['setting']['link_url_target'] . '"';
                }
            }
        }

        // Menu type
        if ($item_info['type'] == 'menu') {
            $data['child_content'] = $this->getItems($item_info['setting']['menu_id']);
        }

        // Content type
        if ($item_info['type'] == 'content') {
            $data['child_content'] = $this->load->controller('extension/maza/layout_builder', ['group' => 'content_builder', 'group_owner' => $item_info['setting']['content_id'], 'suffix' => $item_info['item_id'] . self::$suffix]);
            $data['dropdown_100']  = empty($item_info['setting']['content_width']);
        }

        if (!empty($data['child_content'])) {
            $data['has_dropdown'] = true;
        }

        if ($this->mz_skin_config->get('flag_compile_route_asset')) {
            $this->addCSS($item_info);
        }

        return $this->load->view('extension/maza/menu', $data);
    }

    /**
     * Get recursive categories
     * @param int $parent_id
     * @param int $depth
     * @return array
     */
    private function getCategories(int $parent_id = 0, int $depth = 0, $path = null): array {
        $category_data = array();

        if ($depth < 0 || $depth > 0) {
            $categories = $this->model_catalog_category->getCategories($parent_id);
        } else {
            $categories = array();
        }

        if ($path) {
            $path .= '_';
        }

        foreach ($categories as $category) {
            $category_data[] = array(
                'category_id' => $category['category_id'],
                'name' => $category['name'],
                'children' => $this->getCategories($category['category_id'], $depth - 1, $path . $category['category_id']),
                'href' => $this->url->link('product/category', 'path=' . $path . $category['category_id']),
            );
        }

        return $category_data;
    }

    /**
     * Add CSS of menu item
     * @param array $item_info item setting
     */
    private function addCSS(array $item_info): void {
        // Sub menu background
        // Get layer wise list of background image
        $background_image_src = $background_image_position = $background_image_repeat = array();

        foreach ($item_info['setting']['sub_menu_background'] ?? [] as $background_image) {
            // ** Custom background image
            if ($background_image['status'] === 'image') {
                // Get image width and height
                list($width_orig, $height_orig) = $this->model_extension_maza_image->getEstimatedSize($background_image['image']);

                $background_image_src[]      = 'url("' . $this->model_tool_image->resize($background_image['image'], $width_orig, $height_orig) . '")';
                $background_image_position[] = str_replace('_', ' ', $background_image['image_position']);
                $background_image_repeat[]   = $background_image['image_repeat'];

                // ** Backgrond Pattern
            } elseif ($background_image['status'] === 'pattern' && !empty($background_image['overlay_pattern'])) {
                $overlay_pattern = $this->model_extension_maza_asset->overlayPatterns($background_image['overlay_pattern']);

                if ($overlay_pattern) {
                    $background_image_src[] = 'url("' . $this->config->get('mz_store_url') . 'image/' . $overlay_pattern['image'] . '")';
                    ;
                    $background_image_position[] = 'Left Top';
                    $background_image_repeat[]   = 'repeat';
                } // -- overlay pattern

            } // -- background pattern
        } // -- background image

        $background_image_src      = implode(', ', $background_image_src);
        $background_image_position = implode(', ', $background_image_position);
        $background_image_repeat   = implode(', ', $background_image_repeat);

        $scss = '';

        // Background
        $background_scss = '';

        if ($background_image_src) {
            $background_scss .= "background-image: $background_image_src;";
        }
        if ($background_image_position) {
            $background_scss .= "background-position: $background_image_position;";
        }
        if ($background_image_repeat) {
            $background_scss .= "background-repeat: $background_image_repeat;";
        }

        if ($background_scss) {
            $scss .= ".mz-sub-menu-{$item_info['item_id']}{{$background_scss}}";
        }

        // Label
        $label_scss = '';
        if ($item_info['setting']['label']) {
            if ($item_info['setting']['label_background_color']) {
                $label_scss .= "background-color: {$item_info['setting']['label_background_color']};";
            }
            if ($item_info['setting']['label_text_color']) {
                $label_scss .= "color: {$item_info['setting']['label_text_color']};";
            }
            if ($label_scss) {
                $scss .= ".mz-menu-label-{$item_info['item_id']}{{$label_scss}}";
            }
        }

        // Content menu type
        if ($item_info['type'] == 'content' && !empty($item_info['setting']['content_width'])) {
            $scss .= ".mz-sub-menu-{$item_info['item_id']}{ width: {$item_info['setting']['content_width']}px;}";
        }

        $this->mz_document->addCSSCode($scss);
    }
}