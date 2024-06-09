<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2021, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventRecentViewed extends Controller {
    public function beforeController(string $route): void {
        // Register product in recent view list
        if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];

            if(empty($this->session->data['mz_recent_viewed_product'])){
                $this->session->data['mz_recent_viewed_product'] = [];
            }

            if(in_array($product_id, $this->session->data['mz_recent_viewed_product'])){
                unset($this->session->data['mz_recent_viewed_product'][array_search($product_id, $this->session->data['mz_recent_viewed_product'])]);
            }

            if(count($this->session->data['mz_recent_viewed_product']) > 24){
                array_pop($this->session->data['mz_recent_viewed_product']);
            }

            array_unshift($this->session->data['mz_recent_viewed_product'], $product_id);
        }

        // Register article in recent view list
        if (isset($this->request->get['article_id'])) {
			$article_id = (int)$this->request->get['article_id'];

            if(empty($this->session->data['mz_recent_viewed_article'])){
                $this->session->data['mz_recent_viewed_article'] = [];
            }

            if(in_array($article_id, $this->session->data['mz_recent_viewed_article'])){
                unset($this->session->data['mz_recent_viewed_article'][array_search($article_id, $this->session->data['mz_recent_viewed_article'])]);
            }

            if(count($this->session->data['mz_recent_viewed_article']) > 24){
                array_pop($this->session->data['mz_recent_viewed_article']);
            }

            array_unshift($this->session->data['mz_recent_viewed_article'], $article_id);
        }
    }
}
