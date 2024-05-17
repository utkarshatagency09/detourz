<?php
final class MZ_CONFIG {
    public STATIC $DIR_HELPER           = DIR_SYSTEM . 'library/maza/helper/';
    public STATIC $DIR_SVG_IMAGE        = DIR_IMAGE . 'catalog/maza/svg/';
    public STATIC $DIR_TESTIMONIAL_IMAGE= DIR_IMAGE . 'catalog/maza/testimonial/';
    public STATIC $DIR_CATALOG          = NULL;
    public STATIC $DIR_CUSTOM_CODE      = NULL;
    public STATIC $DIR_GLOBAL_JS        = NULL;
    public STATIC $DIR_GLOBAL_CSS       = NULL;
    public STATIC $DIR_THEME_ASSET      = NULL;
    public STATIC $DIR_THEME_CONFIG     = NULL;
    public STATIC $DIR_CSS_CACHE        = NULL;
    public STATIC $DIR_JS_CACHE         = NULL;
    public STATIC $DIR_CSS_COMBINE      = NULL;
    public STATIC $DIR_JS_COMBINE       = NULL;
    public STATIC $DIR_SKINS            = NULL;
    public STATIC $DIR_SKIN_CONTENT     = NULL;
    public STATIC $DIR_SKIN_HEADER      = NULL;
    public STATIC $DIR_SKIN_FOOTER      = NULL;
    public STATIC $DIR_CACHE            = DIR_CACHE . 'maza/';
    
    public static function load(): void {
        // Catalog directory
        self::$DIR_CATALOG          = defined('DIR_CATALOG')?DIR_CATALOG:DIR_APPLICATION;
        
        // Custom code directory
        self::$DIR_CUSTOM_CODE      = self::$DIR_CATALOG . 'view/javascript/maza/custom_code/';
        !is_dir(self::$DIR_CUSTOM_CODE) && maza\createDirPath(self::$DIR_CUSTOM_CODE);
        !is_dir(self::$DIR_CUSTOM_CODE . 'css') && maza\createDirPath(self::$DIR_CUSTOM_CODE . 'css');
        !is_dir(self::$DIR_CUSTOM_CODE . 'js') && maza\createDirPath(self::$DIR_CUSTOM_CODE . 'js');
        !is_dir(self::$DIR_CUSTOM_CODE . 'header') && maza\createDirPath(self::$DIR_CUSTOM_CODE . 'header');
        !is_dir(self::$DIR_CUSTOM_CODE . 'footer') && maza\createDirPath(self::$DIR_CUSTOM_CODE . 'footer');
        
        // Global javascript directory
        self::$DIR_GLOBAL_JS        = self::$DIR_CATALOG . 'view/javascript/maza/javascript/';
        !is_dir(self::$DIR_GLOBAL_JS) && maza\createDirPath(self::$DIR_GLOBAL_JS);
        
        // Global css directory
        self::$DIR_GLOBAL_CSS       = self::$DIR_CATALOG . 'view/javascript/maza/stylesheet/';
        !is_dir(self::$DIR_GLOBAL_CSS) && maza\createDirPath(self::$DIR_GLOBAL_CSS);
        
        // Theme config directory
        self::$DIR_THEME_CONFIG     = self::$DIR_CATALOG . 'view/theme/' . maza\Registry::theme('theme_code') . '/config/';
        !is_dir(self::$DIR_THEME_CONFIG) && maza\createDirPath(self::$DIR_THEME_CONFIG);
        
        // Theme asset directory
        self::$DIR_THEME_ASSET      = self::$DIR_CATALOG . 'view/theme/' . maza\Registry::theme('theme_code') . '/asset/';
        !is_dir(self::$DIR_THEME_ASSET) && maza\createDirPath(self::$DIR_THEME_ASSET);
    
        // css cache directory
        self::$DIR_CSS_CACHE        = self::$DIR_THEME_ASSET . 'stylesheet/' . maza\Registry::skin('skin_code') . '.' . maza\Registry::skin('skin_id') . '/';
        !is_dir(self::$DIR_CSS_CACHE) && maza\createDirPath(self::$DIR_CSS_CACHE);
        
        // css combine directory
        self::$DIR_CSS_COMBINE      = self::$DIR_CSS_CACHE . 'combine/';
        !is_dir(self::$DIR_CSS_COMBINE) && maza\createDirPath(self::$DIR_CSS_COMBINE);
        
        // javasript cache directory
        self::$DIR_JS_CACHE         = self::$DIR_THEME_ASSET . 'javascript/' . maza\Registry::skin('skin_code') . '.' . maza\Registry::skin('skin_id') . '/';
        !is_dir(self::$DIR_JS_CACHE) && maza\createDirPath(self::$DIR_JS_CACHE);
        
        // js combine directory
        self::$DIR_JS_COMBINE       = self::$DIR_JS_CACHE . 'combine/';
        !is_dir(self::$DIR_JS_COMBINE) && maza\createDirPath(self::$DIR_JS_COMBINE);
        
        // Skins directory
        self::$DIR_SKINS            = self::$DIR_CATALOG . 'view/theme/' . maza\Registry::theme('theme_code') . '/skins/';
        !is_dir(self::$DIR_SKINS) && maza\createDirPath(self::$DIR_SKINS);
        
        // Skin content directory
        self::$DIR_SKIN_CONTENT     = self::$DIR_CATALOG . 'view/theme/' . maza\Registry::theme('theme_code') . '/skins/content/' . maza\Registry::skin('skin_code') . '/';
        !is_dir(self::$DIR_SKIN_CONTENT) && maza\createDirPath(self::$DIR_SKIN_CONTENT);
        
        // Skin header directory
        self::$DIR_SKIN_HEADER      = self::$DIR_CATALOG . 'view/theme/' . maza\Registry::theme('theme_code') . '/skins/header/' . maza\Registry::skin('skin_header_code') . '/';
        !is_dir(self::$DIR_SKIN_HEADER) && maza\createDirPath(self::$DIR_SKIN_HEADER);
        
        // Skin footer directory
        self::$DIR_SKIN_FOOTER      = self::$DIR_CATALOG . 'view/theme/' . maza\Registry::theme('theme_code') . '/skins/footer/' . maza\Registry::skin('skin_footer_code') . '/';
        !is_dir(self::$DIR_SKIN_FOOTER) && maza\createDirPath(self::$DIR_SKIN_FOOTER);
        
        // Create missing directory
        !is_dir(self::$DIR_CSS_CACHE . 'route') && maza\createDirPath(self::$DIR_CSS_CACHE . 'route');
        !is_dir(self::$DIR_JS_CACHE . 'route') && maza\createDirPath(self::$DIR_JS_CACHE . 'route');
        
        // SVG directory
        !is_dir(self::$DIR_SVG_IMAGE) && maza\createDirPath(self::$DIR_SVG_IMAGE);
        
        // Maza cache directory
        !is_dir(self::$DIR_CACHE) && maza\createDirPath(self::$DIR_CACHE);
        
        // Cache temp directory
        !is_dir(self::$DIR_CACHE . 'temp/') && maza\createDirPath(self::$DIR_CACHE . 'temp/');
    }
}