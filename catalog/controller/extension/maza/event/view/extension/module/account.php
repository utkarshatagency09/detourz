<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewExtensionModuleAccount extends Controller {
    public function before(string $route, array &$data): void {
        if ($this->config->get('maza_notification_status')) {
            $this->load->language('extension/maza/notification');

            $data['mz_notification'] = $this->url->link('extension/maza/account/notification/channel', '', true);
        }
    }
}
