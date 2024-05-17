<?php

/**
 * @package        MazaTheme
 * @author        Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license        https://themeforest.net/licenses
 * @link        https://pocotheme.com/
 */
class ModelExtensionMazaInstall extends model {
    public function addHooks() {
        $this->deleteHooks();

        // Product
        $this->mz_hook->addHook('mz_catalog_product_data_list', 'catalog_product_data_list', 'extension/maza/hooks/product/AddToProductDataList');
        $this->mz_hook->addHook('mz_catalog_products_view', 'catalog_products_view', 'extension/maza/hooks/product/view');
        $this->mz_hook->addHook('mz_catalog_product_detail', 'catalog_product_detail', 'extension/maza/hooks/product/detail');

        // Catalog data
        $this->mz_hook->addHook('mz_catalog_data', 'tab', 'extension/maza/hooks/data/tab');
        $this->mz_hook->addHook('mz_catalog_data', 'accordion', 'extension/maza/hooks/data/accordion');
        $this->mz_hook->addHook('mz_catalog_data', 'faq', 'extension/maza/hooks/data/faq');
        $this->mz_hook->addHook('mz_catalog_data', 'popup', 'extension/maza/hooks/data/popup');
    }

    public function deleteHooks() {
        // Product
        $this->mz_hook->deleteHooks('mz_catalog_product_data_list');
        $this->mz_hook->deleteHooks('mz_catalog_products_view');
        $this->mz_hook->deleteHooks('mz_catalog_product_detail');

        // Catalog data
        $this->mz_hook->deleteHooks('mz_catalog_data');
    }

    public function intallOCModXml() {
        if (version_compare(VERSION, '3.0.0.0') < 0) { // For opencart 2
            $this->load->model('extension/modification');
            $model_modification = $this->model_extension_modification;
        } else {
            $this->load->model('setting/modification');
            $model_modification = $this->model_setting_modification;
        }

        $this->db->query("UPDATE `" . DB_PREFIX . "modification` SET `status` = 0 WHERE `code` LIKE 'maza_%'");

        $files = glob(DIR_SYSTEM . 'library/maza/ocmod/*.ocmod.xml');

        foreach ($files as $file) {
            if (is_file($file)) {
                // If xml file just put it straight into the DB
                $xml = file_get_contents($file);

                if ($xml) {
                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->loadXml($xml);

                    $name = $dom->getElementsByTagName('name')->item(0);

                    if ($name) {
                        $name = $name->nodeValue;
                    } else {
                        $name = '';
                    }

                    $code = $dom->getElementsByTagName('code')->item(0);

                    if ($code) {
                        $code = $code->nodeValue;

                        // Check to see if the modification is already installed or not.
                        $modification_info = $model_modification->getModificationByCode($code);

                        if ($modification_info) {
                            $model_modification->deleteModification($modification_info['modification_id']);
                        }
                    } else {
                        throw new Exception($this->language->get('error_code'));
                    }

                    $author = $dom->getElementsByTagName('author')->item(0);

                    if ($author) {
                        $author = $author->nodeValue;
                    } else {
                        $author = '';
                    }

                    $version = $dom->getElementsByTagName('version')->item(0);

                    if ($version) {
                        $version = $version->nodeValue;
                    } else {
                        $version = '';
                    }

                    $link = $dom->getElementsByTagName('link')->item(0);

                    if ($link) {
                        $link = $link->nodeValue;
                    } else {
                        $link = '';
                    }

                    $modification_data = array(
                        'extension_install_id' => 0,
                        'name'                 => $name,
                        'code'                 => $code,
                        'author'               => $author,
                        'version'              => $version,
                        'link'                 => $link,
                        'xml'                  => $xml,
                        'status'               => 1,
                    );

                    $model_modification->addModification($modification_data);
                }
            }
        }
    }

    public function unintallOCModXml() {
        if (version_compare(VERSION, '3.0.0.0') < 0) { // For opencart 2
            $this->load->model('extension/modification');
            $model_modification = $this->model_extension_modification;
        } else {
            $this->load->model('setting/modification');
            $model_modification = $this->model_setting_modification;
        }

        $this->db->query("UPDATE `" . DB_PREFIX . "modification` SET `status` = 0 WHERE `code` LIKE 'maza_%'");

        $files = glob(DIR_SYSTEM . 'library/maza/ocmod/*.ocmod.xml');

        foreach ($files as $file) {
            if (is_file($file)) {
                // If xml file just put it straight into the DB
                $xml = file_get_contents($file);

                if ($xml) {
                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->loadXml($xml);

                    $code = $dom->getElementsByTagName('code')->item(0);

                    if ($code) {
                        $code = $code->nodeValue;

                        // Check to see if the modification is already installed or not.
                        $modification_info = $model_modification->getModificationByCode($code);

                        if ($modification_info) {
                            $model_modification->deleteModification($modification_info['modification_id']);
                        }
                    } else {
                        throw new Exception($this->language->get('error_code'));
                    }
                }
            }
        }
    }

    public function updateDatabase() {
        $this->db->query("SET SESSION sql_mode = ''");

        ### Maza core DB ###
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_skin` (
                    `skin_id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `theme_id` SMALLINT NOT NULL,
                    `skin_code` VARCHAR(100),
                    `name` VARCHAR(64) NOT NULL,
                    `parent_skin_id` SMALLINT NOT NULL DEFAULT 0,
                    `status` INT(1) NOT NULL DEFAULT 0
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_skin_setting` (
                    `setting_id` MEDIUMINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `skin_id` SMALLINT NOT NULL,
                    `code` VARCHAR(128) NOT NULL,
                    `key` VARCHAR(128) NOT NULL,
                    `value` TEXT NOT NULL,
                    `is_serialized` TINYINT(1) NOT NULL,
                    UNIQUE KEY(`skin_id`, `key`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_theme` (
                    `theme_id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `theme_code` VARCHAR(100) NOT NULL,
                    `name` VARCHAR(100) NOT NULL,
                    `version` CHAR(10) NOT NULL,
                    `status` INT(1) NOT NULL DEFAULT 0,
                    UNIQUE KEY(`theme_code`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_theme_setting` (
                    `setting_id` MEDIUMINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `theme_id` SMALLINT NOT NULL,
                    `store_id` int(11) NOT NULL DEFAULT 0,
                    `code` VARCHAR(128) NOT NULL,
                    `key` VARCHAR(128) NOT NULL,
                    `value` TEXT NOT NULL,
                    `is_serialized` TINYINT(1) NOT NULL,
                    UNIQUE KEY(`theme_id`, `store_id`, `key`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_header` (
                    `header_id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `theme_id` SMALLINT NOT NULL,
                    `code` VARCHAR(100),
                    `name` VARCHAR(64) NOT NULL,
                    `parent_header_id` SMALLINT NOT NULL DEFAULT 0,
                    `status` INT(1) NOT NULL DEFAULT 0
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_footer` (
                    `footer_id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `theme_id` SMALLINT NOT NULL,
                    `code` VARCHAR(100),
                    `name` VARCHAR(64) NOT NULL,
                    `parent_footer_id` SMALLINT NOT NULL DEFAULT 0,
                    `status` INT(1) NOT NULL DEFAULT 0
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_hook` (
                    `hook_id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `code` VARCHAR(100) NOT NULL,
                    `action` VARCHAR(160) NOT NULL,
                    `status` TINYINT(1) NOT NULL DEFAULT 1,
                    `trigger` VARCHAR(100) NOT NULL,
                    UNIQUE KEY(`trigger`, `action`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_cache` (
                    `cache_id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `key` VARCHAR(255) NOT NULL,
                    `value` LONGTEXT NOT NULL,
                    `timestamp` INT UNSIGNED NOT NULL DEFAULT 0,
                    UNIQUE KEY(`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_layout_entry` (
                    `entry_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `parent_entry_id` INT NOT NULL DEFAULT 0,
                    `group` VARCHAR(50) NOT NULL,
                    `group_owner` INT NOT NULL,
                    `skin_id` SMALLINT NOT NULL,
                    `type` CHAR(10) NOT NULL,
                    `code` VARCHAR(100),
                    `setting` LONGTEXT,
                    INDEX(`group`, `group_owner`, `parent_entry_id`, `skin_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_module_setting` (
                    `setting_id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `module_id` INT(11) NOT NULL,
                    `skin_id` SMALLINT NOT NULL,
                    `setting` LONGTEXT NOT NULL
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_fonts` (
                    `font_id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `name` VARCHAR(100) NOT NULL,
                    `font_family` VARCHAR(100) NOT NULL UNIQUE KEY,
                    `type` CHAR(15) NOT NULL,
                    `parent_font_id` SMALLINT NOT NULL DEFAULT 0,
                    `url` TEXT
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_asset` (
                    `asset_id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `type` VARCHAR(20) NOT NULL,
                    `url` TEXT NOT NULL NOT NULL
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        ## menu ##
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_menu` (
                    `menu_id` MEDIUMINT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `name` VARCHAR(60) NOT NULL,
                    `status` TINYINT(1) NOT NULL DEFAULT 0,
                    `date_modified` datetime NOT NULL
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_menu_item` (
                    `item_id` MEDIUMINT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `menu_id` MEDIUMINT(11) NOT NULL,
                    `name` VARCHAR(60) NOT NULL,
                    `status` TINYINT(1) NOT NULL DEFAULT 0,
                    `customer` TINYINT(1) NOT NULL DEFAULT '0',
                    `customer_group_id` INT(11) NOT NULL,
                    `type` CHAR(15) NOT NULL,
                    `parent_item_id` INT(11) NOT NULL DEFAULT 0,
                    `sort_order` INT(11) NOT NULL,
                    `setting` TEXT
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        ## tags ##
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_to_tags` (
                    `product_id` int(11) NOT NULL,
                    `tag_id` int(11) NOT NULL,
                    PRIMARY KEY (`product_id`,`tag_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_tags` (
                    `tag_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `language_id` int(11) NOT NULL,
                    `name` varchar(255) DEFAULT NULL,
                    `viewed` int(11) NOT NULL DEFAULT '0',
                    `used` int(11) NOT NULL DEFAULT '0',
                    KEY `viewed` (`viewed`),
                    KEY `used` (`used`),
                    UNIQUE KEY (`language_id`, `name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_to_tags` (
                    `article_id` int(11) NOT NULL,
                    `tag_id` int(11) NOT NULL,
                    PRIMARY KEY (`article_id`,`tag_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_tags` (
                    `tag_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    `language_id` int(11) NOT NULL,
                    `name` varchar(255) DEFAULT NULL,
                    `viewed` int(11) NOT NULL DEFAULT '0',
                    `used` int(11) NOT NULL DEFAULT '0',
                    KEY `viewed` (`viewed`),
                    KEY `used` (`used`),
                    UNIQUE KEY (`language_id`, `name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        ## Blog ##
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_category` (
                    `category_id` int(11) NOT NULL AUTO_INCREMENT,
                    `image` varchar(255) DEFAULT NULL,
                    `parent_id` int(11) NOT NULL DEFAULT '0',
                    `top` tinyint(1) NOT NULL,
                    `column` int(3) NOT NULL,
                    `sort_order` int(3) NOT NULL DEFAULT '0',
                    `status` tinyint(1) NOT NULL,
                    `date_added` datetime NOT NULL,
                    `date_modified` datetime NOT NULL,
                    PRIMARY KEY (`category_id`),
                    KEY `parent_id` (`parent_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_category_description` (
                    `category_id` int(11) NOT NULL,
                    `language_id` int(11) NOT NULL,
                    `name` varchar(255) NOT NULL,
                    `description` text NOT NULL,
                    `meta_title` varchar(255) NOT NULL,
                    `meta_description` varchar(255) NOT NULL,
                    `meta_keyword` varchar(255) NOT NULL,
                    PRIMARY KEY (`category_id`,`language_id`),
                    KEY `name` (`name`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_category_path` (
                    `category_id` int(11) NOT NULL,
                    `path_id` int(11) NOT NULL,
                    `level` int(11) NOT NULL,
                    PRIMARY KEY (`category_id`,`path_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_category_filter` (
                    `category_id` int(11) NOT NULL,
                    `filter_id` int(11) NOT NULL,
                    PRIMARY KEY (`category_id`,`filter_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_category_to_layout` (
                    `category_id` int(11) NOT NULL,
                    `store_id` int(11) NOT NULL,
                    `layout_id` int(11) NOT NULL,
                    PRIMARY KEY (`category_id`,`store_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_category_to_store` (
                    `category_id` int(11) NOT NULL,
                    `store_id` int(11) NOT NULL,
                    PRIMARY KEY (`category_id`,`store_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_author` (
                    `author_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `image` varchar(1000) NOT NULL,
                    `status` TINYINT(1) NOT NULL,
                    `sort_order` int(3) NOT NULL DEFAULT '0',
                    `date_added` DATETIME NOT NULL,
                    `date_modified` DATETIME NOT NULL,
                    PRIMARY KEY (`author_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_author_description` (
                    `author_id` INT(11) NOT NULL,
                    `language_id` int(11) NOT NULL,
                    `name` varchar(255) NOT NULL,
                    `description` text NOT NULL,
                    `meta_title` varchar(255) NOT NULL,
                    `meta_description` varchar(255) NOT NULL,
                    `meta_keyword` varchar(255) NOT NULL,
                    PRIMARY KEY (`author_id`,`language_id`),
                    KEY `name` (`name`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_author_to_layout` (
                    `author_id` INT(11) NOT NULL,
                    `store_id` int(11) NOT NULL,
                    `layout_id` int(11) NOT NULL,
                    PRIMARY KEY (`author_id`,`store_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article` (
                    `article_id` int(11) NOT NULL AUTO_INCREMENT,
                    `image` varchar(255) DEFAULT NULL,
                    `author_id` int(11) NOT NULL,
                    `allow_comment` tinyint(1) NOT NULL DEFAULT '1',
                    `date_available` date NOT NULL DEFAULT '0000-00-00',
                    `sort_order` int(11) NOT NULL DEFAULT '0',
                    `status` tinyint(1) NOT NULL DEFAULT '0',
                    `featured` tinyint(1) NOT NULL DEFAULT '0',
                    `viewed` int(5) NOT NULL DEFAULT '0',
                    `date_added` datetime NOT NULL,
                    `date_modified` datetime NOT NULL,
                    PRIMARY KEY (`article_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_description` (
                    `article_id` int(11) NOT NULL,
                    `language_id` int(11) NOT NULL,
                    `name` varchar(255) NOT NULL,
                    `description` text NOT NULL,
                    `tag` text NOT NULL,
                    `meta_title` varchar(255) NOT NULL,
                    `meta_description` varchar(255) NOT NULL,
                    `meta_keyword` varchar(255) NOT NULL,
                    PRIMARY KEY (`article_id`,`language_id`),
                    KEY `name` (`name`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_filter` (
                    `article_id` int(11) NOT NULL,
                    `filter_id` int(11) NOT NULL,
                    PRIMARY KEY (`article_id`,`filter_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_image` (
                    `article_image_id` int(11) NOT NULL AUTO_INCREMENT,
                    `article_id` int(11) NOT NULL,
                    `image` varchar(255) DEFAULT NULL,
                    `sort_order` int(3) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`article_image_id`),
                    KEY `article_id` (`article_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_audio` (
                    `article_id` INT(11) NOT NULL,
                    `title` VARCHAR(255) NOT NULL,
                    `url` VARCHAR(500) NOT NULL,
                    `sort_order` SMALLINT NOT NULL DEFAULT 0,
                    INDEX (`article_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_related` (
                    `article_id` int(11) NOT NULL,
                    `related_id` int(11) NOT NULL,
                    PRIMARY KEY (`article_id`,`related_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_product` (
                    `article_id` int(11) NOT NULL,
                    `product_id` int(11) NOT NULL,
                    PRIMARY KEY (`article_id`,`product_id`),
                    KEY `product_id` (`product_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_to_category` (
                    `article_id` int(11) NOT NULL,
                    `category_id` int(11) NOT NULL,
                    PRIMARY KEY (`article_id`,`category_id`),
                    KEY `category_id` (`category_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_to_layout` (
                    `article_id` int(11) NOT NULL,
                    `store_id` int(11) NOT NULL,
                    `layout_id` int(11) NOT NULL,
                    PRIMARY KEY (`article_id`,`store_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_article_to_store` (
                    `article_id` int(11) NOT NULL,
                    `store_id` int(11) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`article_id`,`store_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_comment` (
                    `comment_id` int(11) NOT NULL AUTO_INCREMENT,
                    `parent_comment_id` int(11) NOT NULL DEFAULT '0',
                    `article_id` int(11) NOT NULL,
                    `customer_id` int(11) NOT NULL,
                    `author` varchar(64) NOT NULL,
                    `email` varchar(64) NOT NULL,
                    `website` varchar(1000) NOT NULL,
                    `text` text NOT NULL,
                    `status` tinyint(1) NOT NULL DEFAULT '0',
                    `date_added` datetime NOT NULL,
                    `date_modified` datetime NOT NULL,
                    PRIMARY KEY (`comment_id`),
                    KEY `article_id` (`article_id`),
                    KEY `customer_id` (`customer_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_blog_comment_path` (
                    `comment_id` int(11) NOT NULL,
                    `path_id` int(11) NOT NULL,
                    `level` int(11) NOT NULL,
                    PRIMARY KEY (`comment_id`,`path_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        ## Testimonial ##
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_testimonial` (
                    `testimonial_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `image` varchar(1000) NOT NULL,
                    `email` varchar(64),
                    `rating` TINYINT NOT NULL DEFAULT '0',
                    `status` TINYINT(1) NOT NULL DEFAULT '0',
                    `sort_order` int(3) NOT NULL DEFAULT '0',
                    `date_added` DATE NOT NULL,
                    `date_modified` DATE NOT NULL,
                    PRIMARY KEY (`testimonial_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_testimonial_description` (
                    `testimonial_id` INT(11) NOT NULL,
                    `language_id` int(11) NOT NULL,
                    `name` varchar(50) NOT NULL,
                    `extra` varchar(50),
                    `description` varchar(1000) NOT NULL,
                    PRIMARY KEY (`testimonial_id`,`language_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_testimonial_to_store` (
                    `testimonial_id` int(11) NOT NULL,
                    `store_id` int(11) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`testimonial_id`,`store_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        ## Page builder ##
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_page` (
                    `page_id` int(11) NOT NULL AUTO_INCREMENT,
                    `status` tinyint(1) NOT NULL,
                    `override_skin_id` int(11) NOT NULL DEFAULT '0',
                    `date_added` datetime NOT NULL,
                    `date_modified` datetime NOT NULL,
                    PRIMARY KEY (`page_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_page_description` (
                    `page_id` int(11) NOT NULL,
                    `language_id` int(11) NOT NULL,
                    `name` varchar(255) NOT NULL,
                    `meta_title` varchar(255) NOT NULL,
                    `meta_description` varchar(255) NOT NULL,
                    `meta_keyword` varchar(255) NOT NULL,
                    PRIMARY KEY (`page_id`,`language_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_page_to_store` (
                    `page_id` int(11) NOT NULL,
                    `store_id` int(11) NOT NULL,
                    PRIMARY KEY (`page_id`,`store_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        ### content builder ###
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_content` (
                    `content_id` int(11) NOT NULL AUTO_INCREMENT,
                    `status` tinyint(1) NOT NULL,
                    `name` varchar(255) NOT NULL,
                    `date_added` datetime NOT NULL,
                    `date_modified` datetime NOT NULL,
                    PRIMARY KEY (`content_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        ### Catalog data ##
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_catalog_data` (
                    `data_id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `status` tinyint(1) NOT NULL DEFAULT '1',
                    `sort_order` int(11) NOT NULL DEFAULT '0',
                    `customer` tinyint(1) NOT NULL DEFAULT '0',
                    `customer_group_id` int(11) NOT NULL,
                    `date_start` datetime,
                    `date_end` datetime,
                    `page` varchar(50) NOT NULL,
                    `hook` varchar(50) NOT NULL,
                    `is_filter` tinyint(1) NOT NULL DEFAULT '0',
                    `filter_special` tinyint(1) NOT NULL DEFAULT '0',
                    `filter_quantity_min` SMALLINT,
                    `filter_quantity_max` SMALLINT,
                    `filter_price_min` DECIMAL(15,4),
                    `filter_price_max` DECIMAL(15,4),
                    `sub_category` tinyint(1) NOT NULL DEFAULT '1',
                    `setting` TEXT NOT NULL,
                    `date_added` datetime NOT NULL,
                    `date_modified` datetime NOT NULL,
                    PRIMARY KEY (`data_id`),
                    INDEX(`page`, `customer_group_id`, `customer`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_catalog_data_to_store` (
                    `data_id` int(11) NOT NULL,
                    `store_id` int(11) NOT NULL,
                    PRIMARY KEY (`data_id`,`store_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_catalog_data_to_product` (
                    `data_id` int(11) NOT NULL,
                    `product_id` int(11) NOT NULL,
                    PRIMARY KEY (`data_id`,`product_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_catalog_data_to_category` (
                    `data_id` int(11) NOT NULL,
                    `category_id` int(11) NOT NULL,
                    PRIMARY KEY (`data_id`,`category_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_catalog_data_to_manufacturer` (
                    `data_id` int(11) NOT NULL,
                    `manufacturer_id` int(11) NOT NULL,
                    PRIMARY KEY (`data_id`,`manufacturer_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_catalog_data_to_filter` (
                    `data_id` int(11) NOT NULL,
                    `filter_id` int(11) NOT NULL,
                    PRIMARY KEY (`data_id`,`filter_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        ### Filter ###
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_filter` (
                    `filter_id` int(11) NOT NULL AUTO_INCREMENT,
                    `status` tinyint(1) NOT NULL,
                    `sort_order` INT(11) NOT NULL,
                    `filter_language_id` int(11) NOT NULL,
                    `setting` TEXT NOT NULL,
                    `date_added` datetime NOT NULL,
                    `date_modified` datetime NOT NULL,
                    `date_sync` datetime NOT NULL,
                    PRIMARY KEY (`filter_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_filter_description` (
                    `filter_id` int(11) NOT NULL,
                    `language_id` int(11) NOT NULL,
                    `name` varchar(100) NOT NULL,
                    PRIMARY KEY (`filter_id`,`language_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_filter_to_category` (
                    `filter_id` int(11) NOT NULL,
                    `category_id` int(11) NOT NULL,
                    PRIMARY KEY (`filter_id`,`category_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_filter_value` (
                    `value_id` int(11) NOT NULL AUTO_INCREMENT,
                    `filter_id` int(11) NOT NULL,
                    `status` tinyint(1) NOT NULL,
                    `sort_order` INT(11) NOT NULL,
                    `image` varchar(255),
                    `regex` tinyint(1) NOT NULL DEFAULT 0,
                    `value` varchar(1000) NOT NULL,
                    PRIMARY KEY (`value_id`),
                    KEY filter_id (`filter_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_filter_value_description` (
                    `value_id` int(11) NOT NULL,
                    `language_id` int(11) NOT NULL,
                    `name` varchar(100) NOT NULL,
                    PRIMARY KEY (`value_id`,`language_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_filter_value_to_product` (
                    `value_id` int(11) NOT NULL,
                    `product_id` int(11) NOT NULL,
                    `trash` TINYINT(1) NOT NULL DEFAULT 0,
                    PRIMARY KEY (`value_id`, product_id),
                    INDEX(`product_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
            ");

        ## Form builder ##
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_form` (
                    `form_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `captcha` VARCHAR(32) NOT NULL DEFAULT '',
                    `spam_keywords` VARCHAR(1000),
                    `information_id` INT(11) NOT NULL DEFAULT 0,
                    `record` TINYINT(1) NOT NULL DEFAULT 1,
                    `email_field_id` INT(11) NOT NULL DEFAULT 0,
                    `subject_field_id` INT(11) NOT NULL DEFAULT 0,
                    `mail_admin_status` TINYINT(1) NOT NULL DEFAULT 1,
                    `mail_admin_to` VARCHAR(255),
                    `mail_customer_status` TINYINT(1) NOT NULL DEFAULT 1,
                    `date_added` DATETIME NOT NULL,
                    `date_modified` DATETIME NOT NULL,
                    PRIMARY KEY (`form_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_form_description` (
                    `form_id` INT(11) NOT NULL,
                    `language_id` INT(11) NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `success` VARCHAR(1000) NOT NULL,
                    `submit_text` VARCHAR(100) NOT NULL DEFAULT '',
                    `mail_customer_subject` VARCHAR(255) NOT NULL DEFAULT '',
                    `mail_customer_message` TEXT NOT NULL DEFAULT '',
                    PRIMARY KEY (`form_id`,`language_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_form_field` (
                    `form_field_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `form_id` INT(11) NOT NULL,
                    `status` TINYINT(1) NOT NULL DEFAULT 1,
                    `sort_order` INT(11) NOT NULL DEFAULT 0,
                    `column` TINYINT(1) NOT NULL DEFAULT 1,
                    `is_required` TINYINT(1) NOT NULL DEFAULT 1,
                    `customer` TINYINT(1) NOT NULL DEFAULT 0,
                    `type` VARCHAR(16) NOT NULL DEFAULT 'text',
                    `name` VARCHAR(32) NOT NULL,
                    `value` VARCHAR(1000) NOT NULL DEFAULT '',
                    `validation` VARCHAR(255) NOT NULL DEFAULT '',
                    `min` INT(11),
                    `max` INT(11),
                    `decimal` TINYINT NOT NULL DEFAULT 0,
                    `date_added` DATETIME NOT NULL,
                    `date_modified` DATETIME NOT NULL,
                    PRIMARY KEY (`form_field_id`),
                    INDEX(`form_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_form_field_description` (
                    `form_field_id` INT(11) NOT NULL,
                    `language_id` INT(11) NOT NULL,
                    `label` VARCHAR(255) NOT NULL,
                    `placeholder` VARCHAR(255) NOT NULL DEFAULT '',
                    `help` VARCHAR(1000) NOT NULL DEFAULT '',
                    `error` VARCHAR(1000) NOT NULL DEFAULT '',
                    PRIMARY KEY (`form_field_id`,`language_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_form_field_customer_group` (
                    `form_field_id` INT(11) NOT NULL,
                    `customer_group_id` INT(11) NOT NULL,
                    PRIMARY KEY (`form_field_id`,`customer_group_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_form_field_value` (
                    `form_field_value_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `form_field_id` INT(11) NOT NULL,
                    `sort_order` INT(11) NOT NULL DEFAULT 0,
                    PRIMARY KEY (`form_field_value_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_form_field_value_description` (
                    `form_field_value_id` INT(11) NOT NULL,
                    `language_id` INT(11) NOT NULL,
                    `form_field_id` INT(11) NOT NULL,
                    `name` VARCHAR(100) NOT NULL,
                    PRIMARY KEY (`form_field_value_id`,`language_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_form_record` (
                    `form_record_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `form_id` INT(11) NOT NULL,
                    `language_id` INT(11) NOT NULL,
                    `currency_id` INT(11) NOT NULL,
                    `store_id` INT(11) NOT NULL DEFAULT 0,
                    `customer_id` INT(11),
                    `page_url` VARCHAR(1000) NOT NULL,
                    `product_id` INT(11),
                    `category_id` INT(11),
                    `manufacturer_id` INT(11),
                    `ip_address` VARCHAR(60) NOT NULL,
                    `date_added` DATETIME NOT NULL,
                    PRIMARY KEY (`form_record_id`),
                    INDEX (`form_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_form_record_value` (
                    `form_record_id` INT(11) NOT NULL,
                    `form_id` INT(11) NOT NULL,
                    `name` VARCHAR(32) NOT NULL,
                    `value` TEXT NOT NULL,
                    INDEX (`form_record_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        ## Document ##
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_document` (
                    `document_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `store_id` INT(11) NOT NULL DEFAULT 0,
                    `route` VARCHAR(64) NOT NULL,
                    `status` TINYINT(1) NOT NULL DEFAULT 1,
                    `og_image_width` SMALLINT,
                    `og_image_height` SMALLINT,
                    `og_video` VARCHAR(1000),
                    `date_added` DATETIME NOT NULL,
                    `date_modified` DATETIME NOT NULL,
                    PRIMARY KEY (`document_id`), UNIQUE KEY (`store_id`, `route`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_document_description` (
                    `document_id` INT(11) NOT NULL,
                    `language_id` INT(11) NOT NULL,
                    `meta_title` VARCHAR(70),
                    `meta_description` VARCHAR(160),
                    `meta_keyword` VARCHAR(255),
                    `og_title` VARCHAR(64),
                    `og_description` VARCHAR(255),
                    `og_image` VARCHAR(255),
                    `og_image_alt` VARCHAR(70),
                    PRIMARY KEY (`document_id`,`language_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        // Gallery
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_gallery` (
                    `gallery_id` int(11) NOT NULL AUTO_INCREMENT,
                    `status` tinyint(1) NOT NULL,
                    `name` varchar(100) NOT NULL,
                    `image` TEXT,
                    `video` TEXT,
                    `date_added` datetime NOT NULL,
                    PRIMARY KEY (`gallery_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        // manufacturer
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_manufacturer_description` (
                    `manufacturer_id` int(11) NOT NULL,
                    `language_id` int(11) NOT NULL,
                    `description` text NOT NULL,
                    `meta_title` varchar(255) NOT NULL,
                    `meta_description` varchar(255) NOT NULL,
                    `meta_keyword` varchar(255) NOT NULL,
                    PRIMARY KEY (`manufacturer_id`,`language_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_manufacturer_to_layout` (
                    `manufacturer_id` int(11) NOT NULL,
                    `store_id` int(11) NOT NULL,
                    `layout_id` int(11) NOT NULL,
                    PRIMARY KEY (`manufacturer_id`,`store_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        // product video
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_video` (
                    `product_video_id` int(11) NOT NULL AUTO_INCREMENT,
                    `product_id` int(11) NOT NULL,
                    `url` VARCHAR(1000) NOT NULL,
                    `image` VARCHAR(1000),
                    `sort_order` SMALLINT NOT NULL DEFAULT 0,
                    PRIMARY KEY (`product_video_id`), INDEX (`product_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_video_description` (
                    `product_video_id` int(11) NOT NULL,
                    `language_id` int(11) NOT NULL,
                    `product_id` int(11) NOT NULL,
                    `title` VARCHAR(160),
                    PRIMARY KEY (`product_video_id`,`language_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        // Product audio
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_audio` (
                `product_audio_id` int(11) NOT NULL AUTO_INCREMENT,
                `product_id` int(11) NOT NULL,
                `url` VARCHAR(1000) NOT NULL,
                `sort_order` SMALLINT NOT NULL DEFAULT 0,
                PRIMARY KEY (`product_audio_id`), INDEX (`product_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_audio_description` (
                `product_audio_id` int(11) NOT NULL,
                `language_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `title` VARCHAR(160),
                PRIMARY KEY (`product_audio_id`,`language_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

        // Product label
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_label` (
                    `product_label_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(60) NOT NULL,
                    `status` TINYINT(1) NOT NULL DEFAULT 1,
                    `sort_order` SMALLINT NOT NULL DEFAULT 0,
                    `type` VARCHAR(16) NOT NULL,
                    `setting` TEXT,
                    `customer` TINYINT(1) NOT NULL DEFAULT '0',
                    `product_page_status` TINYINT(1) NOT NULL DEFAULT 1,
                    `date_added` datetime NOT NULL,
                    `date_modified` datetime NOT NULL,
                    PRIMARY KEY (`product_label_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_label_description` (
                    `product_label_id` INT(11) NOT NULL,
                    `language_id` INT(11) NOT NULL,
                    `name` VARCHAR(60) NOT NULL,
                    PRIMARY KEY (`product_label_id`,`language_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_label_customer_group` (
                    `product_label_id` INT(11) NOT NULL,
                    `customer_group_id` INT(11) NOT NULL,
                    PRIMARY KEY (`product_label_id`,`customer_group_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_label_to_store` (
                    `product_label_id` INT(11) NOT NULL,
                    `store_id` INT(11) NOT NULL,
                    PRIMARY KEY (`product_label_id`,`store_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_product_label_style` (
                    `product_label_id` INT(11) NOT NULL,
                    `skin_id` INT(11) NOT NULL,
                    `position` VARCHAR(30) NOT NULL,
                    `shape` VARCHAR(30) NOT NULL,
                    `visibility` VARCHAR(30) NOT NULL,
                    `color_text` VARCHAR(30) NOT NULL,
                    `color_bg` VARCHAR(30) NOT NULL,
                    `custom_class` VARCHAR(30) NOT NULL,
                    `product_page_position` VARCHAR(30) NOT NULL,
                    `product_page_visibility` VARCHAR(30) NOT NULL,
                    PRIMARY KEY (`product_label_id`,`skin_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        // Notification
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_notification` (
                    `notification_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `customer_id` INT(11) NOT NULL,
                    `type` VARCHAR(30) NOT NULL,
                    `message` VARCHAR(1000) NOT NULL,
                    `product_id` INT(11) NOT NULL DEFAULT 0,
                    `manufacturer_id` INT(11) NOT NULL DEFAULT 0,
                    `article_id` INT(11) NOT NULL DEFAULT 0,
                    `date_added` DATETIME NOT NULL,
                    `read` TINYINT(1) NOT NULL DEFAULT 0,
                    PRIMARY KEY (`notification_id`), INDEX(`customer_id`), INDEX(`date_added`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_notification_subscribe` (
                    `subscribe_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `customer_id` INT(11) NOT NULL DEFAULT 0,
                    `email` VARCHAR(64),
                    `product_id` INT(11) NOT NULL DEFAULT 0,
                    `manufacturer_id` INT(11) NOT NULL DEFAULT 0,
                    `token` CHAR(32) NOT NULL,
                    `date_added` DATETIME NOT NULL,
                    PRIMARY KEY (`subscribe_id`),
                    INDEX(`product_id`),
                    INDEX(`manufacturer_id`),
                    INDEX(`customer_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_notification_channel` (
                `channel_id` INT(11) NOT NULL AUTO_INCREMENT,
                `default` TINYINT(1) NOT NULL DEFAULT 0,
                `sort_order` INT(11) NOT NULL DEFAULT 0,
                `status` TINYINT(1) NOT NULL DEFAULT 0,
                `date_added` DATETIME NOT NULL,
                PRIMARY KEY (`channel_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_notification_channel_description` (
                `channel_id` INT(11) NOT NULL,
                `language_id` INT(11) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` VARCHAR(1000) NOT NULL,
                PRIMARY KEY (`channel_id`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_notification_channel_to_store` (
                `channel_id` int(11) NOT NULL,
                `store_id` int(11) NOT NULL,
                PRIMARY KEY (`channel_id`,`store_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_notification_channel_subscribe` (
                `channel_id` INT(11) NOT NULL,
                `customer_id` INT(11) NOT NULL,
                `methods` VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        
        // Push notification
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_push_notification_subscriber` (
                `subscriber_id` INT(11) NOT NULL AUTO_INCREMENT,
                `customer_id` INT(11) NOT NULL DEFAULT 0,
                `endpoint` VARCHAR(1000) NOT NULL,
                `key_auth` VARCHAR(255) NOT NULL,
                `key_p256dh` VARCHAR(255) NOT NULL,
                `date_expire` DATETIME,
                `date_added` DATETIME NOT NULL,
                PRIMARY KEY (`subscriber_id`),
                INDEX (`endpoint`),
                INDEX (`customer_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

        // Push notification queue
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_push_notification_queue` (
                `push_id` INT(11) NOT NULL AUTO_INCREMENT,
                `endpoint` VARCHAR(1000) NOT NULL,
                `key_auth` VARCHAR(255) NOT NULL,
                `key_p256dh` VARCHAR(255) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `message` TEXT NOT NULL,
                `image` VARCHAR(500) NOT NULL,
                `url` VARCHAR(1000) NOT NULL,
                `date_added` DATETIME NOT NULL,
                `reserved_id` VARCHAR(8),
                PRIMARY KEY (`push_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

        // Mail queue
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_mail_queue` (
                    `mail_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `to` VARCHAR(64) NOT NULL,
                    `from` VARCHAR(64) NOT NULL,
                    `reply_to` VARCHAR(64),
                    `sender` VARCHAR(70),
                    `subject` VARCHAR(70) NOT NULL,
                    `body` TEXT NOT NULL,
                    `date_added` DATETIME NOT NULL,
                    `reserved_id` VARCHAR(8),
                    PRIMARY KEY (`mail_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        // SMS queue
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_sms_queue` (
                    `sms_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `telephone` CHAR(15) NOT NULL,
                    `message` TEXT NOT NULL,
                    `date_added` DATETIME NOT NULL,
                    `reserved_id` VARCHAR(8),
                    PRIMARY KEY (`sms_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        // Redirect
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_redirect_url` (
                    `redirect_url_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `from` VARCHAR(1000) NOT NULL,
                    `to` VARCHAR(1000) NOT NULL,
                    `store_id` int(11) NOT NULL DEFAULT 0,
                    `date_added` DATETIME NOT NULL,
                    PRIMARY KEY (`redirect_url_id`),
                    INDEX(`from`(255))
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            ");

        ### change opencart table ###

        // table oc_layout
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "layout`");
        $field = array_column($query->rows, 'Field');

        if (!in_array('mz_override_skin_id', $field)) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "layout` ADD COLUMN `mz_override_skin_id` int(11) NOT NULL DEFAULT '0'");
        }
        if (!in_array('mz_layout_type', $field)) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "layout` ADD COLUMN `mz_layout_type` VARCHAR(100) NOT NULL DEFAULT 'default'");
        }

        // Table oc_product
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product`");
        $field = array_column($query->rows, 'Field');

        if (!in_array('mz_featured', $field)) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD COLUMN `mz_featured` tinyint(1) NOT NULL DEFAULT '0'");
        }

        // Table oc_manufacturer
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "manufacturer`");
        $field = array_column($query->rows, 'Field');

        if (!in_array('mz_featured', $field)) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "manufacturer` ADD COLUMN `mz_featured` tinyint(1) NOT NULL DEFAULT '0'");
        }

        // table oc_product_special
        // $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_special`");
        // foreach($query->rows as $column){
        //     if($column['Field'] == 'date_start' && $column['Type'] !== 'datetime'){
        //         $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_special` MODIFY COLUMN `date_start` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
        //     } elseif($column['Field'] == 'date_end' && $column['Type'] !== 'datetime'){
        //         $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_special` MODIFY COLUMN `date_end` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
        //     }
        // }
        // $field = array_column($query->rows, 'Field');
        // if(!in_array('mz_quantity', $field)){
        //     $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_special` ADD COLUMN `mz_quantity` INT(11) NOT NULL DEFAULT 0");
        // }

        // Table oc_product_attribute
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_attribute`");
        foreach ($query->rows as $row) {
            if ($row['Field'] == 'text' && $row['Type'] !== 'varchar(1000)') {
                $this->db->query("ALTER TABLE " . DB_PREFIX . "product_attribute CHANGE `text` `text` VARCHAR(1000) NOT NULL");
            }
        }
        $query     = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "product_attribute`");
        $key_names = array_column($query->rows, 'Key_name');
        if (!in_array('mz_attribute_id', $key_names)) {
            $this->db->query("CREATE INDEX mz_attribute_id ON " . DB_PREFIX . "product_attribute (`attribute_id`, `language_id`)");
        }
        if (!in_array('mz_text', $key_names)) {
            $this->db->query("CREATE INDEX mz_text ON " . DB_PREFIX . "product_attribute (`text`(100))");
        }

        // Table oc_option_value
        $query     = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "option_value`");
        $key_names = array_column($query->rows, 'Key_name');
        if (!in_array('mz_option_id', $key_names)) {
            $this->db->query("CREATE INDEX mz_option_id ON " . DB_PREFIX . "option_value (`option_id`)");
        }

        // Table oc_option_value_description
        $query     = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "option_value_description`");
        $key_names = array_column($query->rows, 'Key_name');
        if (!in_array('mz_option_id', $key_names)) {
            $this->db->query("CREATE INDEX mz_option_id ON " . DB_PREFIX . "option_value_description (`option_id`, `language_id`, `name`)");
        }

        // Table oc_filter_description
        $query     = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "filter_description`");
        $key_names = array_column($query->rows, 'Key_name');
        if (!in_array('mz_filter_group_id', $key_names)) {
            $this->db->query("CREATE INDEX mz_filter_group_id ON " . DB_PREFIX . "filter_description (`filter_group_id`, `language_id`, `name`)");
        }

        ### data ###
        if (!$this->config->get('mz_version')) {
            $this->db->query("UPDATE `" . DB_PREFIX . "layout` SET mz_layout_type = 'home' WHERE layout_id = '1'");
            $this->db->query("UPDATE `" . DB_PREFIX . "layout` SET mz_layout_type = 'product' WHERE layout_id = '2'");
            $this->db->query("UPDATE `" . DB_PREFIX . "layout` SET mz_layout_type = 'category' WHERE layout_id = '3'");
            $this->db->query("UPDATE `" . DB_PREFIX . "layout` SET mz_layout_type = 'search' WHERE layout_id = '13'");
            $this->db->query("UPDATE `" . DB_PREFIX . "layout` SET mz_layout_type = 'information' WHERE layout_id = '11'");

            // Add layout
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = 'Manufacturer products', `mz_layout_type` = 'manufacturer_info'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = LAST_INSERT_ID(), `store_id` = 0, `route` = 'product/manufacturer/info'");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = 'Quick view', `mz_layout_type` = 'quick_view'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = LAST_INSERT_ID(), `store_id` = 0, `route` = 'extension/maza/product/quick_view'");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = 'Special', `mz_layout_type` = 'special'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = LAST_INSERT_ID(), `store_id` = 0, `route` = 'product/special'");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = 'All Products', `mz_layout_type` = 'products'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = LAST_INSERT_ID(), `store_id` = 0, `route` = 'extension/maza/products'");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = 'Blog Home', `mz_layout_type` = 'home'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = LAST_INSERT_ID(), `store_id` = 0, `route` = 'extension/maza/blog/home'");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = 'Blog all articles', `mz_layout_type` = 'blog_all'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = LAST_INSERT_ID(), `store_id` = 0, `route` = 'extension/maza/blog/all'");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = 'Blog category', `mz_layout_type` = 'blog_category'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = LAST_INSERT_ID(), `store_id` = 0, `route` = 'extension/maza/blog/category'");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = 'Blog search', `mz_layout_type` = 'blog_search'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = LAST_INSERT_ID(), `store_id` = 0, `route` = 'extension/maza/blog/search'");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = 'Blog author', `mz_layout_type` = 'blog_author'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = LAST_INSERT_ID(), `store_id` = 0, `route` = 'extension/maza/blog/author'");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = 'Blog article', `mz_layout_type` = 'blog_article'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = LAST_INSERT_ID(), `store_id` = 0, `route` = 'extension/maza/blog/article'");
        }

        // Remove home page module from design -> layouts -> home page
        $this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE layout_id = '1'");

        // Fonts
        $this->load->model('extension/maza/asset');

        $fonts = $this->model_extension_maza_asset->getFonts('std');
        if (!$fonts) {

            $fonts = array(
                array('name' => 'Sans Serif', 'font_family' => 'Sans-Serif', 'type' => 'std'),
                array('name' => 'Serif', 'font_family' => 'Serif', 'type' => 'std'),
                array('name' => 'Monospace', 'font_family' => 'Monospace', 'type' => 'std'),
                array('name' => 'Cursive', 'font_family' => 'Cursive', 'type' => 'std'),
                array('name' => 'Fantasy', 'font_family' => 'Fantasy', 'type' => 'std'),
                array('name' => 'Arial', 'font_family' => 'Arial', 'type' => 'std'),
                array('name' => 'Helvetica', 'font_family' => 'Helvetica', 'type' => 'std'),
                array('name' => 'Gill Sans', 'font_family' => 'Gill Sans', 'type' => 'std'),
                array('name' => 'Lucida', 'font_family' => 'Lucida', 'type' => 'std'),
                array('name' => 'Helvetica Narrow', 'font_family' => 'Helvetica Narrow', 'type' => 'std'),
                array('name' => 'Times', 'font_family' => 'Times', 'type' => 'std'),
                array('name' => 'Times New Roman', 'font_family' => 'Times New Roman', 'type' => 'std'),
                array('name' => 'Palatino', 'font_family' => 'Palatino', 'type' => 'std'),
                array('name' => 'Bookman', 'font_family' => 'Bookman', 'type' => 'std'),
                array('name' => 'New Century Schoolbook', 'font_family' => 'New Century Schoolbook', 'type' => 'std'),
                array('name' => 'Andale Mono', 'font_family' => 'Andale Mono', 'type' => 'std'),
                array('name' => 'Courier New', 'font_family' => 'Courier New', 'type' => 'std'),
                array('name' => 'Courier', 'font_family' => 'Courier', 'type' => 'std'),
                array('name' => 'Lucidatypewriter', 'font_family' => 'Lucidatypewriter', 'type' => 'std'),
                array('name' => 'Fixed', 'font_family' => 'Fixed', 'type' => 'std'),
                array('name' => 'Comic Sans MS', 'font_family' => 'Comic Sans MS', 'type' => 'std'),
                array('name' => 'Zapf Chancery', 'font_family' => 'Zapf Chancery', 'type' => 'std'),
                array('name' => 'Coronetscript', 'font_family' => 'Coronetscript', 'type' => 'std'),
                array('name' => 'Florence', 'font_family' => 'Florence', 'type' => 'std'),
                array('name' => 'Parkavenue', 'font_family' => 'Parkavenue', 'type' => 'std'),
                array('name' => 'Comic Sans', 'font_family' => 'Comic Sans', 'type' => 'std'),
                array('name' => 'Impact', 'font_family' => 'Impact', 'type' => 'std'),
                array('name' => 'Arnoldboecklin', 'font_family' => 'Arnoldboecklin', 'type' => 'std'),
                array('name' => 'Oldtown', 'font_family' => 'Oldtown', 'type' => 'std'),
                array('name' => 'Blippo', 'font_family' => 'Blippo', 'type' => 'std'),
                array('name' => 'Brushstroke', 'font_family' => 'Brushstroke', 'type' => 'std'),
                array('name' => 'Georgia', 'font_family' => 'Georgia', 'type' => 'std'),
                array('name' => 'Verdana', 'font_family' => 'Verdana', 'type' => 'std'),
            );

            array_map([$this->model_extension_maza_asset, 'addFont'], $fonts);
        }

    }

    // public function addStartup(){
    //     $content = file_get_contents(DIR_SYSTEM . 'framework.php');

    //     if(is_writable(DIR_SYSTEM . 'framework.php') && stripos($content, 'library/maza/startup.php') === FALSE){
    //         file_put_contents(DIR_SYSTEM . 'framework.php', str_replace('$registry->set(\'document\', new Document());', '$registry->set(\'document\', new Document());' . PHP_EOL . PHP_EOL . 'require_once(modification(DIR_SYSTEM . \'library/maza/startup.php\')); // Maza startup', $content));
    //     }
    // }

    public function getThemeSetting($theme_code, $code = null) {
        if (is_file(DIR_CATALOG . 'view/theme/' . $theme_code . '/setting/theme.json')) {
            $setting = json_decode(file_get_contents(DIR_CATALOG . 'view/theme/' . $theme_code . '/setting/theme.json'), true);

            if ($code) {
                return isset($setting[$code]) ? $setting[$code] : array();
            } else {
                return $setting;
            }
        }
    }

    public function getSkinSetting($theme_code, $skin_code, $code = null) {
        if (is_file(DIR_CATALOG . 'view/theme/' . $theme_code . '/setting/skin/' . $skin_code . '.json')) {
            $setting = json_decode(file_get_contents(DIR_CATALOG . 'view/theme/' . $theme_code . '/setting/skin/' . $skin_code . '.json'), true);

            if ($code) {
                return isset($setting[$code]) ? $setting[$code] : array();
            } else {
                return $setting;
            }
        }
        return array();
    }
}
