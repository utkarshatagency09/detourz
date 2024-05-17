<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaMenu extends Controller {
        private $error = array();
        
        public function index(): void {
		$this->load->language('extension/maza/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/menu');

		$this->getList();
	}
        
        /**
         * Add menu
         */
        public function add(): void {
		$this->load->language('extension/maza/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/menu');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_menu->addMenu($this->request->post);

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
                        
			$this->response->redirect($this->url->link('extension/maza/menu', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
        /**
         * Edit menu
         */
	public function edit(): void {
		$this->load->language('extension/maza/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/menu');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_menu->editMenu($this->request->get['menu_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('extension/maza/menu', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
        /**
         * Copy menu
         */
        public function copy(): void {
		$this->load->language('extension/maza/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/menu');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $menu_id) {
				$this->model_extension_maza_menu->copyMenu($menu_id);
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

			$this->response->redirect($this->url->link('extension/maza/menu', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
        /**
         * Delete menu
         */
        public function delete(): void {
		$this->load->language('extension/maza/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/menu');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $menu_id) {
				$this->model_extension_maza_menu->deleteMenu($menu_id);
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

			$this->response->redirect($this->url->link('extension/maza/menu', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
        public function getList(): void {
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
			$sort = 'name';
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
                    'href' => $this->url->link('extension/maza/menu/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => false,
                    'form_target_id' => false,
                );
                
                $header_data['buttons'][] = array(
                    'id' => 'button-copy',
                    'name' => '',
                    'class' => 'btn-default',
                    'tooltip' => $this->language->get('button_copy'),
                    'icon' => 'fa-copy',
                    'formaction' => $this->url->link('extension/maza/menu/copy', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => false,
                    'form_target_id' => 'form-mz-menu',
                    'confirm' => $this->language->get('text_confirm')
                );
                
                $header_data['buttons'][] = array(
                    'id' => 'button-delete',
                    'name' => '',
                    'class' => 'btn-danger',
                    'tooltip' => $this->language->get('button_delete'),
                    'icon' => 'fa-trash',
                    'formaction' => $this->url->link('extension/maza/menu/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => 'form-mz-menu',
                    'confirm' => $this->language->get('text_confirm')
                );

                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-menu',
                    'target' => '_blank'
                );

                $header_data['form_target_id'] = 'form-mz-menu';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // List
		$data['menus'] = array();

		$filter_data = array(
                        'filter_name' => $filter_name,
                        'filter_status' => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$page_total = $this->model_extension_maza_menu->getTotalMenus($filter_data);

		$results = $this->model_extension_maza_menu->getMenus($filter_data);

		foreach ($results as $result) {
			$data['menus'][] = array(
				'menu_id' => $result['menu_id'],
				'name'    => $result['name'],
                                'status'  => $result['status']?$this->language->get('text_enabled'):$this->language->get('text_disabled'),
                                'edit'    => $this->url->link('extension/maza/menu/edit', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $result['menu_id'] . $url, true),
                                'items'    => $this->url->link('extension/maza/menu/item', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $result['menu_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('extension/maza/menu', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
                $data['sort_status'] = $this->url->link('extension/maza/menu', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
                
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
		$pagination->url = $this->url->link('extension/maza/menu', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

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
                
		$this->response->setOutput($this->load->view('extension/maza/menu/list', $data));
	}
        
        /**
         * Form to add or edit Menu
         */
        protected function getForm(): void {
                if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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
                $header_data['title'] = !isset($this->request->get['menu_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false)
                );
                
                $header_data['menu_active'] = 'tab-mz-general';
                $header_data['buttons'][] = array(
                    'id' => 'button-save',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'href' => false,
                    'target' => false,
                    'form_target_id' => 'form-mz-menu',
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-cancel',
                    'name' => '',
                    'tooltip' => $this->language->get('button_cancel'),
                    'icon' => 'fa-reply',
                    'class' => 'btn-default',
                    'href' => $this->url->link('extension/maza/menu', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-menu-add-menu',
                    'target' => '_blank'
                );
                $header_data['form_target_id'] = 'form-mz-menu';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Setting
                $setting = array();
                $setting['name'] = '';
                $setting['status'] = true;
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } elseif(isset($this->request->get['menu_id'])) {
                    $setting = array_merge($setting, $this->model_extension_maza_menu->getMenu($this->request->get['menu_id']));
                }

                // Data
                $data = array_merge($data, $setting);
                
                if (!isset($this->request->get['menu_id'])) {
			$data['action'] = $this->url->link('extension/maza/menu/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/menu/edit', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $this->request->get['menu_id'] . $url, true);
		}
                
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
                
		$this->response->setOutput($this->load->view('extension/maza/menu/form', $data));
	}
        
        protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/menu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
                
		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 60)) {
                        $this->error['name'] = $this->language->get('error_name');
                }
                
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
        
        protected function validateDelete(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/menu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
        
        protected function validateCopy(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/menu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
        
        /**
         * Mega item setting
         */
        public function item(): void {
                $this->load->language('extension/maza/menu');

		$this->document->setTitle($this->language->get('heading_title'));
                
                $this->document->addStyle('view/javascript/maza/colorpicker/css/colorpicker.css');
                $this->document->addScript('view/javascript/maza/colorpicker/js/colorpicker.js');
                
                $this->load->model('catalog/category');
                $this->load->model('tool/image');
                $this->load->model('customer/customer_group');
                $this->load->model('extension/maza/menu');
                $this->load->model('extension/maza/asset');
                $this->load->model('extension/maza/content_builder');
                $this->load->model('catalog/category');
                
                $url = '&menu_id=' . $this->request->get['menu_id'];
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                
                // Header
                $header_data = array();
                $header_data['theme_select'] = false;
                $header_data['skin_select'] = false;
                $header_data['title'] = $this->language->get('text_menu_items');
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
                    array('name' => $this->language->get('tab_type'), 'id' => 'tab-mz-type', 'href' => false),
                    array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
                    array('name' => $this->language->get('tab_meta'), 'id' => 'tab-mz-meta', 'href' => false),
                    array('name' => $this->language->get('tab_sub_menu'), 'id' => 'tab-mz-sub-menu', 'href' => false),
                );
                $header_data['menu_active'] = 'tab-mz-general';
                
                // Buttons
                $header_data['buttons'][] = array( // Button save
                    'name' => false,
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'class' => 'btn-primary',
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-menu-item',
                );
                $header_data['buttons'][] = array( // Button cancel
                    'name' => false,
                    'tooltip' => $this->language->get('button_cancel'),
                    'icon' => 'fa-reply',
                    'class' => 'btn-default',
                    'href' => $this->url->link('extension/maza/menu', 'user_token=' . $this->session->data['user_token'], true),
                    'target' => '_self',
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-menu-add-links',
                    'target' => '_blank'
                );
                
                // Form submit id
                $header_data['form_target_id'] = 'form-menu-item';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Submit form and save item in case of no error
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateItemForm()){
                    if (!isset($this->request->get['menu_item_id'])) {
                            $menu_item_id = $this->model_extension_maza_menu->addItem($this->request->get['menu_id'], $this->request->post);
                    } else {
                            $this->model_extension_maza_menu->editItem($this->request->get['menu_item_id'], $this->request->post);
                    }
                    
                    // Add item id in url and redirect to it after newly added item for next setting
                    if(isset($menu_item_id)){
                       $this->response->redirect($this->url->link('extension/maza/menu/item', 'user_token=' . $this->session->data['user_token'] . '&menu_item_id=' . $menu_item_id . $url, true)); 
                    }
                    
                    $this->session->data['success'] = $this->language->get('text_success');
                }
                
                if(isset($this->error['warning'])){
                        $data['warning'] = $this->error['warning'];
                } elseif(isset($this->session->data['warning'])){
                        $data['warning'] = $this->session->data['warning'];
                        unset($this->session->data['warning']);
                }
                
                if(isset($this->session->data['success'])){
                    $data['success'] = $this->session->data['success'];
                    unset($this->session->data['success']);
                }
                
                // item errors
                foreach ($this->error as $label => $error) {
                    $data['err_' . $label] = $error;
                }
                
                $data['add_item'] = $this->url->link('extension/maza/menu/item', 'user_token=' . $this->session->data['user_token'] . $url, true);
                $data['delete_item'] = $this->url->link('extension/maza/menu/deleteItem', 'user_token=' . $this->session->data['user_token'] . $url, true);
                $data['duplicate_item'] = $this->url->link('extension/maza/menu/duplicateItem', 'user_token=' . $this->session->data['user_token'] . $url, true);
                
                if (!isset($this->request->get['menu_item_id'])) {
			$data['action'] = $this->url->link('extension/maza/menu/item', 'user_token=' . $this->session->data['user_token'] . $url, true);
                        $data['menu_item_id'] = 0;
		} else {
			$data['action'] = $this->url->link('extension/maza/menu/item', 'user_token=' . $this->session->data['user_token'] . '&menu_item_id=' . $this->request->get['menu_item_id'] . $url, true);
                        $data['menu_item_id'] = (int)$this->request->get['menu_item_id'];
		}
                
                if (isset($this->request->get['menu_item_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$item_info = $this->model_extension_maza_menu->getItem($this->request->get['menu_item_id']);
                } else {
                        $item_info = array();
                }
                
                
                // Language
                $this->load->model('localisation/language');
                $data['languages'] = $this->model_localisation_language->getLanguages();
                
                
                // Setting
                $setting = array();
                
                // General
                $setting['name']             =  '';
                $setting['status']           =  0;
                $setting['parent_item_id']   =  0;
                $setting['sort_order']       =  0;
                $setting['customer']  	     =  0; // 0 = ALL, -1 = Guest, 1 = logged
                $setting['customer_group_id']=  0;
                $setting['type']             =  'link';
                $setting['setting']          =  array();
                $setting['setting']['title'] =  '';
                $setting['setting']['description'] =  '';
                
                // Label
                $setting['setting']['label'] =  array();
                $setting['setting']['label_text_color']         =  '';
                $setting['setting']['label_background_color']   =  '';
                
                // icon
                $setting['setting']['icon_image']  =  array();
                $setting['setting']['icon_svg']    =  array();
                $setting['setting']['icon_font']   =  array();
                $setting['setting']['icon_size']   =  NULL;
                $setting['setting']['icon_width']  =  NULL;
                $setting['setting']['icon_height'] =  NULL;
                
                
                // Sub menu background
                for($layer = 1; $layer <= 3; $layer++){
                    $setting['setting']['sub_menu_background']['layer_' . $layer] = array(
                            'status'            =>  'none',
                            'image'             =>  'no_image.png',
                            'thumb'             =>  $this->model_tool_image->resize('no_image.png', 80, 80),
                            'image_position'    =>  'left_top',
                            'image_repeat'      =>  'repeat',
                            'image_attachment'  =>  'scroll',
                            'overlay_pattern'   =>  'default',
                    );
                }
                
                // Category type
                $setting['setting']['category_top_id']  =  0;
                $setting['setting']['category_depth']   =  1;
                $setting['setting']['category_column']  =  0;
                
                // System item
                $setting['setting']['system_item']      =  'language';
                
                // link type
                $setting['setting']['link_code']        =  '';
		$setting['setting']['link_url_target']  =  '_self';
                
                // Menu type
                $setting['setting']['menu_id']          =  0;
                
                // Content menu
                $setting['setting']['content_width']    =  '';
                $setting['setting']['content_id']       =  0;
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } else {
                    $setting = array_merge($setting, $item_info); 
                }
                
                $data = array_merge($data, $setting);
                
                // Data
                $data['menu_items'] = $this->model_extension_maza_menu->getItems($this->request->get['menu_id']);
                
                if(isset($this->request->get['menu_item_id'])){
                    $data['parent_items'] = $this->model_extension_maza_menu->getItems($this->request->get['menu_id'], 0, 'link', $this->request->get['menu_item_id']);
                } else {
                    $data['parent_items'] = $this->model_extension_maza_menu->getItems($this->request->get['menu_id'], 0, 'link');
                }

                $data['list_customer'] = array(
                        array('id' => 0, 'name' => $this->language->get('text_all')),
                        array('id' => -1, 'name' => $this->language->get('text_guest')),
                        array('id' => 1, 'name' => $this->language->get('text_logged')),
                );

                $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
                
                // sub menu background
                foreach ($data['setting']['sub_menu_background'] as $code => $sub_menu_background) {
                    $data['setting']['sub_menu_background'][$code]['thumb'] = $this->model_tool_image->resize($sub_menu_background['image'], 80, 80);
                }
                
                
                $data['list_background_status'] = array(
                        array('code' => 'none', 'name' => $this->language->get('text_none')),
                        array('code' => 'image', 'name' => $this->language->get('text_image')),
                        array('code' => 'pattern', 'name' => $this->language->get('text_pattern')),
                );
                $data['background_image_positions'] = array(
                        array('code' => 'left_top', 'name' => $this->language->get('text_left_top')),
                        array('code' => 'right_top', 'name' => $this->language->get('text_right_top')),
                        array('code' => 'left_bottom', 'name' => $this->language->get('text_left_bottom')),
                        array('code' => 'right_bottom', 'name' => $this->language->get('text_right_bottom')),
                );
                $data['background_image_repeats'] = array(
                        array('code' => 'repeat', 'name' => $this->language->get('text_repeat')),
                        array('code' => 'repeat-x', 'name' => $this->language->get('text_repeat_x')),
                        array('code' => 'repeat-y', 'name' => $this->language->get('text_repeat_y')),
                        array('code' => 'no-repeat', 'name' => $this->language->get('text_no_repeat')),
                );
                $data['background_image_attachments'] = array(
                        array('code' => 'scroll', 'name' => $this->language->get('text_scroll')),
                        array('code' => 'fixed', 'name' => $this->language->get('text_fixed')),
                );
                $data['overlay_patterns'] = $this->model_extension_maza_asset->overlayPatterns();
                foreach ($data['overlay_patterns'] as $key => $pattern) {
                    $data['overlay_patterns'][$key]['image'] = $this->config->get('mz_store_url') . 'image/' . $pattern['image'];
                }
                
                // Menu type
                $data['list_menu_type'] = array(
                    array('id' => 'link', 'name' => $this->language->get('text_link')),
                    array('id' => 'system','name' => $this->language->get('text_system')),
                    array('id' => 'category', 'name' => $this->language->get('text_category')),
                    array('id' => 'menu', 'name' => $this->language->get('text_menu')),
                    array('id' => 'content', 'name' => $this->language->get('text_mega_menu')),
                );
                
                // System items
                $data['list_system_items'] = array(
                        array('id' => 'customer', 'name' => $this->language->get('text_customer')),
                        array('id' => 'language', 'name' => $this->language->get('text_language')),
                        array('id' => 'currency', 'name' => $this->language->get('text_currency')),
                        array('id' => 'wishlist', 'name' => $this->language->get('text_wishlist')),
                        array('id' => 'compare', 'name' => $this->language->get('text_compare')),
                        array('id' => 'notification', 'name' => $this->language->get('text_notification')),
                        array('id' => 'tel', 'name' => $this->language->get('text_tel')),
                        array('id' => 'fax', 'name' => $this->language->get('text_fax')),
                        array('id' => 'email', 'name' => $this->language->get('text_email')),
                );

                $category_info = $this->model_catalog_category->getCategory($setting['setting']['category_top_id']);

                if ($category_info) {
                        $data['setting']['category_top_name'] =  ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'];
                } else {
                        $data['setting']['category_top_name'] =  '';
                }
                
                // Content
                $content_info =  $this->model_extension_maza_content_builder->getContent($data['setting']['content_id']);
                if($content_info){
                    $data['setting']['content_name'] =  $content_info['name'];
                } else {
                    $data['setting']['content_name'] =  '';
                }
                
                // Menu type
                $menu_info =  $this->model_extension_maza_menu->getMenu($data['setting']['menu_id']);
                if($menu_info){
                    $data['setting']['menu_name'] =  $menu_info['name'];
                } else {
                    $data['setting']['menu_name'] =  '';
                }
                
                if(!empty($setting['setting']['link_code'])){
                        $data['link_info'] = $this->load->controller('extension/maza/common/linkmanager/info', $setting['setting']['link_code']);
                } else {
                        $data['link_info'] = '';
                }

                // Image
                $data['placeholder_image']  = $this->model_tool_image->resize('no_image.png', 100, 100);
                $data['placeholder_svg']    = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                $data['placeholder_font']   = 'fa fa-font';
                
                // Icon thumb Image
                $data['thumb_icon_image'] = array();
                
                foreach ($setting['setting']['icon_image'] as $language_id => $image) {
                    if($image){
                        $data['thumb_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
                    } else {
                        $data['thumb_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                    }
                }
                
                // Icon thumb svg
                $data['thumb_icon_svg'] = array();
                
                foreach ($setting['setting']['icon_svg'] as $language_id => $image_svg) {
                    if($image_svg){
                        $data['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
                    } else {
                        $data['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                    }
                }
                
                
                $data['menu_id'] = (int)$this->request->get['menu_id'];
                $data['user_token'] = $this->session->data['user_token'];
                
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		$this->response->setOutput($this->load->view('extension/maza/menu/item', $data));
        }
        
        /**
         * Validate menu item setting
         */
        protected function validateItemForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/menu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
                
                // Name
                if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_item_name');
		}
                
                
                // Menu
                if($this->request->post['type'] == 'menu' && (empty($this->request->post['setting']['menu_id']) || empty($this->request->post['setting']['menu_name']))){
                    $this->error['setting']['menu'] = $this->language->get('error_menu');
                }
                
                // Mega Menu
                if($this->request->post['type'] == 'content' && (empty($this->request->post['setting']['content_id']) || empty($this->request->post['setting']['content_name']))){
                    $this->error['setting']['content'] = $this->language->get('error_content');
                }
                
                if(!isset($this->error['warning']) && $this->error){
                        $this->error['warning'] = $this->language->get('error_warning');
                }

		return !$this->error;
	}
        
        /**
         * Delete menu item
         */
        public function deleteItem(): void {
                $this->load->language('extension/maza/menu');
                $this->load->model('extension/maza/menu');
                $this->load->model('extension/maza/layout_builder');
                
                $url = '&menu_id=' . $this->request->get['menu_id'];
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                
                if(isset($this->request->get['menu_item_id']) && $this->validateDelete()){
                        $this->model_extension_maza_menu->deleteItem($this->request->get['menu_item_id']);
                        
                        $this->session->data['success'] = $this->language->get('text_success');
                        
                        $this->response->redirect($this->url->link('extension/maza/menu/item', 'user_token=' . $this->session->data['user_token'] . $url, true));
                } else {
                        $this->response->redirect($this->url->link('extension/maza/menu/item', 'user_token=' . $this->session->data['user_token'] . $url, true));
                }
                
        }
        
        /**
         * duplicate menu item
         */
        public function duplicateItem(): void {
                $this->load->language('extension/maza/menu');
                $this->load->model('extension/maza/menu');
                
                $url = '&menu_id=' . $this->request->get['menu_id'];
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                
                if(isset($this->request->get['menu_item_id']) && $this->validateCopy()){
                        $this->model_extension_maza_menu->duplicateItem($this->request->get['menu_item_id']);
                                                
                        $this->session->data['success'] = $this->language->get('text_success');
                        
                        $this->response->redirect($this->url->link('extension/maza/menu/item', 'user_token=' . $this->session->data['user_token'] . '&menu_item_id=' . (int)$this->request->get['menu_item_id'] . $url, true));
                } else {
                        $this->response->redirect($this->url->link('extension/maza/menu/item', 'user_token=' . $this->session->data['user_token'] . $url, true));
                }
                
        }
        
        public function autocomplete(): void {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/menu');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
                                'filter_status' => 1,
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_maza_menu->getMenus($filter_data);

			foreach ($results as $result) {
                                if(isset($this->request->get['skip_menu_id']) && $this->request->get['skip_menu_id'] == $result['menu_id']){
                                    continue; // Skip
                                } else {
                                    $json[] = array(
                                            'menu_id'       => $result['menu_id'],
                                            'name'          => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                                    );
                                }
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
