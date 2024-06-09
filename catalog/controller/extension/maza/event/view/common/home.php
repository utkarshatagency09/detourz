<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewCommonHome extends Controller {
    public function before(string $route, array &$data): void {
        // Layout builder
		$data['mz_content'] = $this->mz_load->view($this->mz_cache->getVar('mz_content'), $data);
		$data['mz_component'] = $this->mz_load->view($this->mz_cache->getVar('mz_component'), $data);
    }
}
