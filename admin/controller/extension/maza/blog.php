<?php
/**
 * @package        MazaTheme
 * @author         Jay padaliya
 * @copyright      Copyright (c) 2020, TemplateMaza
 * @license        https://themeforest.net/licenses
 * @link           https://pocotheme.com/
 */

class ControllerExtensionMazaBlog extends Controller {
    public function index() {
        $this->load->language('extension/maza/blog');

        $this->document->setTitle($this->language->get('heading_title'));

        $url = '';

        if (isset($this->request->get['mz_theme_code'])) {
            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
        }

        if (isset($this->request->get['mz_skin_id'])) {
            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
        }

        // Header
        $header_data                 = array();
        $header_data['title']        = $this->language->get('heading_title');
        $header_data['theme_select'] = $header_data['skin_select'] = false;

        $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

        // Report

        // Total Article in this month
        $this->load->model('extension/maza/blog/article');
        $data['total_article'] = $this->model_extension_maza_blog_article->getTotalArticles(['filter_start_date' => date('Y-m-1')]);
        $data['article']       = $this->url->link('extension/maza/blog/article', 'user_token=' . $this->session->data['user_token'] . '&filter_start_date=' . date('Y-m-1') . $url);

        // Total comment today
        $this->load->model('extension/maza/blog/comment');
        $data['total_comment'] = $this->model_extension_maza_blog_comment->getTotalComments(['filter_date_added' => date('Y-m-d')]);
        $data['comment']       = $this->url->link('extension/maza/blog/comment', 'user_token=' . $this->session->data['user_token'] . '&filter_date_added=' . date('Y-m-d') . $url);

        // Total author
        $this->load->model('extension/maza/blog/author');
        $data['total_author'] = $this->model_extension_maza_blog_author->getTotalAuthors();
        $data['author']       = $this->url->link('extension/maza/blog/author', 'user_token=' . $this->session->data['user_token'] . $url, true);

        // Total article viewed today
        $this->load->model('extension/maza/blog/report');
        $data['total_viewed'] = $this->model_extension_maza_blog_report->getTotalArticleViews();
        $data['report']       = $this->url->link('extension/maza/blog/report', 'user_token=' . $this->session->data['user_token'] . $url, true);

        // Latest article
        $data['articles'] = array();

        $filter_data = array(
            'sort' => 'a.date_added',
            'order' => 'DESC',
            'start' => 0,
            'limit' => 5,
        );

        $results = $this->model_extension_maza_blog_article->getArticles($filter_data);

        foreach ($results as $result) {
            $data['articles'][] = array(
                'article_id' => $result['article_id'],
                'name' => $result['name'],
                'author' => $result['author'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'view' => $this->url->link('extension/maza/blog/article/edit', 'user_token=' . $this->session->data['user_token'] . '&article_id=' . $result['article_id'], true),
            );
        }

        // Data
        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if (isset($this->session->data['warning'])) {
            $data['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        $data['header']         = $this->load->controller('extension/maza/common/header/main');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
        $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

        $this->response->setOutput($this->load->view('extension/maza/blog', $data));
    }

    public function chart() {
        $this->load->language('extension/maza/blog');

        $json = array();

        $this->load->model('extension/maza/blog');

        $json['article'] = array();
        $json['comment'] = array();
        $json['xaxis']   = array();

        $json['article']['label'] = $this->language->get('text_article');
        $json['comment']['label'] = $this->language->get('text_comment');
        $json['article']['data']  = array();
        $json['comment']['data']  = array();

        if (isset($this->request->get['range'])) {
            $range = $this->request->get['range'];
        } else {
            $range = 'day';
        }

        switch ($range) {
            default:
            case 'day':
                $results = $this->model_extension_maza_blog->getTotalArticlesByDay();

                foreach ($results as $key => $value) {
                    $json['article']['data'][] = array($key, $value['total']);
                }

                $results = $this->model_extension_maza_blog->getTotalCommentsByDay();

                foreach ($results as $key => $value) {
                    $json['comment']['data'][] = array($key, $value['total']);
                }

                for ($i = 0; $i < 24; $i++) {
                    $json['xaxis'][] = array($i, $i);
                }
                break;
            case 'week':
                $results = $this->model_extension_maza_blog->getTotalArticlesByWeek();

                foreach ($results as $key => $value) {
                    $json['article']['data'][] = array($key, $value['total']);
                }

                $results = $this->model_extension_maza_blog->getTotalCommentsByWeek();

                foreach ($results as $key => $value) {
                    $json['comment']['data'][] = array($key, $value['total']);
                }

                $date_start = strtotime('-' . date('w') . ' days');

                for ($i = 0; $i < 7; $i++) {
                    $date = date('Y-m-d', $date_start + ($i * 86400));

                    $json['xaxis'][] = array(date('w', strtotime($date)), date('D', strtotime($date)));
                }
                break;
            case 'month':
                $results = $this->model_extension_maza_blog->getTotalArticlesByMonth();

                foreach ($results as $key => $value) {
                    $json['article']['data'][] = array($key, $value['total']);
                }

                $results = $this->model_extension_maza_blog->getTotalCommentsByMonth();

                foreach ($results as $key => $value) {
                    $json['comment']['data'][] = array($key, $value['total']);
                }

                for ($i = 1; $i <= date('t'); $i++) {
                    $date = date('Y') . '-' . date('m') . '-' . $i;

                    $json['xaxis'][] = array(date('j', strtotime($date)), date('d', strtotime($date)));
                }
                break;
            case 'year':
                $results = $this->model_extension_maza_blog->getTotalArticlesByYear();

                foreach ($results as $key => $value) {
                    $json['article']['data'][] = array($key, $value['total']);
                }

                $results = $this->model_extension_maza_blog->getTotalCommentsByYear();

                foreach ($results as $key => $value) {
                    $json['comment']['data'][] = array($key, $value['total']);
                }

                for ($i = 1; $i <= 12; $i++) {
                    $json['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i)));
                }
                break;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}