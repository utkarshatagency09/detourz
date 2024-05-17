<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaLayoutBuilderSettingContent extends Controller {
        
        /**
         * Edit indivisual setting of content
         * @method POST
         */
        public function index(){
                if(!empty($this->request->get['code'])){
                        $code = str_replace('.', '/', $this->request->get['code']);

                        $this->load->language('extension/mz_content/'. $code);
                        
                        $this->load->controller('extension/mz_content/'. $code);

                        $data['content'] = $this->response->getOutput();
                        $data['target_id'] = $this->request->get['target_id'];

                        $this->response->setOutput($this->load->view('extension/maza/layout_builder/setting_content', $data));
                }
        }
}
