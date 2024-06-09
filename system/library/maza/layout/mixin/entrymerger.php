<?php
namespace maza\layout\mixin;
/**
 * Merge layout entry
 */
trait EntryMerger
{
    /**
     * Shortcode data
     */
    private $shortcode_data = array();

    /**
     * Asset data
     */
    private $asset_data = array();

    /**
     * Content entry
     */
    protected function entryContent(string $code, string $raw_data, int $entry_id, string $mz_suffix){
        $content_data = array();
        parse_str(html_entity_decode($raw_data), $content_data);
        
        if ($content_data['content_status']) {
            $content_data['entry_id']  = $entry_id;
            $content_data['mz_suffix'] = $mz_suffix;

            list($entry_data, $shortcode_data, $asset_data) = $this->load->controller('extension/mz_content/' . str_replace('.', '/', $code) . '/output', $content_data);

            foreach($shortcode_data as $key => $value){
                if(isset($this->shortcode_data[$key])){
                    $this->shortcode_data[$key] = array_merge($this->shortcode_data[$key], $value);
                } else {
                    $this->shortcode_data[$key] = $value;
                }
            }
    
            $this->asset_data = array_merge($this->asset_data, $asset_data);
            
            if($entry_data){
                return '<div class="entry-content content-' . str_replace('_', '-', explode('.', $code)[1]) . '">' . $entry_data . '</div>';       
            }
        }
    }

    /**
     * Design entry
     */
    protected function entryDesign(string $code, string $raw_data, int $entry_id, string $mz_suffix){
        $design_data = array();
        parse_str(html_entity_decode($raw_data), $design_data);
        
        if ($design_data['design_status']) {
            $design_data['entry_id']  = $entry_id;
            $design_data['mz_suffix'] = $mz_suffix;

            list($entry_data, $shortcode_data, $asset_data) = $this->load->controller('extension/mz_design/' . $code . '/output', $design_data);

            foreach($shortcode_data as $key => $value){
                if(isset($this->shortcode_data[$key])){
                    $this->shortcode_data[$key] = array_merge($this->shortcode_data[$key], $value);
                } else {
                    $this->shortcode_data[$key] = $value;
                }
            }
    
            $this->asset_data = array_merge($this->asset_data, $asset_data);
            
            return '<div class="entry-design design-' . str_replace('_', '-', $code) . '">' . $entry_data . '</div>';                
        }
    }

    /**
     * Widget entry
     */
    protected function entryWidget(string $code, string $raw_data, int $entry_id, string $mz_suffix){
        $widget_data = array();
        parse_str(html_entity_decode($raw_data), $widget_data);

        $this->shortcode_data['widget'][] = array('code' => $code, 'entry_id' => $entry_id, 'data' => $widget_data, 'suffix' => $mz_suffix, 'cache' => false);

        $data  = '<div class="entry-widget widget-' . $code . '">';
        $data .= '{mz_widget.' . $code . '.' . $entry_id . $mz_suffix . '}';
        $data .= '</div>';
        
        return $data;
    }

    /**
     * Module entry
     */
    protected function entryModule(string $code, int $entry_id, string $mz_suffix){
        $this->shortcode_data['module'][] = array('code' => $code, 'entry_id' => $entry_id, 'suffix' => $mz_suffix, 'cache' => false);

        $data  = '<div class="entry-module module-' . explode('.', $code)[0] . '">';
        $data .= '{mz_module.' . $code . '.' . $entry_id . $mz_suffix . '}';
        $data .= '</div>';
        
        return $data;
    }

    /**
     * Sub layout
     */
    protected function layout_builder(array $data): string {
        list($shortcode_data, $content, $asset_data) = $this->load->controller('extension/maza/layout_builder/getLayoutData', $data);
            
        foreach($shortcode_data as $key => $value){
            if(isset($this->shortcode_data[$key])){
                $this->shortcode_data[$key] = array_merge($this->shortcode_data[$key], $value);
            } else {
                $this->shortcode_data[$key] = $value;
            }
        }

        $this->asset_data = array_merge($this->asset_data, $asset_data);
        
        return $content;
    }
    
    /**
     * Get output data of design with shortcode data
     * @param array $setting design setting
     * @return array data
     */
    final public function output(array $setting): array {
        $data[0] = $this->index($setting);
        $data[1] = $this->shortcode_data;
        $data[2] = $this->asset_data;
        
        return $data;
    }
}
