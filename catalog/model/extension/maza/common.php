<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaCommon extends model {

    // Create link from code
    public function createLink(string $code): array {
        $link = array();

        $exploded = explode('.', $code);
        $type = array_shift($exploded);
        $value = implode('.', $exploded);

        switch($type){
            case 'system': $link['href'] = $this->getSystemLink($value);
                break;
            case 'information': $link['href'] = $this->url->link('information/information', 'information_id=' . (int)$value);
                break;
            case 'category': $link['href'] = $this->url->link('product/category', 'path=' . $this->model_extension_maza_catalog_category->getCategoryPath($value));
                break;
            case 'product': $link['href'] = $this->url->link('product/product', 'product_id=' . (int)$value);
                break;
            case 'manufacturer': $link['href'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . (int)$value);
                break;
            case 'blog_category': $link['href'] = $this->url->link('extension/maza/blog/category', 'path=' . (int)$value);
                break;
            case 'blog_article': $link['href'] = $this->url->link('extension/maza/blog/article', 'article_id=' . (int)$value);
                break;
            case 'page_builder': $link['href'] = $this->url->link('extension/maza/page', 'page_id=' . (int)$value);
                break;
            case 'custom': $link['href'] = $value;
                break;
            case 'route': $link['href'] = $this->url->link($value);
                break;
        }

        if ($type == 'popup') {
            $link['href'] = '#' . $value;
            $link['attr'] = 'data-bs-toggle="modal" role="button"';
        }
        if ($type == 'drawer') {
            $link['href'] = '#' . $value;
            $link['attr'] = 'data-bs-toggle="offcanvas" role="button" aria-expanded="false" aria-controls="' . $value . '"';
        }
        if ($type == 'collapsible') {
            $link['href'] = '#' . $value;
            $link['attr'] = 'data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="' . $value . '"';
        }

        return $link;
    }

    private function getSystemLink(string $page): string {
        // Common
        if(in_array($page, array('home'))){
            return $this->url->link('common/' . $page);
        }
        
        // Account
        if(in_array($page, array('wishlist', 'account', 'login', 'logout', 'register', 'return', 'voucher', 'order'))){
            
            // Return
            $page = ($page == 'return')?$page . '/add': $page;
            
            return $this->url->link('account/' . $page);
        }
        
        // Product
        if(in_array($page, array('compare', 'special', 'search', 'manufacturer'))){
            return $this->url->link('product/' . $page);
        }
        
        // Checkout
        if(in_array($page, array('checkout', 'cart'))){
            return $this->url->link('checkout/' . $page);
        }
        
        // Information
        if(in_array($page, array('contact', 'sitemap', 'tracking'))){
            return $this->url->link('information/' . $page);
        }
        
        // Affiliate
        if(in_array($page, array('affiliate_login', 'affiliate_register'))){
            
            $page = ($page == 'affiliate_login') ? 'login' : 'register';
            
            return $this->url->link('affiliate/' . $page);
        }
        
        // Blog
        if(strpos($page, 'blog/') === 0){
            return $this->url->link('extension/maza/' . $page);
        }
        
        // Extension maza
        if(strpos($page, 'maza/') === 0){
            return $this->url->link('extension/' . $page);
        }

        return '';
    }
    
    /**
     * Send mail
     * @param string $to email id
     * @param string $subject mail subject
     * @param string $body mail body
     * @return NULL
     */
    public function sendMail(string $to, string $subject, string $body, array $option = array()): void {
        // Message
        if(strpos($body, '<html') === false){
            $message  = '<html dir="' . $this->language->get('direction') . '" lang="en">' . "\n";
            $message .= '  <head>' . "\n";
            $message .= '    <title>' . $subject . '</title>' . "\n";
            $message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
            $message .= '  </head>' . "\n";
            $message .= '  <body>' . html_entity_decode($body, ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
            $message .= '</html>' . "\n";
        } else {
            $message = $body;
        }
        
        
        // Mail
        if (version_compare(VERSION, '3.0.0.0') < 0) {
            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
        } else {
            $mail = new Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
        }

        $mail->setTo($to);
        $mail->setFrom($this->config->get('config_email'));

        if(!empty($option['replyTo'])){
            $mail->setReplyTo($option['replyTo']);
        }

        $mail->setSender(html_entity_decode($option['sender']??$this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
        $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
        $mail->setHtml($message);
        $mail->send();
    }
}
