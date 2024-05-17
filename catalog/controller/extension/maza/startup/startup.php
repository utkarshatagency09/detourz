<?php
require_once(DIR_SYSTEM . 'library/maza/startup.php');

class ControllerExtensionMazaStartupStartup extends Controller {
	public function index(): void {
		// Config
        $this->config->load('maza/default');
        $this->config->load('maza/catalog');

		// Library
        $this->mz_theme_config  = new maza\config\Theme();
        $this->mz_skin_config   = new maza\config\Skin();
        $this->mz_document      = new maza\Document();
        $this->mz_schema        = new maza\Schema();
        $this->mz_cache         = new maza\Cache($this->config->get('mz_cache_engine'), $this->config->get('cache_expire'));
        $this->mz_load          = new maza\Loader($this->registry);
        $this->mz_hook          = new maza\Hook($this->registry);
        $this->mz_minifier      = new maza\Minifier();
        $this->mz_browser       = new maza\Browser($this->request->server['HTTP_USER_AGENT']);
        // $this->mz_geolocator    = new maza\GeoLocator();

		if ($this->config->get('db_autostart')) {
            $this->mz_db = new maza\DB($this->config->get('db_engine'), $this->config->get('db_hostname'), $this->config->get('db_username'), $this->config->get('db_password'), $this->config->get('db_database'), $this->config->get('db_port'));
        }

		// Model Autoload
        if ($this->config->has('mz_model_autoload')) {
            foreach ($this->config->get('mz_model_autoload') as $value) {
                $this->load->model($value);
            }
        }

        // Default http header
        foreach ($this->config->get('mz_response_header') as $header) {
            $this->response->addHeader($header);
        }

        // Disable developer mode if user is not admin
        if($this->config->get('maza_developer_mode') && empty($this->session->data['user_id'])){
            $this->config->set('maza_developer_mode', false);
        }

        // Developer
        if($this->config->get('maza_developer_mode')){
            $this->config->set('maza_cache_status', 0);
            $this->config->set('maza_cache_partial', 0);
            $this->config->set('maza_minify_css', 0);
            $this->config->set('maza_minify_js', 0);
            $this->config->set('maza_minify_html', 0);
            $this->config->set('maza_combine_css', 0);
            $this->config->set('maza_combine_js', 0);
            // $this->config->set('maza_css_autoprefix', 0);
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            @set_time_limit(0);
        }

        // Theme
        $mz_theme_code = $this->config->get('theme_default_directory');
        
        if(isset($this->session->data['user_id']) && isset($this->request->get['mz_theme_code'])){
            $mz_theme_code = $this->request->get['mz_theme_code'];
        }

        $this->load->model('extension/maza/theme');

        $theme_setting = $this->model_extension_maza_theme->getSetting($mz_theme_code, $this->config->get('config_store_id'));
        
        if($theme_setting){
            foreach ($theme_setting as $key => $value) {
                $this->mz_theme_config->set($key, $value);
            }
            
            $this->mz_theme_config->set('theme_code', $mz_theme_code);
            $this->config->set('maza_status', TRUE);
        } else {
            $theme_config = $this->model_extension_maza_theme->getThemeConfig($mz_theme_code);
            
            if($theme_config){
                throw new Exception('Please install theme');
            }

            return;
        }

		// Language
        $this->load->language('extension/maza/common');

		// Startup
		foreach ($this->config->get('mz_startup') as $route) {
			$action = new Action($route);
			$action->execute($this->registry);
		}
	}
}
