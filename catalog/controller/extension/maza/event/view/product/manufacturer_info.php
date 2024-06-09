<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewProductManufacturerInfo extends Controller {
        
    public function before(string $route, array &$data): void {
        $this->load->language('extension/maza/product/manufacturer_info');

        // Notify me
        if ($this->config->get('maza_notification_status') && $this->config->get('maza_notification_manufacturer')) {
            $this->load->model('extension/maza/notification');

            $data['mz_notifyme'] = $this->model_extension_maza_notification->isManufacturerSubscribed($this->request->get['manufacturer_id']);
        }

        // Data
        $data['manufacturer_id'] = $this->request->get['manufacturer_id'];

        $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

        if(is_file(DIR_IMAGE . $manufacturer_info['image'])){
            $data['thumb'] = $this->model_tool_image->resize($manufacturer_info['image'], 100, 100);
        }

        // Description
        $this->load->model('extension/maza/catalog/manufacturer');

        $manufacturer_description = $this->model_extension_maza_catalog_manufacturer->getManufacturerDescription($this->request->get['manufacturer_id']);

        if ($manufacturer_description) {
            $data['description'] = html_entity_decode($manufacturer_description['description'], ENT_QUOTES, 'UTF-8');
        }

        // sort
        $url = '';

        $url .= $this->load->controller('extension/module/mz_filter/url', $url);

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $sorts = array();

        $sorts[] = array(
            'text'  => $this->language->get('text_bestseller'),
            'value' => 'order_quantity-DESC',
            'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=order_quantity&order=DESC' . $url)
        );

        $sorts[] = array(
            'text'  => $this->language->get('text_popular'),
            'value' => 'p.viewed-DESC',
            'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.viewed&order=DESC' . $url)
        );

        $sorts[] = array(
            'text'  => $this->language->get('text_newest'),
            'value' => 'p.date_added-DESC',
            'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.date_added&order=DESC' . $url)
        );

        array_splice($data['sorts'], 1, 0, $sorts);

        // Layout builder
		$data['mz_content'] = $this->mz_load->view($this->mz_cache->getVar('mz_content'), $data);
		$data['mz_component'] = $this->mz_load->view($this->mz_cache->getVar('mz_component'), $data);
    }
}
