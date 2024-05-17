<?php
class ControllerExtensionMazahooksData extends Controller {
    private static $data = array();

    /**
     * Fetch and cache data from database
     */
    public function index(array $catalog_data): void {
        $this->load->model('extension/maza/extension');

        // Data
        foreach($catalog_data as $value){
            $info = array(
                'data_id' => $value['data_id'],
                'title' => maza\getOfLanguage($value['setting']['title']),
                'icon_font' => maza\getOfLanguage($value['setting']['icon_font']),
                'icon_size' => $value['setting']['icon_size'],
                'sort_order' => $value['sort_order'],
            );

            // svg image
            $icon_svg = maza\getOfLanguage($value['setting']['icon_svg']);
            if($icon_svg && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg)){
                $info['icon_svg'] = $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $icon_svg);
            } else {
                $info['icon_svg'] = false;
            }

            $image_width = $value['setting']['icon_width'];
            $image_height = $value['setting']['icon_height'];

            // Image
            $icon_image = maza\getOfLanguage($value['setting']['icon_image']);
            if($icon_image && is_file(DIR_IMAGE . $icon_image)){
                list($image_width, $image_height) = $this->model_extension_maza_image->getEstimatedSize($icon_image, $image_width, $image_height);
                $info['icon_image'] = $this->model_tool_image->resize($icon_image, $image_width, $image_height);
            } else {
                $info['icon_image'] = false;
            }

            $info['image_width'] = $image_width;
            $info['image_height'] = $image_height;

            $suffix = 'data' . $value['data_id'];

            // HTML
            if($value['setting']['value_type'] == 'html'){
                $info['content'] = html_entity_decode(maza\getOfLanguage($value['setting']['value_html']));
            }

            // Module
            else if($value['setting']['value_type'] == 'module' && $value['setting']['value_module']){
                $info['content'] = $this->model_extension_maza_extension->getModuleOutput($value['setting']['value_module'], $suffix);
            }

            // Widget
            else if($value['setting']['value_type'] == 'widget' && $value['setting']['value_widget_code'] && $value['setting']['value_widget_data']){
                $info['content'] = $this->model_extension_maza_extension->getWidgetOutput($value['setting']['value_widget_code'], $value['setting']['value_widget_data']);
            }

            // Content builder
            else if($value['setting']['value_type'] == 'content_builder'){
                $info['content'] = $this->load->controller('extension/maza/layout_builder', ['group' => 'content_builder', 'group_owner' => $value['setting']['value_content_builder_id'], 'suffix' => $suffix]);
            }
            
            // Popup
            if($value['hook'] == 'popup'){
                $info['unique_id'] = $value['setting']['popup_unique_id'];
                $info['size'] = $value['setting']['popup_size'];
                $info['close_button'] = $value['setting']['popup_close_button'];
                $info['auto_start_status'] = $value['setting']['popup_auto_start_status'];
                $info['auto_start_delay'] = $value['setting']['popup_auto_start_delay'];
                $info['auto_close_status'] = $value['setting']['popup_auto_close_status'];
                $info['auto_close_delay'] = $value['setting']['popup_auto_close_delay'];
                $info['do_not_show_again'] = $value['setting']['popup_do_not_show_again'];
            }

            self::$data[$value['hook']][] = $info;
        }
    }

    /**
     * tabs hook callback
     */
    public function tab(): array {
        return self::$data['tab']??[];
    }

    /**
     * accordion hook callback
     */
    public function accordion(): array {
        return self::$data['accordion']??[];
    }

    /**
     * faq hook callback
     */
    public function faq(): array {
        return self::$data['faq']??[];
    }

    /**
     * popup hook callback
     */
    public function popup(): array {
        return self::$data['popup']??[];
    }
}