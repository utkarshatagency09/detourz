<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
*/
namespace maza;
/**
 * Merge two or more array and their sub sequence array
 * @param array $arrs array list to merge
 * @return array merged array
 */
function array_merge_subsequence(...$arrs){
    $data = array();
    
    foreach ($arrs as $arr) {
        foreach ($arr as $key => $value) {
            // merge array by array in case of both value is array
            if(isset($data[$key]) && is_array($data[$key]) && is_array($value)){
                $data[$key] = array_merge_subsequence($data[$key], $value);
            } elseif(is_int($key)){
                $data[] = $value;
            } else {
                $data[$key] = $value;
            }
        }
    }
    
    return $data;
}

//function parse_components($data){
//    $twig = new \Twig_Environment(new \Twig_Loader_String(), array('autoescape' => false));
//    Registry::get('mz_cache')->setVar('page_component', $twig->render(Registry::get('mz_cache')->getVar('page_component'), $data));
//}