<?php
class ControllerExtensionMazaCommonFontIconManager extends Controller {
	public function index() {
		$this->load->language('extension/maza/common/font_icon_manager');
                $this->load->model('extension/maza/asset');

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		// Make sure we have the correct package
		if (isset($this->request->get['package'])) {
			$package = $this->request->get['package'];
		} else {
			$package = false;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
                
                // Get list of packages or icons of package
                $items = array();
                
                if($package && file_exists(MZ_CONFIG::$DIR_THEME_ASSET . 'font_icon/' . $package . '.php')){
                    $_ = array();
                    
                    require_once MZ_CONFIG::$DIR_THEME_ASSET . 'font_icon/' . $package . '.php';
                    
                    // Font icon
                    $items = array_map(function($icon){
                        $icon['type'] = 'icon';
                        return $icon;
                    }, $_['icons']);
                    
                    // Font icon style
                    if(isset($_['style_class'])){
                        $data['style_class'] = $_['style_class'];
                    }
                } else {
                    $items = $this->model_extension_maza_asset->getFontIconPackages();
                }
                
                // filter icon or package by name filter
                if($filter_name){
                    $items = array_filter($items, function($item) use($filter_name) {
                        if(stripos($item['name'], $filter_name) !== false){
                            return TRUE;
                        }
                    });
                }
                
                // Get total number of package or icons
		$item_total = count($items);
//
//		// Split the array based on current page number and max number of items per page of 10
		$items = array_splice($items, ($page - 1) * 18, 18);
                
                $data['items'] = array();
                
                foreach ($items as $item) {
                    if($item['type'] == 'package'){
                        $url = '';
                        
                        if (isset($this->request->get['mz_theme_code'])) {
                                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                        }
                        if (isset($this->request->get['mz_skin_id'])) {
                                $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                        }
                        if (isset($this->request->get['target'])) {
                                $url .= '&target=' . $this->request->get['target'];
                        }
                        if (isset($this->request->get['thumb'])) {
                                $url .= '&thumb=' . $this->request->get['thumb'];
                        }
                        
                        $item['href'] = $this->url->link('extension/maza/common/font_icon_manager', 'user_token=' . $this->session->data['user_token'] . '&package=' . $item['code'] . $url, true);
                    }
                    
                    $data['items'][] = $item;
                }


		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['package'])) {
			$data['package'] = urlencode($this->request->get['package']);
		} else {
			$data['package'] = '';
		}

		if (isset($this->request->get['filter_name'])) {
			$data['filter_name'] = $this->request->get['filter_name'];
		} else {
			$data['filter_name'] = '';
		}

		// Return the target ID for the file manager to set the value
		if (isset($this->request->get['target'])) {
			$data['target'] = $this->request->get['target'];
		} else {
			$data['target'] = '';
		}

		// Return the thumbnail for the file manager to show a thumbnail
		if (isset($this->request->get['thumb'])) {
			$data['thumb'] = $this->request->get['thumb'];
		} else {
			$data['thumb'] = '';
		}

		// Parent
		$url = '';
                
                if (isset($this->request->get['mz_theme_code'])) {
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if (isset($this->request->get['mz_skin_id'])) {
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }

		if (isset($this->request->get['package'])) {
			$pos = strrpos($this->request->get['package'], '/');

			if ($pos) {
				$url .= '&package=' . urlencode(substr($this->request->get['package'], 0, $pos));
			}
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$data['parent'] = $this->url->link('extension/maza/common/font_icon_manager', 'user_token=' . $this->session->data['user_token'] . $url, true);


		$url = '';
                
                if (isset($this->request->get['mz_theme_code'])) {
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if (isset($this->request->get['mz_skin_id'])) {
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }

		if (isset($this->request->get['package'])) {
			$url .= '&package=' . urlencode(html_entity_decode($this->request->get['package'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$pagination = new Pagination();
		$pagination->total = $item_total;
		$pagination->page = $page;
		$pagination->limit = 18;
		$pagination->url = $this->url->link('extension/maza/common/font_icon_manager', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
                
                $data['url'] = '';
                
                if (isset($this->request->get['mz_theme_code'])) {
                        $data['url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if (isset($this->request->get['mz_skin_id'])) {
                        $data['url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }

		$this->response->setOutput($this->load->view('extension/maza/common/font_icon_manager', $data));
	}
}