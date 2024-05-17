<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewCommonColumnRight extends Controller {
    public function before(string $route, array &$data): void {
        // Layout builder
		if($this->config->get('mz_layout_type') === 'default'){
            $layout_builder = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_column_right', 'group_owner' => $this->config->get('mz_layout_id')]);

            if ($layout_builder) {
                $data['modules'][] = $layout_builder;
            }
        }
    }
}
