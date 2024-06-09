<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventModelToolImage extends Controller {
	/**
	 * Add placeholder image to missing image src
	 */
	public function resizeBefore(string $route, array &$param) {
		list($filename, $width, $height) = $param;

		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return $filename ? $this->url->link('extension/maza/tool/image/placeholder', 'width=' . $width . '&height=' . $height) : false;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		if ($extension == 'gif') {
			if ($this->request->server['HTTPS']) {
				return $this->config->get('config_ssl') . 'image/' . $filename;
			} else {
				return $this->config->get('config_url') . 'image/' . $filename;
			}
		}

		if (!isset($param[3])) {
			$param[3] = $this->mz_skin_config->get('catalog_image_scale');
		}

		if (!isset($param[4])) {
			$param[4] = $this->mz_skin_config->get('catalog_image_quality');
		}
	}
}