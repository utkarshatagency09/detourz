<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaForm extends Controller {
    private $error = array();
        
    public function index(): void {
		$this->load->language('extension/maza/form');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form');

		$this->getList();
	}
        
    public function add(): void {
		$this->load->language('extension/maza/form');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_form->addForm($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
            if(isset($this->request->get['filter_name'])){
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
                        
			$this->response->redirect($this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
	public function edit(): void {
		$this->load->language('extension/maza/form');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_form->editForm($this->request->get['form_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
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

			$this->response->redirect($this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
    
    public function copy(): void {
		$this->load->language('extension/maza/form');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form');
        $this->load->model('extension/maza/form/field');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $form_id) {
				$new_form_id = $this->model_extension_maza_form->copyForm($form_id);

                $this->model_extension_maza_form_field->copyForm($form_id, $new_form_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
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

			$this->response->redirect($this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
    
    public function delete(): void {
		$this->load->language('extension/maza/form');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/form');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $form_id) {
				$this->model_extension_maza_form->deleteForm($form_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
            if(isset($this->request->get['filter_name'])){
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

			$this->response->redirect($this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
    public function getList(): void {
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'fd.name';
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

        // $header_data['menu'] = array(
        //     array('name' => $this->language->get('tab_form'), 'id' => 'tab-mz-form', 'href' => false),
        //     array('name' => $this->language->get('tab_field'), 'id' => 'tab-mz-field', 'href' => $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . $url, true)),
        //     array('name' => $this->language->get('tab_record'), 'id' => 'tab-mz-record', 'href' => $this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . $url, true))
        // );

        // $header_data['menu_active'] = 'tab-mz-form';

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
            'href' => $this->url->link('extension/maza/form/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => false,
            'form_target_id' => false,
        );
        
        $header_data['buttons'][] = array(
            'id' => 'button-copy',
            'name' => '',
            'class' => 'btn-default',
            'tooltip' => $this->language->get('button_copy'),
            'icon' => 'fa-copy',
            'formaction' => $this->url->link('extension/maza/form/copy', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => false,
            'form_target_id' => 'form-mz-form',
            'confirm' => $this->language->get('text_confirm')
        );
        
        $header_data['buttons'][] = array(
            'id' => 'button-delete',
            'name' => '',
            'class' => 'btn-danger',
            'tooltip' => $this->language->get('button_delete'),
            'icon' => 'fa-trash',
            'formaction' => $this->url->link('extension/maza/form/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => FALSE,
            'form_target_id' => 'form-mz-form',
            'confirm' => $this->language->get('text_confirm')
        );

        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#page-form',
            'target' => '_blank'
        );

        $header_data['form_target_id'] = 'form-mz-form';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
        // List
		$data['forms'] = array();

		$filter_data = array(
            'filter_name' => $filter_name,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$page_total = $this->model_extension_maza_form->getTotalForms($filter_data);

		$results = $this->model_extension_maza_form->getForms($filter_data);

		foreach ($results as $result) {
			$data['forms'][] = array(
				'form_id' => $result['form_id'],
				'name'    => $result['name'],
                'records' => $result['records'],
                'date_added' => $result['date_added'],
                'edit'    => $this->url->link('extension/maza/form/edit', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true),
                'view_record'   => $this->url->link('extension/maza/form/record', 'user_token=' . $this->session->data['user_token'] . '&filter_form_id=' . $result['form_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . '&sort=fd.name' . $url, true);
        $data['sort_records'] = $this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . '&sort=records' . $url, true);
        $data['sort_date_added'] = $this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . '&sort=f.date_added' . $url, true);
                
        $data['sort'] = $sort;
		$data['order'] = $order;

		$url = '';
                
        if(isset($this->request->get['filter_name'])){
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
		$pagination->total = $page_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($page_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($page_total - $this->config->get('config_limit_admin'))) ? $page_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $page_total, ceil($page_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;
        
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
                
		$this->response->setOutput($this->load->view('extension/maza/form_list', $data));
	}

    protected function getForm(): void {
        $url = '';
        
        if(isset($this->request->get['filter_name'])){
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
        $header_data['title'] = !isset($this->request->get['form_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $header_data['theme_select'] = $header_data['skin_select'] = false;
        $header_data['menu'] = array(
            array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
            array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
            array('name' => $this->language->get('tab_mail'), 'id' => 'tab-mz-mail', 'href' => false),
        );

        if(isset($this->request->get['form_id'])){
            $header_data['menu'][] = array('name' => $this->language->get('tab_field'), 'id' => 'tab-mz-field', 'href' => $this->url->link('extension/maza/form/field', 'user_token=' . $this->session->data['user_token'] . '&filter_form_id=' . $this->request->get['form_id'] . $url, true));
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
            'form_target_id' => 'form-mz-form',
        );
        $header_data['buttons'][] = array(
            'id' => 'button-cancel',
            'name' => '',
            'tooltip' => $this->language->get('button_cancel'),
            'icon' => 'fa-reply',
            'class' => 'btn-default',
            'href' => $this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => FALSE,
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#page-form-add',
            'target' => '_blank'
        );
        $header_data['form_target_id'] = 'form-mz-form';
        
        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
        
        // Setting
        $setting = array();
        $setting['captcha'] = '';
        $setting['spam_keywords'] = '';
        $setting['information_id'] = 0;
        $setting['record'] = true;
        $setting['email_field_id'] = 0;
        $setting['subject_field_id'] = 0;
        $setting['mail_admin_status'] = true;
        $setting['mail_admin_to'] = '';
        $setting['mail_customer_status'] = false;
        $setting['form_description'] = array();
        
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $setting = array_merge($setting, $this->request->post);
        } elseif(isset($this->request->get['form_id'])) {
            $setting = array_merge($setting, $this->model_extension_maza_form->getForm($this->request->get['form_id']));
            $setting['form_description'] = $this->model_extension_maza_form->getFormDescriptions($this->request->get['form_id']);
        }

        // Data
        $data = array_merge($data, $setting);
                
        if (!isset($this->request->get['form_id'])) {
			$data['action'] = $this->url->link('extension/maza/form/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/form/edit', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $this->request->get['form_id'] . $url, true);
		}

        $this->load->model('extension/maza/opencart');

		$data['captchas'] = array();

		// Get a list of installed captchas
		$extensions = $this->model_extension_maza_opencart->getInstalled('captcha');

		foreach ($extensions as $code) {
			$this->load->language('extension/captcha/' . $code, 'extension');

			if ($this->config->get('captcha_' . $code . '_status')) {
				$data['captchas'][] = array(
					'text'  => $this->language->get('extension')->get('heading_title'),
					'value' => $code
				);
			}
		}

        // Information
        $this->load->model('catalog/information');

        $data['informations'] = $this->model_catalog_information->getInformations();

        // Get form fields
        $data['fields'] = array();

        if(isset($this->request->get['form_id'])){
            $this->load->model('extension/maza/form/field');

            $fields = $this->model_extension_maza_form_field->getFields(['filter_form_id' => $this->request->get['form_id'], 'filter_status' => 1]);

            foreach ($fields as $field) {
                $data['fields'][] = array(
                    'form_field_id' => $field['form_field_id'],
                    'name' => $field['label'],
                    'type' => $field['type'],
                );
            }
        }

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
                
		$this->response->setOutput($this->load->view('extension/maza/form_form', $data));
	}
        
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/form')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        foreach ($this->request->post['form_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
                $this->error['name'][$language_id] = $this->language->get('error_name');
            }

            if ((utf8_strlen($value['success']) < 1) || (utf8_strlen($value['success']) > 1000)) {
                $this->error['success'][$language_id] = $this->language->get('error_success');
            }

            if($this->request->post['mail_customer_status']){
                if ((utf8_strlen($value['mail_customer_subject']) < 1) || (utf8_strlen($value['mail_customer_subject']) > 255)) {
                    $this->error['mail_customer_subject'][$language_id] = $this->language->get('error_customer_subject');
                }
                if (empty($value['mail_customer_message'])) {
                    $this->error['mail_customer_message'][$language_id] = $this->language->get('error_customer_message');
                }
            }
			
		}
                
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
        
    protected function validateDelete(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/form')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
        
    protected function validateCopy(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/form')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
    
    public function autocomplete(): void {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/form');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'pd.name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_maza_form->getForms($filter_data);

			foreach ($results as $result) {
                $json[] = array(
                    'form_id'       => $result['form_id'],
                    'name'          => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
