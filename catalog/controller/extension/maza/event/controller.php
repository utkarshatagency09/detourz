<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventController extends Controller {
	public function minify(): void {
		$output = $this->response->getOutput();

		if($this->config->get('maza_minify_html') && $output){
			$search = array(
				'/\>[^\S ]+/s',     // strip whitespaces after tags, except space
				'/[^\S ]+\</s',     // strip whitespaces before tags, except space
				'/\s*\n\s*/s',      // shorten multiple whitespace sequences by newline
				'/(\s)+/s',         // shorten multiple horizontal sequences
			);

			$replace = array('>', '<', "\n", '\\1');

			$this->response->setOutput(preg_replace($search, $replace, $output));
		}
	}
}
