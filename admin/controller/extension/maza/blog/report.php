<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2018, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaBlogReport extends Controller {
	public function index(): void {
		$this->load->language('extension/maza/blog/report');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/blog/report');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'a.viewed';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
		
		if(isset($this->request->get['mz_theme_code'])){
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if(isset($this->request->get['mz_skin_id'])){
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}
		
		// Header
		$header_data = array();
		$header_data['title'] = $this->language->get('text_list');
		$header_data['theme_select'] = $header_data['skin_select'] = false;
		// $header_data['menu'] = array();
		// if ($this->user->hasPermission('access', 'extension/maza/blog')) $header_data['menu'][] = array('name' => $this->language->get('tab_dashboard'), 'id' => 'tab-mz-dashboard', 'href' => $this->url->link('extension/maza/blog', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/blog/category')) $header_data['menu'][] = array('name' => $this->language->get('tab_category'), 'id' => 'tab-mz-category', 'href' => $this->url->link('extension/maza/blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/blog/article')) $header_data['menu'][] = array('name' => $this->language->get('tab_article'), 'id' => 'tab-mz-article', 'href' => $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/blog/author')) $header_data['menu'][] = array('name' => $this->language->get('tab_author'), 'id' => 'tab-mz-author', 'href' => $this->url->link('extension/maza/blog/author', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// if ($this->user->hasPermission('access', 'extension/maza/blog/comment')) $header_data['menu'][] = array('name' => $this->language->get('tab_comment'), 'id' => 'tab-mz-comment', 'href' => $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . $url, true));
		// $header_data['menu'][] = array('name' => $this->language->get('tab_report'), 'id' => 'tab-mz-report', 'href' => false);
		// if ($this->user->hasPermission('access', 'extension/maza/blog/setting')) $header_data['menu'][] = array('name' => $this->language->get('tab_setting'), 'id' => 'tab-mz-setting', 'href' => $this->url->link('extension/maza/blog/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
		
		
		// $header_data['menu_active'] = 'tab-mz-report';
		$header_data['buttons'][] = array(
			'id' => 'button-reset',
			'name' => '',
			'tooltip' => $this->language->get('button_reset'),
			'icon' => 'fa-refresh',
			'class' => 'btn-danger',
			'href' => FALSE,
			'target' => FALSE,
			'form_target_id' => 'form-mz-report',
			'confirm' => $this->language->get('text_confirm')
		);
		$header_data['form_target_id'] = 'form-mz-report';
		
		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
		
		// Report list
		$url = '';
		if(isset($this->request->get['mz_theme_code'])){
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if(isset($this->request->get['mz_skin_id'])){
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
                
		$data['reset'] = $this->url->link('extension/maza/blog/report/reset', 'user_token=' . $this->session->data['user_token'] . $url, true);
                
		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'sort' => $sort,
			'order' => $order,
		);

		$data['articles'] = array();

		$article_viewed_total = $this->model_extension_maza_blog_report->getTotalArticleViews();

		$article_total = $this->model_extension_maza_blog_report->getTotalArticlesViewed();

		$results = $this->model_extension_maza_blog_report->getArticlesViewed($filter_data);

		foreach ($results as $result) {
			if ($result['viewed']) {
				$percent = round($result['viewed'] / $article_viewed_total * 100, 2);
			} else {
				$percent = 0;
			}

			$data['articles'][] = array(
				'name'    => $result['name'],
				'viewed'  => $result['viewed'],
				'percent' => $percent . '%',
			);
		}

		if(isset($this->session->data['warning'])){
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}elseif (isset($this->error['warning'])) {
			$data['warning'] = $this->error['warning'];
		}
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
                
		// Sort order
		$url = '';
                
		if(isset($this->request->get['mz_theme_code'])){
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if(isset($this->request->get['mz_skin_id'])){
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/maza/blog/report', 'user_token=' . $this->session->data['user_token'] . '&sort=ad.name' . $url, true);
		$data['sort_viewed'] = $this->url->link('extension/maza/blog/report', 'user_token=' . $this->session->data['user_token'] . '&sort=a.viewed' . $url, true);
                
                
		$data['sort'] = $sort;
		$data['order'] = $order;
                
		// Pagination
		$url = '';
                
		if(isset($this->request->get['mz_theme_code'])){
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if(isset($this->request->get['mz_skin_id'])){
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $article_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/maza/blog/report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($article_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($article_total - $this->config->get('config_limit_admin'))) ? $article_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $article_total, ceil($article_total / $this->config->get('config_limit_admin')));

		$data['user_token'] = $this->session->data['user_token'];
                
		// Columns
		$data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		$this->response->setOutput($this->load->view('extension/maza/blog/report', $data));
	}
        
	/**
	 * Reset report
	 */
	public function reset() {
		$this->load->language('extension/maza/blog/report');

		if (!$this->user->hasPermission('modify', 'extension/maza/blog/report')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/maza/blog/report');

			$this->model_extension_maza_blog_report->reset();

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->response->redirect($this->url->link('extension/maza/blog/report', 'user_token=' . $this->session->data['user_token'], true));
	}
}
