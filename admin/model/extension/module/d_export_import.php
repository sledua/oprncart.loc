<?php
/*
 *  location: admin/model
 */

class ModelExtensionModuleDExportImport extends Model {

    private $codename = 'd_export_import';

    public function checkCompleteVersion(){
        $return = false;
        if(!file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_export_import_pro.json')){
            $return = true; 
        }

        return $return;
    }

    public function getRiotTags(){
        $result = array();
        $files = glob(DIR_APPLICATION . 'view/template/extension/'.$this->codename.'/tags/*.tag', GLOB_BRACE);
        foreach($files as $file){
            $result[] = 'view/template/extension/'.$this->codename.'/tags/'.basename($file).'?'.rand();
        }
        
        return $result;
    }

    public function getModules(){
        $dir = DIR_CONFIG.$this->codename.'/*.php';
        $files = glob($dir);
        $result = array();
        foreach($files as $file){
            $fileName = basename($file, '.php');
            $setting = $this->getModuleSetting($fileName);
            if(empty($setting['opencart_version'])){
                $result[] = $fileName;
            }
            else{
                if(in_array(VERSION, $setting['opencart_version'])){
                    $result[] = $fileName;
                }
            }
        }
        return $result;
    }

    public function getModuleSetting($codename){
        $results = array();

        $file = DIR_CONFIG.$this->codename.'/'.$codename.'.php';

        if (file_exists($file)) {
            $_ = array();

            require($file);

            $results = array_merge($results, $_);
        }
        
        return $results;
    }

    public function getModuleFilters($codename){
        $setting =$this->getModuleSetting($codename);
        $results = array();

        if(!empty($setting['main_sheet']['columns'])){
            $results = $setting['main_sheet']['columns'];
        }

        $results = array_filter($results, function($value){
            return !empty($value['filter'])?true:false;
        });

        $results = array_map(function($value) use ($setting){
            if(!empty($value['concat'])){
                $table = array_filter($setting['main_sheet']['tables'] ,function($filter_value) use ($value){
                    return $filter_value['name'] == $value['table'];
                });
                if(!empty($table)){
                    $value['table'] = array_pop($table);
                }
            }
            return $value;
        }, $results);

        return $results;
    }

    public function getTabs($active){
        $dir = DIR_APPLICATION.'controller/extension/'.$this->codename.'/*.php';
        $files = glob($dir);
        $result = array();

        foreach($files as $file){
            $result[] = basename($file, '.php');
        }

        return $this->prepareTabs($result, $active);
    }

    public function prepareTabs($tabs, $active){
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/load');

        $data['tabs'] = array();
        $icons =array('excel'=> 'fa fa-file-excel-o', 'setting' => 'fa fa-cog');

        $data['text_complete_version'] = $this->language->get('text_complete_version');

        $data['notify'] = $this->checkCompleteVersion();

        foreach ($tabs as $tab) {
            $this->load->language('extension/'.$this->codename.'/'.$tab);

            if(isset($icons[$tab])){
                $icon = $icons[$tab];
            }
            else{
                $icon = 'fa fa-list';
            }

            $data['tabs'][] = array(
                'title' => $this->language->get('text_title'),
                'active' => ($tab == $active)?true:false,
                'icon' => $icon,
                'href' => $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/'.$tab)
                );
        }
        return $this->model_extension_d_opencart_patch_load->view('extension/'.$this->codename.'/partials/tabs', $data);
    }

    public function ajax($link){
        return str_replace('&amp;', '&', $link);
    }

    public function getGroupId(){
        if(VERSION >= '2.0.0.0'){
            $user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . $this->user->getId() . "'");
            $user_group_id = (int)$user_query->row['user_group_id'];
        }else{
            $user_group_id = $this->user->getGroupId();
        }

        return $user_group_id;
    }
}
?>