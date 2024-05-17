<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaCommon extends model {
        /**
         * Clear all caches
         */
        public function clearCache(): void {
                // Clear asset caches
                $this->mz_document->clear();

                // Clear Maza cache
                $this->mz_cache->clear();

                // Clear all asset files
                maza\emptyFolder(\MZ_CONFIG::$DIR_THEME_ASSET . 'stylesheet/');
                maza\emptyFolder(\MZ_CONFIG::$DIR_THEME_ASSET . 'javascript/');
        }
}
