<?php
namespace maza;

/**
* Function to get data of active language
* If data if empty for active language then fall back to find in default language
* @param array $array language array
* @return mixed value of language, null if all empty
*/
function getOfLanguage($array){
    if(!is_array($array)){
        return '';
    }
    if(!empty($array[Registry::config('config_language_id')])){
        return $array[Registry::config('config_language_id')];
    }
    if(!empty($array['1'])){
        return $array['1'];
    }
    if($array){
        return array_values($array)[0];
    }

    return '';
}