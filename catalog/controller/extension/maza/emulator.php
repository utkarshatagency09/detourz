<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEmulator extends Controller {
    public function index() {
        $this->load->language('extension/maza/emulator');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $data = array();
        
        $data['breakpoints'] = array();
        
        foreach($this->mz_skin_config->get('style_breakpoints') as $code => $width){
            if($code == 'xs'){
                $width = '375px';
            } else {
                $width .= 'px';
            }
            
            $data['breakpoints'][] = array(
                'width'  => $width,
                'height' => '100%',
                'name'   => $this->language->get('text_' . $code)
            );
        }
        
        $data['breakpoints'][] = array(
            'width'  => '375px',
            'height' => '812px',
            'name'   => $this->language->get('text_iphone')
        );
        
        $data['breakpoints'][] = array(
            'width'  => '768px',
            'height' => '1024px',
            'name'   => $this->language->get('text_ipad')
        );
        
        $data['breakpoints'][] = array(
            'width'  => '1440px',
            'height' => '900px',
            'name'   => $this->language->get('text_laptop')
        );
        
        if(isset($this->request->get['url'])){
            $data['url'] = $this->request->get['url'];
        } else {
            $data['url'] = '';
        }
        
        if(isset($this->request->get['width'])){
            $data['width'] = $this->request->get['width'];
        } else {
            $data['width'] = '100%';
        }
        
        if(isset($this->request->get['height'])){
            $data['height'] = $this->request->get['height'];
        } else {
            $data['height'] = '100%';
        }

        // Load document data from header and footer
        $this->load->controller('common/header');
        $this->load->controller('common/footer');
        
        // $this->document->addStyle('catalog/view/theme/' . $this->mz_theme_config->get('theme_code') . '/asset/stylesheet/' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '/main.' . $this->session->data['language'] . '.css');
        
        $data['styles'] = array_merge($this->document->getStyles('header'), $this->document->getStyles('footer'));
        
        $data['mz_cdn'] = $this->config->get('maza_cdn');
        
        $this->response->setOutput($this->load->view('extension/maza/emulator', $data));
    }
}
