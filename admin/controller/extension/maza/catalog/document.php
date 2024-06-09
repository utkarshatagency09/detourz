<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaCatalogDocument extends Controller {
    private $error = array();
        
    public function index(): void {
		$this->load->language('extension/maza/catalog/document');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/document');

		$this->getList();
	}
        
    public function add(): void {
		$this->load->language('extension/maza/catalog/document');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/document');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDocument()) {
			$this->model_extension_maza_catalog_document->addDocument($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
            if(isset($this->request->get['filter_status'])){
                    $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if(isset($this->request->get['filter_store_id'])){
                    $url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
            }
            if(isset($this->request->get['filter_route'])){
                    $url .= '&filter_route=' . urlencode(html_entity_decode($this->request->get['filter_route'], ENT_QUOTES, 'UTF-8'));
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
                        
			$this->response->redirect($this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
	public function edit(): void {
		$this->load->language('extension/maza/catalog/document');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/document');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDocument()) {
			$this->model_extension_maza_catalog_document->editDocument($this->request->get['document_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_status'])){
                    $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if(isset($this->request->get['filter_store_id'])){
                    $url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
            }
            if(isset($this->request->get['filter_route'])){
                    $url .= '&filter_route=' . urlencode(html_entity_decode($this->request->get['filter_route'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
    
    public function delete(): void {
		$this->load->language('extension/maza/catalog/document');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/catalog/document');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $document_id) {
				$this->model_extension_maza_catalog_document->deleteDocument($document_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
            if(isset($this->request->get['filter_status'])){
                    $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if(isset($this->request->get['filter_store_id'])){
                    $url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
            }
            if(isset($this->request->get['filter_route'])){
                    $url .= '&filter_route=' . urlencode(html_entity_decode($this->request->get['filter_route'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
    public function getList(): void {
        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
        if (isset($this->request->get['filter_store_id'])) {
            $filter_store_id = $this->request->get['filter_store_id'];
		} else {
			$filter_store_id = '';
		}
        if (isset($this->request->get['filter_route'])) {
            $filter_route = $this->request->get['filter_route'];
		} else {
			$filter_route = '';
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'd.route';
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
        
        if(isset($this->request->get['filter_status'])){
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if(isset($this->request->get['filter_store_id'])){
            $url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
        }
        if(isset($this->request->get['filter_route'])){
            $url .= '&filter_route=' . urlencode(html_entity_decode($this->request->get['filter_route'], ENT_QUOTES, 'UTF-8'));
        }
        if(isset($this->request->get['mz_theme_code'])){
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        if(isset($this->request->get['mz_skin_id'])){
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
                
        // Header
        $header_data = array();
        $header_data['theme_select'] = false;
        $header_data['skin_select'] = false;
        $header_data['title'] = $this->language->get('heading_title');

        // $this->load->language('extension/maza/common/column_left');

        // $header_data['menu'] = array();
        // if ($this->user->hasPermission('access', 'extension/maza/catalog/product')) $header_data['menu'][] = array('name' => $this->language->get('tab_product'), 'id' => 'tab-mz-product', 'href' => $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // if ($this->user->hasPermission('access', 'extension/maza/catalog/manufacturer')) $header_data['menu'][] = array('name' => $this->language->get('tab_manufacturer'), 'id' => 'tab-mz-manufacturer', 'href' => $this->url->link('extension/maza/catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // if ($this->user->hasPermission('access', 'extension/maza/catalog/product_label')) $header_data['menu'][] = array('name' => $this->language->get('tab_product_label'), 'id' => 'tab-mz-product-label', 'href' => $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // if ($this->user->hasPermission('access', 'extension/maza/catalog/data')) $header_data['menu'][] = array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // $header_data['menu'][] = array('name' => $this->language->get('tab_document'), 'id' => 'tab-mz-document', 'href' => false);
        // if ($this->user->hasPermission('access', 'extension/maza/catalog/redirect')) $header_data['menu'][] = array('name' => $this->language->get('tab_redirect'), 'id' => 'tab-mz-redirect', 'href' => $this->url->link('extension/maza/catalog/redirect', 'user_token=' . $this->session->data['user_token'] . $url, true));
        
        // $header_data['menu_active'] = 'tab-mz-document';

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
            'class' => 'btn-primary',
            'tooltip' => $this->language->get('button_add'),
            'icon' => 'fa-plus',
            'href' => $this->url->link('extension/maza/catalog/document/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => false,
            'form_target_id' => false,
        );
        
        $header_data['buttons'][] = array(
            'id' => 'button-delete',
            'name' => '',
            'class' => 'btn-danger',
            'tooltip' => $this->language->get('button_delete'),
            'icon' => 'fa-trash',
            'formaction' => $this->url->link('extension/maza/catalog/document/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => FALSE,
            'form_target_id' => 'form-mz-document',
            'confirm' => $this->language->get('text_confirm')
        );

        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#page-document',
            'target' => '_blank'
        );

        $header_data['form_target_id'] = 'form-mz-document';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

        $this->load->model('setting/store');
                
        // List
		$data['documents'] = array();

		$filter_data = array(
            'filter_route' => $filter_route,
            'filter_store_id' => $filter_store_id,
            'filter_status' => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$page_total = $this->model_extension_maza_catalog_document->getTotalDocuments($filter_data);

		$results = $this->model_extension_maza_catalog_document->getDocuments($filter_data);

		foreach ($results as $result) {
            $store_info = $this->model_setting_store->getStore($result['store_id']);

            if ($store_info) {
                $store_name = $store_info['name'];
            } else {
                $store_name = $this->config->get('config_name');
            }

			$data['documents'][] = array(
				'document_id'   => $result['document_id'],
				'route'         => $result['route'],
                'store'         => $store_name,
                'status'        => $result['status'],
                'date_modified' => $result['date_modified'],
                'edit'    => $this->url->link('extension/maza/catalog/document/edit', 'user_token=' . $this->session->data['user_token'] . '&document_id=' . $result['document_id'] . $url, true),
			);
		}

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
                
        if(isset($this->request->get['filter_status'])){
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if(isset($this->request->get['filter_store_id'])){
            $url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
        }
        if(isset($this->request->get['filter_route'])){
            $url .= '&filter_route=' . urlencode(html_entity_decode($this->request->get['filter_route'], ENT_QUOTES, 'UTF-8'));
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

		$data['sort_route'] = $this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . '&sort=d.route' . $url, true);
        $data['sort_status'] = $this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . '&sort=d.status' . $url, true);
        $data['sort_date_modified'] = $this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . '&sort=d.date_modified' . $url, true);
                
        $data['sort'] = $sort;
		$data['order'] = $order;

		$url = '';
                
        if(isset($this->request->get['filter_status'])){
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if(isset($this->request->get['filter_store_id'])){
            $url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
        }
        if(isset($this->request->get['filter_route'])){
            $url .= '&filter_route=' . urlencode(html_entity_decode($this->request->get['filter_route'], ENT_QUOTES, 'UTF-8'));
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
		$pagination->url = $this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($page_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($page_total - $this->config->get('config_limit_admin'))) ? $page_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $page_total, ceil($page_total / $this->config->get('config_limit_admin')));

        $data['filter_route'] = $filter_route;
        $data['filter_store_id'] = $filter_store_id;
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
                
		$this->response->setOutput($this->load->view('extension/maza/catalog/document_list', $data));
	}

    protected function getForm(): void {
        $url = '';
        
        if(isset($this->request->get['filter_status'])){
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if(isset($this->request->get['filter_store_id'])){
            $url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
        }
        if(isset($this->request->get['filter_route'])){
            $url .= '&filter_route=' . urlencode(html_entity_decode($this->request->get['filter_route'], ENT_QUOTES, 'UTF-8'));
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
        $header_data['title'] = !isset($this->request->get['document_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $header_data['theme_select'] = $header_data['skin_select'] = false;
        $header_data['menu'] = array(
            array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
            array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
            array('name' => $this->language->get('tab_ogp'), 'id' => 'tab-mz-ogp', 'href' => false),
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
            'form_target_id' => 'form-mz-document',
        );
        $header_data['buttons'][] = array(
            'id' => 'button-cancel',
            'name' => '',
            'tooltip' => $this->language->get('button_cancel'),
            'icon' => 'fa-reply',
            'class' => 'btn-default',
            'href' => $this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => FALSE,
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#page-document',
            'target' => '_blank'
        );
        $header_data['form_target_id'] = 'form-mz-document';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
        
        // Setting
        $setting = array();
        $setting['status']      = 1;
        $setting['route']       = '';
        $setting['store_id']    = 0;
        $setting['og_image_width'] = '1200';
        $setting['og_image_height'] = '627';
        $setting['og_video'] = '';
        $setting['document_description'] = array();
        
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $setting = array_merge($setting, $this->request->post);
        } elseif(isset($this->request->get['document_id'])) {
            $setting = array_merge($setting, $this->model_extension_maza_catalog_document->getDocument($this->request->get['document_id']));
            $setting['document_description'] = $this->model_extension_maza_catalog_document->getDocumentDescriptions($this->request->get['document_id']);
        }
       
        // Data
        $data = array_merge($data, $setting);
                
        if (!isset($this->request->get['document_id'])) {
			$data['action'] = $this->url->link('extension/maza/catalog/document/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/catalog/document/edit', 'user_token=' . $this->session->data['user_token'] . '&document_id=' . $this->request->get['document_id'] . $url, true);
		}

        $this->load->model('tool/image');

        $data['thumb_og_image'] = array();

        foreach($setting['document_description'] as $language_id => $description){
            if($description['og_image']){
                $data['thumb_og_image'][$language_id] = $this->model_tool_image->resize($description['og_image'], 100, 100);
            }
        }

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

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['placeholder_image']  = $this->model_tool_image->resize('no_image.png', 100, 100);
                
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
                
		$this->response->setOutput($this->load->view('extension/maza/catalog/document_form', $data));
	}
        
    protected function validateDocument(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/catalog/document')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        if ((strlen($this->request->post['route']) < 1) || (strlen($this->request->post['route']) > 64)) {
            $this->error['route'] = $this->language->get('error_route');
        } else {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_document WHERE route = '" . $this->db->escape($this->request->post['route']) . "' AND store_id = '" . (int)$this->request->post['store_id'] . "'");

            if ($query->num_rows > 0 && (!isset($this->request->get['document_id']) || $this->request->get['document_id'] != $query->row['document_id'])) {
                $this->error['route'] = $this->language->get('error_route_unique');
            }
        }
                
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
        
    protected function validateDelete(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/catalog/document')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}
