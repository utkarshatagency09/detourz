<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionModuleMzProductListing extends Controller {
    private $error = array();
    
    public function index(): void {
        $this->load->language('extension/module/mz_product_listing');
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('extension/maza/module');
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/filter');
        $this->load->model('tool/image');
        $this->load->model('extension/maza/opencart');
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
        
        // Header
        $header_data = array();
        $header_data['title'] = $this->language->get('heading_title');
        $header_data['menu'] = array(
            array('name' => $this->language->get('tab_general'), 'id' => 'menu-general', 'href' => false),
            array('name' => $this->language->get('tab_tabs'), 'id' => 'menu-tabs', 'href' => false),
            array('name' => $this->language->get('tab_image'), 'id' => 'menu-image', 'href' => false),
            array('name' => $this->language->get('tab_product'), 'id' => 'menu-product', 'href' => false),
            array('name' => $this->language->get('tab_layout'), 'id' => 'menu-layout', 'href' => false),
            array('name' => $this->language->get('tab_carousel'), 'id' => 'menu-carousel', 'href' => false),
        );
        
        $header_data['menu_active'] = 'menu-general';
        
        // Buttons
        $header_data['buttons'][] = array( // Button save
            'id' => 'button-save',
            'name' => false,
            'tooltip' => $this->language->get('button_save'),
            'icon' => 'fa-save',
            'class' => 'btn-primary',
            'href' => FALSE,
            'target' => FALSE,
            'form_target_id' => 'form-product-listing',
        );
        $header_data['buttons'][] = array( // Button import
            'id' => 'button-import',
            'name' => false,
            'tooltip' => $this->language->get('button_import'),
            'icon' => 'fa-upload',
            'class' => 'btn-warning',
            'href' => FALSE,
            'target' => FALSE,
            'form_target_id' => false,
        );
        if (isset($this->request->get['module_id'])) {
            $header_data['buttons'][] = array( // Button export
                'id' => 'button-export',
                'name' => false,
                'tooltip' => $this->language->get('button_export'),
                'icon' => 'fa-download',
                'class' => 'btn-warning',
                'href' => $this->url->link('extension/module/mz_product_listing/export', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'] . $url, true),
                'target' => '_self',
                'form_target_id' => false,
            );
            $header_data['buttons'][] = array( // Button delete
                'id' => 'button-delete',
                'name' => false,
                'tooltip' => $this->language->get('button_delete'),
                'icon' => 'fa-trash',
                'class' => 'btn-danger',
                'confirm' => $this->language->get('text_confirm'),
                'href' => $this->url->link('extension/module/mz_product_listing/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true),
                'target' => '_self',
                'form_target_id' => false,
            );
        }
        $header_data['buttons'][] = array( // Button cancel
            'id' => 'button-cancel',
            'name' => false,
            'tooltip' => $this->language->get('button_cancel'),
            'icon' => 'fa-reply',
            'class' => 'btn-default',
            'href' => $this->url->link('extension/maza/module', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => '_self',
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#module-maza-product-listing',
            'target' => '_blank'
        );
        
        // Form submit id
        $header_data['form_target_id'] = 'form-product-listing';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
        
        // Submit form and save module in case of no error
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()){
            if (!isset($this->request->get['module_id'])) {
                    $module_id = $this->model_extension_maza_module->addModule('mz_product_listing', $this->mz_skin_config->get('skin_id'), $this->request->post);
            } else {
                    $this->model_extension_maza_module->editModule($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'), $this->request->post);
            }
            
            $this->session->data['success'] = $this->language->get('text_success');
            
            // Add module id in url and redirect to it after newly added module
            if(isset($module_id)){
                $this->response->redirect($this->url->link('extension/module/mz_product_listing', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true)); 
            }
        }
        
        if(isset($this->session->data['warning'])){
            $data['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        } elseif(isset($this->error['warning'])){
            $data['warning'] = $this->error['warning'];
        }
        
        if(isset($this->session->data['success'])){
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        
        foreach ($this->error as $label => $error) {
            $data['err_' . $label] = $error;
        }
        
        if (!isset($this->request->get['module_id'])) {
                $data['action'] = $this->url->link('extension/module/mz_product_listing', 'user_token=' . $this->session->data['user_token'] . $url, true);
                $data['import'] = $this->url->link('extension/module/mz_product_listing/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
                $data['action'] = $this->url->link('extension/module/mz_product_listing', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
                $data['import'] = $this->url->link('extension/module/mz_product_listing/import', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'] . $url, true);
        }
        
        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                $global_setting = $this->model_extension_maza_opencart->getModule($this->request->get['module_id']);
                $module_setting = $this->model_extension_maza_module->getSetting($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'));
        } else {
                $global_setting = $module_setting = array();
        }
        
        // Language
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        $data['language_id'] = $this->config->get('config_language_id');
        
        // Setting
        $setting = array();
        
        // General
        $setting['name']            = ''; // Name of module
        $setting['status']          = false; // status of module
        $setting['title']           = ''; // Heading Title of module
        
        // Tabs
        $setting['tabs']            = array(); // product tabs
        
        // Image
        $setting['product_image_width']     = 200;
        $setting['product_image_height']    = 200;
        $setting['product_image_srcset']    = array('lg' => null, 'md' => null, 'sm' => null, 'xs' => null);
        $setting['product_image_position']  = 'top';
        $setting['tab_icon_width']          = 30;
        $setting['tab_icon_height']         = 30;
        $setting['tab_icon_position']       = 'left';
        $setting['banner_status']           = false;
        $setting['banner_width']            = 200;
        $setting['banner_height']           = 200;
        $setting['banner_image']            = array();
        $setting['banner_svg']              = array();
        $setting['banner_link_code']        = '';
        
        // product
        $setting['product_grid_cart']             = 1;
        $setting['product_grid_compare']          = 1;
        $setting['product_grid_wishlist']         = 1;
        $setting['product_grid_countdown']        = -1;
        $setting['product_grid_sold']             = -1;
        $setting['product_grid_additional_image'] = -1;
        $setting['product_grid_quick_view']       = 1;
        $setting['product_grid_label']            = 1;
        $setting['product_grid_rating']           = 1;
        $setting['product_grid_manufacturer']     = 0;
        $setting['product_grid_description']      = 0;
        $setting['product_grid_tax']              = 0;
        $setting['product_limit']                 = 10;
        
        // layout
        $setting['tab_status']      = 1;
        $setting['url_target']      = '_self';
        $setting['column_xs']       = 2;
        $setting['column_sm']       = 2;
        $setting['column_md']       = 4;
        $setting['column_lg']       = 4;
        $setting['column_xl']       = 4;
        
        // Carousel
        $setting['carousel_status']   =   1;
        $setting['carousel_autoplay'] =   0;
        $setting['carousel_pagination'] = 0;
        //$setting['carousel_loop']     =   1;
        $setting['carousel_row']      =   1;
        $setting['carousel_nav_icon_image']   = array();
        $setting['carousel_nav_icon_svg']     = array();
        $setting['carousel_nav_icon_font']    = array();
        $setting['carousel_nav_icon_size']    = null;
        $setting['carousel_nav_icon_width']   = null;
        $setting['carousel_nav_icon_height']  = null;
        
        // Get global name of module
        if($global_setting){
            $setting['name'] = $global_setting['name'];
        }
        
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $setting = array_merge($setting, $this->request->post);
        } else {
            $setting = array_merge($setting, $module_setting); 
        }
        
        $data = array_merge($data, $setting);
        
        // Tabs
        foreach ($setting['tabs'] as $key => $tab) {
            
            // Custom products
            if(isset($tab['custom_product'])){
                $data['tabs'][$key]['custom_product'] = array();
                
                foreach ($tab['custom_product'] as $product_id) {
                    $product_info = $this->model_catalog_product->getProduct($product_id);
                    if($product_info){
                        $data['tabs'][$key]['custom_product'][] = array(
                            'product_id'    => $product_info['product_id'],
                            'name'          => $product_info['name'],
                        );
                    } 
                }
            } else {
                $data['tabs'][$key]['custom_product'] = array();
            }
            
            // Filter category
            if(isset($tab['filter_category'])){
                $data['tabs'][$key]['filter_category'] = array();
                
                foreach ($tab['filter_category'] as $category_id) {
                    $category_info = $this->model_catalog_category->getCategory($category_id);
                    if($category_info){
                        $data['tabs'][$key]['filter_category'][] = array(
                            'category_id'    => $category_info['category_id'],
                            'name'          => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'],
                        );
                    } 
                }
            } else {
                $data['tabs'][$key]['filter_category'] = array();
            }
            
            // Filter manufaturer
            if(isset($tab['filter_manufacturer'])){
                $data['tabs'][$key]['filter_manufacturer'] = array();
                
                foreach ($tab['filter_manufacturer'] as $manufacturer_id) {
                    $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
                    if ($manufacturer_info) {
                            $data['tabs'][$key]['filter_manufacturer'][] = array(
                                    'manufacturer_id' => $manufacturer_info['manufacturer_id'],
                                    'name'        => $manufacturer_info['name']
                            );
                    }
                }
            } else {
                $data['tabs'][$key]['filter_manufacturer'] = array();
            }
            
            // Filter product filter
            if(isset($tab['filter_product_filter'])){
                $data['tabs'][$key]['filter_product_filter'] = array();
                
                foreach ($tab['filter_product_filter'] as $filter_id) {
                    $filter_info = $this->model_catalog_filter->getFilter($filter_id);
                    if ($filter_info) {
                        $data['tabs'][$key]['filter_product_filter'][] = array(
                            'filter_id' => $filter_info['filter_id'],
                            'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
                        );
                    }
                }
            } else {
                $data['tabs'][$key]['filter_product_filter'] = array();
            }
            
            // Icon thumb Image
            $data['tabs'][$key]['thumb_icon_image'] = array();
            foreach ($setting['tabs'][$key]['icon_image'] as $language_id => $image) {
                if($image){
                    $data['tabs'][$key]['thumb_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
                } else {
                    $data['tabs'][$key]['thumb_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
            // Icon thumb svg
            $data['tabs'][$key]['thumb_icon_svg'] = array();
            foreach ($setting['tabs'][$key]['icon_svg'] as $language_id => $image_svg) {
                if($image_svg){
                    $data['tabs'][$key]['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
                } else {
                    $data['tabs'][$key]['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        // Banner Image thumb
        $data['thumb_banner_image'] = array();
        foreach ($setting['banner_image'] as $language_id => $banner_image) {
            if($banner_image){
                $data['thumb_banner_image'][$language_id] = $this->model_tool_image->resize($banner_image, 100, 100);
            } else {
                $data['thumb_banner_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }
        // image svg
        $data['thumb_banner_svg'] = array();
        foreach ($setting['banner_svg'] as $language_id => $banner_svg) {
            if($banner_svg){
                $data['thumb_banner_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $banner_svg;
            } else {
                $data['thumb_banner_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
            }
        }

        if(!empty($setting['banner_link_code'])){
            $data['banner_link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $setting['banner_link_code']);
        } else {
            $data['banner_link_info'] =  '';
        }
        
        
        // Tabs
        $data['list_product_source'] = array(
            array('id' => 'featured', 'name' => $this->language->get('text_featured')),
            array('id' => 'product', 'name' => $this->language->get('text_custom_product')),
            array('id' => 'related', 'name' => $this->language->get('text_related_product')),
            array('id' => 'latest', 'name' => $this->language->get('text_latest_product')),
            array('id' => 'special', 'name' => $this->language->get('text_special_product')),
            array('id' => 'most_viewed', 'name' => $this->language->get('text_most_viewed_product')),
            array('id' => 'recent_viewed', 'name' => $this->language->get('text_recent_viewed_product')),
            array('id' => 'best_seller', 'name' => $this->language->get('text_best_seller_product')),
            array('id' => 'random', 'name' => $this->language->get('text_random_product')),
            array('id' => 'filter', 'name' => $this->language->get('text_filter_product')),
        );
        
        $data['sort_by'] = array(
            'name' => $this->language->get('text_name'),
            'model' => $this->language->get('text_model'),
            'price' => $this->language->get('text_price'),
            'quantity' => $this->language->get('text_quantity'),
            'rating' => $this->language->get('text_rating'),
            'viewed' => $this->language->get('text_viewed'),
            'order_quantity' => $this->language->get('text_order_quantity'),
            'sort_order' => $this->language->get('text_sort_order'),
            'date_added' => $this->language->get('text_date_added'),
            'random' => $this->language->get('text_random'),
        );
        
        // Icon position
        $data['list_icon_position'] = array(
            array('id' => 'top', 'name' => $this->language->get('text_top')),
            array('id' => 'left', 'name' => $this->language->get('text_left')),
            array('id' => 'right', 'name' => $this->language->get('text_right')),
        );
        
        // Porduct image position
        $data['list_product_image_position'] = array(
            array('id' => 'top', 'name' => $this->language->get('text_top')),
            array('id' => 'left', 'name' => $this->language->get('text_left')),
        );
        
        // Image
        $data['placeholder'] = $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        // carousel nav icon image
        $data['thumb_carousel_nav_icon_image'] = array();
        
        foreach ($data['carousel_nav_icon_image'] as $language_id => $image) {
            if($image){
                $data['thumb_carousel_nav_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
            } else {
                $data['thumb_carousel_nav_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }
        // carousel nav icon svg
        $data['thumb_carousel_nav_icon_svg'] = array();
        foreach ($data['carousel_nav_icon_svg'] as $language_id => $image_svg) {
            if($image_svg){
                $data['thumb_carousel_nav_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
            } else {
                $data['thumb_carousel_nav_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
            }
        }
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $data['header'] = $this->load->controller('extension/maza/common/header/main');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
        $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left/module', 'mz_product_listing');
        
        $this->response->setOutput($this->load->view('extension/module/mz_product_listing', $data));
    }
    
    protected function validateForm(): bool {
        if (!$this->user->hasPermission('modify', 'extension/module/mz_product_listing')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
            
        // Module name
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_module_name');
        }
            
        // Tabs
        if(isset($this->request->post['tabs'])){
            $tabs = array_values($this->request->post['tabs']);
        } else {
            $tabs = array();
        }
        
        if(empty($tabs)){
            $this->error['tab'] = $this->language->get('error_tab');
        } else {
            foreach ($tabs as $key => $tab) {
                // Tab name
                foreach ($tab['name'] as $language_id => $value) {
                    if ((utf8_strlen($value) < 2) || (utf8_strlen($value) > 64)) {
                        $this->error['tabs'][$key]['name'][$language_id] = $this->language->get('error_tab_name');
                    }
                }

                if($tab['source'] == 'filter'){
                    // Sub Category depth must be positive
                    if ($tab['filter_sub_category_depth'] && $tab['filter_sub_category_depth'] < 0) {
                        $this->error['tabs'][$key]['filter_sub_category_depth'] = $this->language->get('error_limit');
                    }

                    // maximum price must be positive
                    // if ($tab['filter_max_price'] < 0) {
                    //     $this->error['tabs'][$key]['filter_max_price'] = $this->language->get('error_price');
                    // }

                    // // minimum price must be positive
                    // if ($tab['filter_min_price'] < 0) {
                    //     $this->error['tabs'][$key]['filter_min_price'] = $this->language->get('error_price');
                    // }
                    
                    // // maximum quantity must be positive
                    // if ($tab['filter_max_quantity'] < 0) {
                    //     $this->error['tabs'][$key]['filter_max_quantity'] = $this->language->get('error_quantity');
                    // }

                    // // minimum quantity must be positive
                    // if ($tab['filter_min_quantity'] < 0) {
                    //     $this->error['tabs'][$key]['filter_min_quantity'] = $this->language->get('error_quantity');
                    // }

                    // // maximum rating must be positive
                    // if ($tab['filter_max_rating'] < 0) {
                    //     $this->error['tabs'][$key]['filter_max_rating'] = $this->language->get('error_rating');
                    // }

                    // // minimum price must be positive
                    // if ($tab['filter_min_rating'] < 0) {
                    //     $this->error['tabs'][$key]['filter_min_rating'] = $this->language->get('error_rating');
                    // }
                }
            }
        }
                
        if ($this->request->post['product_image_width'] <= 0) {
            $this->error['product_image_width'] = $this->language->get('error_width');
        }

        if ($this->request->post['product_image_height'] <= 0) {
            $this->error['product_image_height'] = $this->language->get('error_height');
        }
                
        if ($this->request->post['banner_status'] && $this->request->post['banner_width'] <= 0) {
            $this->error['banner_width'] = $this->language->get('error_width');
        }

        if ($this->request->post['banner_status'] && $this->request->post['banner_height'] <= 0) {
            $this->error['banner_height'] = $this->language->get('error_height');
        }
                
        if ($this->request->post['product_limit'] <= 0) {
            $this->error['product_limit'] = $this->language->get('error_product_limit');
        }
                
        if(!isset($this->error['warning']) && $this->error){
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }
    
    public function delete(): void {
        $this->load->language('extension/module/mz_product_listing');
        $this->load->model('extension/maza/opencart');
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
        
        if(isset($this->request->get['module_id']) && $this->validateDelete()){
            $this->model_extension_maza_opencart->deleteModule($this->request->get['module_id']);
            
            $this->session->data['success'] = $this->language->get('text_success');
            
            $this->response->redirect($this->url->link('extension/module/mz_product_listing', 'user_token=' . $this->session->data['user_token'] . $url, true));
        } else {
            $this->response->redirect($this->url->link('extension/module/mz_product_listing', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true));
        }
    }
    
    protected function validateDelete(): bool {
        if (!$this->user->hasPermission('modify', 'extension/module/mz_product_listing')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
    
    /**
     * Export setting
     */
    public function export(): void {
        $this->load->model('extension/maza/module');
        $this->load->language('extension/module/mz_product_listing');
        
        $module_setting = $this->model_extension_maza_module->getSetting($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'));
        
        if($module_setting){
            header('Content-Type: application/json; charset=utf-8');
            header('Content-disposition: attachment; filename="module.mz_product_listing.' . $this->mz_skin_config->get('skin_code') . '(' . $module_setting['name'] . ').json"');
            
            echo json_encode(['type' => 'module', 'code' => 'mz_product_listing', 'setting' => $module_setting]);
        } else {
            $this->session->data['warning'] = $this->language->get('error_module_empty');
            
            $url = '';
        
            if(isset($this->request->get['mz_theme_code'])){
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
            }

            if(isset($this->request->get['mz_skin_id'])){
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
            }
            
            $this->response->redirect($this->url->link('extension/module/mz_product_listing', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true)); 
        }
    }
    
    /**
     * Import setting
     */
    public function import(): void {
        $this->load->language('extension/module/mz_product_listing');
        
        if(isset($this->request->get['module_id'])){
            $module_id = $this->request->get['module_id'];
        } else {
            $module_id = 0;
        }
        
        $warning = '';

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/module/mz_product_listing')) {
            $warning = $this->language->get('error_permission');
        } else {
            if (isset($this->request->files['file']['name'])) {
                if (substr($this->request->files['file']['name'], -4) != 'json') {
                    $warning = $this->language->get('error_filetype');
                }

                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $warning = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            } else {
                $warning = $this->language->get('error_upload');
            }
        }

        if (!$warning) {
            $file = $this->request->files['file']['tmp_name'];

            if (is_file($file)) {
                $data = json_decode(file_get_contents($file), true);
                
                if($data && $data['type'] == 'module' && $data['code'] == 'mz_product_listing'){
                    $this->load->model('extension/maza/module');
                    
                    if (!$module_id) {
                        $module_id = $this->model_extension_maza_module->addModule('mz_product_listing', $this->mz_skin_config->get('skin_id'), $data['setting']);
                    } else {
                        $this->model_extension_maza_module->editModule($module_id, $this->mz_skin_config->get('skin_id'), $data['setting']);
                    }
                    
                    $this->session->data['success'] = $this->language->get('text_success_import');
                } else {
                    $warning = $this->language->get('error_import_file');
                }
            } else {
                $warning = $this->language->get('error_file');
            }
        }
        
        if($warning){
            $this->session->data['warning'] = $warning;
        }
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }

        if($module_id){
            $this->response->redirect($this->url->link('extension/module/mz_product_listing', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true)); 
        } else {
            $this->response->redirect($this->url->link('extension/module/mz_product_listing', 'user_token=' . $this->session->data['user_token'] . $url, true)); 
        }
    }
}
