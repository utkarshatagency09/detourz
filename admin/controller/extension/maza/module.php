<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2021, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaModule extends Controller {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/extension/module');
                $this->load->language('extension/maza/module');

                $this->document->setTitle($this->language->get('heading_title'));
                
                // Header
                $header_data = array();
                $header_data['theme_select'] = false;
                $header_data['skin_select'] = false;
                $header_data['title'] = $this->language->get('heading_title');
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                $url = '';
                
                if(isset($this->request->get['mz_theme_code'])){
                    $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                
                if(isset($this->request->get['mz_skin_id'])){
                    $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
                
                $this->load->model('extension/maza/opencart');
                
                $installed = $this->model_extension_maza_opencart->getInstalled('module');

                $data['modules'] = array();

                // Create a new language container so we don't pollute the current one
                $language = new Language($this->config->get('config_language'));
                
                // Compatibility code for old extension folders
                $files = glob(DIR_APPLICATION . 'controller/extension/module/mz_*.php');

                if ($files) {
                    foreach ($files as $file) {
                        $extension = basename($file, '.php');

                        if($extension == 'mz_engine' || !$this->user->hasPermission('access', 'extension/module/' . $extension)) continue;

                        $this->load->language('extension/module/' . $extension, 'extension');

                        if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                            $name = $this->language->get('heading_title');
                        } else {
                            $name = $this->language->get('extension')->get('heading_title');
                        }

                        $data['modules'][] = array(
                            'name'      => $name,
                            'install'   => $this->url->link('extension/extension/module/install', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
                            'uninstall' => $this->url->link('extension/extension/module/uninstall', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
                            'installed' => in_array($extension, $installed),
                            'edit'      => $this->url->link('extension/module/' . $extension, 'user_token=' . $this->session->data['user_token'] . $url, true)
                        );
                    }
                }

                $sort_order = array();

                foreach ($data['modules'] as $key => $value) {
                    $sort_order[$key] = $value['name'];
                }

                array_multisort($sort_order, SORT_ASC, $data['modules']);

                if (isset($this->session->data['success'])) {
                    $data['success'] = $this->session->data['success'];
                    unset($this->session->data['success']);
                } else {
                    $data['success'] = '';
                }
                
                $data['header'] = $this->load->controller('common/header');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		        $this->response->setOutput($this->load->view('extension/maza/module', $data));
        }
}
