<?php
class ControllerExtensionMazaInstallExport extends Controller {
    /**
     * Export theme setting
     */
    public function theme(): void {
        $this->load->model('extension/maza/theme');
        
        $query = $this->db->query("SELECT `code` FROM " . DB_PREFIX . "mz_theme_setting WHERE store_id = 0 AND theme_id = '" . (int)$this->mz_theme_config->get('theme_id') . "' GROUP BY `code`");
        
        $data = array();
        
        foreach($query->rows as $row){
            $data[$row['code']] = $this->model_extension_maza_theme->getSetting($this->mz_theme_config->get('theme_code'), $row['code']);
        }
        
        
        if($data){
            header('Content-Type: application/json; charset=utf-8');
            header('Content-disposition: attachment; filename="maza.theme.' . $this->mz_theme_config->get('theme_code') . '.setting.json"');
            
            echo json_encode($data);
        }
    }
    
    /**
     * Export skin setting
     */
    public function skin(): void {
        $this->load->model('extension/maza/skin');
        
        $query = $this->db->query("SELECT `code` FROM " . DB_PREFIX . "mz_skin_setting WHERE skin_id = '" . (int)$this->mz_skin_config->get('skin_id') . "' GROUP BY `code`");
        
        $data = array();
        
        foreach($query->rows as $row){
            $data[$row['code']] = $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), $row['code']);
        }
        
        
        if($data){
            header('Content-Type: application/json; charset=utf-8');
            header('Content-disposition: attachment; filename="maza.skin.' . $this->mz_skin_config->get('skin_code') . '.setting.json"');
            
            echo json_encode($data);
        }
    }

    /**
     * Export theme package
     */
    public function package(): void {
        if(!$this->user->hasPermission('modify', 'extension/maza/install/export')){
            die('You have no permission to export package');
        }

        $pkg_file = DIR_UPLOAD . 'mz_package.ocmod.zip';

        $files = [
            // Admin
            DIR_APPLICATION . 'controller/extension/maza.php',
            DIR_APPLICATION . 'controller/extension/mz_content.php',
            DIR_APPLICATION . 'controller/extension/mz_design.php',
            DIR_APPLICATION . 'controller/extension/mz_widget.php',
        ];

        $directories = [
            // Admin
            DIR_APPLICATION . 'controller/extension/maza/',
            DIR_APPLICATION . 'controller/extension/mz_content/',
            DIR_APPLICATION . 'controller/extension/mz_design/',
            DIR_APPLICATION . 'controller/extension/mz_widget/',
            DIR_APPLICATION . 'language/en-gb/extension/maza/',
            DIR_APPLICATION . 'language/en-gb/extension/mz_content/',
            DIR_APPLICATION . 'language/en-gb/extension/mz_design/',
            DIR_APPLICATION . 'language/en-gb/extension/mz_widget/',
            DIR_APPLICATION . 'model/extension/maza/',
            DIR_APPLICATION . 'view/template/extension/maza/',
            DIR_APPLICATION . 'view/template/extension/mz_content/',
            DIR_APPLICATION . 'view/template/extension/mz_design/',
            DIR_APPLICATION . 'view/template/extension/mz_widget/',
            DIR_APPLICATION . 'view/image/maza/',
            DIR_APPLICATION . 'view/javascript/maza/',
            DIR_APPLICATION . 'view/stylesheet/maza/',

            // Catalog
            DIR_CATALOG . 'controller/extension/maza/',
            DIR_CATALOG . 'controller/extension/mz_content/',
            DIR_CATALOG . 'controller/extension/mz_design/',
            DIR_CATALOG . 'controller/extension/mz_widget/',
            DIR_CATALOG . 'language/en-gb/extension/maza/',
            DIR_CATALOG . 'language/en-gb/extension/mz_content/',
            DIR_CATALOG . 'language/en-gb/extension/mz_design/',
            DIR_CATALOG . 'language/en-gb/extension/mz_widget/',
            DIR_CATALOG . 'model/extension/maza/',
            DIR_CATALOG . 'view/javascript/maza/javascript/',
            DIR_CATALOG . 'view/javascript/maza/stylesheet/',

            // Image
            DIR_IMAGE . 'catalog/maza/svg/',
            DIR_IMAGE . 'catalog/maza/theme/',

            // System
            DIR_SYSTEM . 'config/maza/',
            DIR_SYSTEM . 'library/maza/',
        ];

        // Theme template
        foreach(glob(DIR_CATALOG . 'view/theme/mz_*', GLOB_ONLYDIR) as $theme){
            $directories[] = $theme;
        }

        // Get files from all directory
        foreach($directories as $directory){
            $files = array_merge($files, maza\getFiles($directory));
        }

        // Special named files
        $globs = [
            // Admin
            DIR_APPLICATION . 'controller/extension/module/mz_*.php',
            DIR_APPLICATION . 'language/*/extension/module/mz_*.php',
            DIR_APPLICATION . 'model/extension/module/mz_*.php',
            DIR_APPLICATION . 'view/template/extension/module/mz_*.twig',

            // Catalog
            DIR_CATALOG . 'controller/extension/module/mz_*.php',
            DIR_CATALOG . 'language/*/extension/module/mz_*.php',
            DIR_CATALOG . 'model/extension/module/mz_*.php',
        ];

        foreach($globs as $glob){
            foreach(glob($glob, GLOB_BRACE) as $file){
                $files[] = $file;
            }
        }
        
        // Create backup zip
        $package_zip = new ZipArchive();
        $package_zip->open($pkg_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach($files as $file){
            $package_zip->addFile($file, 'upload' . substr($file, strlen(dirname(DIR_APPLICATION))));
        }
        
        $package_zip->close();

        header('Pragma: public');
        header('Expires: 0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="mz_package.' . $this->mz_theme_config->get('version') . '.ocmod.zip"');
        header('Content-Transfer-Encoding: binary');

        readfile($pkg_file);

        unlink($pkg_file);
    }
}
