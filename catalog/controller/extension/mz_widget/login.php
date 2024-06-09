<?php
class ControllerExtensionMzWidgetLogin extends maza\layout\Widget {
    public function index(array $setting): string {
        $this->load->language('extension/mz_widget/login');
        
        if($this->customer->isLogged()){
            return '';
        }
        
        $data['title'] = maza\getOfLanguage($setting['widget_title']);
        
        $data['size']             = $setting['widget_size'];
        $data['color']            = $setting['widget_color'];
        
        $data['action'] = $this->url->link('account/login', '', true);
        $data['forgotten'] = $this->url->link('account/forgotten', '', true);
        $data['register'] = $this->url->link('account/register', '', true);
        
        if (!isset($this->request->get['route'])) {
            $data['redirect'] = $this->url->link('common/home');
        } else {
            $url_data = $this->request->get;

            unset($url_data['_route_']);

            $route = $url_data['route'];

            unset($url_data['route']);

            $url = '';

            if ($url_data) {
                $url = '&' . urldecode(http_build_query($url_data, '', '&'));
            }

            $data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
        }

        $data['socialauth'] = $this->load->controller('extension/maza/account/socialauth', ['size' => $setting['widget_size']]);
        
        return $this->load->view('extension/mz_widget/login', $data);
    }

    /**
     * Change default setting
     */
    public function getSettings(): array {
        $setting = parent::getSettings();
        
        $setting['widget_status_customer'] = 'guest';
        $setting['widget_cache'] = 'hard';
        
        return $setting;
    }
}
