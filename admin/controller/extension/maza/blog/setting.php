<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaBlogSetting extends Controller {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/maza/blog/setting');
                
                $this->load->model('extension/maza/asset');

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
                // $header_data['menu'] = array();
                // if ($this->user->hasPermission('access', 'extension/maza/blog')) $header_data['menu'][] = array('name' => $this->language->get('tab_dashboard'), 'id' => 'tab-mz-dashboard', 'href' => $this->url->link('extension/maza/blog', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/category')) $header_data['menu'][] = array('name' => $this->language->get('tab_category'), 'id' => 'tab-mz-category', 'href' => $this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/article')) $header_data['menu'][] = array('name' => $this->language->get('tab_article'), 'id' => 'tab-mz-article', 'href' => $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/author')) $header_data['menu'][] = array('name' => $this->language->get('tab_author'), 'id' => 'tab-mz-author', 'href' => $this->url->link('extension/maza/blog/author', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/comment')) $header_data['menu'][] = array('name' => $this->language->get('tab_comment'), 'id' => 'tab-mz-comment', 'href' => $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // if ($this->user->hasPermission('access', 'extension/maza/blog/report')) $header_data['menu'][] = array('name' => $this->language->get('tab_report'), 'id' => 'tab-mz-report', 'href' => $this->url->link('extension/maza/blog/report', 'user_token=' . $this->session->data['user_token'] . $url, true));
                // $header_data['menu'][] = array('name' => $this->language->get('tab_setting'), 'id' => 'tab-mz-setting', 'href' => false);
                
                
                // $header_data['menu_active'] = 'tab-mz-setting';
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
                    'href' => $this->url->link('extension/maza/blog/setting/export', 'user_token=' . $this->session->data['user_token'] . $url, true),
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
                    'form_target_id' => 'form-mz-setting',
                );
                $header_data['form_target_id'] = 'form-mz-setting';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Submit form
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()){
                    $this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'blog', $this->request->post);
                    // clear asset files for new settings
                    $this->mz_document->clear();
                    
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
                
                foreach($this->error as $key => $val){
                    $data['err_' . $key] = $val;
                }
                
                $data['import'] = $this->url->link('extension/maza/blog/setting/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
                $data['action'] = $this->url->link('extension/maza/blog/setting', 'user_token=' . $this->session->data['user_token'] . $url, true);
                
                // Setting
                $setting = array();
                
                // Home page
                // $setting['blog_home_meta_title']        = '';
                // $setting['blog_home_meta_description']  = '';
                // $setting['blog_home_meta_keyword']      = '';
                
                // Article listing
                $setting['blog_article_grid_limit']              = 15;
                $setting['blog_article_grid_image_width']        = 200;
                $setting['blog_article_grid_image_height']       = 200;
                $setting['blog_article_grid_image_srcset']       = array('lg' => null, 'md' => null, 'sm' => null, 'xs' => null);
                $setting['blog_article_grid_image_lazy_loading'] = 0;
                $setting['blog_article_grid_additional_image']   = 1;
                $setting['blog_article_grid_description_limit']  = 100;
                
                
                // Category page
                $setting['blog_category_image_width']         =   80;
                $setting['blog_category_image_height']        =   80;
                $setting['blog_category_refine_image_width']  =   '80';
                $setting['blog_category_refine_image_height'] =   '80';
                
                
                // Article page
                $setting['blog_article_image_width']          =   228;
                $setting['blog_article_image_height']         =   228;
                $setting['blog_article_image_srcset']         =   array('lg' => null, 'md' => null, 'sm' => null, 'xs' => null);
                $setting['blog_article_author_thumb_width']   =   80;
                $setting['blog_article_author_thumb_height']  =   80;
                
                // Author page
                $setting['blog_author_image_width']         =   80;
                $setting['blog_author_image_height']        =   80;
                  
                // Comment  
                $setting['blog_comment_status']               =   1;
                $setting['blog_allow_guest_comment']          =   1;
                $setting['blog_comment_require_approval']     =   -1;
                $setting['blog_comment_form_captcha']         =   0;
                
                
                
                $this->load->model('localisation/language');
                $data['languages'] = $this->model_localisation_language->getLanguages();
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } else {
                    $setting = array_merge($setting, $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'blog')); 
                }
                
                
                // Data
                $data = array_merge($data, $setting);
                
                $data['user_token'] = $this->session->data['user_token'];
                
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		        $this->response->setOutput($this->load->view('extension/maza/blog/setting', $data));
        }
        
        protected function validateForm() {
                if (!$this->user->hasPermission('modify', 'extension/maza/blog/setting')) {
                    $this->error['warning'] = $this->language->get('error_permission');
                }
                
                // Home
                // if(empty($this->request->post['blog_home_meta_title'])){
                //     $this->error['home_meta_title'] = $this->language->get('error_meta_title');
                // }
                
                // Article listing
                if($this->request->post['blog_article_grid_limit'] <= 0){
                    $this->error['article_grid_limit'] = $this->language->get('error_positive_number');
                }
                
                if($this->request->post['blog_article_grid_description_limit'] <= 0){
                    $this->error['article_grid_description_limit'] = $this->language->get('error_positive_number');
                }
                
                if($this->request->post['blog_article_grid_image_width'] <= 0 || $this->request->post['blog_article_grid_image_height'] <= 0){
                    $this->error['article_grid_image_size'] = $this->language->get('error_positive_number');
                }
                
                // Category page
                if($this->request->post['blog_category_image_width'] <= 0 || $this->request->post['blog_category_image_height'] <= 0){
                    $this->error['category_image_size'] = $this->language->get('error_positive_number');
                }
                
                if($this->request->post['blog_category_refine_image_width'] <= 0 || $this->request->post['blog_category_refine_image_height'] <= 0){
                    $this->error['category_refine_image_size'] = $this->language->get('error_positive_number');
                }
                
                // Author page
                if($this->request->post['blog_author_image_width'] <= 0 || $this->request->post['blog_author_image_height'] <= 0){
                    $this->error['author_image_size'] = $this->language->get('error_positive_number');
                }
                
                // Article page
                if($this->request->post['blog_article_image_width'] <= 0 || $this->request->post['blog_article_image_height'] <= 0){
                    $this->error['article_image_size'] = $this->language->get('error_positive_number');
                }
                
                if($this->request->post['blog_article_author_thumb_width'] <= 0 || $this->request->post['blog_article_author_thumb_height'] <= 0){
                    $this->error['article_author_thumb_size'] = $this->language->get('error_positive_number');
                }
                
                if(!isset($this->error['warning']) && $this->error){
                    $this->error['warning'] = $this->language->get('error_warning');
                }

		        return !$this->error;
	    }
        
        
        /**
         * Export setting
         */
        public function export(){
                $this->load->model('extension/maza/skin');
                $this->load->language('extension/maza/blog/setting');
                
                $setting = $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'blog');
                
                if($setting){
                    header('Content-Type: application/json; charset=utf-8');
                    header('Content-disposition: attachment; filename="maza.setting.blog.' . $this->mz_skin_config->get('skin_code') . '.json"');
                    
                    echo json_encode(['type' => 'maza', 'code' => 'blog', 'setting' => $setting]);
                } else {
                    $this->session->data['warning'] = $this->language->get('error_no_setting');
                    
                    $url = '';
                
                    if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                    }

                    if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                    }
                    
                    $this->response->redirect($this->url->link('extension/maza/blog/setting', 'user_token=' . $this->session->data['user_token'] . $url, true)); 
                }
        }
        
        /**
         * Import setting
         */
        public function import(){
                $this->load->language('extension/maza/blog/setting');
                
                $warning = '';

                // Check user has permission
                if (!$this->user->hasPermission('modify', 'extension/maza/blog/setting')) {
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
                                
                                if($data && $data['type'] == 'maza' && $data['code'] == 'blog'){
                                    $this->load->model('extension/maza/skin');
                                    
                                    $this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'blog', $data['setting']);
                                    
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

                $this->response->redirect($this->url->link('extension/maza/blog/setting', 'user_token=' . $this->session->data['user_token'] . $url, true)); 
        }
}
