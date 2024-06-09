<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaExtension extends model {
    /**
     * Check extension is installed or not
     * @param string $type extension type
     * @param string $code code of extension
     * @return boolean
     */
    public function hasInstalled(string $type, string $code): bool {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `type` = '" . $this->db->escape($type) . "' AND `code` = '" . $this->db->escape($code) . "'");
        
        if($query->row){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get module output from given module code
     * @param string $code module code
     * @param string $suffix unique suffix to discrete content of same module instance
     * @return string
     */
    public function getModuleOutput(string $code, string $suffix = null): string{
        $part = explode('.', $code);

        if (!isset($part[1]) && ($this->config->get('module_' . $part[0] . '_status') || $this->config->get($part[0] . '_status'))) {
            return $this->load->controller('extension/module/' . $part[0]);
        }

        if (isset($part[1])) {
            $setting = $this->model_extension_maza_opencart->getModule($part[1]);

            if ($setting && $setting['status']) {

                if($suffix){
                    $setting['mz_suffix'] = $suffix;
                }

                return $this->load->controller('extension/module/' . $part[0], $setting);
            }
        }
    }

    public function getWidgetOutput(string $code, string $raw_data, string $suffix = null): string{
        $widget_data = array();
        parse_str(html_entity_decode($raw_data), $widget_data);

        if($widget_data && $widget_data['widget_status']){

            if($suffix){
                $widget_data['mz_suffix'] = $suffix;
            }

            return $this->load->controller('extension/mz_widget/' . $code, $widget_data);
        }
        return '';
    }
}
