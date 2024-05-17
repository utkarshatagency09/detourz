<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaContentBuilder extends Controller {
        private $error = array();
        
         public function index() {
		$this->load->language('extension/maza/content_builder');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/content_builder');

		$this->getList();
	}
        
        /**
         * Add content
         */
        public function add() {
		$this->load->language('extension/maza/content_builder');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/content_builder');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_content_builder->addContent($this->request->post);

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
                        
			$this->response->redirect($this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
        /**
         * Edit content
         */
	public function edit() {
		$this->load->language('extension/maza/content_builder');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/content_builder');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_content_builder->editContent($this->request->get['content_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
        /**
         * Copy content
         */
        public function copy() {
		$this->load->language('extension/maza/content_builder');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/content_builder');
                $this->load->model('extension/maza/layout_builder');
                $this->load->model('extension/maza/skin');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $content_id) {
				$new_content_id = $this->model_extension_maza_content_builder->copyContent($content_id);
                                
                                // layout
                                foreach($this->model_extension_maza_skin->getSkins() as $skin_info){
                                    $content_builder = $this->model_extension_maza_layout_builder->getLayout($skin_info['skin_id'], 'content_builder', $content_id);
                                    $this->model_extension_maza_layout_builder->editLayout($skin_info['skin_id'], 'content_builder', $new_content_id, $content_builder);
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

			$this->response->redirect($this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
        public function delete() {
		$this->load->language('extension/maza/content_builder');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/content_builder');
                $this->load->model('extension/maza/layout_builder');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $content_id) {
				$this->model_extension_maza_content_builder->deleteContent($content_id);
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

			$this->response->redirect($this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
                    'href' => $this->url->link('extension/maza/content_builder/add', 'user_token=' . $this->session->data['user_token'], true),
                    'target' => false,
                    'form_target_id' => false,
                );
                
                $header_data['buttons'][] = array(
                    'id' => 'button-copy',
                    'name' => '',
                    'class' => 'btn-default',
                    'tooltip' => $this->language->get('button_copy'),
                    'icon' => 'fa-copy',
                    'formaction' => $this->url->link('extension/maza/content_builder/copy', 'user_token=' . $this->session->data['user_token'], true),
                    'target' => false,
                    'form_target_id' => 'form-mz-content',
                    'confirm' => $this->language->get('text_confirm')
                );
                
                $header_data['buttons'][] = array(
                    'id' => 'button-delete',
                    'name' => '',
                    'class' => 'btn-danger',
                    'tooltip' => $this->language->get('button_delete'),
                    'icon' => 'fa-trash',
                    'formaction' => $this->url->link('extension/maza/content_builder/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => 'form-mz-content',
                    'confirm' => $this->language->get('text_confirm')
                );

                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-content-builder',
                    'target' => '_blank'
                );
                $header_data['form_target_id'] = 'form-mz-content';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // List
		$data['contents'] = array();

		$filter_data = array(
                        'filter_name' => $filter_name,
                        'filter_status' => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$page_total = $this->model_extension_maza_content_builder->getTotalContents($filter_data);

		$results = $this->model_extension_maza_content_builder->getContents($filter_data);

		foreach ($results as $result) {
			$data['contents'][] = array(
				'content_id' => $result['content_id'],
				'name'    => $result['name'],
                                'status'  => $result['status']?$this->language->get('text_enabled'):$this->language->get('text_disabled'),
                                'edit'    => $this->url->link('extension/maza/content_builder/edit', 'user_token=' . $this->session->data['user_token'] . '&content_id=' . $result['content_id'] . $url, true),
                                'content'    => $this->url->link('extension/maza/content_builder/content', 'user_token=' . $this->session->data['user_token'] . '&content_id=' . $result['content_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
                $data['sort_status'] = $this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
                
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
		$pagination->url = $this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

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
                
		$this->response->setOutput($this->load->view('extension/maza/content_builder/list', $data));
	}
        
        /**
         * Form to add or edit Content
         */
        protected function getForm() {
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
                $header_data['title'] = !isset($this->request->get['content_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
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
                    'form_target_id' => 'form-mz-content',
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-cancel',
                    'name' => '',
                    'tooltip' => $this->language->get('button_cancel'),
                    'icon' => 'fa-reply',
                    'class' => 'btn-default',
                    'href' => $this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-content-builder',
                    'target' => '_blank'
                );
                $header_data['form_target_id'] = 'form-mz-content';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Setting
                $setting = array();
                $setting['name'] = '';
                $setting['status'] = true;
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } elseif(isset($this->request->get['content_id'])) {
                    $setting = array_merge($setting, $this->model_extension_maza_content_builder->getContent($this->request->get['content_id']));
                }

                // Data
                $data = array_merge($data, $setting);
                
                if (!isset($this->request->get['content_id'])) {
			$data['action'] = $this->url->link('extension/maza/content_builder/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/content_builder/edit', 'user_token=' . $this->session->data['user_token'] . '&content_id=' . $this->request->get['content_id'] . $url, true);
		}
                
                $data['user_token'] = $this->session->data['user_token'];
                
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
                if (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}
                if(isset($this->error['warning'])){
                        $data['warning'] = $this->error['warning'];
                }
                foreach($this->error as $key => $val){
                    $data['err_' . $key] = $val;
                }
                
                // Columns
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		$this->response->setOutput($this->load->view('extension/maza/content_builder/form', $data));
	}
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/maza/content_builder')) {
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
        
        protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/maza/content_builder')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
        
        protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'extension/maza/content_builder')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
        
        public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/content_builder');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
                                'filter_status' => 1,
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_maza_content_builder->getContents($filter_data);

			foreach ($results as $result) {
                                if(isset($this->request->get['skip_content_id']) && $this->request->get['skip_content_id'] == $result['content_id']){
                                    continue; // Skip
                                } else {
                                    $json[] = array(
                                            'content_id' => $result['content_id'],
                                            'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                                    );
                                }
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
        
        /**
         * Drag and drop content editor
         */
        public function content() {
                $this->load->model('extension/maza/content_builder');
                $this->load->model('tool/image');
                $this->load->model('extension/maza/opencart');
                $this->load->language('extension/maza/content_builder/content');

                $this->document->addStyle('view/javascript/maza/colorpicker/css/colorpicker.css');
                $this->document->addScript('view/javascript/maza/colorpicker/js/colorpicker.js');
                $this->document->addStyle('view/javascript/maza/jquery-ui-1.12.1.Interactions/jquery-ui.min.css');
                $this->document->addStyle('view/stylesheet/maza/mz_stylesheet.css');
                $this->document->addScript('view/javascript/maza/jquery-ui-1.12.1.Interactions/jquery-ui.min.js');
                $this->document->addScript('view/javascript/maza/layout_builder.js');
                $this->document->addScript('view/javascript/maza/mz_common.js');
                
                $url = '';
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                $url .= '&content_id=' . $this->request->get['content_id'];
                
                // Redirect to homepage in case of inactive maza engine
                if(!$this->config->get('maza_status')){
                    $this->response->redirect($this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . $url, true));
                }
                
                $data['cancel'] = $this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . $url, true);
                $data['skin_target'] = $this->url->link('extension/maza/content_builder/content', 'user_token=' . $this->session->data['user_token'] . '&content_id=' . $this->request->get['content_id'] . '&mz_theme_code=' . $this->request->get['mz_theme_code']);
                $data['export'] = $this->url->link('extension/maza/content_builder/export', 'user_token=' . $this->session->data['user_token'] . '&content_id=' . $this->request->get['content_id'] . $url, true);
                
                // Get skins
                $data['mz_skins'] = $this->model_extension_maza_skin->getSkins($this->mz_theme_config->get('theme_id'));
                $data['mz_skin_info'] = $this->model_extension_maza_skin->getSkin($this->mz_skin_config->get('skin_id'));
                
                // Get content info
                $content_info = $this->model_extension_maza_content_builder->getContent($this->request->get['content_id']);
                
                if($content_info){
                    $data['content_name'] = $content_info['name'];
                    $this->document->setTitle($content_info['name'] . ' | ' . $this->language->get('heading_title'));
                } else {
                    $data['warning'] = $this->language->get('error_content_deleted');
                    $this->document->setTitle($this->language->get('heading_title'));
                }
                
                // Layout entries
                $data['content_builder'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'content_builder', 'group_owner' => $content_info['content_id']]);
                
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
                                        'content_id' => $module['module_id'],
					'name'      => strip_tags($module['name']),
                                        'path'      => strip_tags($heading_title) . ' > ' . $module['name'],
					'code'      => $code . '.' .  $module['module_id'],
                                        'edit'      => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'] . $url, true)
				);
			}

			if ($this->config->has('module_' . $code . '_status') || $this->config->has($code . '_status') || $module_data) {
				$data['extensions'][] = array(
					'name'   => strip_tags($heading_title),
                                        'path'   => strip_tags($heading_title),
					'code'   => $code,
					'module' => $module_data,
                                        'edit'   => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . $url, true)
				);
			}
		}
                
                // Data
                $data['user_token'] = $this->session->data['user_token'];
                $data['url'] = $url;
                $data['content_id'] = (int)$this->request->get['content_id'];
                
                $data['device_views'] = array(
                    array('icon' => 'fa-desktop','code' => 'xl'),
                    array('icon' => 'fa-laptop', 'code' => 'lg'),
                    array('icon' => 'fa-tablet fa-rotate-270','code' => 'md'),
                    array('icon' => 'fa-tablet', 'code' => 'sm'),
                    array('icon' => 'fa-mobile', 'code' => 'xs')
                );
                
                
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
                $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                
		$this->response->setOutput($this->load->view('extension/maza/content_builder/content', $data));
        }
        
        /**
         * Submit content content
         */
        public function submitContentForm() {
                $json = array();
                
                $this->load->language('extension/maza/content_builder/content');
                $this->load->model('extension/maza/layout_builder');
                $this->load->model('extension/maza/content_builder');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->get['content_id'] && $this->validateContent()){
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
                if(isset($data['content_builder'])){
                    $content_builder = $data['content_builder'];
                } else {
                    $content_builder = array();
                }
                $this->model_extension_maza_layout_builder->editLayout($this->mz_skin_config->get('skin_id'), 'content_builder', $this->request->get['content_id'], $content_builder);
        }
        
        /**
         * Duplicate skin layout from current skin to selected skin
         */
        public function duplicateLayout() {
                $json = array();
                
                $this->load->language('extension/maza/layout_builder');
                $this->load->model('extension/maza/layout_builder');
                $this->load->model('extension/maza/content_builder');
                
                if($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->get['content_id']) && isset($this->request->post['duplicate_to_skin_id']) && $this->validateContent()){
                    // layout
                    $content_builder = $this->model_extension_maza_layout_builder->getLayout($this->mz_skin_config->get('skin_id'), 'content_builder', $this->request->get['content_id']);
                    $this->model_extension_maza_layout_builder->editLayout($this->request->post['duplicate_to_skin_id'], 'content_builder', $this->request->get['content_id'], $content_builder);
                    

                    $json['success'] = $this->language->get('text_duplicate_layout_success');
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                }
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        /**
         * Validate content
         */
        protected function validateContent() {
		if (!$this->user->hasPermission('modify', 'extension/maza/content_builder')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
                
                // Get module info
                $content_info = $this->model_extension_maza_content_builder->getContent($this->request->get['content_id']);
                
                if(empty($this->error['warning']) && (!$content_info || !$this->config->get('maza_status'))){
                    $this->error['warning'] = $this->language->get('error_content_deleted');
                }

		return !$this->error;
	}

        /**
         * Export layout
         */
        public function export() {
                $data = array();
                
                $this->load->model('extension/maza/content_builder');
                
                $content_info = $this->model_extension_maza_content_builder->getContent($this->request->get['content_id']);
                
                if($content_info){
                    $data['content_builder'] = $this->load->controller('extension/maza/layout_builder/getLayout', ['group' => 'content_builder', 'group_owner' => $content_info['content_id']]);
                    
                    header('Content-Type: application/json; charset=utf-8');
                    header('Content-disposition: attachment; filename="layout.content_builder.' . $this->mz_skin_config->get('skin_code') . '(' . $content_info['name'] . ').json"');
                    
                    echo json_encode(['type' => 'content_builder', 'data' => $data]);
                } else {
                    $url = '';
                
                    if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                    }
                    if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                    }
                    
                    $this->response->redirect($this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token']  . $url, true));
                }
	}

        /**
         * Import layout
         */
        public function import(){
                $this->load->language('extension/maza/layout_builder');
                $this->load->language('extension/maza/content_builder');

                $json = array();

                // Check user has permission
                if (!$this->user->hasPermission('modify', 'extension/maza/content_builder')) {
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
                                
                                if($data && $data['type'] == 'content_builder'){
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
