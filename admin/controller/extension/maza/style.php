<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazastyle extends Controller {
        private $error = array();
        
        public function index(): void {
                $this->load->language('extension/maza/style');
                
                $this->load->model('tool/image');
                $this->load->model('extension/maza/asset');

		        $this->document->setTitle($this->language->get('heading_title'));
                
                $this->document->addStyle('view/javascript/maza/colorpicker/css/colorpicker.css');
                $this->document->addScript('view/javascript/maza/colorpicker/js/colorpicker.js');
                
                $url = '';
                
                if(isset($this->request->get['mz_theme_code'])){
                    $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                
                if(isset($this->request->get['mz_skin_id'])){
                    $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
                
                // Header
                $header_data = array();
                
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_layout'), 'id' => 'tab-mz-layout', 'href' => false),
                    array('name' => $this->language->get('tab_color'), 'id' => 'tab-mz-color', 'href' => false),
                    array('name' => $this->language->get('tab_body'), 'id' => 'tab-mz-body', 'href' => false),
                    array('name' => $this->language->get('tab_global'), 'id' => 'tab-mz-global', 'href' => false),
                    array('name' => $this->language->get('tab_typography'), 'id' => 'tab-mz-typography', 'href' => false),
                    array('name' => $this->language->get('tab_components'), 'id' => 'tab-mz-components', 'href' => false),
                );
                
                $header_data['menu_active'] = 'tab-mz-layout';
                $header_data['buttons'][] = array(
                    'id' => 'button-import',
                    'name' => false,
                    'tooltip' => $this->language->get('button_import'),
                    'icon' => 'fa-upload',
                    'class' => 'btn-info',
                    'href' => false,
                    'target' => FALSE,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-export',
                    'name' => false,
                    'tooltip' => $this->language->get('button_export'),
                    'icon' => 'fa-download',
                    'class' => 'btn-info',
                    'href' => $this->url->link('extension/maza/style/export', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-save',
                    'name' => false,
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'class' => 'btn-primary',
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-mz-style',
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-style',
                    'target' => '_blank'
                );
                $header_data['form_target_id'] = 'form-mz-style';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Submit form
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()){
                    $this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'style', $this->request->post);
                    // clear asset files for new settings
                    $this->mz_document->clear();
                    
                    $data['success'] = $this->language->get('text_success');
                }
                
                if(isset($this->error['warning'])){
                    $data['warning'] = $this->error['warning'];
                }
                
                if (isset($this->session->data['success'])) {
                    $data['success'] = $this->session->data['success'];
                    unset($this->session->data['success']);
                }
                if (isset($this->session->data['warning'])) {
                    $data['warning'] = $this->session->data['warning'];
                    unset($this->session->data['warning']);
                }
                
                $data['import'] = $this->url->link('extension/maza/style/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
                $data['action'] = $this->url->link('extension/maza/style', 'user_token=' . $this->session->data['user_token'] . $url, true);
                
                // Setting
                
                ## Layout
                $setting = array();
                $setting['style_page_loader_status']    = 0;
                $setting['style_back_to_top_status']    = 1;
                $setting['style_loader_spinner_image']  = array();
                $setting['style_loader_spinner_svg']    = array();
                $setting['style_layout_style']          = 'full';
                // $setting['style_gutter_width']          = 30;
                $setting['style_breakpoints']           = array('xs' => 0, 'sm' => 576, 'md' => 768, 'lg' => 992, 'xl' => 1200);
                $setting['style_container_width']       = array('sm' => 540, 'md' => 720, 'lg' => 960, 'xl' => 1140);
                
                ## Color
                $setting['style_color_palette'] = 'skin_' . $this->mz_skin_config->get('skin_code');
                $setting['style_color_custom'] = array();
                
                ## Body
                $setting['style_body_bg'] = null;
                $setting['style_body_color'] = null;
                
                for($layer = 1; $layer <= 3; $layer++){
                    $setting['style_body_background']['layer_' . $layer] = array(
                            'status'            =>  'none',
                            'image'             =>  'no_image.png',
                            'thumb'             =>  $this->model_tool_image->resize('no_image.png', 80, 80),
                            'image_position'    =>  'left_top',
                            'image_repeat'      =>  'repeat',
                            'image_attachment'  =>  'scroll',
                            'overlay_pattern'   =>  'default',
                    );
                }
                
                ## Global
                $setting['style_enable_rounded'] = 1;
                $setting['style_enable_shadows'] = 0;
                $setting['style_enable_gradients'] = 0;
                $setting['style_spacer'] = null;
                $setting['style_hr_border_color'] = null;
                $setting['style_hr_border_width'] = null;
                $setting['style_hr_margin_y'] = null;
                // $setting['style_print_page_size'] = null;
                
                // table
                $setting['style_table_cell_padding'] = null;
                $setting['style_table_cell_padding_sm'] = null;
                $setting['style_table_striped_order'] = null;
                
                // Button
                $setting['style_btn_padding_x'] = null;
                $setting['style_btn_padding_y'] = null;
                $setting['style_btn_line_height'] = null;
                $setting['style_btn_padding_x_sm'] = null;
                $setting['style_btn_padding_y_sm'] = null;
                $setting['style_btn_line_height_sm'] = null;
                $setting['style_btn_padding_x_lg'] = null;
                $setting['style_btn_padding_y_lg'] = null;
                $setting['style_btn_line_height_lg'] = null;
                $setting['style_btn_border_width'] = null;
                $setting['style_btn_border_radius'] = null;
                $setting['style_btn_border_radius_sm'] = null;
                $setting['style_btn_border_radius_lg'] = null;
                // $setting['style_btn_block_spacing_y'] = null;
                
                // Form
                $setting['style_label_margin_bottom'] = null;
                $setting['style_input_padding_x'] = null;
                $setting['style_input_padding_y'] = null;
                $setting['style_input_line_height'] = null;
                $setting['style_input_padding_x_sm'] = null;
                $setting['style_input_padding_y_sm'] = null;
                $setting['style_input_line_height_sm'] = null;
                $setting['style_input_padding_x_lg'] = null;
                $setting['style_input_padding_y_lg'] = null;
                $setting['style_input_line_height_lg'] = null;
                $setting['style_input_bg'] = null;
                $setting['style_input_color'] = null;
                $setting['style_input_disabled_bg'] = null;
                $setting['style_input_border_width'] = null;
                $setting['style_input_border_color'] = null;
                $setting['style_input_border_radius'] = null;
                $setting['style_input_border_radius_sm'] = null;
                $setting['style_input_border_radius_lg'] = null;
                $setting['style_input_focus_bg'] = null;
                $setting['style_input_focus_color'] = null;
                $setting['style_input_focus_border_color'] = null;
                $setting['style_input_placeholder_color'] = null;
                $setting['style_form_group_margin_bottom'] = null;
                $setting['style_form_text_margin_top'] = null;
                
                ## Typography
                // General
                $setting['style_font_family_base'] = array();
                $setting['style_line_height_base'] = null;
                $setting['style_font_size_base'] = '1.6rem';
                
                // Color
                $setting['style_min_contrast_ratio'] = 4.5;
                $data['list_min_contrast_ratio'] = array(3, 4.5, 7);
                
                // Heading
                $setting['style_headings_margin_bottom'] = null;
                $setting['style_headings_font_family'] = array();
                $setting['style_headings_font_weight'] = null;
                $setting['style_headings_line_height'] = null;
                $setting['style_headings_color'] = null;
                $setting['style_h1_font_size'] = null;
                $setting['style_h2_font_size'] = null;
                $setting['style_h3_font_size'] = null;
                $setting['style_h4_font_size'] = null;
                $setting['style_h5_font_size'] = null;
                $setting['style_h6_font_size'] = null;
                
                // paragraph
                $setting['style_paragraph_margin_bottom'] = null;
                
                // Link
                $setting['style_link_color'] = null;
                $setting['style_link_hover_color'] = null;
                
                // Mark
                $setting['style_mark_bg'] = null;
                $setting['style_mark_padding'] = null;
                
                ## Components
                $setting['style_line_height_lg'] = null;
                $setting['style_line_height_sm'] = null;
                $setting['style_border_width'] = null;
                $setting['style_border_color'] = null;
                $setting['style_border_radius'] = null;
                $setting['style_border_radius_lg'] = null;
                $setting['style_border_radius_sm'] = null;
                $setting['style_border_radius_sm'] = null;
                $setting['style_component_active_color'] = null;
                $setting['style_component_active_bg'] = null;
                
                // Dropdown
                $setting['style_dropdown_min_width'] = null;
                $setting['style_dropdown_padding_y'] = null;
                $setting['style_dropdown_spacer'] = null;
                $setting['style_dropdown_item_padding_x'] = null;
                $setting['style_dropdown_item_padding_y'] = null;
                $setting['style_dropdown_bg'] = null;
                $setting['style_dropdown_border_color'] = null;
                $setting['style_dropdown_border_radius'] = null;
                $setting['style_dropdown_border_width'] = null;
                $setting['style_dropdown_link_color'] = null;
                $setting['style_dropdown_link_hover_color'] = null;
                $setting['style_dropdown_link_hover_bg'] = null;
                $setting['style_dropdown_link_active_color'] = null;
                $setting['style_dropdown_link_active_bg'] = null;
                
                // Pagination
                $setting['style_pagination_padding_x'] = null;
                $setting['style_pagination_padding_y'] = null;
                $setting['style_pagination_padding_x_sm'] = null;
                $setting['style_pagination_padding_y_sm'] = null;
                $setting['style_pagination_padding_x_lg'] = null;
                $setting['style_pagination_padding_y_lg'] = null;
                $setting['style_pagination_line_height'] = null;
                $setting['style_pagination_color'] = null;
                $setting['style_pagination_bg'] = null;
                $setting['style_pagination_border_width'] = null;
                $setting['style_pagination_border_color'] = null;
                $setting['style_pagination_hover_color'] = null;
                $setting['style_pagination_hover_bg'] = null;
                $setting['style_pagination_hover_border_color'] = null;
                $setting['style_pagination_active_color'] = null;
                $setting['style_pagination_active_bg'] = null;
                $setting['style_pagination_active_border_color'] = null;
                
                // Card
                $setting['style_card_spacer_x'] = null;
                $setting['style_card_spacer_y'] = null;
                $setting['style_card_border_color'] = null;
                $setting['style_card_border_radius'] = null;
                $setting['style_card_border_width'] = null;
                
                // Modal
                $setting['style_modal_xl'] = null;
                $setting['style_modal_lg'] = null;
                $setting['style_modal_md'] = null;
                $setting['style_modal_sm'] = null;
                $setting['style_modal_backdrop_bg'] = null;
                $setting['style_modal_backdrop_opacity'] = null;
                
                // Alert
                $setting['style_alert_padding_x'] = null;
                $setting['style_alert_padding_y'] = null;
                $setting['style_alert_margin_bottom'] = null;
                $setting['style_alert_border_radius'] = null;
                $setting['style_alert_border_width'] = null;
                
                // Breadcrumbs
                $setting['style_breadcrumb_padding_x'] = null;
                $setting['style_breadcrumb_padding_y'] = null;
                $setting['style_breadcrumb_item_padding'] = null;
                $setting['style_breadcrumb_margin_bottom'] = null;
                $setting['style_breadcrumb_divider'] = null;
                $setting['style_breadcrumb_border_radius'] = null;
                $setting['style_breadcrumb_bg'] = null;
                $setting['style_breadcrumb_active_color'] = null;
                $setting['style_breadcrumb_divider_color'] = null;
                
                $this->load->model('localisation/language');
                $data['languages'] = $this->model_localisation_language->getLanguages();
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } else {
                    $setting = array_merge($setting, $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'style')); 
                }
                
                // modify post changes
                // Background
                foreach ($setting['style_body_background'] as $code => $layer_background) {
                    $setting['style_body_background'][$code]['thumb'] = $this->model_tool_image->resize($layer_background['image'], 80, 80);
                }
                
                // Data
                $data = array_merge($data, $setting);
                $data['user_token'] = $this->session->data['user_token'];
                
                $data['help_emulator'] = sprintf($this->language->get('help_emulator'), $this->config->get('mz_store_url') . 'index.php?route=extension/maza/emulator&width=375&url=' . urlencode(($this->request->server['HTTPS']?HTTPS_CATALOG:HTTP_CATALOG) . '?' . $url) . $url);
                
                // Placeholder
                $data['placeholder']        = $this->model_tool_image->resize('no_image.png', 80, 80);
                $data['placeholder_image']  = $this->model_tool_image->resize('no_image.png', 100, 100);
                $data['placeholder_svg']    = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                $data['placeholder_font']   = 'fa fa-font';
                
                // Loader spinner image
                $data['thumb_loader_spinner_image'] = array();

                foreach ($setting['style_loader_spinner_image'] as $language_id => $icon_image) {
                    if($icon_image){
                        $data['thumb_loader_spinner_image'][$language_id] = $this->model_tool_image->resize($icon_image, 100, 100);
                    } else {
                        $data['thumb_loader_spinner_image'][$language_id] = $data['placeholder_image'];
                    }
                }

                // Loader spinner svg
                $data['thumb_loader_spinner_svg'] = array();

                foreach ($setting['style_loader_spinner_svg'] as $language_id => $icon_svg) {
                    if($icon_svg){
                        $data['thumb_loader_spinner_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $icon_svg;
                    } else {
                        $data['thumb_loader_spinner_svg'][$language_id] = $data['placeholder_svg'];
                    }
                }
                
                // Color data
                $data['color_palettes'] = $this->model_extension_maza_asset->getColorPalettes();
                $data['color_types'] = $this->model_extension_maza_asset->getColorTypes();
                
                // Custom color
                $custom_color = array();
                foreach ($data['color_types'] as $type) {
                    if(!empty($data['style_color_custom'][$type])){
                        $custom_color[$type] = $data['style_color_custom'][$type];
                    } else {
                        $custom_color[$type] = $data['style_color_custom'][$type] = $data['color_palettes']['default']['color'][$type];
                    }
                }
                
                $data['color_palettes']['custom'] = array(
                        'name' => $this->language->get('text_custom'),
                        'code' => 'custom',
                        'color' => $custom_color
                );
                
                $data['list_number_order'] = array(
                    array('code' => 'odd', 'text' => $this->language->get('text_odd')),
                    array('code' => 'even', 'text' => $this->language->get('text_even')),
                );
                
                // background data
                $data['body_background_status'] = array(
                        array('code' => 'none', 'name' => $this->language->get('text_none')),
                        array('code' => 'image', 'name' => $this->language->get('text_image')),
                        array('code' => 'pattern', 'name' => $this->language->get('text_pattern')),
                );
                $data['background_image_positions'] = array(
                        array('code' => 'left_top', 'name' => $this->language->get('text_left_top')),
                        array('code' => 'left_center', 'name' => $this->language->get('text_left_center')),
                        array('code' => 'left_bottom', 'name' => $this->language->get('text_left_bottom')),
                        array('code' => 'right_top', 'name' => $this->language->get('text_right_top')),
                        array('code' => 'right_center', 'name' => $this->language->get('text_right_center')),
                        array('code' => 'right_bottom', 'name' => $this->language->get('text_right_bottom')),
                        array('code' => 'center_top', 'name' => $this->language->get('text_center_top')),
                        array('code' => 'center_center', 'name' => $this->language->get('text_center_center')),
                        array('code' => 'center_bottom', 'name' => $this->language->get('text_center_bottom')),
                );
                $data['background_image_repeats'] = array(
                        array('code' => 'repeat', 'name' => $this->language->get('text_repeat')),
                        array('code' => 'repeat-x', 'name' => $this->language->get('text_repeat_x')),
                        array('code' => 'repeat-y', 'name' => $this->language->get('text_repeat_y')),
                        array('code' => 'no-repeat', 'name' => $this->language->get('text_no_repeat')),
                );
                $data['background_image_attachments'] = array(
                        array('code' => 'scroll', 'name' => $this->language->get('text_scroll')),
                        array('code' => 'fixed', 'name' => $this->language->get('text_fixed')),
                );
                $data['overlay_patterns'] = $this->model_extension_maza_asset->overlayPatterns();
                foreach ($data['overlay_patterns'] as $key => $pattern) {
                    $data['overlay_patterns'][$key]['image'] = $this->config->get('mz_store_url') . 'image/' . $pattern['image'];
                }
                
                // Fonts
                $data['font_family_list'] = $this->model_extension_maza_asset->getFonts();
                
                $data['font_weight_list'] = array(
                        array(
                            'code' => 'normal',
                            'name' => 'Normal'
                        ),
                        array(
                            'code' => 'bold',
                            'name' => 'bold'
                        ),
                        array(
                            'code' => 'bolder',
                            'name' => 'bolder'
                        ),
                        array(
                            'code' => 'lighter',
                            'name' => 'lighter'
                        ),
                        array(
                            'code' => 'initial',
                            'name' => 'initial'
                        ),
                        array(
                            'code' => 'inherit',
                            'name' => 'inherit'
                        ),
                );
                foreach (range(100, 900, 100) as $value) {
                    $data['font_weight_list'][] = array(
                            'code' => $value,
                            'name' => $value
                    );
                }
                
                // google fonts
                $data['google_fonts'] = $this->model_extension_maza_asset->getFonts('google');
                
                foreach ($data['google_fonts'] as $key => $font) {
                    $data['google_fonts'][$key]['parent_font_info'] = $this->model_extension_maza_asset->getFont($font['parent_font_id']);
                }
                
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		        $this->response->setOutput($this->load->view('extension/maza/style', $data));
        }
        
        protected function validate(): bool {
                if (!$this->user->hasPermission('modify', 'extension/maza/style')) {
                    $this->error['warning'] = $this->language->get('error_permission');
                }

                return !$this->error;
        }
        
        /**
         * Export setting
         */
        public function export(): void {
                $this->load->model('extension/maza/skin');
                $this->load->language('extension/maza/style');
                
                $setting = $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'style');
                
                if($setting){
                    header('Content-Type: application/json; charset=utf-8');
                    header('Content-disposition: attachment; filename="maza.setting.style.' . $this->mz_skin_config->get('skin_code') . '.json"');
                    
                    echo json_encode(['type' => 'maza', 'code' => 'style', 'setting' => $setting]);
                } else {
                    $this->session->data['warning'] = $this->language->get('error_no_setting');
                    
                    $url = '';
                
                    if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                    }

                    if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                    }
                    
                    $this->response->redirect($this->url->link('extension/maza/style', 'user_token=' . $this->session->data['user_token'] . $url, true)); 
                }
        }
        
        /**
         * Import setting
         */
        public function import(): void {
                $this->load->language('extension/maza/style');
                
                $warning = '';

                // Check user has permission
                if (!$this->user->hasPermission('modify', 'extension/maza/style')) {
                        $warning = $this->language->get('error_permission');
                } else {
                    if (isset($this->request->files['file']['name'])) {
                            if (substr($this->request->files['file']['name'], -4) != 'json') {
                                    $warning = $this->language->get('error_filetype');
                            }

                            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                                    $warning = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                            }
                    } else {
                            $warning = $this->language->get('error_upload');
                    }
                }

                if (!$warning) {
                        $file = $this->request->files['file']['tmp_name'];

                        if (is_file($file)) {
                                $data = json_decode(file_get_contents($file), true);
                                
                                if($data && $data['type'] == 'maza' && $data['code'] == 'style'){
                                    $this->load->model('extension/maza/skin');
                                    
                                    $this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'style', $data['setting']);
                                    
                                    $this->session->data['success'] = $this->language->get('text_success_import');
                                } else {
                                    $warning = $this->language->get('error_import_file');
                                }
                        } else {
                                $warning = $this->language->get('error_file');
                        }
                }
                
                if($warning){
                    $this->session->data['warning'] = $warning;
                }
                
                $url = '';
                
                if(isset($this->request->get['mz_theme_code'])){
                    $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                
                if(isset($this->request->get['mz_skin_id'])){
                    $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }

                $this->response->redirect($this->url->link('extension/maza/style', 'user_token=' . $this->session->data['user_token'] . $url, true)); 
        }
}
