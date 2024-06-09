<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaLayoutBuilderEditComponent extends Controller {
        public function index(){
                $this->load->language('extension/maza/layout_builder');
                $this->load->language('extension/maza/layout_builder/edit_component');
                
                $this->load->model('tool/image');
                $this->load->model('extension/maza/asset');
                $this->load->model('extension/maza/common');
                $this->load->model('extension/maza/content_builder');
                $this->load->model('localisation/language');
                $this->load->model('customer/customer_group');
                
                $data = array();
                
                $screen_sizes = array('xl','lg', 'md', 'sm', 'xs');
                
                $setting = maza\layout\Component::getSettings();
                
                if(!empty($this->request->post)){
                    $setting = array_merge($setting, $this->request->post);
                }
                
                $this->load->model('localisation/language');
                $languages = $this->model_localisation_language->getLanguages();
                
                // modify post changes
                // Background
                foreach($screen_sizes as $size){
                    foreach ($setting[$size]['component_background_image'] as $code => $layer_background) {
                        foreach($languages as $language){
                            if(isset($layer_background['image'][$language['language_id']]) && is_file(DIR_IMAGE . $layer_background['image'][$language['language_id']])){
                                $setting[$size]['component_background_image'][$code]['thumb'][$language['language_id']] = $this->model_tool_image->resize($layer_background['image'][$language['language_id']], 80, 80);
                            } else {
                                $setting[$size]['component_background_image'][$code]['thumb'][$language['language_id']] = $this->model_tool_image->resize('no_image.png', 80, 80);
                            }
                        }
                    }
                }
                
                $data = array_merge($data, $setting);
                
                
                // Data
                $data['screen_sizes'] = $screen_sizes;
                $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 80, 80);
                
                $data['status_customers'] = array(
                        array('code' => 'all', 'name' => $this->language->get('text_all')),
                        array('code' => 'logged', 'name' => $this->language->get('text_logged')),
                        array('code' => 'guest', 'name' => $this->language->get('text_guest')),
                );
                $data['status_customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
                
                $data['list_text_align']  = array(
                        array('code' => 'default', 'name' => $this->language->get('text_default')),
                        array('code' => 'left', 'name' => $this->language->get('text_left')),
                        array('code' => 'center', 'name' => $this->language->get('text_center')),
                        array('code' => 'right', 'name' => $this->language->get('text_right')),
                );
                $data['list_flex_direction']  = array(
                        array('code' => 'row', 'name' => $this->language->get('text_row')),
                        array('code' => 'row-reverse', 'name' => $this->language->get('text_row_reverse')),
                        array('code' => 'column', 'name' => $this->language->get('text_column')),
                        array('code' => 'column-reverse', 'name' => $this->language->get('text_column_reverse')),
                );
                $data['list_justify_content']  = array(
                        array('code' => 'start', 'name' => $this->language->get('text_start')),
                        array('code' => 'center', 'name' => $this->language->get('text_center')),
                        array('code' => 'end', 'name' => $this->language->get('text_end')),
                        array('code' => 'between', 'name' => $this->language->get('text_between')),
                        array('code' => 'around', 'name' => $this->language->get('text_around')),
                );
                $data['list_align_items']  = array(
                        array('code' => 'start', 'name' => $this->language->get('text_start')),
                        array('code' => 'center', 'name' => $this->language->get('text_center')),
                        array('code' => 'end', 'name' => $this->language->get('text_end')),
                        array('code' => 'stretch', 'name' => $this->language->get('text_stretch')),
                );
                $data['list_flex_wrap']  = array(
                        array('code' => 'nowrap', 'name' => $this->language->get('text_nowrap')),
                        array('code' => 'wrap', 'name' => $this->language->get('text_wrap')),
                        array('code' => 'wrap-reverse', 'name' => $this->language->get('text_wrap_reverse')),
                );
                $data['background_status'] = array(
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
                        array('code' => 'initial', 'name' => $this->language->get('text_default')),
                        array('code' => 'scroll', 'name' => $this->language->get('text_scroll')),
                        array('code' => 'fixed', 'name' => $this->language->get('text_fixed')),
                        array('code' => 'local', 'name' => $this->language->get('text_local'))
                );
                $data['background_image_sizes'] = array(
                        array('code' => 'initial', 'name' => $this->language->get('text_default')),
                        array('code' => 'auto', 'name' => $this->language->get('text_auto')),
                        array('code' => 'cover', 'name' => $this->language->get('text_cover')),
                        array('code' => 'contain', 'name' => $this->language->get('text_contain')),
                        array('code' => '100%', 'name' => $this->language->get('text_100'))
                );
                $data['overlay_patterns'] = $this->model_extension_maza_asset->overlayPatterns();
                foreach ($data['overlay_patterns'] as $key => $pattern) {
                    $data['overlay_patterns'][$key]['image'] = $this->config->get('mz_store_url') . 'image/' . $pattern['image'];
                }
                
                $data['component_types'] = array(
                    array('code' => 'drawer', 'text' => $this->language->get('text_drawer')),
                    array('code' => 'popup', 'text' => $this->language->get('text_popup')),
                    array('code' => 'sticky', 'text' => $this->language->get('text_sticky'))
                );
                $data['list_open_from'] = array(
                    array('code' => 'start', 'text' => $this->language->get('text_left')),
                    array('code' => 'end', 'text' => $this->language->get('text_right')),
                    array('code' => 'top', 'text' => $this->language->get('text_top')),
                    array('code' => 'bottom', 'text' => $this->language->get('text_bottom')),
                );
                $data['list_sticky_position'] = array(
                    array('code' => 'top', 'text' => $this->language->get('text_top')),
                    array('code' => 'bottom', 'text' => $this->language->get('text_bottom'))
                );
                $data['list_popup_size'] = array(
                    array('id' => 'xl', 'name' => $this->language->get('text_xl')),
                    array('id' => 'lg', 'name' => $this->language->get('text_lg')),
                    array('id' => 'md', 'name' => $this->language->get('text_md')),
                    array('id' => 'sm', 'name' => $this->language->get('text_sm')),
                );

                $color_types = $this->model_extension_maza_asset->getColorTypes();
                
                $data['colors'] = array();
                foreach($color_types as $color_type){
                    $data['colors'][] = array('code' => $color_type, 'text' => ucfirst($color_type));
                }
                
                $data['languages'] = $this->model_localisation_language->getLanguages();
                
                $data['user_token'] = $this->session->data['user_token'];
                
                $data['languages'] = $languages;
                
		$this->response->setOutput($this->load->view('extension/maza/layout_builder/edit_component', $data));
        }
}
