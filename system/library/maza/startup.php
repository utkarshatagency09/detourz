<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2021, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
*/
// Composer autoload
require_once(DIR_SYSTEM . 'library/maza/vendor/autoload.php');

// Constant
require_once(modification(DIR_CONFIG . 'maza/constant.php'));

// Config
require_once(modification(DIR_CONFIG . 'maza/config.php'));

// Helper
require_once(modification(MZ_CONFIG::$DIR_HELPER . 'file.php'));
require_once(modification(MZ_CONFIG::$DIR_HELPER . 'array.php'));
require_once(modification(MZ_CONFIG::$DIR_HELPER . 'url.php'));
require_once(modification(MZ_CONFIG::$DIR_HELPER . 'oc.php'));
require_once(modification(MZ_CONFIG::$DIR_HELPER . 'datetime.php'));

// Set registry data
Maza\Registry::$registry = $registry;

