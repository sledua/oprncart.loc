<?php
/*
*  location: admin/controller
*/

include_once(DIR_SYSTEM.'library/xlsxwriter.class.php');

class ModelExtensionDExportImportExport extends Model
{
    private $codename = 'd_export_import';

    private $writer = array();

    private $module_setting = array();

    private $setting = array();

    public function export($type, $language_id, $filters = array())
    {
        if (!file_exists(DIR_CACHE.$this->codename.'/')) {
            mkdir(DIR_CACHE.$this->codename.'/', 0777);
        }

        $this->updateProgress(0, 0);

        $cache = 'ei_export';

        $this->load->model('extension/module/'.$this->codename);

        $this->module_setting = $this->{'model_extension_module_'.$this->codename}->getModuleSetting($type);

        $this->load->model('setting/setting');

        $this->setting = $this->model_setting_setting->getSetting($this->codename);

        if (empty($this->setting[$this->codename.'_setting'])) {
            $this->config->load($this->codename);
            $this->setting = $this->config->get($this->codename.'_setting');
        } else {
            $this->setting = $this->setting[$this->codename.'_setting'];
        }

        $json = array();

        set_time_limit(1800);

        $that = $this;
        set_error_handler('ModelExtensionDExportImportExport::customErrorHandler', E_ALL);

        register_shutdown_function(array('ModelExtensionDExportImportExport', 'fatal_error_shutdown_handler'));

        try {
            if (file_exists($cache)) {
                $this->session->data['ei_export_progress'] = $this->cache->get($cache);
            }

            if (!isset($this->session->data['ei_export_progress'])) {
                $this->clearFolder(DIR_CACHE.$this->codename);

                if (!empty($this->module_setting['events_export_before'])) {
                    foreach ($this->module_setting['events_export_before'] as $action) {
                        $this->load->controller($action);
                    }
                }

                $this->session->data['ei_export_progress'] = array(
                    'last_step' => 0
                    );
            }

            $count = $this->getTotal($this->module_setting['main_sheet'], $language_id, $filters);

            $last_step = $this->session->data['ei_export_progress']['last_step'];
            
            $this->writer  = new XLSXWriter();
            
            $this->setTitles($this->module_setting['main_sheet']);

            foreach ($this->module_setting['sheets'] as $value) {
                $this->setTitles($value);
            }

            $styles = array('halign' => 'left', 'valign' => 'center');

            if (!empty($this->setting['limit'])) {
                $start = $last_step*$this->setting['limit'];
                $max_count = $start+$this->setting['limit'];
            } else {
                $max_count = $count;
                $start = 0;
            }

            for ($i = $start; $i < $max_count && $i<$count; $i += $this->setting['limit_step']) {
                if (($i+$this->setting['limit_step']) <= $count) {
                    $limit = $this->setting['limit_step'];
                } else {
                    $limit = $count - $i;
                }

                $filter_data = array(
                    'limit' => $limit,
                    'start' => $i,
                    'filters' => $filters
                    );

                $main_sheet_data = $this->getData($this->module_setting['main_sheet'], $language_id, $filter_data);

                $j = 0;

                foreach ($main_sheet_data as $main_sheet_row) {
                    $column_name = $this->module_setting['main_sheet']['table']['name'].'_'.$this->module_setting['main_sheet']['table']['key'];

                    if (isset($main_sheet_row[$column_name])) {
                        $filter_data_secondary = array(
                            'filter_key' => $main_sheet_row[$column_name]
                            );
                    }

                    if (empty($this->module_setting['main_sheet']['values'])) {
                        $this->writer->writeSheetRow($this->module_setting['main_sheet']['name'], $main_sheet_row, $styles);
                    } else {
                        $values = $this->getData($this->module_setting['main_sheet']['values'], $language_id, $filter_data_secondary);

                        if(!empty($values)) {
                            foreach ($values as $key => $value) {
                                if($key == 0){
                                    $row = array_merge(array_values($main_sheet_row), array_values($value));
                                }
                                else{
                                    $row = array_merge(array_values(array_fill(0,count($main_sheet_row), '')), array_values($value));
                                }

                                $this->writer->writeSheetRow($this->module_setting['main_sheet']['name'], $row, $styles);
                            }
                        } else {
                            $this->writer->writeSheetRow($this->module_setting['main_sheet']['name'], $main_sheet_row, $styles);
                        }

                        $values = null;
                    }

                    foreach ($this->module_setting['sheets'] as $sheet_setting) {
                        $sheet_data = $this->getData($sheet_setting, $language_id, $filter_data_secondary);
                        foreach ($sheet_data as $sheet_row) {
                            if (empty($sheet_setting['values'])) {
                                $this->writer->writeSheetRow($sheet_setting['name'], $sheet_row, $styles);
                            } else {
                                $sheet_table_column = $sheet_setting['table']['name'].'_'.$sheet_setting['table']['key'];

                                if (isset($sheet_row[$sheet_table_column])) {
                                    $filter_data_values = array(
                                        'filter_key' => $sheet_row[$sheet_table_column]
                                        );
                                } else {
                                    $filter_data_values = array();
                                }

                                $values = $this->getData($sheet_setting['values'], $language_id, $filter_data_values);

                                foreach ($values as $key => $value) {
                                    if ($key == 0) {
                                        $row = array_merge(array_values($sheet_row), array_values($value));
                                    } else {
                                        $row = array_merge(array_values(array_fill(0, count($sheet_row), '')), array_values($value));
                                    }

                                    $this->writer->writeSheetRow($sheet_setting['name'], $row, $styles);
                                }
                                $values = null;
                            }
                        }

                        $sheet_data = null;
                    }

                    $j++;
                    $this->updateProgress(($i+$j), $count);
                }
                $main_sheet_data = null;
            }
            $filename = DIR_CACHE.$this->codename.'/'.$type.'_'.date("Y-m-d_H-i-s");

            if (!empty($this->setting['limit'])) {
                $filename .= '_'.$last_step*$this->setting['limit'];
            }

            $filename .= '.xlsx';

            $this->writer->writeToFile($filename);

            $last_step++;

            if (!empty($this->setting['limit'])) {
                $progress = $count ? round($last_step * $this->setting['limit'] / $count * 100, 3) : 100;
            } else {
                $progress = 100;
            }


            if ($progress >= 100) {
                unset($this->session->data['ei_export_progress']);

                if (file_exists($cache)) {
                    unlink($cache);
                }

                if (!empty($this->module_setting['events_export_after'])) {
                    foreach ($this->module_setting['events_export_after'] as $action) {
                        $this->load->controller($action);
                    }
                }

                if (file_exists(DIR_APPLICATION.'view/javascript/'.$this->codename.'/progress_info.json')) {
                    unlink(DIR_APPLICATION.'view/javascript/'.$this->codename.'/progress_info.json');
                }

                $json['success'] = true;
            } else {
                $this->session->data['ei_export_progress']['last_step'] = $last_step;

                $this->cache->set($cache, $this->session->data['ei_export_progress']);
            }
            $json['type'] = $type;
        } catch (Exception $e) {
            $errstr = $e->getMessage();
            $errline = $e->getLine();
            $errfile = $e->getFile();
            $errno = $e->getCode();
            $json['error'] = $e->getMessage();
        }

        return $json;
    }

    public function updateProgress($current, $count)
    {
        if ($count) {
            $progress_data = array(
                'progress' => $count ? round($current / $count * 100, 3) : 100,
                'current' => $current,
                'memory_usaged' => $this->getUsageMemory()
            );
        } else {
            $progress_data = $this->language->get('text_prepare_export');
        }

        if (file_exists(DIR_APPLICATION.'view/javascript/'.$this->codename.'/progress_info.json')) {
            if (is_writable(DIR_APPLICATION.'view/javascript/'.$this->codename.'/progress_info.json')) {
                file_put_contents(DIR_APPLICATION.'view/javascript/'.$this->codename.'/progress_info.json', json_encode($progress_data));
            }
        } else {
            file_put_contents(DIR_APPLICATION.'view/javascript/'.$this->codename.'/progress_info.json', json_encode($progress_data));
        }
    }

    public function setTitles($setting)
    {
        $results = array();
        $columns_width = array();

        foreach ($setting['columns'] as $value) {
            $results[] = $value['name'];
            $width = strlen($value['name'])+2;
            $width = $width<8?8:$width;
            $columns_width[] = $width;
        }

        if (!empty($setting['values']['columns'])) {
            foreach ($setting['values']['columns'] as $value) {
                $results[] = $value['name'];
                $width = strlen($value['name'])+2;
                $width = $width<8?8:$width;
                $columns_width[] = $width;
            }
        }

        $this->writer->setColumnWidths($columns_width);

        $columns_width = null;

        $header_styles = array('fill'=> '#00B050', 'color' => '#fff', 'halign' => 'left', 'valign' => 'center');

        $this->writer->writeSheetRow($setting['name'], $results, $header_styles);

        $header_data = null;
    }

    public function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024, ($i=floor(log($size, 1024)))), 2).' '.$unit[$i];
    }

    public function getUsageMemory()
    {
        return $this->convert(memory_get_peak_usage(true));
    }

    public function save($type)
    {
        $upload_tmp_dir = ini_get('upload_tmp_dir');
        if (!empty($upload_tmp_dir)) {
            $dir_tmp = $upload_tmp_dir;
        } elseif (!empty($this->request->sever['TMPDIR'])) {
            $dir_tmp = $this->request->sever['TMPDIR'];
        } else {
            $dir_tmp = DIR_CACHE.$this->codename;
        }

        $temp = tempnam($dir_tmp, 'zip');
        $zip = new ZipArchive();
        $zip->open($temp, ZipArchive::OVERWRITE);

        foreach (glob(DIR_CACHE.$this->codename."/*.xlsx") as $file) {
            $basename = basename($file);
            $zip->addFile($file, $basename);
        }

        $zip->close();

        header('Pragma: public');
        header('Expires: 0');
        header('Content-Description: File Transfer');
        header('Content-Type: mbooth/xml');
        header('Content-Disposition: attachment; filename=' . $type.'_'.date("Y-m-d_H-i-s")  . '.zip');
        header('Content-Transfer-Encoding: binary');

        readfile($temp);
        unlink($temp);

        $this->clearFolder(DIR_CACHE.$this->codename);
    }

    public function getData($setting, $language_id, $data = array())
    {
        $sql = "SELECT ";

        $implode = array();

        foreach ($setting['columns'] as $column) {
            if (!empty($column['concat'])) {
                $table = $this->getTableByName($column['table'], $setting);
                if (!empty($table)) {
                    $implode[] = "( SELECT GROUP_CONCAT(".$column['column']." SEPARATOR ',') FROM `".DB_PREFIX.$table['full_name']."` WHERE `".$table['key']."` = `".$setting['table']['name']."`.`".$setting['table']['key']."` ) as ".$column['table'].'_'.$column['column'];
                }
            } else {
                $implode[] = $column['table'].'.'.$column['column'].' as '.$column['table'].'_'.$column['column'];
            }
        }

        if (count($implode) > 0) {
            $sql .= implode(' , ', $implode);
        } else {
            $sql .= ' * ';
        }

        $sql .= " FROM `".DB_PREFIX.$setting['table']['full_name']."` ".$setting['table']['name'].' ';

        foreach ($setting['tables'] as $table) {
            if (isset($table['concat']) && $table['concat'] == '1') {
                continue;
            }

            $sql .= ' '.$table['join']."  JOIN `".DB_PREFIX.$table['full_name']."` ".$table['name'];

            $sql .= "  ON (";

            if (!empty($table['prefix']) || !empty($table['postfix'])) {
                $sql .= "CONCAT(";
            }

            if (!empty($table['prefix'])) {
                $sql .= "'" . $table['prefix'] . "' , ";
            }

            if (isset($table['related_table']) && $table['related_table'] == '1') {
                $sql .= $table['related_table'].'.'.$setting['table']["key"];
            } else {
                $sql .= $setting['table']["name"].'.'.$setting['table']["key"];
            }

            if (!empty($table['postfix'])) {
                $sql .= ", '" . $table['postfix']."'";
            }

            if (!empty($table['prefix']) || !empty($table['postfix'])) {
                $sql .= ")";
            }

            $sql .= ' = '.$table["name"].'.'.$table["key"];
            
            if (!empty($table['multi_language'])) {
                $sql .= ' AND '.$table["name"].'.language_id = '.(int)$language_id;
            }

            $sql .= ")";
        }

        $implode = array();

        if (!empty($setting['table']['multi_language'])) {
            $implode[] = $setting['table']["name"].'.language_id= '.(int)$language_id;
        }

        if (!empty($data['filter_key'])) {
            $implode[] = $setting['table']['name'].'.'.$setting['table']['related_key'].'='.$data['filter_key'];
        }

        if (!empty($data['filters'])) {
            foreach ($data['filters'] as $filter) {
                if (is_numeric($filter['value'])) {
                    $value = $filter['value'];
                } elseif ($filter['condition'] != 'LIKE') {
                    $value = $this->db->escape($filter['value']);
                } else {
                    $value = $this->db->escape($filter['value']);
                }
                if($filter['concat']) {
                    $filter_query = '';
                    if ($filter['condition'] != 'LIKE') {
                        $filter_query = str_replace(array('[[db_prefix]]','[[condition]]', '[[value]]'), array(DB_PREFIX, html_entity_decode($filter['condition'], ENT_QUOTES, 'UTF-8'), $value), $filter['column']);
                    } else {
                        $filter_query = str_replace(array('[[db_prefix]]', '[[condition]]', '[[value]]'), array(DB_PREFIX, "LIKE '%", $value."%'"), $filter['column']);
                    }
                    $implode[] = '`'.$setting['table']['name'].'`.`'.$setting['table']['key'].'` IN '.$filter_query;
                } else {
                    if ($filter['condition'] != 'LIKE') {
                        $implode[] = $filter['column']." ".html_entity_decode($filter['condition'], ENT_QUOTES, 'UTF-8') . " ".$value;
                    } else {
                        $implode[] = $filter['column']." LIKE '%".$value."%'";
                    }
                }
            }
        }
        
        if (count($implode) > 0) {
            $sql .= " WHERE ".implode(' AND ', $implode);
        }

        if (!isset($data['filter_key'])) {
            $sql .= " GROUP BY ".$setting['table']["name"].'.'.$setting['table']["key"];
        }
        if (is_array($setting['table']["key"])) {
            $sql .= " ORDER BY ".$setting['table']["name"].'.'.implode(',', $setting['table']["key"])." ASC";
        } else {
            $sql .= " ORDER BY ".$setting['table']["name"].'.'. $setting['table']["key"]." ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);

        $export_data = $query->rows;

        foreach ($export_data as $key => $value) {
            $export_data[$key] = array_map(function ($item) {
                $item = html_entity_decode($item, ENT_QUOTES, 'UTF-8');
                return $item;
            }, $value);
        }
        
        return $export_data;
    }

    public function getTotal($setting, $language_id, $filters = array())
    {
        $sql = "SELECT count(*) as total FROM `".DB_PREFIX.$setting['table']['full_name']."` ".$setting['table']['name'].' ';

        foreach ($setting['tables'] as $table) {
            if (!isset($table['concat']) && !isset($table['prefix']) && !isset($table['postfix'])) {
                $sql .= " LEFT JOIN `".DB_PREFIX.$table['full_name']."` ".$table['name']."  ON (".$setting['table']["name"].'.'.$setting['table']["key"]." = ".$table["name"].'.'.$table["key"];

                if (!empty($table['multi_language'])) {
                    $sql .= ' AND '.$table["name"].'.language_id = '.(int)$language_id;
                }

                $sql .= ")";
            }
        }

        if (!empty($filters)) {
            $implode = array();

            foreach ($filters as $filter) {

                if (is_numeric($filter['value'])) {
                    $value = $filter['value'];
                } elseif ($filter['condition'] != 'LIKE') {
                    $value = $this->db->escape($filter['value']);
                } else {
                    $value = $this->db->escape($filter['value']);
                }
                if($filter['concat']) {
                    $filter_query = '';
                    if ($filter['condition'] != 'LIKE') {
                        $filter_query = str_replace(array('[[db_prefix]]','[[condition]]', '[[value]]'), array(DB_PREFIX, html_entity_decode($filter['condition'], ENT_QUOTES, 'UTF-8'), $value), $filter['column']);
                    } else {
                        $filter_query = str_replace(array('[[db_prefix]]', '[[condition]]', '[[value]]'), array(DB_PREFIX, "LIKE '%", $value."%'"), $filter['column']);
                    }
                    $implode[] = '`'.$setting['table']['name'].'`.`'.$setting['table']['key'].'` IN '.$filter_query;
                } else {
                    if ($filter['condition'] != 'LIKE') {
                        $implode[] = $filter['column']." ".html_entity_decode($filter['condition'], ENT_QUOTES, 'UTF-8') . " ".$value;
                    } else {
                        $implode[] = $filter['column']." LIKE '%".$value."%'";
                    }
                }
            }

            if (count($implode) > 0) {
                $sql .= " WHERE ".implode(' AND ', $implode);
            }
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTableByName($name, $setting)
    {
        if ($setting['table']['name'] == $name) {
            return $setting['table'];
        }

        foreach ($setting['tables'] as $table) {
            if ($table['name'] == $name) {
                return $table;
            }
        }
        return array();
    }

    public static function customErrorHandler($errno, $errstr, $errfile, $errline)
    {
        $json = array();
        $json['error'] = $errstr;
        $json['errno'] = $errno;
        $json['errfile'] = $errfile;
        $json['errline'] = $errline;

        self::clearFolderStatic(DIR_CACHE."d_export_import");

        header('Content-Type: application/json');
        echo json_encode($json);
        exit();
    }

    public function clearFolder($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file) {
            if(is_dir("$dir/$file")) {
                self::delTree("$dir/$file");
            } else {
                unlink("$dir/$file");
            }
        }

        return true;
    }

    public static function clearFolderStatic($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file) {
            if(is_dir("$dir/$file")) {
                self::delTree("$dir/$file");
            } else {
                unlink("$dir/$file");
            }
        }

        return true;
    }

    public static function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }


    public static function fatal_error_shutdown_handler()
    {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
            self::customErrorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }
}
