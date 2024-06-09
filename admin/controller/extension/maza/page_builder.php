<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaPageBuilder extends Controller {
        private $error = array();
        
        public function index() {
		$this->load->language('extension/maza/page_builder');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/page_builder');

		$this->getList();
	}
        
        /**
         * Add page
         */
        public function add() {
		$this->load->language('extension/maza/page_builder');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/page_builder');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_page_builder->addPage($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
                        if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['mz_theme_code'])){
                                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                        }
                        if(isset($this->request->get['mz_skin_id'])){
                                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                        }
                        if (isset($this->request->get['sort'])) {
                                $url .= '&sort=' . $this->request->get['sort'];
                        }
                        if (isset($this->request->get['order'])) {
                                $url .= '&order=' . $this->request->get['order'];
                        }
                        if (isset($this->request->get['page'])) {
                                $url .= '&page=' . $this->request->get['page'];
                        }
                        
			$this->response->redirect($this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
        /**
         * Edit page
         */
	public function edit() {
		$this->load->language('extension/maza/page_builder');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/page_builder');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_page_builder->editPage($this->request->get['page_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['mz_theme_code'])){
                                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                        }
                        if(isset($this->request->get['mz_skin_id'])){
                                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                        }
                        if (isset($this->request->get['sort'])) {
                                $url .= '&sort=' . $this->request->get['sort'];
                        }
                        if (isset($this->request->get['order'])) {
                                $url .= '&order=' . $this->request->get['order'];
                        }
                        if (isset($this->request->get['page'])) {
                                $url .= '&page=' . $this->request->get['page'];
                        }

			$this->response->redirect($this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
        public function copy() {
		$this->load->language('extension/maza/page_builder');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/page_builder');
                $this->load->model('extension/maza/layout_builder');
                $this->load->model('extension/maza/skin');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $page_id) {
				$new_page_id = $this->model_extension_maza_page_builder->copyPage($page_id);
                                
                                // layout
                                foreach($this->model_extension_maza_skin->getSkins() as $skin_info){
                                    $page_content = $this->model_extension_maza_layout_builder->getLayout($skin_info['skin_id'], 'page', $page_id);
                                    $this->model_extension_maza_layout_builder->editLayout($skin_info['skin_id'], 'page', $new_page_id, $page_content);
                                    
                                    $page_component = $this->model_extension_maza_layout_builder->getLayout($skin_info['skin_id'], 'page_component', $page_id);
                                    $this->model_extension_maza_layout_builder->editLayout($skin_info['skin_id'], 'page_component', $new_page_id, $page_component);
                                }
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['mz_theme_code'])){
                                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                        }
                        if(isset($this->request->get['mz_skin_id'])){
                                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                        }
                        if (isset($this->request->get['sort'])) {
                                $url .= '&sort=' . $this->request->get['sort'];
                        }
                        if (isset($this->request->get['order'])) {
                                $url .= '&order=' . $this->request->get['order'];
                        }
                        if (isset($this->request->get['page'])) {
                                $url .= '&page=' . $this->request->get['page'];
                        }

			$this->response->redirect($this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
        public function delete() {
		$this->load->language('extension/maza/page_builder');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/page_builder');
                $this->load->model('extension/maza/layout_builder');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $page_id) {
				$this->model_extension_maza_page_builder->deletePage($page_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
                        if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['mz_theme_code'])){
                                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                        }
                        if(isset($this->request->get['mz_skin_id'])){
                                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                        }
                        if (isset($this->request->get['sort'])) {
                                $url .= '&sort=' . $this->request->get['sort'];
                        }
                        if (isset($this->request->get['order'])) {
                                $url .= '&order=' . $this->request->get['order'];
                        }
                        if (isset($this->request->get['page'])) {
                                $url .= '&page=' . $this->request->get['page'];
                        }

			$this->response->redirect($this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
        public function getList() {
                if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}
                if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.name';
		}
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
                
                $url = '';
                
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
                
                // Header
                $header_data = array();
                $header_data['theme_select'] = false;
                $header_data['skin_select'] = false;
                $header_data['title'] = $this->language->get('heading_title');
                $header_data['buttons'][] = array(
                    'id' => 'button-add',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_add'),
                    'icon' => 'fa-plus',
                    'href' => $this->url->link('extension/maza/page_builder/add', 'user_token=' . $this->session->data['user_token'], true),
                    'target' => false,
                    'form_target_id' => false,
                );
                
                $header_data['buttons'][] = array(
                    'id' => 'button-copy',
                    'name' => '',
                    'class' => 'btn-default',
                    'tooltip' => $this->language->get('button_copy'),
                    'icon' => 'fa-copy',
                    'formaction' => $this->url->link('extension/maza/page_builder/copy', 'user_token=' . $this->session->data['user_token'], true),
                    'target' => false,
                    'form_target_id' => 'form-mz-page',
                    'confirm' => $this->language->get('text_confirm')
                );
                
                $header_data['buttons'][] = array(
                    'id' => 'button-delete',
                    'name' => '',
                    'class' => 'btn-danger',
                    'tooltip' => $this->language->get('button_delete'),
                    'icon' => 'fa-trash',
                    'formaction' => $this->url->link('extension/maza/page_builder/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => 'form-mz-page',
                    'confirm' => $this->language->get('text_confirm')
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-page-builder',
                    'target' => '_blank'
                );
                $header_data['form_target_id'] = 'form-mz-page';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // List
		$data['pages'] = array();

		$filter_data = array(
                        'filter_name' => $filter_name,
                        'filter_status' => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$page_total = $this->model_extension_maza_page_builder->getTotalPages($filter_data);

		$results = $this->model_extension_maza_page_builder->getPages($filter_data);

		foreach ($results as $result) {
			$data['pages'][] = array(
				'page_id' => $result['page_id'],
				'name'    => $result['name'],
                                'skin'    => $result['skin'],
                                'status'  => $result['status']?$this->language->get('text_enabled'):$this->language->get('text_disabled'),
                                'edit'    => $this->url->link('extension/maza/page_builder/edit', 'user_token=' . $this->session->data['user_token'] . '&page_id=' . $result['page_id'] . $url, true),
                                'layout'  => $this->url->link('extension/maza/page_builder/layout', 'user_token=' . $this->session->data['user_token'] . '&page_id=' . $result['page_id'] . $url, true)
			);
		}
                

		if(isset($this->session->data['warning'])){
                        $data['warning'] = $this->session->data['warning'];
                        unset($this->session->data['warning']);
                }
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';
                
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token'] . '&sort=p.name' . $url, true);
                $data['sort_skin'] = $this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token'] . '&sort=skin' . $url, true);
                $data['sort_status'] = $this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
                
                $data['sort'] = $sort;
		$data['order'] = $order;

		$url = '';
                
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $page_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($page_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($page_total - $this->config->get('config_limit_admin'))) ? $page_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $page_total, ceil($page_total / $this->config->get('config_limit_admin')));

                $data['filter_name'] = $filter_name;
                $data['filter_status'] = $filter_status;
                
                $data['default_url'] = '&user_token=' . $this->session->data['user_token'];
                if(isset($this->request->get['mz_theme_code'])){
                        $data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
                
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		$this->response->setOutput($this->load->view('extension/maza/page_builder/list', $data));
	}
        
        /**
         * Form to add or edit Page
         */
        protected function getForm() {
                $this->load->model('localisation/language');
                $this->load->model('setting/store');
                $this->load->model('design/layout');
                
                if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.name';
		}
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

                $url = '';
                
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
                
                $data = array();
                
                // Header
                $header_data = array();
                $header_data['title'] = !isset($this->request->get['page_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
                    array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),                   
                );

                if (version_compare(VERSION, '3.0.0.0') >= 0) { // OC 3 and UP
                    $header_data['menu'][] = array('name' => $this->language->get('tab_seo'), 'id' => 'tab-mz-seo', 'href' => false);
                }
                
                $header_data['menu_active'] = 'tab-mz-general';
                $header_data['buttons'][] = array(
                    'id' => 'button-save',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'href' => false,
                    'target' => false,
                    'form_target_id' => 'form-mz-page',
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-cancel',
                    'name' => '',
                    'tooltip' => $this->language->get('button_cancel'),
                    'icon' => 'fa-reply',
                    'class' => 'btn-default',
                    'href' => $this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-page-builder',
                    'target' => '_blank'
                );
                $header_data['form_target_id'] = 'form-mz-page';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Setting
                $setting = array();
                $setting['page_description'] = array();
                $setting['page_store'] = array(0);
                $setting['status'] = true;
                $setting['override_skin_id'] = 0;

                if (version_compare(VERSION, '3.0.0.0') < 0) { // OC 2
                        $setting['keyword'] = '';
                } else {
                        $setting['page_seo_url'] = array();
                }
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } elseif(isset($this->request->get['page_id'])) {
                    $setting = array_merge($setting, $this->model_extension_maza_page_builder->getPage($this->request->get['page_id']));
                    $setting['page_description'] = $this->model_extension_maza_page_builder->getPageDescriptions($this->request->get['page_id']);
                    $setting['page_store']       = $this->model_extension_maza_page_builder->getPageStores($this->request->get['page_id']);

                    if (version_compare(VERSION, '3.0.0.0') >= 0) {
                        $setting['page_seo_url']      = $this->model_extension_maza_page_builder->getPageSeoUrls($this->request->get['page_id']);      
                    }
                }

                // Data
                $data = array_merge($data, $setting);
                
                if (!isset($this->request->get['page_id'])) {
			$data['action'] = $this->url->link('extension/maza/page_builder/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/page_builder/edit', 'user_token=' . $this->session->data['user_token'] . '&page_id=' . $this->request->get['page_id'] . $url, true);
		}

                // Skins
                $this->load->model('extension/maza/skin');

                $data['skins'] = $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
                
                // Stores
                $data['stores'] = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		$stores = $this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}
                
                // General
                $data['languages'] = $this->model_localisation_language->getLanguages();
                $data['user_token'] = $this->session->data['user_token'];
                
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
                
                if(isset($this->error['warning'])){
                        $data['warning'] = $this->error['warning'];
                } elseif (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}
                
                foreach($this->error as $key => $val){
                    $data['err_' . $key] = $val;
                }
                
                // Columns
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		$this->response->setOutput($this->load->view('extension/maza/page_builder/form', $data));
	}
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/maza/page_builder')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['page_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if (!empty($this->request->post['page_seo_url'])) {
			$this->load->model('design/seo_url');
			
			foreach ($this->request->post['page_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
	
						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['page_id']) || ($seo_url['query'] != 'mz_page_id=' . $this->request->get['page_id']))) {		
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
				
								break;
							}
						}
					}
				}
			}
		}

                // OC 2
                if (!empty($this->request->post['keyword'])) {
			$this->load->model('catalog/url_alias');

			$url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

			if ($url_alias_info && isset($this->request->get['page_id']) && $url_alias_info['query'] != 'mz_page_id=' . $this->request->get['page_id']) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}

			if ($url_alias_info && !isset($this->request->get['page_id'])) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
        
        protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/maza/page_builder')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
        
        protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'extension/maza/page_builder')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
        
        public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/page_builder');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_maza_page_builder->getPages($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'page_id' => $result['page_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
        public function layout() {
                $this->load->model('tool/image');
                $this->load->model('extension/maza/page_builder');
                $this->load->model('extension/maza/opencart');
                
                $this->load->language('extension/maza/layout_builder');
                $this->load->language('extension/maza/page_builder');
                
                $this->document->addStyle('view/javascript/maza/colorpicker/css/colorpicker.css');
                $this->document->addScript('view/javascript/maza/colorpicker/js/colorpicker.js');
                $this->document->addStyle('view/javascript/maza/jquery-ui-1.12.1.Interactions/jquery-ui.min.css');
                $this->document->addStyle('view/stylesheet/maza/mz_stylesheet.css');
                $this->document->addScript('view/javascript/maza/jquery-ui-1.12.1.Interactions/jquery-ui.min.js');
                $this->document->addScript('view/javascript/maza/layout_builder.js');
                $this->document->addScript('view/javascript/maza/mz_common.js');
                
                $url = '';
                
                if(isset($this->request->get['mz_theme_code'])){
                    $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                    $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
                
                $data['cancel'] = $this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token']  . $url, true);
                $data['skin_target'] = $this->url->link('extension/maza/page_builder/layout', 'user_token=' . $this->session->data['user_token'] . '&page_id=' . $this->request->get['page_id'] . '&mz_theme_code=' . $this->request->get['mz_theme_code']);
                $data['export'] = $this->url->link('extension/maza/page_builder/export', 'user_token=' . $this->session->data['user_token'] . '&page_id=' . $this->request->get['page_id'] . $url, true);
                
                // Get skins
                $data['mz_skins'] = $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
                $data['mz_skin_info'] = $this->model_extension_maza_skin->getSkin($this->mz_skin_config->get('skin_id'));
                
                // Get page info
                $page_info = $this->model_extension_maza_page_builder->getPage($this->request->get['page_id']);
                
                if($page_info){
                    $data['page_name'] = $page_info['name'];
                    $this->document->setTitle($page_info['name'] . ' | ' . $this->language->get('heading_title'));
                } else {
                    $data['warning'] = $this->language->get('error_page_deleted');
                    $this->document->setTitle($this->language->get('heading_title'));
                }
                
                // Page entries
                $data['page_content'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'page', 'group_owner' => $page_info['page_id']]);
                
                $data['page_component'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'page_component', 'group_owner' => $page_info['page_id']]);
                
                $this->load->model('extension/maza/extension');
                
                // widgets
                $data['widgets'] = $this->model_extension_maza_extension->getWidgets();
                
                // Designs
                $data['designs'] = $this->model_extension_maza_extension->getDesigns();
                
                // Get a list of installed modules
                $data['extensions'] = array();
                
		$extensions = $this->model_extension_maza_opencart->getInstalled('module');

		// Add all the modules which have multiple settings for each module
		foreach ($extensions as $code) {
			$this->load->language('extension/module/' . $code, 'extension');

			$module_data = array();

			$modules = $this->model_extension_maza_opencart->getModulesByCode($code);

                        if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                                $heading_title = $this->language->get('heading_title');
                        } else {
                                $heading_title = $this->language->get('extension')->get('heading_title');
                        }

			foreach ($modules as $module) {
				$module_data[] = array(
                                        'module_id' => $module['module_id'],
					'name'      => strip_tags($module['name']),
                                        'path'      => strip_tags($heading_title) . ' > ' . $module['name'],
					'code'      => $code . '.' .  $module['module_id'],
                                        'edit'      => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id']. $url, true)
				);
			}

			if ($this->config->has('module_' . $code . '_status') || $this->config->has($code . '_status') || $module_data) {
				$data['extensions'][] = array(
					'name'   => strip_tags($heading_title),
                                        'path'   => strip_tags($heading_title),
					'code'   => $code,
					'module' => $module_data,
                                        'edit'   => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token']. $url, true)
				);
			}
		}
                
                
                // Data
                $data['user_token'] = $this->session->data['user_token'];
                $data['page_id'] = $this->request->get['page_id'];
                $data['url'] = $url;
                
                $data['device_views'] = array(
                    array('icon' => 'fa-desktop','code' => 'xl'),
                    array('icon' => 'fa-laptop', 'code' => 'lg'),
                    array('icon' => 'fa-tablet fa-rotate-270','code' => 'md'),
                    array('icon' => 'fa-tablet', 'code' => 'sm'),
                    array('icon' => 'fa-mobile', 'code' => 'xs')
                );
                
                
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
                $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                
		$this->response->setOutput($this->load->view('extension/maza/page_builder/layout', $data));
        }
        
        /**
         * Submit layout
         */
        public function submitForm() {
                $json = array();
                
                $this->load->language('extension/maza/layout_builder');
                $this->load->language('extension/maza/page_builder');
                $this->load->model('extension/maza/layout_builder');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->get['page_id'] && $this->validateLayout()){
                    $this->editLayout($this->request->post);
                    
                    $json['success'] = $this->language->get('text_layout_success');
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * Edit layout
         * @param array $data
         */
        private function editLayout($data){
                // page
                if(isset($data['page'])){
                    $page_content = $data['page'];
                } else {
                    $page_content = array();
                }
                $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'page', $this->request->get['page_id'], $page_content);

                // page component
                if(isset($data['page_component'])){
                    $page_component = $data['page_component'];
                } else {
                    $page_component = array();
                }
                $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'page_component', $this->request->get['page_id'], $page_component);
        }
        
        /**
         * Duplicate skin layout from current skin to selected skin
         */
        public function duplicateLayout() {
                $json = array();
                
                $this->load->language('extension/maza/layout_builder');
                $this->load->language('extension/maza/page_builder');
                $this->load->model('extension/maza/layout_builder');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->get['page_id']) && isset($this->request->post['duplicate_to_skin_id']) && $this->validateLayout()){
                    // page
                    $page_content = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'page', $this->request->get['page_id']);
                    $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'page', $this->request->get['page_id'], $page_content);
                    
                    // page component
                    $page_component = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'page_component', $this->request->get['page_id']);
                    $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'page_component', $this->request->get['page_id'], $page_component);
                    
                    $json['success'] = $this->language->get('text_duplicate_layout_success');
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * Validate layout
         */
        protected function validateLayout() {
		if (!$this->user->hasPermission('modify', 'extension/maza/layout_builder')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        /**
         * Export layout
         */
        public function export() {
                $data = array();
                
                $this->load->model('extension/maza/page_builder');
                
                $page_info = $this->model_extension_maza_page_builder->getPage($this->request->get['page_id']);
                
                if($page_info){
                    $data['page'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'page', 'group_owner' => $page_info['page_id']]);
                    $data['page_component'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'page_component', 'group_owner' => $page_info['page_id']]);
                    
                    header('Content-Type: application/json; charset=utf-8');
                    header('Content-disposition: attachment; filename="layout.page.' . $this->mz_skin_config->get('skin_code') . '(' . $page_info['name'] . ').json"');
                    
                    echo json_encode(['type' => 'page', 'data' => $data]);
                } else {
                    $url = '';
                
                    if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                    }
                    if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                    }
                    
                    $this->response->redirect($this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token']  . $url, true));
                }
	}
        
        /**
         * Import layout
         */
        public function import(){
                $this->load->language('extension/maza/layout_builder');
                $this->load->language('extension/maza/page_builder');

                $json = array();

                // Check user has permission
                if (!$this->user->hasPermission('modify', 'extension/maza/page_builder')) {
                        $json['error'] = $this->language->get('error_permission');
                } else {
                    if (isset($this->request->files['file']['name'])) {
                            if (substr($this->request->files['file']['name'], -4) != 'json') {
                                    $json['error'] = $this->language->get('error_filetype');
                            }

                            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                            }
                    } else {
                            $json['error'] = $this->language->get('error_upload');
                    }
                }

                if (!$json) {
                        $file = $this->request->files['file']['tmp_name'];

                        if (is_file($file)) {
                                $data = json_decode(file_get_contents($file), true);
                                
                                if($data && $data['type'] == 'page'){
                                    $this->load->model('extension/maza/layout_builder');
                                    
                                    $this->editLayout($data['data']);
                                    
                                    $json['success'] = $this->language->get('text_success_import');
                                } else {
                                    $json['error'] = $this->language->get('error_layout_support');
                                }
                        } else {
                                $json['error'] = $this->language->get('error_file');
                        }
                }

                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
}
