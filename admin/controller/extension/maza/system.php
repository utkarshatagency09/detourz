<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaSystem extends Controller {
    private $error = array();
    
    public function index(): void {
        $this->load->language('extension/maza/system');

        $this->document->setTitle($this->language->get('heading_title'));
        
        // Header
        $header_data = array();
        $header_data['theme_select'] = false;
        $header_data['skin_select'] = false;
        $header_data['title'] = $this->language->get('heading_title');
        
        $header_data['menu'] = array(
            array('name' => $this->language->get('tab_setting'), 'id' => 'menu-setting', 'href' => false),
            array('name' => $this->language->get('tab_asset'), 'id' => 'menu-asset', 'href' => false),
            array('name' => $this->language->get('tab_seo'), 'id' => 'menu-seo', 'href' => false),
            array('name' => $this->language->get('tab_service'), 'id' => 'menu-service', 'href' => false),
            array('name' => $this->language->get('tab_cron'), 'id' => 'menu-cron', 'href' => false),
            array('name' => $this->language->get('tab_backup'), 'id' => 'menu-backup', 'href' => false)
        );
        
        $header_data['menu_active'] = 'menu-setting';
        $header_data['buttons'][] = array(
            'id' => 'button-save',
            'name' => null,
            'tooltip' => $this->language->get('button_save'),
            'icon' => 'fa-save',
            'class' => 'btn-primary',
            'href' => FALSE,
            'target' => FALSE,
            'form_target_id' => 'form-system',
        );
        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#page-system',
            'target' => '_blank'
        );
        $header_data['form_target_id'] = 'form-system';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
        
        $this->load->model('setting/setting');
        
        // Submit form
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()){
            $this->model_setting_setting->editSetting('maza', $this->request->post);
            
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
        
        
        $url = '';
        
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
        
        $data['action'] = $this->url->link('extension/maza/system', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['export'] = $this->url->link('extension/maza/system/export', 'user_token=' . $this->session->data['user_token'] . $url, true);
        
        // Setting
        $setting = array();
        $setting['maza_developer_mode'] = 0;
        
        $setting['maza_cache_status']   = 1;
        $setting['maza_cache_partial']  = 1;
        $setting['maza_cache_page']     = 0;

        $setting['maza_notification_status']            = 1;
        $setting['maza_notification_manufacturer']      = 1;
        $setting['maza_notification_sms']               = 0;
        $setting['maza_notification_push']              = 0;
        $setting['maza_notification_push_public_key']   = '';
        $setting['maza_notification_push_private_key']  = '';
        
        $setting['maza_minify_css']     = 1;
        $setting['maza_minify_js']      = 1;
        $setting['maza_minify_html']    = 1;
        
        $setting['maza_combine_css']    = 1;
        $setting['maza_combine_js']     = 1;
        $setting['maza_js_position']    = 'footer'; // Header, footer, default
        $setting['maza_css_autoprefix'] = 1;
        $setting['maza_cdn']            = 1;
        $setting['maza_webp']           = 1;
        
        $setting['maza_schema']         = 1;
        $setting['maza_ogp']            = 1;
        $setting['maza_query_keyword']  = 1;

        $setting['maza_api_google_map_key']     = '';
        $setting['maza_api_exchangerate_key']   = '';

        $setting['maza_gatewayapi_sender'] = 'ExampleSMS';
        $setting['maza_gatewayapi_token']  = '';

        $setting['maza_socialauth_apple_status']    = 0;
        $setting['maza_socialauth_apple_id'] = '';
        $setting['maza_socialauth_apple_team_id']   = '';
        $setting['maza_socialauth_apple_key_id']    = '';
        $setting['maza_socialauth_apple_key_content']= '';
        $setting['maza_socialauth_google_status']   = 0;
        $setting['maza_socialauth_google_id']       = '';
        $setting['maza_socialauth_google_secret']   = '';
        $setting['maza_socialauth_facebook_status'] = 0;
        $setting['maza_socialauth_facebook_id']     = '';
        $setting['maza_socialauth_facebook_secret'] = '';
        $setting['maza_socialauth_instagram_status'] = 0;
        $setting['maza_socialauth_instagram_id']     = '';
        $setting['maza_socialauth_instagram_secret'] = '';
        $setting['maza_socialauth_twitter_status']  = 0;
        $setting['maza_socialauth_twitter_key']     = '';
        $setting['maza_socialauth_twitter_secret']  = '';
        $setting['maza_socialauth_linkedin_status'] = 0;
        $setting['maza_socialauth_linkedin_key']    = '';
        $setting['maza_socialauth_linkedin_secret'] = '';
        $setting['maza_socialauth_paypal_status']   = 0;
        $setting['maza_socialauth_paypal_id']       = '';
        $setting['maza_socialauth_paypal_secret']   = '';
        $setting['maza_socialauth_amazon_status']   = 0;
        $setting['maza_socialauth_amazon_id']       = '';
        $setting['maza_socialauth_amazon_secret']   = '';
        $setting['maza_socialauth_discord_status']  = 0;
        $setting['maza_socialauth_discord_id']      = '';
        $setting['maza_socialauth_discord_secret']  = '';
        
        // Cron
        $setting['maza_cron_status']    = 0;
        
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $setting = array_merge($setting, $this->request->post);
        } else {
            $setting = array_merge($setting, $this->model_setting_setting->getSetting('maza')); 
        }

        if (version_compare(VERSION, '3.0.3.7', '<')) {
            unset($setting['maza_webp']);
        }

        // Create public and private key if not available
        // Do not share private key, otherwise anyone can send notification as a name of your
        if (!$setting['maza_notification_push_public_key'] || !$setting['maza_notification_push_private_key']) {
            $vapid = Minishlink\WebPush\VAPID::createVapidKeys();
            $setting['maza_notification_push_public_key']   = $vapid['publicKey'];
            $setting['maza_notification_push_private_key']  = $vapid['privateKey'];
        }
        
        $data = array_merge($data, $setting);
        
        $data['cron_url'] = $this->config->get('mz_store_url') . 'index.php?route=extension/maza/cron&username=[USERNAME]&password=[PASSWORD]';
        
        $data['sitemap'] = $this->config->get('mz_store_url') . 'index.php?route=extension/maza/sitemap';

        $data['js_positions'] = array(
            array('code' => 'header', 'text' => $this->language->get('text_header')),
            array('code' => 'footer', 'text' => $this->language->get('text_footer')),
            array('code' => 'default', 'text' => $this->language->get('text_default'))
        );
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $data['header'] = $this->load->controller('extension/maza/common/header/main');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
        $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
        
        $this->response->setOutput($this->load->view('extension/maza/system', $data));
    }
        
    public function import(): void {
        $this->load->language('extension/maza/system');

        $json = array();

        if (!$this->user->hasPermission('modify', 'extension/maza/system')) {
            $json['error'] = $this->language->get('error_permission');
                
        } elseif(is_uploaded_file($this->request->files['import']['tmp_name'])) {
            
            $restore_zip = new ZipArchive();
            
            if(substr($this->request->files['import']['name'], -4) == '.zip' && $restore_zip->open($this->request->files['import']['tmp_name'])){
                $restore_dir = DIR_UPLOAD . 'mz.' . $this->session->data['user_token'] . '.restore';
                
                $restore_zip->extractTo($restore_dir);
                
                // Restore custom code
                $file_type = array('css' => '.css', 'js' => '.js', 'header' => '.html', 'footer' => '.html');

                $custom_code_dir = DIR_CATALOG . 'view/javascript/maza/custom_code/';
                
                foreach($file_type as $folder => $extension){
                    foreach(glob($restore_dir . '/custom_code/' . $folder . '/*' . $extension) as $file){
                        is_file($custom_code_dir . $folder . '/' . basename($file)) && unlink($custom_code_dir . $folder . '/' . basename($file));
                        rename($file, $custom_code_dir . $folder . '/' . basename($file));
                    }
                }
                
                
                // Restore database
                $this->load->model('extension/maza/system');
                
                if(is_file($restore_dir . '/backup.sql')){
                    $this->model_extension_maza_system->restore(file_get_contents($restore_dir . '/backup.sql'));
                }
                
                $this->load->model('extension/maza/common');
                
                $this->model_extension_maza_common->clearCache();
                
                $json['success'] = $this->language->get('text_restore_success');
                
            } else {
                $json['error'] = $this->language->get('error_filetype');
            }
        } else {
            $json['error'] = $this->language->get('error_file');
        }
    

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
        
    public function export(): void {
        $this->load->language('extension/maza/system');

        $this->load->model('extension/maza/system');

        // Backup databse
        $backup = array();

        // Theme
        if(!empty($this->request->get['export_theme'])){
            $backup = array_merge($backup, array(
                'mz_skin',
                'mz_skin_setting',
                'mz_theme',
                'mz_theme_setting',
                'mz_header',
                'mz_footer',
                'mz_layout_entry',
                'mz_fonts',
                'mz_asset',
                'mz_page',
                'mz_page_description',
                'mz_page_to_store',
                'mz_content',
                'layout',
                'layout_route',
                'mz_form',
                'mz_form_description',
                'mz_form_field',
                'mz_form_field_description',
                'mz_form_field_customer_group',
                'mz_form_field_value',
                'mz_form_field_value_description',
                'mz_form_record',
                'mz_form_record_value',
                'mz_document',
                'mz_document_description',
                'mz_gallery',
                'mz_product_label',
                'mz_product_label_description',
                'mz_product_label_customer_group',
                'mz_product_label_to_store',
                'mz_product_label_style',
            ));
        }

        // Blog
        if(!empty($this->request->get['export_blog'])){
            $backup = array_merge($backup, array(
                'mz_blog_category',
                'mz_blog_category_description',
                'mz_blog_category_path',
                'mz_blog_category_filter',
                'mz_blog_category_to_layout',
                'mz_blog_category_to_store',
                'mz_blog_author',
                'mz_blog_author_description',
                'mz_blog_author_to_layout',
                'mz_blog_article',
                'mz_blog_article_description',
                'mz_blog_article_filter',
                'mz_blog_article_image',
                'mz_blog_article_related',
                'mz_blog_article_product',
                'mz_blog_article_to_category',
                'mz_blog_article_to_layout',
                'mz_blog_article_to_store',
                'mz_blog_comment',
                'mz_blog_comment_path',
            ));
        }

        // Opencart catalog
        if(!empty($this->request->get['export_catalog'])){
            $backup = array_merge($backup, array(
                'category',
                'category_description',
                'category_path',
                'category_to_layout',
                'category_to_store',
                'manufacturer',
                'manufacturer_to_store',
                'product',
                'product_description',
                'product_discount',
                'product_image',
                'product_option',
                'product_option_value',
                'product_special',
                'product_to_category',
                'product_to_layout',
                'product_to_store',
                'mz_catalog_data',
                'mz_catalog_data_to_store',
                'mz_catalog_data_to_product',
                'mz_catalog_data_to_category',
                'mz_catalog_data_to_manufacturer',
                'mz_catalog_data_to_filter',
                'mz_manufacturer_description',
                'mz_manufacturer_to_layout',
                'mz_product_video',
                'mz_product_video_description',
            ));
        }

        // Filter
        if(!empty($this->request->get['export_filter'])){
            $backup = array_merge($backup, array(
                'mz_filter',
                'mz_filter_description',
                'mz_filter_to_category',
                'mz_filter_value',
                'mz_filter_value_description',
                'mz_filter_value_to_product',
            ));
        }

        // Testimonial
        if(!empty($this->request->get['export_testimonial'])){
            $backup = array_merge($backup, array(
                'mz_testimonial',
                'mz_testimonial_description',
                'mz_testimonial_to_store',
            ));
        }

        // Module
        if(!empty($this->request->get['export_module'])){
            $backup[] = 'mz_module_setting';
            $backup[] = 'module';
        }

        // Menu
        if(!empty($this->request->get['export_menu'])){
            $backup[] = 'mz_menu';
            $backup[] = 'mz_menu_item';
        }

        if($backup){
            $backup_zip_file        =   DIR_UPLOAD . 'mz.' . $this->session->data['user_token'] . '.backup.zip';

            // Create backup zip
            $backup_zip = new ZipArchive();
            $backup_zip->open($backup_zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $custom_code_dir = DIR_CATALOG . 'view/javascript/maza/custom_code/';

            // Backup custom code
            if(!empty($this->request->get['export_theme'])){
                $options = array('add_path' => 'custom_code/', 'remove_path' => substr($custom_code_dir, 0, -1));
                $backup_zip->addGlob($custom_code_dir . 'css/*.css', 0, $options);
                $backup_zip->addGlob($custom_code_dir . 'js/*.js', 0, $options);
                $backup_zip->addGlob($custom_code_dir . 'header/*.html', 0, $options);
                $backup_zip->addGlob($custom_code_dir . 'footer/*.html', 0, $options);
            }

            $backup_zip->addFromString('backup.sql', $this->model_extension_maza_system->backup($backup));
            $backup_zip->close();

            header('Pragma: public');
            header('Expires: 0');
            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="mz_' . DB_DATABASE . '_' . date('Y-m-d_H-i-s', time()) . '_backup.zip"');
            header('Content-Transfer-Encoding: binary');

            readfile($backup_zip_file);

            unlink($backup_zip_file);
        } else {
            $this->session->data['warning'] = $this->language->get('error_export');

            $this->response->redirect($this->url->link('extension/maza/system', 'user_token=' . $this->session->data['user_token'], true));
        }
	}
        
    protected function validate(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/system')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
