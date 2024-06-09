<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaCommonClear extends Controller {
    public function index() {
        $this->load->language('extension/maza/common/clear');
        
        if($this->user->hasPermission('modify', 'extension/maza/common/clear')){
            $this->load->model('extension/maza/common');
            
            $this->model_extension_maza_common->clearCache();

            maza\emptyFolder(DIR_CACHE);
            maza\emptyFolder(DIR_IMAGE . 'cache');
            
            $this->session->data['success'] = $this->language->get('text_success');
        } else {
            $this->session->data['warning'] = $this->language->get('error_permission');
        }
        
        $this->response->redirect(html_entity_decode($this->request->get['redirect']));
    }
}
