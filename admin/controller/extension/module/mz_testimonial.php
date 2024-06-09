<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionModuleMzTestimonial extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/mz_testimonial');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/maza/module');
        $this->load->model('extension/maza/testimonial');
        $this->load->model('extension/maza/opencart');

        $url = '';

        if (isset($this->request->get['mz_theme_code'])) {
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }

        if (isset($this->request->get['mz_skin_id'])) {
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }

        // Header
        $header_data          = array();
        $header_data['title'] = $this->language->get('heading_title');
        $header_data['menu']  = array(
            array('name' => $this->language->get('tab_general'), 'id' => 'tab-general', 'href' => false),
            array('name' => $this->language->get('tab_data'), 'id' => 'tab-data', 'href' => false),
            array('name' => $this->language->get('tab_layout'), 'id' => 'tab-layout', 'href' => false),
            array('name' => $this->language->get('tab_carousel'), 'id' => 'tab-carousel', 'href' => false),
        );

        $header_data['menu_active'] = 'tab-general';

        // Buttons
        $header_data['buttons'][] = array( // Button save
            'id' => 'button-save',
            'name' => false,
            'tooltip' => $this->language->get('button_save'),
            'icon' => 'fa-save',
            'class' => 'btn-primary',
            'href' => FALSE,
            'target' => FALSE,
            'form_target_id' => 'form-mz-testimonial',
        );
        $header_data['buttons'][] = array( // Button import
            'id' => 'button-import',
            'name' => false,
            'tooltip' => $this->language->get('button_import'),
            'icon' => 'fa-upload',
            'class' => 'btn-warning',
            'href' => FALSE,
            'target' => FALSE,
            'form_target_id' => false,
        );
        if (isset($this->request->get['module_id'])) {
            $header_data['buttons'][] = array( // Button export
                'id' => 'button-export',
                'name' => false,
                'tooltip' => $this->language->get('button_export'),
                'icon' => 'fa-download',
                'class' => 'btn-warning',
                'href' => $this->url->link('extension/module/mz_testimonial/export', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true),
                'target' => '_self',
                'form_target_id' => false,
            );
            $header_data['buttons'][] = array( // Button delete
                'id' => 'button-delete',
                'name' => false,
                'tooltip' => $this->language->get('button_delete'),
                'icon' => 'fa-trash',
                'class' => 'btn-danger',
                'href' => $this->url->link('extension/module/mz_testimonial/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true),
                'target' => '_self',
                'form_target_id' => false,
            );
        }
        $header_data['buttons'][] = array( // Button cancel
            'id' => 'button-cancel',
            'name' => false,
            'tooltip' => $this->language->get('button_cancel'),
            'icon' => 'fa-reply',
            'class' => 'btn-default',
            'href' => $this->url->link('extension/maza/module', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'target' => '_self',
            'form_target_id' => false,
        );
        $header_data['buttons'][] = array(
            'id' => 'button-docs',
            'name' => null,
            'tooltip' => $this->language->get('button_docs'),
            'icon' => 'fa-info',
            'class' => 'btn-default',
            'href' => 'https://docs.pocotheme.com/#module-maza-testimonial',
            'target' => '_blank'
        );

        // Form submit id
        $header_data['form_target_id'] = 'form-mz-testimonial';

        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

        // Submit form and save module in case of no error
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {

            if (!isset($this->request->get['module_id'])) {
                $module_id = $this->model_extension_maza_module->addModule('mz_testimonial', $this->mz_skin_config->get('skin_id'), $this->request->post);
            } else {
                $this->model_extension_maza_module->editModule($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'), $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            // Add module id in url and redirect to it after newly added module
            if (isset($module_id)) {
                $this->response->redirect($this->url->link('extension/module/mz_testimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true));
            }
        }

        if (isset($this->session->data['warning'])) {
            $data['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        } elseif (isset($this->error['warning'])) {
            $data['warning'] = $this->error['warning'];
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        foreach ($this->error as $label => $error) {
            $data['err_' . $label] = $error;
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/mz_testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true);
            $data['import'] = $this->url->link('extension/module/mz_testimonial/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/mz_testimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
            $data['import'] = $this->url->link('extension/module/mz_testimonial/import', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
        }

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $global_setting = $this->model_extension_maza_opencart->getModule($this->request->get['module_id']);
            $module_setting = $this->model_extension_maza_module->getSetting($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'));
        } else {
            $global_setting = $module_setting = array();
        }

        // Language
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['language_id'] = $this->config->get('config_language_id');

        // Setting
        $setting = array();

        // General
        $setting['name']   = ''; // Name of module
        $setting['status'] = false; // status of module
        $setting['title']  = array(); // Heading Title of module

        // Data
        $setting['testimonial_source']   = 'latest';
        $setting['featured_testimonial'] = array();
        $setting['filter_min_rating']    = 1;
        $setting['filter_sort']          = 'sort_order';
        $setting['filter_order']         = 'ASC';

        // Layout
        $setting['limit']              = 10;
        $setting['image_width']        = 100;
        $setting['image_height']       = 100;
        $setting['lazy_loading']       = true;
        $setting['collapsed']          = 0;
        $setting['button_add_status']  = 1; // Buttun add testimonial status
        $setting['button_view_status'] = 1; // Buttun view all testimonial status
        $setting['column_xs']          = 1;
        $setting['column_sm']          = 1;
        $setting['column_md']          = 1;
        $setting['column_lg']          = 2;
        $setting['column_xl']          = 2;

        // Carousel
        $setting['carousel_status']          = 1;
        $setting['carousel_pagination']      = 0;
        $setting['carousel_autoplay']        = 0;
        $setting['carousel_loop']            = 1;
        $setting['carousel_row']             = 1;
        $setting['carousel_nav_status']      = 1;
        $setting['carousel_nav_icon_image']  = array();
        $setting['carousel_nav_icon_svg']    = array();
        $setting['carousel_nav_icon_font']   = array();
        $setting['carousel_nav_icon_size']   = null;
        $setting['carousel_nav_icon_width']  = null;
        $setting['carousel_nav_icon_height'] = null;

        // Get global name of module
        if ($global_setting) {
            $setting['name'] = $global_setting['name'];
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $setting = array_merge($setting, $this->request->post);
        } else {
            $setting = array_merge($setting, $module_setting);
        }

        $data = array_merge($data, $setting);

        // Data

        // testimonial source
        $data['list_testimonial_source'] = array(
            array('id' => 'featured', 'name' => $this->language->get('text_featured')),
            array('id' => 'latest', 'name' => $this->language->get('text_latest')),
            array('id' => 'filter', 'name' => $this->language->get('text_filter')),
        );

        // Feature testimonial
        $data['featured_testimonials'] = array();

        foreach ($setting['featured_testimonial'] as $testimonial_id) {
            $testimonial_info = $this->model_extension_maza_testimonial->getTestimonial($testimonial_id);

            if ($testimonial_info) {
                $data['featured_testimonials'][] = array(
                    'testimonial_id' => $testimonial_info['testimonial_id'],
                    'name' => $testimonial_info['name']
                );
            }
        }

        // Filter sort list
        $data['list_sort'] = array(
            array('id' => 't.sort_order', 'name' => $this->language->get('text_sort_order')),
            array('id' => 'td.name', 'name' => $this->language->get('text_name')),
            array('id' => 't.date_added', 'name' => $this->language->get('text_date_added')),
        );

        // Filter order list
        $data['list_order'] = array(
            array('id' => 'ASC', 'name' => $this->language->get('text_ascending')),
            array('id' => 'DESC', 'name' => $this->language->get('text_descending')),
        );

        // Image
        $this->load->model('tool/image');

        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg']   = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']  = 'fa fa-font';

        // carousel nav icon image
        $data['thumb_carousel_nav_icon_image'] = array();

        foreach ($data['carousel_nav_icon_image'] as $language_id => $image) {
            if ($image) {
                $data['thumb_carousel_nav_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
            } else {
                $data['thumb_carousel_nav_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }

        // carousel nav icon svg
        $data['thumb_carousel_nav_icon_svg'] = array();

        foreach ($data['carousel_nav_icon_svg'] as $language_id => $image_svg) {
            if ($image_svg) {
                $data['thumb_carousel_nav_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
            } else {
                $data['thumb_carousel_nav_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
            }
        }


        $data['user_token'] = $this->session->data['user_token'];

        $data['header']         = $this->load->controller('extension/maza/common/header/main');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
        $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left/module', 'mz_testimonial');

        $this->response->setOutput($this->load->view('extension/module/mz_testimonial', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/module/mz_testimonial')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        // Module name
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_module_name');
        }

        if ($this->request->post['image_width'] <= 0) {
            $this->error['image_width'] = $this->language->get('error_width');
        }

        if ($this->request->post['image_height'] <= 0) {
            $this->error['image_height'] = $this->language->get('error_height');
        }


        if ($this->request->post['limit'] <= 0) {
            $this->error['limit'] = $this->language->get('limit');
        }

        if (!isset($this->error['warning']) && $this->error) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    public function delete() {
        $this->load->language('extension/module/mz_testimonial');

        $this->load->model('extension/maza/opencart');

        $url = '';

        if (isset($this->request->get['mz_theme_code'])) {
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }

        if (isset($this->request->get['mz_skin_id'])) {
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }

        if (isset($this->request->get['module_id']) && $this->validateDelete()) {
            $this->model_extension_maza_opencart->deleteModule($this->request->get['module_id']);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/mz_testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true));
        } else {
            $this->response->redirect($this->url->link('extension/module/mz_testimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true));
        }

    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/mz_testimonial')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    /**
     * Export setting
     */
    public function export() {
        $this->load->model('extension/maza/module');
        $this->load->language('extension/module/mz_testimonial');

        $module_setting = $this->model_extension_maza_module->getSetting($this->request->get['module_id'], $this->mz_skin_config->get('skin_id'));

        if ($module_setting) {
            header('Content-Type: application/json; charset=utf-8');
            header('Content-disposition: attachment; filename="module.mz_testimonial.' . $this->mz_skin_config->get('skin_code') . '(' . $module_setting['name'] . ').json"');

            echo json_encode(['type' => 'module', 'code' => 'mz_testimonial', 'setting' => $module_setting]);
        } else {
            $this->session->data['warning'] = $this->language->get('error_module_empty');

            $url = '';

            if (isset($this->request->get['mz_theme_code'])) {
                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
            }

            if (isset($this->request->get['mz_skin_id'])) {
                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
            }

            $this->response->redirect($this->url->link('extension/module/mz_testimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true));
        }
    }

    /**
     * Import setting
     */
    public function import() {
        $this->load->language('extension/module/mz_testimonial');

        if (isset($this->request->get['module_id'])) {
            $module_id = $this->request->get['module_id'];
        } else {
            $module_id = 0;
        }

        $warning = '';

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/module/mz_testimonial')) {
            $warning = $this->language->get('error_permission');
        } else {
            if (isset($this->request->files['file']['name'])) {
                if (substr($this->request->files['file']['name'], -4) != 'json') {
                    $warning = $this->language->get('error_filetype');
                }

                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $warning = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            } else {
                $warning = $this->language->get('error_upload');
            }
        }

        if (!$warning) {
            $file = $this->request->files['file']['tmp_name'];

            if (is_file($file)) {
                $data = json_decode(file_get_contents($file), true);

                if ($data && $data['type'] == 'module' && $data['code'] == 'mz_testimonial') {
                    $this->load->model('extension/maza/module');

                    if (!$module_id) {
                        $module_id = $this->model_extension_maza_module->addModule('mz_testimonial', $this->mz_skin_config->get('skin_id'), $data['setting']);
                    } else {
                        $this->model_extension_maza_module->editModule($module_id, $this->mz_skin_config->get('skin_id'), $data['setting']);
                    }

                    $this->session->data['success'] = $this->language->get('text_success_import');
                } else {
                    $warning = $this->language->get('error_import_file');
                }
            } else {
                $warning = $this->language->get('error_file');
            }
        }

        if ($warning) {
            $this->session->data['warning'] = $warning;
        }

        $url = '';

        if (isset($this->request->get['mz_theme_code'])) {
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }

        if (isset($this->request->get['mz_skin_id'])) {
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }

        if ($module_id) {
            $this->response->redirect($this->url->link('extension/module/mz_testimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true));
        } else {
            $this->response->redirect($this->url->link('extension/module/mz_testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
    }
}