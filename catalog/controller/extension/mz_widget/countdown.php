<?php
class ControllerExtensionMzWidgetCountdown extends maza\layout\Widget {
    private static $instance_count = 0;

    public function index(array $setting) {
        $data = array();
        
        $data['title']      = maza\getOfLanguage($setting['widget_title']);
        
        $data['direction']  = $setting['widget_direction'];
        $data['format']     = $setting['widget_format'];
        $data['compact']    = $setting['widget_compact'];
        
        $time_start = new DateTime($setting['widget_timestart']);
        $time_end = new DateTime($setting['widget_timeend']);
        
        $data['until']  = $time_end->format('c');
        
        $data['mz_suffix'] = $setting['mz_suffix']??self::$instance_count++;
        
        if($time_start <= (new Datetime())){
            return $this->load->view('extension/mz_widget/countdown', $data);
        }
	}

    /**
     * Change default setting
     */
    public function getSettings(): array{
        $setting = array();
        
        $setting['widget_cache'] = 'hard';
        
        $setting['xl'] = $setting['lg'] = $setting['md'] = 
        $setting['sm'] = $setting['xs'] = array(
            'widget_flex_grow' => 0,
        );
        
        return \maza\array_merge_subsequence(parent::getSettings(), $setting);
    }
}
