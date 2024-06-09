<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventDocument extends Controller {
    public function before(): void {
        // Schemas
        if ($this->config->get('maza_schema')) {
            $this->mz_schema->add(maza\Schema::organization());
            $this->mz_schema->add(maza\Schema::website());
        }

        // Compile bootstrap file if not exists
        if(!file_exists(MZ_CONFIG::$DIR_CSS_CACHE . 'bootstrap.' . $this->session->data['language'] . '.css') || $this->config->get('maza_developer_mode')){
            // Compile Bootstrap CSS
            file_put_contents(MZ_CONFIG::$DIR_CSS_CACHE . 'bootstrap.' . $this->session->data['language'] . '.css', $this->getBootstrap());
            
            // Delete minified file
            if(file_exists(MZ_CONFIG::$DIR_CSS_CACHE . 'bootstrap.' . $this->session->data['language'] . '.min.css')){
                unlink(MZ_CONFIG::$DIR_CSS_CACHE . 'bootstrap.' . $this->session->data['language'] . '.min.css');
            }
        }
        
        // Compile main css file if not exists
        if(!file_exists(MZ_CONFIG::$DIR_CSS_CACHE . 'main.' . $this->session->data['language'] . '.css') || $this->config->get('maza_developer_mode')){
            // Compile Main CSS
            file_put_contents(MZ_CONFIG::$DIR_CSS_CACHE . 'main.' . $this->session->data['language'] . '.css', $this->getMainCss());
            
            // Delete minified file
            if(file_exists(MZ_CONFIG::$DIR_CSS_CACHE . 'main.' . $this->session->data['language'] . '.min.css')){
                unlink(MZ_CONFIG::$DIR_CSS_CACHE . 'main.' . $this->session->data['language'] . '.min.css');
            }
        }
        
        // Compile main javascript file if not exists
        if(!file_exists(MZ_CONFIG::$DIR_JS_CACHE . 'main.js') || $this->config->get('maza_developer_mode')){
            // Compile Main JS
            file_put_contents(MZ_CONFIG::$DIR_JS_CACHE . 'main.js', $this->getMainJs());
            
            // Delete minified file
            if(file_exists(MZ_CONFIG::$DIR_JS_CACHE . 'main.min.js')){
                unlink(MZ_CONFIG::$DIR_JS_CACHE . 'main.min.js');
            }
        }
        
        // Route base Asset
        // flag to compile route base css or javascript file if missing
        if(!is_file($this->mz_document->getRouteCSSFile(true))
            || !is_file($this->mz_document->getRouteJSFile(true))
            || $this->config->get('maza_developer_mode')){
            
            $this->mz_skin_config->set('flag_compile_route_asset', true);
        }

        // global css & javascript
        if($this->config->get('mz_code_asset_global_javascript')){
            foreach ($this->config->get('mz_code_asset_global_javascript') as $javascript) {
                if($javascript['status']){
                    $this->document->addScript($javascript['url'], 'footer');
                }
            }
        }
        if($this->config->get('mz_code_asset_global_stylesheet')){
            foreach ($this->config->get('mz_code_asset_global_stylesheet') as $stylesheet) {
                if($stylesheet['status']){
                    $this->document->addStyle($stylesheet['url'], 'stylesheet', 'all', 'footer');
                }
            }
        }

        // theme css & javascript
        if($this->mz_theme_config->get('code_asset_theme_javascript')){
            foreach ($this->mz_theme_config->get('code_asset_theme_javascript') as $javascript) {
                if($javascript['status']){
                    $this->document->addScript($javascript['url'], 'footer');
                }
            }
        }
        if($this->mz_theme_config->get('code_asset_theme_stylesheet')){
            foreach ($this->mz_theme_config->get('code_asset_theme_stylesheet') as $stylesheet) {
                if($stylesheet['status']){
                    $this->document->addStyle($stylesheet['url'], 'stylesheet', 'all', 'footer');
                }
            }
        }

        // skin css & javascript
        if($this->mz_skin_config->get('code_asset_skin_javascript')){
            foreach ($this->mz_skin_config->get('code_asset_skin_javascript') as $javascript) {
                if($javascript['status']){
                    $this->document->addScript($javascript['url'], 'footer');
                }
            }
        }
        if($this->mz_skin_config->get('code_asset_skin_stylesheet')){
            foreach ($this->mz_skin_config->get('code_asset_skin_stylesheet') as $stylesheet) {
                if($stylesheet['status']){
                    $this->document->addStyle($stylesheet['url'], 'stylesheet', 'all', 'footer');
                }
            }
        }

        // Fonts
        $fonts = array();
        if(isset($this->mz_skin_config->get('style_font_family_base')[$this->config->get('config_language_id')])){
            $fonts[] = $this->mz_skin_config->get('style_font_family_base')[$this->config->get('config_language_id')];
        }
        if(isset($this->mz_skin_config->get('style_headings_font_family')[$this->config->get('config_language_id')])){
            $fonts[] = $this->mz_skin_config->get('style_headings_font_family')[$this->config->get('config_language_id')];
        }
        foreach ($fonts as $font_id) {
            while ($font_id) {
                $font_info = $this->model_extension_maza_asset->getFont($font_id);

                if(!empty($font_info['url'])){
                    $this->document->addStyle($font_info['url'], 'stylesheet', 'all', 'footer');
                }

                if($font_info){
                    $font_id = $font_info['parent_font_id'];
                } else {
                    $font_id = false;
                }
            }
        }

        // Font icons
        $fonts_icon_packages = $this->model_extension_maza_asset->getFontIconPackages();
        foreach($fonts_icon_packages as $package){
            if($package['status']){
                if(version_compare(VERSION, '3.0.3.7') < 0){
                    $this->document->addStyle($package['css_file'], 'stylesheet', 'all');
                } else {
                    $this->document->addStyle($package['css_file'], 'stylesheet', 'all', 'footer');
                }
            }
        }
        
        // Bootstrap
        $this->document->addStyle('catalog/' . substr(\MZ_CONFIG::$DIR_CSS_CACHE, strlen(\MZ_CONFIG::$DIR_CATALOG)) . 'bootstrap.' . $this->session->data['language'] . '.css', 'stylesheet', 'all');
        $this->document->addScript($this->CDN('catalog/view/javascript/maza/javascript/bootstrap-5.2.0.bundle.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/js/bootstrap.bundle.min.js'), 'footer');
        
        // Lazy loading
        if($this->mz_skin_config->get('catalog_grid_image_lazy_loading') || $this->mz_skin_config->get('blog_article_grid_image_lazy_loading')){
            $this->document->addScript($this->CDN('catalog/view/javascript/maza/javascript/jquery.lazy/jquery.lazy.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js'), 'footer');
            $this->document->addScript($this->CDN('catalog/view/javascript/maza/javascript/jquery.lazy/jquery.lazy.plugins.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.plugins.min.js'), 'footer');
        }
        
        // jquery countdown
        $this->document->addScript($this->CDN('catalog/view/javascript/maza/javascript/jquery.countdown/js/jquery.plugin.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-countdown/2.1.0/js/jquery.plugin.min.js'), 'footer');
        $this->document->addScript($this->CDN('catalog/view/javascript/maza/javascript/jquery.countdown/js/jquery.countdown.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-countdown/2.1.0/js/jquery.countdown.min.js'), 'footer');

        if(file_exists(MZ_CONFIG::$DIR_GLOBAL_JS . 'jquery.countdown/js/jquery.countdown-' . $this->language->get('code') . '.js')){
            $this->document->addScript($this->CDN('catalog/view/javascript/maza/javascript/jquery.countdown/js/jquery.countdown-' . $this->language->get('code') . '.js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-countdown/2.1.0/js/jquery.countdown-' . $this->language->get('code') . '.min.js'), 'footer');
        }
        
        // Main
        $this->document->addStyle('catalog/view/theme/' . $this->mz_theme_config->get('theme_code') . '/asset/stylesheet/' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '/main.' . $this->session->data['language'] . '.css', 'stylesheet', 'all');
        $this->document->addStyle($this->mz_document->getRouteCSSFile(), 'stylesheet', 'all');
        $this->document->addScript('catalog/view/theme/' . $this->mz_theme_config->get('theme_code') . '/asset/javascript/' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '/main.js', 'footer');            
        $this->document->addScript($this->mz_document->getRouteJSFile(), 'footer');
    }

    public function after(): void {
        if($this->mz_skin_config->get('flag_compile_route_asset')){
            ### CSS ###
            $css = $this->mz_document->getRouteCSSFile(true);
            
            // Compile custom CSS
            file_put_contents($css, $this->getCustomCSS());
            
            // Delete minified file
            $minified_css = substr($css, 0, -3) . 'min.css';
            if(file_exists($minified_css)){
                unlink($minified_css);
            }
            
            ### Javascript ###
            $js = $this->mz_document->getRouteJSFile(true);
            
            // Compile JS
            file_put_contents($js, $this->mz_document->getJSCode());
            
            // Delete minified file
            $minified_js = substr($js, 0, -2) . 'min.js';
            if(file_exists($minified_js)){
                unlink($minified_js);
            }
        }
    }
    
    /**
     * Get CDN asset url if exist
     * @param string $local local file url
     * @param string $cdn cdn file url
     * @return string
     */
    private function CDN($local, $cdn){
        if($this->config->get('maza_cdn')){
            return $cdn;
        }
        
        return $local;
    }
    
    /**
     * Get sass style variable
     * @return string Sass variables
     */
    public function getSassStyleVariable(){
            ## Bootstrap variables
            $bootstrap = array();
            // $bootstrap['grid-gutter-width'] = (int)$this->mz_skin_config->get('style_gutter_width') . 'px';
            
            // breakpoints
            $bootstrap['grid-breakpoints'] = array();
            foreach($this->mz_skin_config->get('style_breakpoints') as $breakpoint_code => $breakpoint_pixel){
                if($breakpoint_pixel > 0){
                    $bootstrap['grid-breakpoints'][$breakpoint_code] = (int)$breakpoint_pixel . 'px';
                } else {
                    $bootstrap['grid-breakpoints'][$breakpoint_code] = (int)$breakpoint_pixel;
                }
            }
            
            // Container width
            $bootstrap['container-max-widths'] = array();
            foreach($this->mz_skin_config->get('style_container_width') as $breakpoint_code => $container_width){
                if($container_width > 0){
                    $bootstrap['container-max-widths'][$breakpoint_code] = (int)$container_width . 'px';
                }
            }
            
            // Color Palettes
            if($this->mz_skin_config->get('style_color_palette') && $this->mz_skin_config->get('style_color_palette') !== 'custom'){
                $color_scheme = $this->model_extension_maza_asset->getColorPalettes($this->mz_skin_config->get('style_color_palette'));
            } else {
                $color_scheme = $this->model_extension_maza_asset->getColorPalettes('skin_' . $this->mz_skin_config->get('skin_code'));
            }
            if($this->mz_skin_config->get('style_color_palette') === 'custom'){
                $color_scheme = $this->mz_skin_config->get('style_color_custom');
            }
            foreach ($color_scheme as $type => $color) {
                $bootstrap[$type] = $color;
            }
            // $bootstrap['theme-colors']              = $color_scheme;
            $bootstrap['min-contrast-ratio']  = $this->mz_skin_config->get('style_min_contrast_ratio');
            
            // Body
            $bootstrap['body-bg']           = $this->mz_skin_config->get('style_body_bg');
            $bootstrap['body-color']        = $this->mz_skin_config->get('style_body_color');
            
            ## Global
            $bootstrap['enable-rounded']    = (bool)$this->mz_skin_config->get('style_enable_rounded');
            $bootstrap['enable-gradients']  = (bool)$this->mz_skin_config->get('style_enable_gradients');
            $bootstrap['enable-shadows']    = (bool)$this->mz_skin_config->get('style_enable_shadows');
            $bootstrap['spacer']            = $this->mz_skin_config->get('style_spacer');
            $bootstrap['hr-color']          = $this->mz_skin_config->get('style_hr_border_color');
            $bootstrap['hr-height']         = $this->mz_skin_config->get('style_hr_border_width');
            $bootstrap['hr-margin-y']       = $this->mz_skin_config->get('style_hr_margin_y');
            // $bootstrap['print-page-size']   = $this->mz_skin_config->get('style_print_page_size');
            
            // table
            $bootstrap['table-cell-padding-x'] = $bootstrap['table-cell-padding-y']  = $this->mz_skin_config->get('style_table_cell_padding');
            $bootstrap['table-cell-padding-x-sm'] = $bootstrap['table-cell-padding-y-sm'] = $this->mz_skin_config->get('style_table_cell_padding_sm');
            $bootstrap['table-striped-order']   = $this->mz_skin_config->get('style_table_striped_order');
            
            // Button
            $bootstrap['input-btn-padding-x']       = $this->mz_skin_config->get('style_btn_padding_x');
            $bootstrap['input-btn-padding-y']       = $this->mz_skin_config->get('style_btn_padding_y');
            $bootstrap['input-btn-line-height']     = $this->mz_skin_config->get('style_btn_line_height');
            $bootstrap['input-btn-padding-x-sm']    = $this->mz_skin_config->get('style_btn_padding_x_sm');
            $bootstrap['input-btn-padding-y-sm']    = $this->mz_skin_config->get('style_btn_padding_y_sm');
            $bootstrap['input-btn-line-height-sm']  = $this->mz_skin_config->get('style_btn_line_height_sm');
            $bootstrap['input-btn-padding-x-lg']    = $this->mz_skin_config->get('style_btn_padding_x_lg');
            $bootstrap['input-btn-padding-y-lg']    = $this->mz_skin_config->get('style_btn_padding_y_lg');
            $bootstrap['input-btn-line-height-lg']  = $this->mz_skin_config->get('style_btn_line_height_lg');
            $bootstrap['input-btn-border-width']    = $this->mz_skin_config->get('style_btn_border_width');
            $bootstrap['btn-border-radius']         = $this->mz_skin_config->get('style_btn_border_radius');
            $bootstrap['btn-border-radius-sm']      = $this->mz_skin_config->get('style_btn_border_radius_sm');
            $bootstrap['btn-border-radius-lg']      = $this->mz_skin_config->get('style_btn_border_radius_lg');
            // $bootstrap['btn-block-spacing-y']       = $this->mz_skin_config->get('style_btn_block_spacing_y');
            
            // Form
            $bootstrap['form-label-margin-bottom']       = $this->mz_skin_config->get('style_label_margin_bottom');
            $bootstrap['input-padding-x']           = $this->mz_skin_config->get('style_input_padding_x');
            $bootstrap['input-padding-y']           = $this->mz_skin_config->get('style_input_padding_y');
            $bootstrap['input-line-height']         = $this->mz_skin_config->get('style_input_line_height');
            $bootstrap['input-padding-x-sm']        = $this->mz_skin_config->get('style_input_padding_x_sm');
            $bootstrap['input-padding-y-sm']        = $this->mz_skin_config->get('style_input_padding_y_sm');
            $bootstrap['input-line-height-sm']      = $this->mz_skin_config->get('style_input_line_height_sm');
            $bootstrap['input-padding-x-lg']        = $this->mz_skin_config->get('style_input_padding_x_lg');
            $bootstrap['input-padding-y-lg']        = $this->mz_skin_config->get('style_input_padding_y_lg');
            $bootstrap['input-line-height-lg']      = $this->mz_skin_config->get('style_input_line_height_lg');
            $bootstrap['input-bg']                  = $this->mz_skin_config->get('style_input_bg');
            $bootstrap['input-color']               = $this->mz_skin_config->get('style_input_color');
            $bootstrap['input-disabled-bg']         = $this->mz_skin_config->get('style_input_disabled_bg');
            $bootstrap['input-border-width']        = $this->mz_skin_config->get('style_input_border_width');
            $bootstrap['input-border-color']        = $this->mz_skin_config->get('style_input_border_color');
            $bootstrap['input-border-radius']       = $this->mz_skin_config->get('style_input_border_radius');
            $bootstrap['input-border-radius-sm']    = $this->mz_skin_config->get('style_input_border_radius_sm');
            $bootstrap['input-border-radius-lg']    = $this->mz_skin_config->get('style_input_border_radius_lg');
            $bootstrap['input-focus-bg']            = $this->mz_skin_config->get('style_input_focus_bg');
            $bootstrap['input-focus-color']         = $this->mz_skin_config->get('style_input_focus_color');
            $bootstrap['input-focus-border-color']  = $this->mz_skin_config->get('style_input_focus_border_color');
            $bootstrap['input-placeholder-color']   = $this->mz_skin_config->get('style_input_placeholder_color');
            // $bootstrap['form-group-margin-bottom']  = $this->mz_skin_config->get('style_form_group_margin_bottom');
            $bootstrap['form-text-margin-top']      = $this->mz_skin_config->get('style_form_text_margin_top');
            
            // General
            $font_id = maza\getOfLanguage($this->mz_skin_config->get('style_font_family_base'));
            if($font_id){
                $bootstrap['font-family-base']      = $this->getFontFamily($font_id);
            }
            $bootstrap['line-height-base']      = $this->mz_skin_config->get('style_line_height_base');
            $bootstrap['font-size-base']        = $this->mz_skin_config->get('style_font_size_base');
            
            // Heading
            $font_id = maza\getOfLanguage($this->mz_skin_config->get('style_headings_font_family'));
            if($font_id){
                $bootstrap['headings-font-family']  = $this->getFontFamily($font_id);
            }
            $bootstrap['headings-margin-bottom']= $this->mz_skin_config->get('style_headings_margin_bottom');
            $bootstrap['headings-font-weight']  = $this->mz_skin_config->get('style_headings_font_weight');
            $bootstrap['headings-line-height']  = $this->mz_skin_config->get('style_headings_line_height');
            $bootstrap['headings-color']        = $this->mz_skin_config->get('style_headings_color');
            $bootstrap['h1-font-size']          = $this->mz_skin_config->get('style_h1_font_size');
            $bootstrap['h2-font-size']          = $this->mz_skin_config->get('style_h2_font_size');
            $bootstrap['h3-font-size']          = $this->mz_skin_config->get('style_h3_font_size');
            $bootstrap['h4-font-size']          = $this->mz_skin_config->get('style_h4_font_size');
            $bootstrap['h5-font-size']          = $this->mz_skin_config->get('style_h5_font_size');
            $bootstrap['h6-font-size']          = $this->mz_skin_config->get('style_h6_font_size');
            
            // paragraph
            $bootstrap['paragraph-margin-bottom'] = $this->mz_skin_config->get('style_paragraph_margin_bottom');
            
            // Link
            $bootstrap['link-color']            = $this->mz_skin_config->get('style_link_color');
            $bootstrap['link-hover-color']      = $this->mz_skin_config->get('style_link_hover_color');
            
            // Mark
            $bootstrap['mark-bg']               = $this->mz_skin_config->get('style_mark_bg');
            $bootstrap['mark-padding']          = $this->mz_skin_config->get('style_mark_padding');
            
            ## Components
            $bootstrap['line-height-lg']        = $this->mz_skin_config->get('style_line_height_lg');
            $bootstrap['line-height-sm']        = $this->mz_skin_config->get('style_line_height_sm');
            $bootstrap['border-width']          = $this->mz_skin_config->get('style_border_width');
            $bootstrap['border-color']          = $this->mz_skin_config->get('style_border_color');
            $bootstrap['border-radius']         = $this->mz_skin_config->get('style_border_radius');
            $bootstrap['border-radius-lg']      = $this->mz_skin_config->get('style_border_radius_lg');
            $bootstrap['border-radius-sm']      = $this->mz_skin_config->get('style_border_radius_sm');
            $bootstrap['component-active-color']= $this->mz_skin_config->get('style_component_active_color');
            $bootstrap['component-active-bg']   = $this->mz_skin_config->get('style_component_active_bg');
            
            // Dropdown
            $bootstrap['dropdown-min-width']        = $this->mz_skin_config->get('style_dropdown_min_width');
            $bootstrap['dropdown-padding-y']        = $this->mz_skin_config->get('style_dropdown_padding_y');
            $bootstrap['dropdown-spacer']           = $this->mz_skin_config->get('style_dropdown_spacer');
            $bootstrap['dropdown-item-padding-x']   = $this->mz_skin_config->get('style_dropdown_item_padding_x');
            $bootstrap['dropdown-item-padding-y']   = $this->mz_skin_config->get('style_dropdown_item_padding_y');
            $bootstrap['dropdown-bg']               = $this->mz_skin_config->get('style_dropdown_bg');
            $bootstrap['dropdown-border-color']     = $this->mz_skin_config->get('style_dropdown_border_color');
            $bootstrap['dropdown-border-radius']    = $this->mz_skin_config->get('style_dropdown_border_radius');
            $bootstrap['dropdown-border-width']     = $this->mz_skin_config->get('style_dropdown_border_width');
            $bootstrap['dropdown-link-color']       = $this->mz_skin_config->get('style_dropdown_link_color');
            $bootstrap['dropdown-link-hover-color'] = $this->mz_skin_config->get('style_dropdown_link_hover_color');
            $bootstrap['dropdown-link-hover-bg']    = $this->mz_skin_config->get('style_dropdown_link_hover_bg');
            $bootstrap['dropdown-link-active-color']= $this->mz_skin_config->get('style_dropdown_link_active_color');
            $bootstrap['dropdown-link-active-bg']   = $this->mz_skin_config->get('style_dropdown_link_active_bg');
            
            // Pagination
            $bootstrap['pagination-padding-x']      = $this->mz_skin_config->get('style_pagination_padding_x');
            $bootstrap['pagination-padding-y']      = $this->mz_skin_config->get('style_pagination_padding_y');
            $bootstrap['pagination-padding-x-sm']   = $this->mz_skin_config->get('style_pagination_padding_x_sm');
            $bootstrap['pagination-padding-y-sm']   = $this->mz_skin_config->get('style_pagination_padding_y_sm');
            $bootstrap['pagination-padding-x-lg']   = $this->mz_skin_config->get('style_pagination_padding_x_lg');
            $bootstrap['pagination-padding-y-lg']   = $this->mz_skin_config->get('style_pagination_padding_y_lg');
            // $bootstrap['pagination-line-height']    = $this->mz_skin_config->get('style_pagination_line_height');
            $bootstrap['pagination-color']          = $this->mz_skin_config->get('style_pagination_color');
            $bootstrap['pagination-bg']             = $this->mz_skin_config->get('style_pagination_bg');
            $bootstrap['pagination-border-width']   = $this->mz_skin_config->get('style_pagination_border_width');
            $bootstrap['pagination-border-color']   = $this->mz_skin_config->get('style_pagination_border_color');
            $bootstrap['pagination-hover-color']    = $this->mz_skin_config->get('style_pagination_hover_color');
            $bootstrap['pagination-hover-bg']       = $this->mz_skin_config->get('style_pagination_hover_bg');
            $bootstrap['pagination-hover-border-color'] = $this->mz_skin_config->get('style_pagination_hover_border_color');
            $bootstrap['pagination-active-color']   = $this->mz_skin_config->get('style_pagination_active_color');
            $bootstrap['pagination-active-bg']      = $this->mz_skin_config->get('style_pagination_active_bg');
            $bootstrap['pagination-active-border-color'] = $this->mz_skin_config->get('style_pagination_active_border_color');
            
            // Card
            $bootstrap['card-spacer-x']             = $this->mz_skin_config->get('style_card_spacer_x');
            $bootstrap['card-spacer-y']             = $this->mz_skin_config->get('style_card_spacer_y');
            $bootstrap['card-border-color']         = $this->mz_skin_config->get('style_card_border_color');
            $bootstrap['card-border-radius']        = $this->mz_skin_config->get('style_card_border_radius');
            $bootstrap['card-border-width']         = $this->mz_skin_config->get('style_card_border_width');
            
            // Modal
            $bootstrap['modal-xl']                  = $this->mz_skin_config->get('style_modal_xl');
            $bootstrap['modal-lg']                  = $this->mz_skin_config->get('style_modal_lg');
            $bootstrap['modal-md']                  = $this->mz_skin_config->get('style_modal_md');
            $bootstrap['modal-sm']                  = $this->mz_skin_config->get('style_modal_sm');
            $bootstrap['modal-backdrop-bg']         = $this->mz_skin_config->get('style_modal_backdrop_bg');
            $bootstrap['modal-backdrop-opacity']    = $this->mz_skin_config->get('style_modal_backdrop_opacity');
            
            // Alert
            $bootstrap['alert-padding-x']           = $this->mz_skin_config->get('style_alert_padding_x');
            $bootstrap['alert-padding-y']           = $this->mz_skin_config->get('style_alert_padding_y');
            $bootstrap['alert-margin-bottom']       = $this->mz_skin_config->get('style_alert_margin_bottom');
            $bootstrap['alert-border-radius']       = $this->mz_skin_config->get('style_alert_border_radius');
            $bootstrap['alert-border-width']        = $this->mz_skin_config->get('style_alert_border_width');
            
            // Breadcrumbs
            $bootstrap['breadcrumb-padding-x']      = $this->mz_skin_config->get('style_breadcrumb_padding_x');
            $bootstrap['breadcrumb-padding-y']      = $this->mz_skin_config->get('style_breadcrumb_padding_y');
            $bootstrap['breadcrumb-item-padding-x']   = $this->mz_skin_config->get('style_breadcrumb_item_padding');
            $bootstrap['breadcrumb-margin-bottom']  = $this->mz_skin_config->get('style_breadcrumb_margin_bottom');
            $bootstrap['breadcrumb-divider']        = $this->mz_skin_config->get('style_breadcrumb_divider');
            $bootstrap['breadcrumb-divider-color']  = $this->mz_skin_config->get('style_breadcrumb_divider_color');
            $bootstrap['breadcrumb-border-radius']  = $this->mz_skin_config->get('style_breadcrumb_border_radius');
            $bootstrap['breadcrumb-bg']             = $this->mz_skin_config->get('style_breadcrumb_bg');
            $bootstrap['breadcrumb-active-color']   = $this->mz_skin_config->get('style_breadcrumb_active_color');
            
            
            ## - Bootstrap variables
            
            ## Maza variables
            $maza = array();
            $maza['mz_box_layout']  = $this->mz_skin_config->get('style_layout_style') === 'box';
            
            // Spinner
            $loader_spinner_svg = maza\getOfLanguage($this->mz_skin_config->get('style_loader_spinner_svg'));
            $loader_spinner_image = maza\getOfLanguage($this->mz_skin_config->get('style_loader_spinner_image'));
            
            if($loader_spinner_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $loader_spinner_svg)){
                $maza['mz_spinner_url'] = '"' . $this->config->get('mz_store_url') . substr(MZ_CONFIG::$DIR_SVG_IMAGE, strlen(substr(DIR_APPLICATION, 0, -strlen('catalog/')))) . $loader_spinner_svg . '"';
            } elseif($loader_spinner_image && is_file(DIR_IMAGE . $loader_spinner_image)){
                $maza['mz_spinner_url'] = '"' . $this->config->get('mz_store_url') . 'image/' . $loader_spinner_image . '"';
            } else {
                $maza['mz_spinner_url'] = '"' . $this->config->get('mz_store_url') . 'image/no_image.png"';
            }
            
            // Body background
            $maza['mz_body_background'] = array();
            foreach ($this->mz_skin_config->get('style_body_background') as $background) {
                $bg_data = array();
                
                // Background image
                if($background['status'] == 'image' && $background['image']){
                    $bg_data[] = 'url("' . $this->config->get('mz_store_url') . 'image/' . $background['image'] . '")';
                    $bg_data[] = str_replace('_', ' ', $background['image_position']);
                    $bg_data[] = $background['image_repeat'];
                    $bg_data[] = $background['image_attachment'];
                }
                
                // Background patter
                elseif($background['status'] == 'pattern'){
                    $overlay_pattern = $this->model_extension_maza_asset->overlayPatterns($background['overlay_pattern']);
                    
                    if($overlay_pattern){
                        $bg_data[] = 'url("' . $this->config->get('mz_store_url') . 'image/' . $overlay_pattern['image'] . '")';
                    }
                }
                
                if($bg_data){
                    $maza['mz_body_background'][] = implode(' ', $bg_data);
                }
            }
            $maza['mz_body_background'] = implode(', ', $maza['mz_body_background']);
            
            // Create sass variable format
            return '// Boostrap variables' . PHP_EOL . maza\Scss::getVariableFormat($bootstrap) 
                    . '// Maza variables' . PHP_EOL . maza\Scss::getVariableFormat($maza);
    }
    
    /**
     * Get font family
     * @param int $font_id font id
     */
    private function getFontFamily($font_id){
            $font_family = null;
                
            $font_info = $this->model_extension_maza_asset->getFont($font_id);

            if($font_info){
                $font_family = trim(html_entity_decode($font_info['font_family']), '"\' ');

                if(preg_match('/[\s]/', $font_family) === 1){
                    $font_family = '"' . $font_family . '"';
                }
                
                $parent_font_family = $this->getFontFamily($font_info['parent_font_id']);
                if($parent_font_family){
                    $font_family .= ',' . $parent_font_family;
                }
            }

            return $font_family;
    }
    
    /**
     * Get compiled bootstrap css
     * @return string Bootstrap CSS file
     */
    protected function getBootstrap(){
            $scss = '';
            $scss .= '@import "maza";' . PHP_EOL; 
            $scss .= $this->getSassStyleVariable() . PHP_EOL; // style variable
            $scss .= '@import "bootstrap5/bootstrap";' . PHP_EOL; // Bootstrap SCSS
            
            if($this->language->get('direction') === 'rtl'){
                $scss_compiler = $this->getScss(['rtl' => true]);
            } else {
                $scss_compiler = $this->getScss(['rtl' => false]);
            }
            
            return $scss_compiler->compile($scss, $this->config->get('maza_css_autoprefix'));
    }
    
    /**
     * Get global CSS code
     * @return string CSS code
     */
    protected function getMainCss() {
            $scss = '';
            
            // Style variables
            $scss .= $this->getSassStyleVariable() . PHP_EOL; // style variable
            
            // Bootstrap tools
            $scss .= '@import "bootstrap5/functions";' . PHP_EOL; // Bootstrap SCSS
            $scss .= '@import "skins/bootstrap.config";' . PHP_EOL; // Bootstrap SCSS
            $scss .= '@import "bootstrap5/mixins";' . PHP_EOL; // Bootstrap SCSS
            
            // theme common style
            $scss .= '@import "maza";' . PHP_EOL; 
            $scss .= '@import "skins/common";' . PHP_EOL; 
            
            // Hover effect
            $scss .= '@import "hover_effects";'  . PHP_EOL;
            
            $scss .= $this->model_extension_maza_asset->getCustomCSS();
            
            
            if($this->language->get('direction') === 'rtl'){
                $scss_compiler = $this->getScss(['rtl' => true]);
            } else {
                $scss_compiler = $this->getScss(['rtl' => false]);
            }
            
            return $scss_compiler->compile($scss, $this->config->get('maza_css_autoprefix'));
    }
    
    /**
     * Get global javascript code
     * @return string Javascript code
     */
    protected function getMainJs(){
            $js = '';
            
            // common.js
            if(file_exists(MZ_CONFIG::$DIR_GLOBAL_JS . 'common.js')){
                $js .= file_get_contents(MZ_CONFIG::$DIR_GLOBAL_JS . 'common.js');
            }
            if(file_exists(MZ_CONFIG::$DIR_SKINS . 'common.js')){
                $js .= file_get_contents(MZ_CONFIG::$DIR_SKINS . 'common.js');
            }
            
            // Header js
            if(file_exists(MZ_CONFIG::$DIR_SKIN_HEADER . 'javascript.js')){
                $js .= file_get_contents(MZ_CONFIG::$DIR_JS_CACHE . 'javascript.js');
            }
            
            // Footer js
            if(file_exists(MZ_CONFIG::$DIR_SKIN_FOOTER . 'javascript.js')){
                $js .= file_get_contents(MZ_CONFIG::$DIR_SKIN_FOOTER . 'javascript.js');
            }
            
            // Skin js
            if(file_exists(MZ_CONFIG::$DIR_SKIN_CONTENT . 'javascript.js')){
                $js .= file_get_contents(MZ_CONFIG::$DIR_SKIN_CONTENT . 'javascript.js');
            }
            
            // Custom Js
            $js .= $this->model_extension_maza_asset->getCustomJavacript();
            
            return $js;
    }

    /**
     * Get runtime custom css
     */
    private function getCustomCSS(): string {
        $scss = '';
        $scss .= '@import "bootstrap5/functions";' . PHP_EOL; // Bootstrap SCSS
        $scss .= '@import "skins/bootstrap.config";' . PHP_EOL; // Bootstrap SCSS
        $scss .= '@import "bootstrap5/mixins";' . PHP_EOL; // Bootstrap SCSS
        $scss .= '@import "maza";' . PHP_EOL; 

        // sass style variable file
        $scss .= $this->getSassStyleVariable();

        $scss .=  $this->mz_document->getCSSCode();
        
        if($this->language->get('direction') === 'rtl'){
            $scss_compiler = $this->getScss(['rtl' => true]);
        } else {
            $scss_compiler = $this->getScss(['rtl' => false]);
        }
        
        return $scss_compiler->compile($scss);
    }
    
    /**
     * Get scss compiler
     */
    private function getScss(array $variables = array()): maza\Scss {
            $mz_scss = new maza\Scss();

            // Add import paths of sass file
            $import_paths = array();
            $import_paths[] = MZ_CONFIG::$DIR_GLOBAL_CSS . 'sass/';
            $import_paths[] = DIR_APPLICATION . 'view/theme/' . $this->mz_theme_config->get('theme_code') . '/';
            $mz_scss->setImportPaths($import_paths);
            $mz_scss->setVariables(array_merge([
                'active_header_code' => $this->mz_skin_config->get('skin_header_code'),
                'active_footer_code' => $this->mz_skin_config->get('skin_footer_code'),
                'active_skin_code' => $this->mz_skin_config->get('skin_code'),
            ], $variables));
            
            return $mz_scss;
    }
}
