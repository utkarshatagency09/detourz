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
 * Create directory path recursively
 * @param string $path Description
 * @return boolean success or not
 */
function createDirPath(string $path): bool {
        if (is_dir($path)){
            return true;
        } 
        
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        
        $return = createDirPath($prev_path);
        
        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
}

/**
 * delete path recursively
 * @param string $path
 * @return boolean success or not
 */
function deletePath(string $path) {
        if(is_file($path)){
            unlink($path);
        } elseif(is_dir($path)){
            $path = (substr($path, -1) !== '/') ? $path . '/' : $path;
            $files = glob($path . '*');
            foreach ($files as $file) {
                deletePath($file);
            }
            rmdir($path);
        } else {
            return false;
        }
}

/**
 * Empty folder
 * @param string $path folder path
 */
function emptyFolder(string $path): void {
    if(is_dir($path)){
        deletePath($path);
        mkdir($path);
    }
}

/**
 * Get all files from given directory
 */
function getFiles(string $dir): array {
    $files = array();

    if(substr($dir, -1) != '/'){
        $dir .= '/';
    }
    
    if(is_dir($dir)){
        foreach(array_diff(scandir($dir), array('.', '..')) as $name){
            if(is_dir($dir . $name)){
                $files = array_merge($files, getFiles($dir . $name));
            } else {
                $files[] = $dir . $name;
            }
        }
    }

    return $files;
}