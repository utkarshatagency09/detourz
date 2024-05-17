<?php
class ControllerExtensionModuleMzEngine extends Controller {
	public function index() {
                if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                    $url = 'token=' . $this->session->data['token'];
                } else {
                    $url = 'user_token=' . $this->session->data['user_token'];
                }
            
                if(!$this->config->get('mz_version') || version_compare(MZ_CONST::VERSION, $this->config->get('mz_version')) > 0){
                    $this->response->redirect($this->url->link('extension/maza/install/engine', $url, true));
                } else {
                    $this->response->redirect($this->url->link('extension/maza/skin', $url, true));
                }
	}
        
    public function install(){
            $this->load->model('user/user_group');
            
            // Add Permission for install page
            $routes = array('extension/maza', 'extension/maza/install/engine', 'extension/maza/install/theme');
            
            foreach($routes as $route){
                $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', $route);
                $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $route);
            }
    }
}