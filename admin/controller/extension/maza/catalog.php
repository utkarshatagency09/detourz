<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazacatalog extends Controller {
    private $error = array();
    
    public function index(): void {
        $this->load->language('extension/maza/catalog');

        $this->document->setTitle($this->language->get('heading_title'));
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
        
        // Header
        $header_data = array();
        
        $header_data['menu'] = array(
            array('name' => $this->language->get('tab_general'), 'id' => 'menu-general', 'href' => false),
            array('name' => $this->language->get('tab_product_listing'), 'id' => 'menu-product-listing', 'href' => false),
            array('name' => $this->language->get('tab_product_page'), 'id' => 'menu-product-page', 'href' => false),
            array('name' => $this->language->get('tab_category'), 'id' => 'menu-category', 'href' => false),
            array('name' => $this->language->get('tab_compare'), 'id' => 'menu-compare', 'href' => false),
            array('name' => $this->language->get('tab_account'), 'id' => 'menu-account', 'href' => false),
            array('name' => $this->language->get('tab_checkout'), 'id' => 'menu-checkout', 'href' => false),
        );
        
        $header_data['menu_active'] = 'menu-general';
        $header_data['buttons'][] = array(
            'id' => 'button-import',
            'name' => false,
            'tooltip' => $this->language->get('button_import'),
            'icon' => 'fa-upload',
            'class' => 'btn-info',
            'href' => false,
            'target' => FALSE,
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id' => 'button-export',
            'name' => false,
            'tooltip' => $this->language->get('button_export'),
            'icon' => 'fa-download',
            'class' => 'btn-info',
            'href' => $this->url->link('extension/maza/catalog/export', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => FALSE,
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id' => 'button-save',
            'name' => false,
            'tooltip' => $this->language->get('button_save'),
            'icon' => 'fa-save',
            'class' => 'btn-primary',
            'href' => FALSE,
            'target' => FALSE,
            'form_target_id' => 'form-catalog',
        );
        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#page-catalog',
            'target' => '_blank'
        );
        $header_data['form_target_id'] = 'form-catalog';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
        
        // Submit form
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()){
            $this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'catalog', $this->request->post);
            
            $data['success'] = $this->language->get('text_success');
        }
        
        if(isset($this->error['warning'])){
            $data['warning'] = $this->error['warning'];
        } elseif (isset($this->session->data['warning'])) {
            $data['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        
        $data['import'] = $this->url->link('extension/maza/catalog/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['action'] = $this->url->link('extension/maza/catalog', 'user_token=' . $this->session->data['user_token'] . $url, true);
        
        // Setting
        $setting = array();

        // General
        $setting['catalog_image_scale']                 = 'c';
        $setting['catalog_image_quality']               = 100;
        $setting['catalog_sold_order_statuses']         = array(2,3,5,1,15);
        $setting['catalog_special_countdown_status']    = 1;
        $setting['catalog_special_countdown_showtime']  = 0;
        $setting['catalog_special_sold_status']         = 1;
        $setting['catalog_special_sold_showtime']       = 0;
        $setting['catalog_special_sold_unit']           = 'perc';
        
        // Product listing
        $setting['catalog_grid_countdown_status']   = 1;
        $setting['catalog_grid_sold_status']        = 1;
        $setting['catalog_grid_cart_status']        = 1;
        $setting['catalog_grid_compare_status']     = 1;
        $setting['catalog_grid_wishlist_status']    = 1;
        $setting['catalog_grid_quick_view_status']  = 1;
        $setting['catalog_grid_rating_status']      = 1;
        $setting['catalog_grid_manufacturer_status'] = 0;
        $setting['catalog_grid_tax_status']         = 0;
        $setting['catalog_grid_description_status'] = 0;
        
        $setting['catalog_grid_image_lazy_loading']       =   1;
        $setting['catalog_grid_additional_image']         =   1;
        $setting['catalog_grid_additional_image_limit']   =   3;
        $setting['catalog_grid_image_transform_effect']   =   null;
        $setting['catalog_grid_image_width']              =   $this->config->get('theme_default_image_product_width');
        $setting['catalog_grid_image_height']             =   $this->config->get('theme_default_image_product_height');
        $setting['catalog_grid_image_srcset']             =   array('lg' => null, 'md' => null, 'sm' => null, 'xs' => null);
        
        
        // product page
        $setting['catalog_thumb_image_width']           = $this->config->get('theme_default_image_thumb_width');
        $setting['catalog_thumb_image_height']          = $this->config->get('theme_default_image_thumb_height');
        $setting['catalog_popup_image_width']           = $this->config->get('theme_default_image_popup_width');
        $setting['catalog_popup_image_height']          = $this->config->get('theme_default_image_popup_height');
        $setting['catalog_thumb_image_srcset']          = array('lg' => null, 'md' => null, 'sm' => null, 'xs' => null);
        $setting['catalog_additional_image_slides']     = 4;
        $setting['catalog_product_brand_image_width']   = 80;
        $setting['catalog_product_brand_image_height']  = 80;
        $setting['catalog_related_image_width']         = $this->config->get('theme_default_image_related_width');
        $setting['catalog_related_image_height']        = $this->config->get('theme_default_image_related_height');
        $setting['catalog_related_image_srcset']        = array('lg' => null, 'md' => null, 'sm' => null, 'xs' => null);
        $setting['catalog_option_image_width']          =   50;
        $setting['catalog_option_image_height']         =   50;
        $setting['catalog_option_update_price']         =   1;
        
        // Category
        $setting['catalog_sub_category_product']        =   1;
        $setting['catalog_refine_search_image_width']   =   80;
        $setting['catalog_refine_search_image_height']  =   80;
        $setting['catalog_category_image_width']        =   80;
        $setting['catalog_category_image_height']       =   80;

        // Compare
        $setting['catalog_compare_model_status']        =   1;
        $setting['catalog_compare_stock_status']        =   1;
        $setting['catalog_compare_weight_status']       =   1;
        $setting['catalog_compare_dimension_status']    =   1;
        $setting['catalog_compare_attribute_status']    =   1;

        // Account
        $setting['catalog_account_download_status']     =   1;
        $setting['catalog_account_recurring_status']    =   1;
        $setting['catalog_account_reward_status']       =   1;
        $setting['catalog_account_return_status']       =   1;
        $setting['catalog_account_affiliate_status']    =   1;

        // Checkout
        $setting['catalog_checkout_status']             = 1;
        
        // Default language value
        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $setting = array_merge($setting, $this->request->post);
        } else {
            $setting = array_merge($setting, $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'catalog')); 
        }
        
        $data = array_merge($data, $setting);
        
        $data['list_image_scale'] = array(
            array('code' => '', 'text' => $this->language->get('text_contain')),
            array('code' => 'c', 'text' => $this->language->get('text_cover')),
            array('code' => 'w', 'text' => $this->language->get('text_fit_width')),
            array('code' => 'h', 'text' => $this->language->get('text_fit_height')),
        );

        // Special deal sold unit list
        $data['special_sold_units'] = array(
            array('code' => 'perc', 'text' => $this->language->get('text_percentage')),
            array('code' => 'qty', 'text' => $this->language->get('text_quantity'))
        );
        
        // Order status
        $this->load->model('localisation/order_status');
        
        $data['order_statuses'] = array();
        
        $results = $this->model_localisation_order_status->getOrderStatuses();
        foreach ($results as $result) {
            $data['order_statuses'][] = array(
                'order_status_id' => $result['order_status_id'],
                'name'            => $result['name'] . (($result['order_status_id'] == $this->config->get('config_order_status_id')) ? $this->language->get('text_default') : null),
            );
        }
        
        $data['header'] = $this->load->controller('extension/maza/common/header/main');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
        $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
        
        $this->response->setOutput($this->load->view('extension/maza/catalog', $data));
    }
        
    protected function validate(): bool {
        if (!$this->user->hasPermission('modify', 'extension/maza/catalog')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
        
        
    /**
     * Export setting
     */
    public function export(): void {
        $this->load->model('extension/maza/skin');
        $this->load->language('extension/maza/catalog');
        
        $setting = $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'catalog');
        
        if($setting){
            header('Content-Type: application/json; charset=utf-8');
            header('Content-disposition: attachment; filename="maza.setting.catalog.' . $this->mz_skin_config->get('skin_code') . '.json"');
            
            echo json_encode(['type' => 'maza', 'code' => 'catalog', 'setting' => $setting]);
        } else {
            $this->session->data['warning'] = $this->language->get('error_no_setting');
            
            $url = '';
        
            if(isset($this->request->get['mz_theme_code'])){
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
            }

            if(isset($this->request->get['mz_skin_id'])){
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
            }
            
            $this->response->redirect($this->url->link('extension/maza/catalog', 'user_token=' . $this->session->data['user_token'] . $url, true)); 
        }
    }
    
    /**
     * Import setting
     */
    public function import(): void {
        $this->load->language('extension/maza/catalog');
        
        $warning = '';

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/maza/catalog')) {
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
                
                if($data && $data['type'] == 'maza' && $data['code'] == 'catalog'){
                    $this->load->model('extension/maza/skin');
                    
                    $this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'catalog', $data['setting']);
                    
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

        $this->response->redirect($this->url->link('extension/maza/catalog', 'user_token=' . $this->session->data['user_token'] . $url, true)); 
    }
}
