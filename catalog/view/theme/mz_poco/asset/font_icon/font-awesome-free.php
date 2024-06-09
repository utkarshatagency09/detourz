<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
*/

$_['name']     = 'Font Awesome 6 Free';
$_['status']    = TRUE;

// Font css files
if(maza\Registry::config('maza_cdn')){
    $_['css_file'] = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css';
} else {
    $_['css_file'] = 'catalog/view/theme/' . maza\Registry::theme('theme_code') . '/asset/font_icon/fontawesome-free/css/all.min.css';
}


$_['font_manager_css']  = 'catalog/view/theme/' . maza\Registry::theme('theme_code') . '/asset/font_icon/fontawesome-free/css/mz_font_manager.css';


// font icon class list
$_['icons'] = array();

if(file_exists(MZ_CONFIG::$DIR_THEME_ASSET . 'font_icon/fontawesome-free/mz-font-awesome-free.json')){
    $_['icons'] = json_decode(file_get_contents(MZ_CONFIG::$DIR_THEME_ASSET . 'font_icon/fontawesome-free/mz-font-awesome-free.json'), TRUE);
    
} else { // Extract font icon class list
    $_['icons'] = array();
    
    $icons_metadata = json_decode(file_get_contents(MZ_CONFIG::$DIR_THEME_ASSET . 'font_icon/fontawesome-free/metadata/icons.json'), TRUE);
    foreach ($icons_metadata as $id => $icon) {
        
        // Brand icon
        if(in_array('brands', $icon['styles'])){
            $class = 'fa-brands fa-' . $id;
        }
        // Solid icon
        elseif(in_array('solid', $icon['styles'])){
            $class = 'fa-solid fa-' . $id;
        }
        else{
            continue;
        }
        
        $_['icons'][] = array(
            'name' => $icon['label'],
            'class' => $class
        );
    }
    
    file_put_contents(MZ_CONFIG::$DIR_THEME_ASSET . 'font_icon/fontawesome-free/mz-font-awesome-free.json', json_encode($_['icons']));
}


// Style class
$_['style_class'] = array(
    array('label' => 'Rotate 90', 'class' => 'fa-rotate-90'),
    array('label' => 'Rotate 180', 'class' => 'fa-rotate-180'),
    array('label' => 'Rotate 270', 'class' => 'fa-rotate-270'),
    array('label' => 'Flip horizontal', 'class' => 'fa-flip-horizontal'),
    array('label' => 'Flip vertical', 'class' => 'fa-flip-vertical'),
    array('label' => 'Spin', 'class' => 'fa-spin'),
    array('label' => 'Beat', 'class' => 'fa-beat'),
    array('label' => 'Fade', 'class' => 'fa-fade'),
    array('label' => 'Beat fade', 'class' => 'fa-beat-fade'),
    array('label' => 'Bounce', 'class' => 'fa-bounce'),
    array('label' => 'Flip', 'class' => 'fa-flip'),
    array('label' => 'Shake', 'class' => 'fa-shake'),
);


// Generate Font manager specific css file
$font_manager_css_file = MZ_CONFIG::$DIR_THEME_ASSET . 'font_icon/fontawesome-free/css/mz_font_manager.css';

if(!file_exists($font_manager_css_file)){
    // Get font css code
    if(maza\Registry::config('maza_cdn')){
        $css_code = file_get_contents($_['css_file']);
    } else {
        $css_code = file_get_contents(substr(MZ_CONFIG::$DIR_CATALOG, 0, -strlen('catalog/')) . $_['css_file']);
    }
    

    // Add .font-list wrapper to css code
    $scss = '.icon-thumb{ ' . $css_code . ' }';

    // Load SCSS compiler
    $mz_scss = new maza\Scss();

    file_put_contents($font_manager_css_file, $mz_scss->compile($scss, FALSE, true));
}