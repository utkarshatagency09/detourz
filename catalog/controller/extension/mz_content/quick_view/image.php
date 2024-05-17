<?php
class ControllerExtensionMzContentQuickViewImage extends maza\layout\Content {
        public function index($setting) {
                $data['additional_image_position'] = $setting['content_additional_image_position'];
                $data['wishlist_status']           = $setting['content_wishlist_status'];
                $data['mz_suffix']                 = $setting['mz_suffix'];
                
                $data['srcset_sizes']              = $this->model_extension_maza_image->getSrcSetSize($this->mz_skin_config->get('catalog_thumb_image_srcset'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'));
                $data['additional_srcset_sizes']   = $this->model_extension_maza_image->getSrcSetSize($this->mz_skin_config->get('catalog_additional_image_srcset'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'));
                
                $data['slides_per_view']           = $this->mz_skin_config->get('catalog_additional_image_slides');
                
                return $this->load->view('product/product/image', $data);
        }
}
