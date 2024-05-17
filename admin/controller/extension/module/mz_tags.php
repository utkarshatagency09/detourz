<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionModuleMzTags extends Controller {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/module/mz_tags');

		        $this->document->setTitle($this->language->get('heading_title'));
                
                $this->load->model('extension/maza/module');
                $this->load->model('catalog/category');
                $this->load->model('catalog/manufacturer');
                $this->load->model('extension/maza/blog/category');
                $this->load->model('extension/maza/blog/author');
                $this->load->model('catalog/filter');
                $this->load->model('tool/image');
                $this->load->model('extension/maza/asset');
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
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
                    array('name' => $this->language->get('tab_product'), 'id' => 'tab-mz-product', 'href' => false),
                    array('name' => $this->language->get('tab_blog'), 'id' => 'tab-mz-blog', 'href' => false),
                );
                $header_data['menu_active'] = 'tab-mz-general';
                
                // Header Buttons
                $header_data['buttons'][] = array( // Button save
                    'id' => 'button-save',
                    'name' => false,
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'class' => 'btn-primary',
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-mz-tags',
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
                        'href' => $this->url->link('extension/module/mz_tags/export', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'] . $url, true),
                        'target' => '_self',
                        'form_target_id' => false,
                    );
                    $header_data['buttons'][] = array( // Button delete
                        'id' => 'button-delete',
                        'name' => false,
                        'tooltip' => $this->language->get('button_delete'),
                        'icon' => 'fa-trash',
                        'class' => 'btn-danger',
                        'href' => $this->url->link('extension/module/mz_tags/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true),
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
                    'href' => 'https://docs.pocotheme.com/#module-maza-tags',
                    'target' => '_blank'
                );
                
                // Form submit id
                $header_data['form_target_id'] = 'form-mz-tags';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Submit form and save module in case of no error
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()){
                    
                    if (!isset($this->request->get['module_id'])) {
                            $module_id = $this->model_extension_maza_module->addModule('mz_tags', $this->mz_skin_config->get('skin_id'), $this->request->post);
                    } else {
                            $this->model_extension_maza_module->editModule($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'), $this->request->post);
                    }
                    
                    $this->session->data['success'] = $this->language->get('text_success');
                    
                    // Add module id in url and redirect to it after newly added module
                    if(isset($module_id)){
                       $this->response->redirect($this->url->link('extension/module/mz_tags', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true)); 
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
			$data['action'] = $this->url->link('extension/module/mz_tags', 'user_token=' . $this->session->data['user_token'] . $url, true);
                        $data['import'] = $this->url->link('extension/module/mz_tags/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/module/mz_tags', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
                        $data['import'] = $this->url->link('extension/module/mz_tags/import', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'] . $url, true);
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
                $setting['name']                =   ''; // Name of module
                $setting['status']              =   false; // status of module
                $setting['title']               =   array(); // Heading Title of module
                $setting['tags_source']         =   'product';
                $setting['tag_color']               =   'secondary';
                
                // Product
                $setting['product_tags_type']           =   'featured';
                $setting['product_featured_tags']       =   array();
                $setting['product_filter_auto_filter']  =   0;
                $setting['product_filter_category']     =   array();
                $setting['product_filter_sub_category'] =   1;
                $setting['product_filter_manufacturer'] =   array();
                $setting['product_filter_filter']       =   array();
                $setting['product_filter_limit']        =   10;
                
                // Blog
                $setting['blog_tags_type']           =   'featured';
                $setting['blog_featured_tags']       =   array();
                $setting['blog_filter_auto_filter']  =   0;
                $setting['blog_filter_category']     =   array();
                $setting['blog_filter_sub_category'] =   1;
                $setting['blog_filter_author'] =   array();
                $setting['blog_filter_filter']       =   array();
                $setting['blog_filter_limit']        =   10;
                
                
                
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
                
                // Data
                $data['colors'] = array();
                foreach($this->model_extension_maza_asset->getColorTypes() as $color){
                    $data['colors'][] = array('code' => $color, 'text' => ucfirst($color));
                }
                
                // tags source
                 $data['list_tags_source'] = array(
                    array('id' => 'product', 'name' => $this->language->get('text_product')),
                    array('id' => 'blog', 'name' => $this->language->get('text_blog')),
                );
                 
                // tags type
                $data['list_tags_type'] = array(
                    array('id' => 'featured', 'name' => $this->language->get('text_featured')),
                    array('id' => 'most_viewed', 'name' => $this->language->get('text_most_viewed')),
                    array('id' => 'most_used', 'name' => $this->language->get('text_most_used')),
                );
                
                // Filter product categories
                $data['product_categories'] = array();

		foreach ($setting['product_filter_category'] as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}
                
                // Filter product manufacturer
                $data['product_manufacturers'] = array();
                        
                foreach ($setting['product_filter_manufacturer'] as $manufacturer_id) {
                    $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);

                    if ($manufacturer_info) {
                            $data['product_manufacturers'][] = array(
                                    'manufacturer_id' => $manufacturer_info['manufacturer_id'],
                                    'name'        => $manufacturer_info['name']
                            );
                    }
                }
                
                
                // Filter product Filter
                $data['product_filters'] = array();

		foreach ($setting['product_filter_filter'] as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['product_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}
                
                // Filter blog categories
                $data['blog_categories'] = array();

		foreach ($setting['blog_filter_category'] as $category_id) {
			$category_info = $this->model_extension_maza_blog_category->getCategory($category_id);

			if ($category_info) {
				$data['blog_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}
                
                // Filter blog author
                $data['blog_authors'] = array();
                        
                foreach ($setting['blog_filter_author'] as $author_id) {
                    $author_info = $this->model_extension_maza_blog_author->getAuthor($author_id);

                    if ($author_info) {
                            $data['blog_authors'][] = array(
                                    'author_id' => $author_info['author_id'],
                                    'name'        => $author_info['name']
                            );
                    }
                }
                
                
                // Filter blog Filter
                $data['blog_filters'] = array();

		foreach ($setting['blog_filter_filter'] as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['blog_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}
                
                $data['user_token'] = $this->session->data['user_token'];
                
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left/module', 'mz_tags');
                
		$this->response->setOutput($this->load->view('extension/module/mz_tags', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/mz_tags')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
                
                // Module name
                if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_module_name');
		}
                
                if ($this->request->post['product_filter_limit'] <= 0) {
			$this->error['product_filter_limit'] = $this->language->get('error_limit');
		}
                
                if ($this->request->post['blog_filter_limit'] <= 0) {
			$this->error['blog_filter_limit'] = $this->language->get('error_limit');
		}
                
                if(!isset($this->error['warning']) && $this->error){
                        $this->error['warning'] = $this->language->get('error_warning');
                }

		return !$this->error;
	}
        
        public function delete() {
                $this->load->language('extension/module/mz_tags');
                
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
                        
                        $this->response->redirect($this->url->link('extension/module/mz_tags', 'user_token=' . $this->session->data['user_token'] . $url, true));
                } else {
                        $this->response->redirect($this->url->link('extension/module/mz_tags', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true));
                }
                
        }
        
        protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/module/mz_tags')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        /**
         * Export setting
         */
        public function export(){
                $this->load->model('extension/maza/module');
                $this->load->language('extension/module/mz_tags');
                
                $module_setting = $this->model_extension_maza_module->getSetting($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'));
                
                if($module_setting){
                    header('Content-Type: application/json; charset=utf-8');
                    header('Content-disposition: attachment; filename="module.mz_tags.' . $this->mz_skin_config->get('skin_code') . '(' . $module_setting['name'] . ').json"');
                    
                    echo json_encode(['type' => 'module', 'code' => 'mz_tags', 'setting' => $module_setting]);
                } else {
                    $this->session->data['warning'] = $this->language->get('error_module_empty');
                    
                    $url = '';
                
                    if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                    }

                    if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                    }
                    
                    $this->response->redirect($this->url->link('extension/module/mz_tags', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true)); 
                }
        }
        
        /**
         * Import setting
         */
        public function import(){
                $this->load->language('extension/module/mz_tags');
                
                if(isset($this->request->get['module_id'])){
                    $module_id = $this->request->get['module_id'];
                } else {
                    $module_id = 0;
                }
                
                $warning = '';

                // Check user has permission
                if (!$this->user->hasPermission('modify', 'extension/module/mz_tags')) {
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
                                
                                if($data && $data['type'] == 'module' && $data['code'] == 'mz_tags'){
                                    $this->load->model('extension/maza/module');
                                    
                                    if (!$module_id) {
                                            $module_id = $this->model_extension_maza_module->addModule('mz_tags', $this->mz_skin_config->get('skin_id'), $data['setting']);
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
                    $this->response->redirect($this->url->link('extension/module/mz_tags', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true)); 
                } else {
                    $this->response->redirect($this->url->link('extension/module/mz_tags', 'user_token=' . $this->session->data['user_token'] . $url, true)); 
                }
        }
        
        /**
         * Install extension
         */
        public function install(){
            // Event
            $this->load->model('extension/maza/opencart');
            
            $this->addEvents();
        }
        
        /**
         * uninstall extension
         */
        public function uninstall(){
            // Event
            $this->load->model('extension/maza/opencart');
            
            $this->deleteEvents();
        }
        
        /**
         * Add event
         */
        private function addEvents() {
            $this->deleteEvents();
            
            //** Catalog ***********************************
            $this->model_extension_maza_opencart->addEvent('mz_catalog_module_tags_before_product_search_view', 'catalog/view/product/search/before', 'extension/maza/event/extension/module/mz_tags/productSearchView');
            $this->model_extension_maza_opencart->addEvent('mz_catalog_module_tags_before_blog_search_view', 'catalog/view/extension/maza/blog/search/before', 'extension/maza/event/extension/module/mz_tags/blogSearchView');
        }
        
        /**
         * delete event
         */
        private function deleteEvents() {
            
            // ** Catalog ****************************************************************
            $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_module_tags_before_product_search_view');
            $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_module_tags_before_blog_search_view');
        }
}
