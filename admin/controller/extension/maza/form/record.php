<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaFormRecord extends Controller {
    private $error = array();
        
    public function index(): void {
		$this->load->language('extension/maza/form/record');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form/record');

		$this->getList();
	}
    
    public function delete(): void {
		$this->load->language('extension/maza/form/record');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form/record');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $form_record_id) {
				$this->model_extension_maza_form_record->deleteRecord($form_record_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
            if(isset($this->request->get['filter_form_id'])){
                    $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
            }
            if(isset($this->request->get['filter_language_id'])){
                    $url .= '&filter_language_id=' . $this->request->get['filter_language_id'];
            }
            if(isset($this->request->get['filter_customer_id'])){
                    $url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
            }
            if(isset($this->request->get['filter_date_min'])){
                    $url .= '&filter_date_min=' . $this->request->get['filter_date_min'];
            }
            if(isset($this->request->get['filter_date_max'])){
                    $url .= '&filter_date_max=' . $this->request->get['filter_date_max'];
            }
            if(isset($this->request->get['filter_product_id'])){
                    $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
            }
            if(isset($this->request->get['filter_category_id'])){
                    $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
            }
            if(isset($this->request->get['filter_manufacturer_id'])){
                    $url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
            }
            if(isset($this->request->get['filter_field'])){
                    $url .= '&filter_field[name]=' . $this->request->get['filter_field']['name'];
                    $url .= '&filter_field[match]=' . $this->request->get['filter_field']['match'];
                    $url .= '&filter_field[value]=' . $this->request->get['filter_field']['value'];
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

			$this->response->redirect($this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
    public function getList(): void {
        if (isset($this->request->get['filter_form_id'])) {
            $filter_form_id = $this->request->get['filter_form_id'];
		} else {
			$filter_form_id = null;
		}
        if (isset($this->request->get['filter_language_id'])) {
            $filter_language_id = $this->request->get['filter_language_id'];
		} else {
			$filter_language_id = null;
		}
        if (isset($this->request->get['filter_customer_id'])) {
            $filter_customer_id = $this->request->get['filter_customer_id'];
		} else {
			$filter_customer_id = null;
		}
        if (isset($this->request->get['filter_date_min'])) {
            $filter_date_min = $this->request->get['filter_date_min'];
		} else {
			$filter_date_min = null;
		}
        if (isset($this->request->get['filter_date_max'])) {
            $filter_date_max = $this->request->get['filter_date_max'];
		} else {
			$filter_date_max = null;
		}
        if (isset($this->request->get['filter_product_id'])) {
            $filter_product_id = $this->request->get['filter_product_id'];
		} else {
			$filter_product_id = null;
		}
        if (isset($this->request->get['filter_category_id'])) {
            $filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = null;
		}
        if (isset($this->request->get['filter_manufacturer_id'])) {
            $filter_manufacturer_id = $this->request->get['filter_manufacturer_id'];
		} else {
			$filter_manufacturer_id = null;
		}
        if (isset($this->request->get['filter_field'])) {
            $filter_field = $this->request->get['filter_field'];
		} else {
			$filter_field = null;
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_added';
		}
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
                
        $url = '';
        
        if(isset($this->request->get['filter_form_id'])){
            $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
        }
        if(isset($this->request->get['filter_language_id'])){
            $url .= '&filter_language_id=' . $this->request->get['filter_language_id'];
        }
        if(isset($this->request->get['filter_customer_id'])){
            $url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
        }
        if(isset($this->request->get['filter_date_min'])){
            $url .= '&filter_date_min=' . $this->request->get['filter_date_min'];
        }
        if(isset($this->request->get['filter_date_max'])){
            $url .= '&filter_date_max=' . $this->request->get['filter_date_max'];
        }
        if(isset($this->request->get['filter_product_id'])){
            $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
        }
        if(isset($this->request->get['filter_category_id'])){
            $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
        }
        if(isset($this->request->get['filter_manufacturer_id'])){
            $url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
        }
        if(isset($this->request->get['filter_field'])){
            $url .= '&filter_field[name]=' . $this->request->get['filter_field']['name'];
            $url .= '&filter_field[match]=' . $this->request->get['filter_field']['match'];
            $url .= '&filter_field[value]=' . $this->request->get['filter_field']['value'];
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

        // $header_data['menu'] = array();
        // if ($this->user->hasPermission('access', 'extension/maza/form')) $header_data['menu'][] = array('name' => $this->language->get('tab_form'), 'id' => 'tab-mz-form', 'href' => $this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // if ($this->user->hasPermission('access', 'extension/maza/form/field')) $header_data['menu'][] = array('name' => $this->language->get('tab_field'), 'id' => 'tab-mz-field', 'href' => $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // $header_data['menu'][] = array('name' => $this->language->get('tab_record'), 'id' => 'tab-mz-record', 'href' => false);

        // $header_data['menu_active'] = 'tab-mz-record';

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
            'id' => 'button-delete',
            'name' => '',
            'class' => 'btn-danger',
            'tooltip' => $this->language->get('button_delete'),
            'icon' => 'fa-trash',
            'formaction' => $this->url->link('extension/maza/form/record/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => FALSE,
            'form_target_id' => 'form-mz-record',
            'confirm' => $this->language->get('text_confirm')
        );

        $header_data['form_target_id'] = 'form-mz-record';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
        // List
		$data['records'] = array();

		$filter_data = array(
            'filter_form_id'        => $filter_form_id,
            'filter_language_id'    => $filter_language_id,
            'filter_customer_id'    => $filter_customer_id,
            'filter_date_min'       => $filter_date_min,
            'filter_date_max'       => $filter_date_max,
            'filter_product_id'     => $filter_product_id,
            'filter_category_id'    => $filter_category_id,
            'filter_manufacturer_id'=> $filter_manufacturer_id,
            'filter_field'          => $filter_field,
			'sort'                  => $sort,
			'order'                 => $order,
			'start'                 => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                 => $this->config->get('config_limit_admin')
		);

		$page_total = $this->model_extension_maza_form_record->getTotalRecords($filter_data);

		$results = $this->model_extension_maza_form_record->getRecords($filter_data);

		foreach ($results as $result) {
			$data['records'][] = array(
				'form_record_id'=> $result['form_record_id'],
                'form'          => $result['form'],
                'customer'      => $result['customer']?:$this->language->get('text_guest'),
                'language'      => $result['language'],
                'ip_address'    => $result['ip_address'],
                'date_added'    => $result['date_added'],
                'view'          => $this->url->link('extension/maza/form/record/view', 'user_token=' . $this->session->data['user_token'] . '&form_record_id=' . $result['form_record_id'] . $url, true),
			);
		}

        $this->load->model('extension/maza/form');

        $data['fields'] = array();

        if($filter_form_id){
            $fields = $this->model_extension_maza_form->getFields($filter_form_id);

            foreach($fields as $field){
                $data['fields'][] = array(
                    'name'  => $field['name'],
                    'label' => $field['label'],
                );
            }
        }

        $data['match_types'] = array(
            array('id' => 'contain', 'name' => $this->language->get('text_contain')),
            array('id' => 'exact', 'name' => $this->language->get('text_exact')),
            array('id' => 'regexp', 'name' => $this->language->get('text_regexp')),
        );

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
              
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
                
        if(isset($this->request->get['filter_form_id'])){
            $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
        }
        if(isset($this->request->get['filter_language_id'])){
            $url .= '&filter_language_id=' . $this->request->get['filter_language_id'];
        }
        if(isset($this->request->get['filter_customer_id'])){
            $url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
        }
        if(isset($this->request->get['filter_date_min'])){
            $url .= '&filter_date_min=' . $this->request->get['filter_date_min'];
        }
        if(isset($this->request->get['filter_date_max'])){
            $url .= '&filter_date_max=' . $this->request->get['filter_date_max'];
        }
        if(isset($this->request->get['filter_product_id'])){
            $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
        }
        if(isset($this->request->get['filter_category_id'])){
            $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
        }
        if(isset($this->request->get['filter_manufacturer_id'])){
            $url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
        }
        if(isset($this->request->get['filter_field'])){
            $url .= '&filter_field[name]=' . $this->request->get['filter_field']['name'];
            $url .= '&filter_field[match]=' . $this->request->get['filter_field']['match'];
            $url .= '&filter_field[value]=' . $this->request->get['filter_field']['value'];
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

		$data['sort_form'] = $this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . '&sort=form' . $url, true);
        $data['sort_customer'] = $this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
        $data['sort_language'] = $this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . '&sort=r.language_id' . $url, true);
        $data['sort_ip_address'] = $this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . '&sort=r.ip_address' . $url, true);
        $data['sort_date_added'] = $this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . '&sort=r.date_added' . $url, true);
                
        $data['sort'] = $sort;
		$data['order'] = $order;

		$url = '';
                
        if(isset($this->request->get['filter_form_id'])){
            $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
        }
        if(isset($this->request->get['filter_language_id'])){
            $url .= '&filter_language_id=' . $this->request->get['filter_language_id'];
        }
        if(isset($this->request->get['filter_customer_id'])){
            $url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
        }
        if(isset($this->request->get['filter_date_min'])){
            $url .= '&filter_date_min=' . $this->request->get['filter_date_min'];
        }
        if(isset($this->request->get['filter_date_max'])){
            $url .= '&filter_date_max=' . $this->request->get['filter_date_max'];
        }
        if(isset($this->request->get['filter_product_id'])){
            $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
        }
        if(isset($this->request->get['filter_category_id'])){
            $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
        }
        if(isset($this->request->get['filter_manufacturer_id'])){
            $url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
        }
        if(isset($this->request->get['filter_field'])){
            $url .= '&filter_field[name]=' . $this->request->get['filter_field']['name'];
            $url .= '&filter_field[match]=' . $this->request->get['filter_field']['match'];
            $url .= '&filter_field[value]=' . $this->request->get['filter_field']['value'];
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
		$pagination->url = $this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($page_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($page_total - $this->config->get('config_limit_admin'))) ? $page_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $page_total, ceil($page_total / $this->config->get('config_limit_admin')));

        $data['filter_form_id']     = $filter_form_id;
        $data['filter_language_id'] = $filter_language_id;
        $data['filter_customer_id'] = $filter_customer_id;
        $data['filter_date_min']    = $filter_date_min;
        $data['filter_date_max']    = $filter_date_max;
        $data['filter_product_id']  = $filter_product_id;
        $data['filter_category_id'] = $filter_category_id;
        $data['filter_manufacturer_id'] = $filter_manufacturer_id;
        $data['filter_field']       = $filter_field;

        if($filter_form_id){
            $this->load->model('extension/maza/form');

            $form_info = $this->model_extension_maza_form->getForm($filter_form_id);

            if ($form_info) {
                $data['filter_form'] = $form_info['name'];
            }
        }
        if($filter_customer_id){
            $this->load->model('customer/customer');

            $customer_info = $this->model_customer_customer->getCustomer($filter_customer_id);

            if ($customer_info) {
                $data['filter_customer'] = $customer_info['firstname'] . ' ' . $customer_info['lastname'];
            }
        }
        if($filter_product_id){
            $this->load->model('catalog/product');

            $product_info = $this->model_catalog_product->getProduct($filter_product_id);

            if ($product_info) {
                $data['filter_product'] = $product_info['name'];
            }
        }
        if($filter_category_id){
            $this->load->model('catalog/category');

            $category_info = $this->model_catalog_category->getCategory($filter_category_id);
            
            if ($category_info) {
                $data['filter_category'] = $category_info['name'];
            }
        }
        if($filter_manufacturer_id){
            $this->load->model('catalog/manufacturer');

            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($filter_manufacturer_id);

            if ($manufacturer_info) {
                $data['filter_manufacturer'] = $manufacturer_info['name'];
            }
        }
        
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
                
		$this->response->setOutput($this->load->view('extension/maza/form/record_list', $data));
	}
    
    public function view(): void {
		$this->load->language('extension/maza/form/record');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';
        
        if(isset($this->request->get['filter_form_id'])){
            $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
        }
        if(isset($this->request->get['filter_language_id'])){
            $url .= '&filter_language_id=' . $this->request->get['filter_language_id'];
        }
        if(isset($this->request->get['filter_customer_id'])){
            $url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
        }
        if(isset($this->request->get['filter_date_min'])){
            $url .= '&filter_date_min=' . $this->request->get['filter_date_min'];
        }
        if(isset($this->request->get['filter_date_max'])){
            $url .= '&filter_date_max=' . $this->request->get['filter_date_max'];
        }
        if(isset($this->request->get['filter_product_id'])){
            $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
        }
        if(isset($this->request->get['filter_category_id'])){
            $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
        }
        if(isset($this->request->get['filter_manufacturer_id'])){
            $url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
        }
        if(isset($this->request->get['filter_field'])){
            $url .= '&filter_field[name]=' . $this->request->get['filter_field']['name'];
            $url .= '&filter_field[match]=' . $this->request->get['filter_field']['match'];
            $url .= '&filter_field[value]=' . $this->request->get['filter_field']['value'];
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
        $header_data['title'] = $this->language->get('text_view');
        $header_data['theme_select'] = $header_data['skin_select'] = false;
        $header_data['menu'] = array(
            array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
            array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
        );

        $header_data['menu_active'] = 'tab-mz-general';

        $header_data['buttons'][] = array(
            'id' => 'button-delete',
            'name' => '',
            'class' => 'btn-danger',
            'tooltip' => $this->language->get('button_delete'),
            'icon' => 'fa-trash',
            'formaction' => $this->url->link('extension/maza/form/record/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => FALSE,
            'form_target_id' => 'form-mz-record',
            'confirm' => $this->language->get('text_confirm')
        );
        $header_data['buttons'][] = array(
            'id' => 'button-cancel',
            'name' => '',
            'tooltip' => $this->language->get('button_cancel'),
            'icon' => 'fa-reply',
            'class' => 'btn-default',
            'href' => $this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => FALSE,
            'form_target_id' => false,
        );
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

        $this->load->model('extension/maza/form/record');
        $this->load->model('extension/maza/form');
        $this->load->model('customer/customer');
        $this->load->model('customer/customer_group');
        
        $record_info = $this->model_extension_maza_form_record->getRecord($this->request->get['form_record_id']);
        
        if($record_info){
            $data['form']          = $record_info['form'];
            $data['language']      = $record_info['language'];
            $data['currency']      = $record_info['currency'];
            $data['ip_address']    = $record_info['ip_address'];
            $data['date_added']    = $record_info['date_added'];
            $data['page_url']      = $this->config->get('mz_store_url') . ltrim($record_info['page_url'], '/');

            if($record_info['store_id']){
                $data['store'] = $record_info['store'];
            } else {
                $data['store'] = $this->language->get('text_default');
            }

            if($record_info['product']){
                $data['product']['name'] = $record_info['product'];
                $data['product']['url']  = $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $record_info['product_id'], true);
            }
            if($record_info['category']){
                $data['category']['name'] = $record_info['category'];
                $data['category']['url']  = $this->url->link('catalog/category/edit', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $record_info['category_id'], true);
            }
            if($record_info['manufacturer']){
                $data['manufacturer']['name'] = $record_info['manufacturer'];
                $data['manufacturer']['url']  = $this->url->link('catalog/manufacturer/edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $record_info['manufacturer_id'], true);
            }

            // Customer
            $data['customer']      = $this->model_customer_customer->getCustomer($record_info['customer_id']);

            if($data['customer']){
                $data['customer']['url'] = $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $data['customer']['customer_id'], true);

                $customer_group = $this->model_customer_customer_group->getCustomerGroup($data['customer']['customer_group_id']);

                if($customer_group){
                    $data['customer']['customer_group'] = $customer_group['name'];
                }
            }
        }

        $data['field_data'] = array();

        $record_values = $this->model_extension_maza_form_record->getRecordValues($this->request->get['form_record_id']);
        $fields = $this->model_extension_maza_form->getFields($record_info['form_id']);

        foreach($fields as $field){
            if(isset($record_values[$field['name']])){
                // Decode json for multi select input
                if(in_array($field['type'], ['checkbox', 'file'])){
                    $value = json_decode($record_values[$field['name']], true);
                } else {
                    $value = $record_values[$field['name']];
                }

                $link = '';

                // Create file download link
                if($field['type'] == 'file'){
                    $link = $this->url->link('tool/upload/download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $value['code'], true);
                    $value = $value['name'];
                }

                if($field['type'] == 'url'){
                    $link = $value;
                }

                if($field['type'] == 'email'){
                    $link = 'mailTo:' . $value;
                }

                if($field['type'] == 'tel'){
                    $link = 'tel:' . $value;
                }

                $data['field_data'][$field['name']] = array(
                    'label' => $field['label'],
                    'value' => $value,
                    'link' => $link,
                );
            }
        }
        
        // Columns
        $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
        $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		$this->response->setOutput($this->load->view('extension/maza/form/record_view', $data));
	}
        
    protected function validateDelete(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/form/record')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}
