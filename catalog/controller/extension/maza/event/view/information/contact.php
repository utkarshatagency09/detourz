<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewInformationContact extends Controller {
    public function before(string $route, array &$data): void {
		if ($this->config->get('config_image')) {
			$data['image'] = $this->config->get('mz_store_url') . 'image/' . $this->config->get('config_image');
		}
    }
}
