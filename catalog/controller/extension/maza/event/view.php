<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventView extends Controller {
	public function before(string $route, array &$data): void {
		$data['oc_config'] = $this->config;
		$data['oc_load'] = $this->load;
		$data['oc_url'] = $this->url;
		$data['mz_config'] = $this->mz_skin_config;
		$data['mz_hook'] = $this->mz_hook;
		$data['oc_load'] = $this->load;

		if (!empty($data['column_left'])) {
			$data['column_left'] = $this->mz_load->view($data['column_left'], $data);
		}
		if (!empty($data['column_right'])) {
			$data['column_right'] = $this->mz_load->view($data['column_right'], $data);
		}
		if (!empty($data['content_top'])) {
			$data['content_top'] = $this->mz_load->view($data['content_top'], $data);
		}
		if (!empty($data['content_bottom'])) {
			$data['content_bottom'] = $this->mz_load->view($data['content_bottom'], $data);
		}
	}

	public function debug($route, $data, $output) {
		$route = explode('/', $route);
		$filename = array_pop($route) . '.twig';
		$loc = DIR_CACHE . 'debug_view/' . implode('/', $route) . '/';

		maza\createDirPath($loc);

		file_put_contents($loc . $filename, $output);
	}
}
