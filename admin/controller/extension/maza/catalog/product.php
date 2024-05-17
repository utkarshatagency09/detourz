<?php

/**
 * @package        MazaTheme
 * @author        Jay padaliya
 * @copyright   Copyright (c) 2021, TemplateMaza
 * @license        https://themeforest.net/licenses
 * @link        https://pocotheme.com/
 */

class ControllerExtensionMazaCatalogProduct extends Controller {
    private $error = array();

    public function index(): void {
        $this->load->language('extension/maza/catalog/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/product');

        $this->getList();
    }

    public function edit(): void {
        $this->load->language('extension/maza/catalog/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/maza/catalog/product');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_maza_catalog_product->editProduct($this->request->get['product_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->get['filter_model'])) {
                $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->get['filter_price'])) {
                $url .= '&filter_price=' . $this->request->get['filter_price'];
            }
            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }
            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
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
            if (isset($this->request->get['mz_theme_code'])) {
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
            }
            if (isset($this->request->get['mz_skin_id'])) {
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
            }

            // $this->response->redirect($this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete(): void {
        $this->load->language('extension/maza/catalog/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/product');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $product_id) {
                $this->model_catalog_product->deleteProduct($product_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->get['filter_model'])) {
                $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->get['filter_price'])) {
                $url .= '&filter_price=' . $this->request->get['filter_price'];
            }
            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }
            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
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
            if (isset($this->request->get['mz_theme_code'])) {
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
            }
            if (isset($this->request->get['mz_skin_id'])) {
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
            }

            $this->response->redirect($this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList(): void {
        $this->load->model('tool/image');
        $this->load->model('extension/maza/catalog/product');

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }

        if (isset($this->request->get['filter_model'])) {
            $filter_model = $this->request->get['filter_model'];
        } else {
            $filter_model = '';
        }

        if (isset($this->request->get['filter_price'])) {
            $filter_price = $this->request->get['filter_price'];
        } else {
            $filter_price = null;
        }

        if (isset($this->request->get['filter_quantity'])) {
            $filter_quantity = $this->request->get['filter_quantity'];
        } else {
            $filter_quantity = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.date_added';
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
        if (isset($this->request->get['mz_theme_code'])) {
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        if (isset($this->request->get['mz_skin_id'])) {
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }

        // Header
        $header_data                 = array();
        $header_data['title']        = $this->language->get('text_list');
        $header_data['theme_select'] = $header_data['skin_select'] = false;

        // $this->load->language('extension/maza/common/column_left');

        // $header_data['menu']   = array();
        // $header_data['menu'][] = array('name' => $this->language->get('tab_product'), 'id' => 'tab-mz-product', 'href' => false);
        // if ($this->user->hasPermission('access', 'extension/maza/catalog/manufacturer')) {
        //     $header_data['menu'][] = array('name' => $this->language->get('tab_manufacturer'), 'id' => 'tab-mz-manufacturer', 'href' => $this->url->link('extension/maza/catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // }

        // if ($this->user->hasPermission('access', 'extension/maza/catalog/product_label')) {
        //     $header_data['menu'][] = array('name' => $this->language->get('tab_product_label'), 'id' => 'tab-mz-product-label', 'href' => $this->url->link('extension/maza/catalog/product_label', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // }

        // if ($this->user->hasPermission('access', 'extension/maza/catalog/data')) {
        //     $header_data['menu'][] = array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => $this->url->link('extension/maza/catalog/data', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // }

        // if ($this->user->hasPermission('access', 'extension/maza/catalog/document')) {
        //     $header_data['menu'][] = array('name' => $this->language->get('tab_document'), 'id' => 'tab-mz-document', 'href' => $this->url->link('extension/maza/catalog/document', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // }

        // if ($this->user->hasPermission('access', 'extension/maza/catalog/redirect')) {
        //     $header_data['menu'][] = array('name' => $this->language->get('tab_redirect'), 'id' => 'tab-mz-redirect', 'href' => $this->url->link('extension/maza/catalog/redirect', 'user_token=' . $this->session->data['user_token'] . $url, true));
        // }

        // $header_data['menu_active'] = 'tab-mz-product';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }
        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
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
            'id'             => 'button-add',
            'name'           => '',
            'class'          => 'btn-warning',
            'tooltip'        => $this->language->get('button_add'),
            'icon'           => 'fa-plus',
            'href'           => $this->url->link('catalog/product/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target'         => false,
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id'             => 'button-delete',
            'name'           => '',
            'tooltip'        => $this->language->get('button_delete'),
            'icon'           => 'fa-trash',
            'class'          => 'btn-danger',
            'formaction'     => $this->url->link('extension/maza/catalog/product/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'href'           => false,
            'target'         => false,
            'form_target_id' => 'form-mz-product',
            'confirm'        => $this->language->get('text_confirm'),
        );
        $header_data['form_target_id'] = 'form-mz-product';

        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

        // Product list

        $data['products'] = array();

        $filter_data = array(
            'filter_name'     => $filter_name,
            'filter_model'    => $filter_model,
            'filter_price'    => $filter_price,
            'filter_quantity' => $filter_quantity,
            'filter_status'   => $filter_status,
            'sort'            => $sort,
            'order'           => $order,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin'),
        );

        $product_total = $this->model_catalog_product->getTotalProducts($filter_data);

        $results = $this->model_catalog_product->getProducts($filter_data);

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], 40, 40);
            } else {
                $image = $this->model_tool_image->resize('no_image.png', 40, 40);
            }

            $special = false;

            $product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

            foreach ($product_specials as $product_special) {
                if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
                    $special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

                    break;
                }
            }

            $data['products'][] = array(
                'product_id' => $result['product_id'],
                'image'      => $image,
                'name'       => $result['name'],
                'model'      => $result['model'],
                'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
                'special'    => $special,
                'quantity'   => $result['quantity'],
                'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit'       => $this->url->link('extension/maza/catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true),
                'edit2'      => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true),
            );
        }

        if (isset($this->session->data['warning'])) {
            $data['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        // Sort order
        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }
        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['mz_theme_code'])) {
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        if (isset($this->request->get['mz_skin_id'])) {
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

        $data['sort_name']     = $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
        $data['sort_model']    = $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
        $data['sort_price']    = $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
        $data['sort_quantity'] = $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
        $data['sort_status']   = $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
        $data['sort_order']    = $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

        $data['sort']  = $sort;
        $data['order'] = $order;

        // Pagination
        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }
        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['mz_theme_code'])) {
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        if (isset($this->request->get['mz_skin_id'])) {
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination        = new Pagination();
        $pagination->total = $product_total;
        $pagination->page  = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url   = $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

        $data['filter_name']     = $filter_name;
        $data['filter_model']    = $filter_model;
        $data['filter_price']    = $filter_price;
        $data['filter_quantity'] = $filter_quantity;
        $data['filter_status']   = $filter_status;

        $data['default_url'] = '&user_token=' . $this->session->data['user_token'];
        if (isset($this->request->get['mz_theme_code'])) {
            $data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        if (isset($this->request->get['mz_skin_id'])) {
            $data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }

        $data['user_token'] = $this->session->data['user_token'];

        // Columns
        $data['header']         = $this->load->controller('extension/maza/common/header/main');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
        $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

        $this->response->setOutput($this->load->view('extension/maza/catalog/product_list', $data));
    }

    protected function getForm() {
        $this->load->model('catalog/product');
        $this->load->model('extension/maza/catalog/product');
        $this->load->model('tool/image');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }
        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['mz_theme_code'])) {
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        if (isset($this->request->get['mz_skin_id'])) {
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
        $header_data                 = array();
        $header_data['title']        = $this->language->get('text_edit');
        $header_data['theme_select'] = $header_data['skin_select'] = false;
        $header_data['menu']         = array(
            array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
            array('name' => $this->language->get('tab_video'), 'id' => 'tab-mz-video', 'href' => false),
            array('name' => $this->language->get('tab_audio'), 'id' => 'tab-mz-audio', 'href' => false),
        );

        $header_data['menu_active'] = 'tab-mz-data';
        $header_data['buttons'][]   = array(
            'id'             => 'button-save',
            'name'           => '',
            'class'          => 'btn-primary',
            'tooltip'        => $this->language->get('button_save'),
            'icon'           => 'fa-save',
            'href'           => false,
            'target'         => false,
            'form_target_id' => 'form-mz-product',
        );
        $header_data['buttons'][] = array(
            'id'             => 'button-preview',
            'name'           => '',
            'tooltip'        => $this->language->get('button_preview'),
            'icon'           => 'fa-eye',
            'class'          => 'btn-info',
            'href'           => $this->config->get('mz_store_url') . 'index.php?route=product/product&product_id=' . $this->request->get['product_id'],
            'target'         => '_blank',
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id'             => 'button-edit',
            'name'           => '',
            'tooltip'        => $this->language->get('button_edit'),
            'icon'           => 'fa-pencil',
            'class'          => 'btn-info',
            'href'           => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'] . $url, true),
            'target'         => false,
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id'             => 'button-notification',
            'name'           => '',
            'tooltip'        => $this->language->get('button_notification'),
            'icon'           => 'fa-paper-plane',
            'class'          => 'btn-info',
            'href'           => $this->url->link('extension/maza/notification/send', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'] . $url, true),
            'target'         => false,
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id'             => 'button-cancel',
            'name'           => '',
            'tooltip'        => $this->language->get('button_cancel'),
            'icon'           => 'fa-reply',
            'class'          => 'btn-default',
            'href'           => $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target'         => false,
            'form_target_id' => false,
        );

        $header_data['form_target_id'] = 'form-mz-product';

        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

        $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);

        $this->document->setTitle($product_info['name']);

        // Setting
        $setting                  = array();
        $setting['mz_featured']   = '0';
        $setting['product_video'] = array();
        $setting['product_audio'] = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $setting = array_merge($setting, $this->request->post);
        } else {
            $setting                  = array_merge($setting, $product_info);
            $setting['product_video'] = $this->model_extension_maza_catalog_product->getProductVideos($this->request->get['product_id']);
            $setting['product_audio'] = $this->model_extension_maza_catalog_product->getProductAudios($this->request->get['product_id']);
        }

        // Data
        $data = array_merge($data, $setting);

        if (!isset($this->request->get['product_id'])) {
            $data['action'] = $this->url->link('extension/maza/catalog/product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/maza/catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);
        }

        // Video
        $data['product_videos'] = array();

        foreach ($setting['product_video'] as $video) {
            if (is_file(DIR_IMAGE . $video['image'])) {
                $thumb = $video['image'];
            } else {
                $thumb = 'no_image.png';
            }

            $data['product_videos'][] = array(
                'image'       => $video['image'],
                'thumb'       => $this->model_tool_image->resize($thumb, 80, 80),
                'url'         => $video['url'],
                'description' => $video['description'],
                'sort_order'  => $video['sort_order'],
            );
        }

        // Audio
        $data['product_audios'] = array();

        foreach ($setting['product_audio'] as $audio) {
            $data['product_audios'][] = array(
                'url'        => $audio['url'],
                'description' => $audio['description'],
                'sort_order' => $audio['sort_order'],
            );
        }

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 80, 80);

        $data['user_token'] = $this->session->data['user_token'];

        $data['default_url'] = '&user_token=' . $this->session->data['user_token'];
        if (isset($this->request->get['mz_theme_code'])) {
            $data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }
        if (isset($this->request->get['mz_skin_id'])) {
            $data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if (isset($this->error['warning'])) {
            $data['warning'] = $this->error['warning'];
        } elseif (isset($this->session->data['warning'])) {
            $data['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        foreach ($this->error as $key => $val) {
            $data['err_' . $key] = $val;
        }

        // Columns
        $data['header']         = $this->load->controller('extension/maza/common/header/main');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
        $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

        $this->response->setOutput($this->load->view('extension/maza/catalog/product_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/maza/catalog/product')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/maza/catalog/product')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function upload() {
        $this->load->language('extension/maza/catalog/product');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/maza/catalog/product')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
                // Sanitize the filename
                $filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

                // Validate the filename length
                if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 128)) {
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types
                $allowed = array();

                $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

                $filetypes = explode("\n", $extension_allowed);

                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Allowed file mime types
                $allowed = array();

                $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

                $filetypes = explode("\n", $mime_allowed);

                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                if (!in_array($this->request->files['file']['type'], $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($this->request->files['file']['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Return any upload error
                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }

                // File name should be unique
                if (is_file(DIR_IMAGE . 'uploads/product/' . $filename)) {
                    $json['error'] = $this->language->get('error_file_exists');
                }
            } else {
                $json['error'] = $this->language->get('error_upload');
            }
        }

        if (!$json) {
            maza\createDirPath(DIR_IMAGE . 'uploads/product/');

            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_IMAGE . 'uploads/product/' . $filename);

            $json['url'] = $this->config->get('mz_store_url') . 'image/uploads/product/' . $filename;

            $json['success'] = $this->language->get('text_upload');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
