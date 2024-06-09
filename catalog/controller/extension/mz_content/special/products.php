<?php
class ControllerExtensionMzContentSpecialProducts extends maza\layout\Content {
        public function index($setting) {
                $data = array();
                $data['mz_suffix']                = $setting['mz_suffix'];
                
                $data['list_grid']                = $setting['content_list_grid'];
                $data['view_id']                  = 'special_' . $this->config->get('mz_layout_id');
                
                $data['product_column_xs']        = $setting['content_column_xs'];
                $data['product_column_sm']        = $setting['content_column_sm'];
                $data['product_column_md']        = $setting['content_column_md'];
                $data['product_column_lg']        = $setting['content_column_lg'];
                $data['product_column_xl']        = $setting['content_column_xl'];
                      
                $data['cart_status']              = $this->mz_skin_config->get('catalog_grid_cart_status');
                $data['compare_status']           = $this->mz_skin_config->get('catalog_grid_compare_status');
                $data['wishlist_status']          = $this->mz_skin_config->get('catalog_grid_wishlist_status');
                $data['quick_view_status']        = $this->mz_skin_config->get('catalog_grid_quick_view_status');
                $data['rating_status']            = $this->mz_skin_config->get('catalog_grid_rating_status');
                $data['manufacturer_status']      = $this->mz_skin_config->get('catalog_grid_manufacturer_status');
                $data['tax_status']               = $this->mz_skin_config->get('catalog_grid_tax_status');
                $data['description_status']       = $this->mz_skin_config->get('catalog_grid_description_status');
                $data['special_countdown_status'] = $this->mz_skin_config->get('catalog_grid_countdown_status');
                $data['special_sold_status']      = $this->mz_skin_config->get('catalog_grid_sold_status');
                
                $data['lazy_loading']             = $this->mz_skin_config->get('catalog_grid_image_lazy_loading');
                $data['transparent']              = $this->model_extension_maza_image->transparent($this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
                
                $data['srcset_sizes']             = $this->model_extension_maza_image->getSrcSetSize($this->mz_skin_config->get('catalog_grid_image_srcset'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'));
                
                return $this->load->view('product/common/products', $data);
        }
}
