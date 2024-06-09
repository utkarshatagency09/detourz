<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaFormField extends Controller {
    private $error = array();
        
    public function index(): void {
		$this->load->language('extension/maza/form/field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form/field');

		$this->getList();
	}
        
    public function add(): void {
		$this->load->language('extension/maza/form/field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form/field');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_form_field->addField($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
            if(isset($this->request->get['filter_form_id'])){
                    $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
            }
            if(isset($this->request->get['filter_type'])){
                    $url .= '&filter_type=' . $this->request->get['filter_type'];
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
                        
			$this->response->redirect($this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
	public function edit(): void {
		$this->load->language('extension/maza/form/field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form/field');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_form_field->editField($this->request->get['form_field_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_form_id'])){
                    $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
            }
            if(isset($this->request->get['filter_type'])){
                    $url .= '&filter_type=' . $this->request->get['filter_type'];
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

			$this->response->redirect($this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
    
    public function copy(): void {
		$this->load->language('extension/maza/form/field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form/field');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $form_field_id) {
				$this->model_extension_maza_form_field->copyField($form_field_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_form_id'])){
                    $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
            }
            if(isset($this->request->get['filter_type'])){
                    $url .= '&filter_type=' . $this->request->get['filter_type'];
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

			$this->response->redirect($this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
    
    public function delete(): void {
		$this->load->language('extension/maza/form/field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form/field');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $form_field_id) {
				$this->model_extension_maza_form_field->deleteField($form_field_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
            if(isset($this->request->get['filter_form_id'])){
                    $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
            }
            if(isset($this->request->get['filter_type'])){
                    $url .= '&filter_type=' . $this->request->get['filter_type'];
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

			$this->response->redirect($this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
    public function getList(): void {
        if (isset($this->request->get['filter_form_id'])) {
            $filter_form_id = $this->request->get['filter_form_id'];
		} else {
			$filter_form_id = null;
		}
        if (isset($this->request->get['filter_type'])) {
            $filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = null;
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
        
        if(isset($this->request->get['filter_form_id'])){
            $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
        }
        if(isset($this->request->get['filter_type'])){
            $url .= '&filter_type=' . $this->request->get['filter_type'];
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
                
        // Header
        $header_data = array();
        $header_data['theme_select'] = false;
        $header_data['skin_select'] = false;
        $header_data['title'] = $this->language->get('heading_title');

        // $header_data['menu'] = array();
        // if ($this->user->hasPermission('access', 'extension/maza/form')) $header_data['menu'][] = array('name' => $this->language->get('tab_form'), 'id' => 'tab-mz-form', 'href' => $this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // $header_data['menu'][] = array('name' => $this->language->get('tab_field'), 'id' => 'tab-mz-field', 'href' => false);
        // if ($this->user->hasPermission('access', 'extension/maza/form/record')) $header_data['menu'][] = array('name' => $this->language->get('tab_record'), 'id' => 'tab-mz-record', 'href' => $this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . $url, true));
        
        // $header_data['menu_active'] = 'tab-mz-field';

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
            'href' => $this->url->link('extension/maza/form/field/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => false,
            'form_target_id' => false,
        );
        
        $header_data['buttons'][] = array(
            'id' => 'button-copy',
            'name' => '',
            'class' => 'btn-default',
            'tooltip' => $this->language->get('button_copy'),
            'icon' => 'fa-copy',
            'formaction' => $this->url->link('extension/maza/form/field/copy', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => false,
            'form_target_id' => 'form-mz-field',
            'confirm' => $this->language->get('text_confirm')
        );
        
        $header_data['buttons'][] = array(
            'id' => 'button-delete',
            'name' => '',
            'class' => 'btn-danger',
            'tooltip' => $this->language->get('button_delete'),
            'icon' => 'fa-trash',
            'formaction' => $this->url->link('extension/maza/form/field/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => FALSE,
            'form_target_id' => 'form-mz-field',
            'confirm' => $this->language->get('text_confirm')
        );

        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#page-form-field-add',
            'target' => '_blank'
        );

        $header_data['form_target_id'] = 'form-mz-field';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
        // List
		$data['fields'] = array();

		$filter_data = array(
            'filter_form_id' => $filter_form_id,
            'filter_type' => $filter_type,
            'filter_status' => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$page_total = $this->model_extension_maza_form_field->getTotalFields($filter_data);

		$results = $this->model_extension_maza_form_field->getFields($filter_data);

		foreach ($results as $result) {
			$data['fields'][] = array(
				'form_field_id' => $result['form_field_id'],
				'label'         => $result['label'],
                'form'          => $result['form'],
                'type'          => $this->language->get('text_' . $result['type']),
                'required'      => $result['is_required']?$this->language->get('text_yes'):$this->language->get('text_no'),
                'sort_order'    => $result['sort_order'],
                'status'        => $result['status'],
                'edit'          => $this->url->link('extension/maza/form/field/edit', 'user_token=' . $this->session->data['user_token'] . '&form_field_id=' . $result['form_field_id'] . $url, true),
			);
		}
                

        $data['list_types'] = array(
            array( // Input
                'group' => $this->language->get('text_input'),
                'types' => array(
                    array('id' => 'text', 'name' => $this->language->get('text_text')),
                    array('id' => 'textarea', 'name' => $this->language->get('text_textarea')),
                    array('id' => 'number', 'name' => $this->language->get('text_number')),
                ),
            ),
            array( // Choose
                'group' => $this->language->get('text_choose'),
                'types' => array(
                    array('id' => 'select', 'name' => $this->language->get('text_select')),
                    array('id' => 'radio', 'name' => $this->language->get('text_radio')),
                    array('id' => 'checkbox', 'name' => $this->language->get('text_checkbox')),
                ),
            ),
            array( // Special
                'group' => $this->language->get('text_type'),
                'types' => array(
                    array('id' => 'email', 'name' => $this->language->get('text_email')),
                    array('id' => 'tel', 'name' => $this->language->get('text_tel')),
                    array('id' => 'url', 'name' => $this->language->get('text_url')),
                    array('id' => 'file', 'name' => $this->language->get('text_file')),
                    array('id' => 'date', 'name' => $this->language->get('text_date')),
                    array('id' => 'time', 'name' => $this->language->get('text_time')),
                    array('id' => 'datetime', 'name' => $this->language->get('text_datetime')),
                ),
            ),
        );

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
        if(isset($this->request->get['filter_type'])){
            $url .= '&filter_type=' . $this->request->get['filter_type'];
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

		$data['sort_label'] = $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . '&sort=fd.label' . $url, true);
        $data['sort_sort_order'] = $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . '&sort=f.sort_order' . $url, true);
        $data['sort_required'] = $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . '&sort=f.is_required' . $url, true);
        $data['sort_form'] = $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . '&sort=form' . $url, true);
        $data['sort_type'] = $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . '&sort=f.type' . $url, true);
        $data['sort_status'] = $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . '&sort=f.status' . $url, true);
                
        $data['sort'] = $sort;
		$data['order'] = $order;

		$url = '';
                
        if(isset($this->request->get['filter_form_id'])){
            $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
        }
        if(isset($this->request->get['filter_type'])){
            $url .= '&filter_type=' . $this->request->get['filter_type'];
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
		$pagination->url = $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($page_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($page_total - $this->config->get('config_limit_admin'))) ? $page_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $page_total, ceil($page_total / $this->config->get('config_limit_admin')));

        $data['filter_form_id'] = $filter_form_id;
        $data['filter_type'] = $filter_type;
        $data['filter_status'] = $filter_status;

        if($filter_form_id){
            $this->load->model('extension/maza/form');

            $form_info = $this->model_extension_maza_form->getForm($filter_form_id);

            if ($form_info) {
                $data['filter_form'] = $form_info['name'];
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
                
		$this->response->setOutput($this->load->view('extension/maza/form/field_list', $data));
	}
        
    
    protected function getForm(): void {
        $url = '';
        
        if(isset($this->request->get['filter_form_id'])){
            $url .= '&filter_form_id=' . $this->request->get['filter_form_id'];
        }
        if(isset($this->request->get['filter_type'])){
            $url .= '&filter_type=' . $this->request->get['filter_type'];
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
        $header_data['title'] = !isset($this->request->get['form_field_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $header_data['theme_select'] = $header_data['skin_select'] = false;
        $header_data['menu'] = array(
            array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
            array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
            array('name' => $this->language->get('tab_link'), 'id' => 'tab-mz-link', 'href' => false),
            array('name' => $this->language->get('tab_input'), 'id' => 'tab-mz-input', 'href' => false),
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
            'form_target_id' => 'form-mz-field',
        );
        $header_data['buttons'][] = array(
            'id' => 'button-cancel',
            'name' => '',
            'tooltip' => $this->language->get('button_cancel'),
            'icon' => 'fa-reply',
            'class' => 'btn-default',
            'href' => $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => FALSE,
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#page-form-field-add',
            'target' => '_blank'
        );
        $header_data['form_target_id'] = 'form-mz-field';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

        $this->load->model('extension/maza/form');
        
        // Setting
        $setting = array();
        $setting['form_id'] = 0;
        $setting['status'] = true;
        $setting['sort_order'] = 0;
        $setting['column'] = 1;
        $setting['is_required'] = true;
        $setting['customer'] = 0; // 0 = ALL, -1 = Guest, 1 = logged
        $setting['type'] = 'text';
        $setting['name'] = '';
        $setting['value'] = ''; // Default value of field
        $setting['validation'] = '';
        $setting['min'] = '';
        $setting['max'] = '';
        $setting['decimal'] = 0;
        $setting['field_description'] = array();
        $setting['field_customer_group'] = array('1');
        $setting['field_values'] = array();
         
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $setting = array_merge($setting, $this->request->post);
        } elseif(isset($this->request->get['form_field_id'])) {
            $setting = array_merge($setting, $this->model_extension_maza_form_field->getField($this->request->get['form_field_id']));
            $setting['field_description'] = $this->model_extension_maza_form_field->getFieldDescriptions($this->request->get['form_field_id']);
            $setting['field_customer_group'] = $this->model_extension_maza_form_field->getFieldCustomerGroups($this->request->get['form_field_id']);
            $setting['field_values'] = $this->model_extension_maza_form_field->getFieldValues($this->request->get['form_field_id']);
        }

        // Data
        $data = array_merge($data, $setting);
                
        if (!isset($this->request->get['form_field_id'])) {
			$data['action'] = $this->url->link('extension/maza/form/field/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/form/field/edit', 'user_token=' . $this->session->data['user_token'] . '&form_field_id=' . $this->request->get['form_field_id'] . $url, true);
		}

        if(isset($this->request->get['form_field_id'])){
            $data['form'] = $this->model_extension_maza_form->getForm($data['form_id'])['name'];
        } else {
            $data['form'] = '';
        }
        

        $data['list_customer'] = array(
            array('id' => 0, 'name' => $this->language->get('text_all')),
            array('id' => -1, 'name' => $this->language->get('text_guest')),
            array('id' => 1, 'name' => $this->language->get('text_logged')),
        );

        $data['list_types'] = array(
            array( // Input
                'group' => $this->language->get('text_input'),
                'types' => array(
                    array('id' => 'text', 'name' => $this->language->get('text_text')),
                    array('id' => 'textarea', 'name' => $this->language->get('text_textarea')),
                    array('id' => 'number', 'name' => $this->language->get('text_number')),
                ),
            ),
            array( // Choose
                'group' => $this->language->get('text_choose'),
                'types' => array(
                    array('id' => 'select', 'name' => $this->language->get('text_select')),
                    array('id' => 'radio', 'name' => $this->language->get('text_radio')),
                    array('id' => 'checkbox', 'name' => $this->language->get('text_checkbox')),
                ),
            ),
            array( // Special
                'group' => $this->language->get('text_type'),
                'types' => array(
                    array('id' => 'email', 'name' => $this->language->get('text_email')),
                    array('id' => 'tel', 'name' => $this->language->get('text_tel')),
                    array('id' => 'url', 'name' => $this->language->get('text_url')),
                    array('id' => 'file', 'name' => $this->language->get('text_file')),
                    array('id' => 'date', 'name' => $this->language->get('text_date')),
                    array('id' => 'time', 'name' => $this->language->get('text_time')),
                    array('id' => 'datetime', 'name' => $this->language->get('text_datetime')),
                ),
            ),
        );

        $this->load->model('customer/customer_group');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        $this->load->model('localisation/language');
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
                
		$this->response->setOutput($this->load->view('extension/maza/form/field_form', $data));
	}
        
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/form/field')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        if(empty($this->request->post['form_id'])){
            $this->error['form'] = $this->language->get('error_form');
        }
                
		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 32)) {
            $this->error['name'] = $this->language->get('error_name');
        } elseif(isset($this->request->post['form_id'])) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_form_field WHERE form_id = '" . (int)$this->request->post['form_id'] . "' AND name = '" . $this->db->escape($this->request->post['name']) . "'");
            
            if($query->num_rows && (!isset($this->request->get['form_field_id']) || $query->row['form_field_id'] !== $this->request->get['form_field_id'])){
                $this->error['name'] = $this->language->get('error_name_unique');
            }
        }

        foreach ($this->request->post['field_description'] as $language_id => $value) {
			if ((utf8_strlen($value['label']) < 1) || (utf8_strlen($value['label']) > 255)) {
				$this->error['label'][$language_id] = $this->language->get('error_label');
			}
		}
        
        if(in_array($this->request->post['type'], ['select', 'checkbox', 'radio'])){
            foreach($this->request->post['field_values'] as $key => $value){
                foreach ($value['name'] as $language_id => $name) {
                    if((utf8_strlen($name) < 1) || (utf8_strlen($name) > 100)){
                        $this->error['field_values'][$key][$language_id] = $this->language->get('error_field_value');
                    }
                }
            }
        }
        
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
        
    protected function validateDelete(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/form/field')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
        
    protected function validateCopy(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/form/field')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}
