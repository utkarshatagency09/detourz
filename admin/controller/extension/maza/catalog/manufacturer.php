<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2021, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaCatalogManufacturer extends Controller {
        private $error = array();
    
        public function index(): void {
			$this->load->language('extension/maza/catalog/manufacturer');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('catalog/manufacturer');

			$this->getList();
		}
        
		public function edit(): void {
			$this->load->language('extension/maza/catalog/manufacturer');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('extension/maza/catalog/manufacturer');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_extension_maza_catalog_manufacturer->editManufacturer($this->request->get['manufacturer_id'], $this->request->post);

				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';

				if (isset($this->request->get['filter_name'])) {
						$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
				if(isset($this->request->get['mz_theme_code'])){
						$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
				}
				if(isset($this->request->get['mz_skin_id'])){
						$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
				}

				// $this->response->redirect($this->url->link('extension/maza/catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getForm();
		}
        
		public function delete(): void {
			$this->load->language('extension/maza/catalog/manufacturer');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('catalog/manufacturer');

			if (isset($this->request->post['selected']) && $this->validateDelete()) {
				foreach ($this->request->post['selected'] as $manufacturer_id) {
					$this->model_catalog_manufacturer->deleteManufacturer($manufacturer_id);
				}

				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';

				if (isset($this->request->get['filter_name'])) {
						$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
				if(isset($this->request->get['mz_theme_code'])){
						$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
				}
				if(isset($this->request->get['mz_skin_id'])){
						$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
				}

				$this->response->redirect($this->url->link('extension/maza/catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getList();
		}
        
        
        protected function getList(): void {
			$this->load->model('tool/image');
			$this->load->model('extension/maza/catalog/manufacturer');
		
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
			} else {
				$sort = 'm.name';
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
			if(isset($this->request->get['mz_theme_code'])){
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if(isset($this->request->get['mz_skin_id'])){
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}
			
			// Header
			$header_data = array();
			$header_data['title'] = $this->language->get('text_list');
			$header_data['theme_select'] = $header_data['skin_select'] = false;

			// $this->load->language('extension/maza/common/column_left');
			
			// $header_data['menu'] = array();
			// if ($this->user->hasPermission('access', 'extension/maza/catalog/product')) $header_data['menu'][] = array('name' => $this->language->get('tab_product'), 'id' => 'tab-mz-product', 'href' => $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
			// $header_data['menu'][] = array('name' => $this->language->get('tab_manufacturer'), 'id' => 'tab-mz-manufacturer', 'href' => false);
			// if ($this->user->hasPermission('access', 'extension/maza/catalog/product_label')) $header_data['menu'][] = array('name' => $this->language->get('tab_product_label'), 'id' => 'tab-mz-product-label', 'href' => $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true));
			// if ($this->user->hasPermission('access', 'extension/maza/catalog/data')) $header_data['menu'][] = array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . $url, true));
			// if ($this->user->hasPermission('access', 'extension/maza/catalog/document')) $header_data['menu'][] = array('name' => $this->language->get('tab_document'), 'id' => 'tab-mz-document', 'href' => $this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . $url, true));
			// if ($this->user->hasPermission('access', 'extension/maza/catalog/redirect')) $header_data['menu'][] = array('name' => $this->language->get('tab_redirect'), 'id' => 'tab-mz-redirect', 'href' => $this->url->link('extension/maza/catalog/redirect', 'user_token=' . $this->session->data['user_token'] . $url, true));
			
			// $header_data['menu_active'] = 'tab-mz-manufacturer';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
			
			$header_data['buttons'][] = array(
				'id' => 'button-add',
				'name' => '',
				'class' => 'btn-warning',
				'tooltip' => $this->language->get('button_add'),
				'icon' => 'fa-plus',
				'href' => $this->url->link('catalog/manufacturer/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
				'target' => 'blank',
				'form_target_id' => false,
			);
			$header_data['buttons'][] = array(
				'id' => 'button-delete',
				'name' => '',
				'tooltip' => $this->language->get('button_delete'),
				'icon' => 'fa-trash',
				'class' => 'btn-danger',
				'formaction' => $this->url->link('extension/maza/catalog/manufacturer/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
				'href' => FALSE,
				'target' => FALSE,
				'form_target_id' => 'form-mz-manufacturer',
				'confirm' => $this->language->get('text_confirm')
			);
			$header_data['form_target_id'] = 'form-mz-manufacturer';
			
			$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
			
			// Manufacturer list
			
			$data['manufacturers'] = array();

			$filter_data = array(
				'filter_name'	  => $filter_name,
				'sort'            => $sort,
				'order'           => $order,
				'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
				'limit'           => $this->config->get('config_limit_admin')
			);

			$manufacturer_total = $this->model_extension_maza_catalog_manufacturer->getTotalManufacturers($filter_data);

			$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);

			foreach ($results as $result) {
				if (is_file(DIR_IMAGE . $result['image'])) {
					$image = $this->model_tool_image->resize($result['image'], 40, 40);
				} else {
					$image = $this->model_tool_image->resize('no_image.png', 40, 40);
				}

				$data['manufacturers'][] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'image'      => $image,
					'name'       => $result['name'],
					'sort_order' => $result['sort_order'],
					'edit'       => $this->url->link('extension/maza/catalog/manufacturer/edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $result['manufacturer_id'] . $url, true),
					'edit2'      => $this->url->link('catalog/manufacturer/edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $result['manufacturer_id'] . $url, true)
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
					
			// Sort order
			$url = '';
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

			$data['sort_name'] = $this->url->link('extension/maza/catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=m.name' . $url, true);
			$data['sort_sort_order'] = $this->url->link('extension/maza/catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=m.sort_order' . $url, true);
					
			$data['sort'] = $sort;
			$data['order'] = $order;
					
			// Pagination
			$url = '';
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
			$pagination->total = $manufacturer_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('extension/maza/catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($manufacturer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($manufacturer_total - $this->config->get('config_limit_admin'))) ? $manufacturer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $manufacturer_total, ceil($manufacturer_total / $this->config->get('config_limit_admin')));
					
			$data['filter_name'] = $filter_name;
					
			$data['default_url'] = '&user_token=' . $this->session->data['user_token'];
			if(isset($this->request->get['mz_theme_code'])){
					$data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if(isset($this->request->get['mz_skin_id'])){
					$data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$data['user_token'] = $this->session->data['user_token'];
			
			// Columns
			$data['header'] = $this->load->controller('extension/maza/common/header/main');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
			$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
					
			$this->response->setOutput($this->load->view('extension/maza/catalog/manufacturer_list', $data));
        }
        
        protected function getForm() {
			$this->load->model('catalog/manufacturer');
			$this->load->model('extension/maza/catalog/manufacturer');
                
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
			$header_data['title'] = $this->language->get('text_edit');
			$header_data['theme_select'] = $header_data['skin_select'] = false;
			$header_data['menu'] = array(
				array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
				array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
				array('name' => $this->language->get('tab_design'), 'id' => 'tab-mz-design', 'href' => false),
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
				'form_target_id' => 'form-mz-manufacturer',
			);
			$header_data['buttons'][] = array(
				'id' => 'button-preview',
				'name' => '',
				'tooltip' => $this->language->get('button_preview'),
				'icon' => 'fa-eye',
				'class' => 'btn-info',
				'href' => $this->config->get('mz_store_url') . 'index.php?route=product/manufacturer/info&manufacturer_id=' . $this->request->get['manufacturer_id'],
				'target' => '_blank',
				'form_target_id' => false,
			);
			$header_data['buttons'][] = array(
				'id' => 'button-edit',
				'name' => '',
				'tooltip' => $this->language->get('button_edit'),
				'icon' => 'fa-pencil',
				'class' => 'btn-info',
				'href' => $this->url->link('catalog/manufacturer/edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $this->request->get['manufacturer_id'] . $url, true),
				'target' => FALSE,
				'form_target_id' => false,
			);
			$header_data['buttons'][] = array(
				'id' => 'button-cancel',
				'name' => '',
				'tooltip' => $this->language->get('button_cancel'),
				'icon' => 'fa-reply',
				'class' => 'btn-default',
				'href' => $this->url->link('extension/maza/catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true),
				'target' => FALSE,
				'form_target_id' => false,
			);
			$header_data['form_target_id'] = 'form-mz-manufacturer';
			
			$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			$this->document->setTitle($manufacturer_info['name']);
			
			// Setting
			$setting = array();
			$setting['mz_featured'] = '0';
			$setting['manufacturer_description'] = array();
			$setting['manufacturer_layout'] = array();
			
			if($this->request->server['REQUEST_METHOD'] == 'POST'){
				$setting = array_merge($setting, $this->request->post);
			} else {
				$setting = array_merge($setting, $manufacturer_info);
				$setting['manufacturer_description'] = $this->model_extension_maza_catalog_manufacturer->getManufacturerDescriptions($this->request->get['manufacturer_id']);
				$setting['manufacturer_layout'] = $this->model_extension_maza_catalog_manufacturer->getManufacturerLayouts($this->request->get['manufacturer_id']);
			}

			// Data
			$data = array_merge($data, $setting);
			
			if (!isset($this->request->get['manufacturer_id'])) {
				$data['action'] = $this->url->link('extension/maza/catalog/manufacturer/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
			} else {
				$data['action'] = $this->url->link('extension/maza/catalog/manufacturer/edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $this->request->get['manufacturer_id'] . $url, true);
			}

			$this->load->model('localisation/language');
			$data['languages'] = $this->model_localisation_language->getLanguages();

			$this->load->model('design/layout');
			$data['layouts'] = $this->model_design_layout->getLayouts();

			$this->load->model('setting/store');

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
			
			$data['user_token'] = $this->session->data['user_token'];
			
			$data['default_url'] = '&user_token=' . $this->session->data['user_token'];
			if(isset($this->request->get['mz_theme_code'])){
				$data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if(isset($this->request->get['mz_skin_id'])){
				$data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}
                
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
                
			$this->response->setOutput($this->load->view('extension/maza/catalog/manufacturer_form', $data));
		}
        
        protected function validateForm() {
			if (!$this->user->hasPermission('modify', 'extension/maza/catalog/manufacturer')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
			
			return !$this->error;
		}
        
        protected function validateDelete() {
			if (!$this->user->hasPermission('modify', 'extension/maza/catalog/manufacturer')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}

			return !$this->error;
		}
}
