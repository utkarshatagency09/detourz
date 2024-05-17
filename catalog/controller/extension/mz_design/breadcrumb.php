<?php
class ControllerExtensionMzDesignBreadcrumb extends maza\layout\Design {
	public function index(array $setting): string {
        $data = array();

        $data['home'] = $this->url->link('common/home');
        $data['items'] = array();

        foreach($setting['design_breadcrumb']??[] as $breadcrumb_item){
            // Name
            $name = maza\getOfLanguage($breadcrumb_item['name']);

            if(!$breadcrumb_item['status'] || !$name) continue;
            
            // Url
            $url = array();
            if ($breadcrumb_item['url_link_code']) {
                $url = $this->model_extension_maza_common->createLink($breadcrumb_item['url_link_code']);
            }
            
            $data['items'][] = array(
                'name' => $name,
                'url'  => $url,
                'sort_order' => $breadcrumb_item['sort_order'],
            );
        }

        if($data['items']){
            array_multisort(array_column($data['items'], 'sort_order'), SORT_ASC, SORT_NUMERIC, $data['items']);

            return $this->load->view('extension/mz_design/breadcrumb', $data);
        }

        return '';
	}
}