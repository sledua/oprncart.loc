<?php
/*
*  location: admin/controller
*/

include_once(DIR_SYSTEM . 'library/SpreadsheetReader/SpreadsheetReader.php');

class ModelExtensionDExportImportImport extends Model
{
    private $codename = 'd_export_import';

    private $reader = array();

    private $tables = array();

    private $main_key_name = '';

    private $value_key_name = '';

    private $main_key = 0;

    private $value_key = 0;

    private $previous_main_key = 0;

    private $main_table_name = '';

    private $count_files = 0;

    private $count_sheets = 0;

    private $current_file = 0;

    private $module_setting = array();

    private $setting = array();

    public function prepare_upload_file()
    {

        $this->updateProgress(0, 0);

        if (!file_exists(DIR_CACHE . $this->codename . '/')) {
            mkdir(DIR_CACHE . $this->codename . '/', 0777);
        }

        $filename = $this->request->files['import']['name'];

        $info = pathinfo($filename);

        $ext = $info['extension'];

        if (in_array($ext, array('xlsx', 'zip'))) {
            $target = DIR_CACHE . $this->codename . '/import.' . $ext;

            move_uploaded_file($_FILES['import']['tmp_name'], $target);

            if ($ext == 'zip') {
                $zip = new ZipArchive;

                if ($zip->open($target) === true) {

                    $zip->extractTo(DIR_CACHE . $this->codename . '/');
                    $zip->close();
                    unlink($target);
                } else {

                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    public function import($type, $language_id)
    {

        $json = array();

        set_time_limit(1800);

        $this->load->language('extension/' . $this->codename . '/import');

        if (!file_exists(DIR_CACHE . $this->codename . '/')) {
            mkdir(DIR_CACHE . $this->codename . '/', 0777);
        }

        set_error_handler('ModelExtensionDExportImportImport::customErrorHandler', E_ALL & ~E_WARNING);

        register_shutdown_function(array('ModelExtensionDExportImportImport', 'fatal_error_shutdown_handler'));

        try {
            $files = glob(DIR_CACHE . $this->codename . '/*.xlsx');

            $this->load->model('extension/module/' . $this->codename);
            $this->module_setting = $this->{'model_extension_module_' . $this->codename}->getModuleSetting($type);

            $this->load->model('setting/setting');

            $this->setting = $this->model_setting_setting->getSetting($this->codename);

            if (empty($this->setting[$this->codename . '_setting'])) {
                $this->config->load($this->codename);
                $this->setting = $this->config->get($this->codename . '_setting');
            } else {
                $this->setting = $this->setting[$this->codename . '_setting'];
            }

            if (!empty($this->module_setting['events_import_before'])) {
                foreach ($this->module_setting['events_import_before'] as $action) {
                    $this->load->controller($action);
                }
            }

            if (!empty($this->setting['truncate_table'])) {
                $this->truncateTable($language_id);
            }

            $this->count_files = count($files);

            $this->count_sheets = count($this->module_setting['sheets']) + 1;

            if($files) {
                foreach ($files as $file_index => $file) {
                    $this->current_file = $file_index;

                    $this->reader = new SpreadsheetReader($this->registry, $file);
                    $this->reader->ChangeSheet(0);

                    if ($this->validateFile($type)) {
                        $this->importSheet($this->module_setting['main_sheet'], $language_id, 0, true);

                        foreach ($this->module_setting['sheets'] as $sheet_index => $sheet_setting) {
                            $this->importSheet($sheet_setting, $language_id, ($sheet_index + 1));
                        }

                        $json['success'] = $this->language->get('text_success_import');
                    } else {
                        $json['error'] = $this->language->get('error_validate');
                    }
                }
            } else {
                $json['error'] = $this->language->get('error_files_empty');
            }

            if (!empty($this->module_setting['events_import_after'])) {
                foreach ($this->module_setting['events_import_after'] as $action) {
                    $this->load->controller($action);
                }
            }

            if (file_exists(DIR_APPLICATION . 'view/javascript/' . $this->codename . '/progress_info.json')) {
                unlink(DIR_APPLICATION . 'view/javascript/' . $this->codename . '/progress_info.json');
            }

            $this->clearFolder(DIR_CACHE . $this->codename);

        } catch (Exception $e) {
            $json['error'] = $e->getMessage();
        }
        return $json;
    }

    public function importSheet($sheet_setting, $language_id, $sheet_index)
    {
        $this->main_key = null;

        if (!empty($sheet_setting['values'])) {
            $this->importSheetWithValues($sheet_setting, $language_id, $sheet_index);
            return;
        }

        $this->reader->ChangeSheet($sheet_index);

        $this->reader->next();
        $this->reader->next();

        if ($this->reader->valid()) {
            do {
                $values = $this->reader->current();
                $this->prepareTables($sheet_setting);
                if (array_filter($values)) {
                    $main_sheet = ($sheet_index == 0) ? true : false;

                    $values = $this->getColumns($sheet_setting, $values);

                    $this->setData($sheet_setting, $values, $language_id, $main_sheet);
                }

                $this->updateProgress($sheet_index, $this->reader->key());

                $this->reader->next();
            } while ($this->reader->valid());
        }
    }

    public function importSheetWithValues($sheet_setting, $language_id, $sheet_index)
    {
        $this->main_key = null;
        $this->reader->ChangeSheet($sheet_index);

        $this->reader->next();
        $this->reader->next();

        $count_main_column = count($sheet_setting['columns']);

        $main_data = array();
        $main_key = null;
        if ($this->reader->valid()) {
            do {
                $values = $this->reader->current();
                if (array_filter($values)) {
                    $this->prepareTables($sheet_setting);
                    $main_sheet = ($sheet_index == 0) ? true : false;

                    $row_values = array_slice($values, $count_main_column);
                    $main_row = array_slice($values, 0, $count_main_column);

                    if (count(array_filter($main_row)) != 0) {
                        $main_data = $main_row;
                        $main_row = $this->getColumns($sheet_setting, $main_row);
                        $this->main_key = $main_key;
                        $this->setData($sheet_setting, $main_row, $language_id, $main_sheet);
                        $main_key = $this->main_key;

                        if (count(array_filter($row_values)) != 0) {
                            $this->main_key = null;

                            $row_values = $this->prepareValues($main_data, $row_values, $sheet_setting, $main_sheet ? true : false);
                            $this->prepareTables($sheet_setting, true);
                            $this->setData($sheet_setting['values'], $row_values, $language_id, $main_sheet);
                        }
                    } else {
                        if (count(array_filter($row_values)) != 0) {
                            $row_values = $this->prepareValues($main_data, $row_values, $sheet_setting);
                            $this->prepareTables($sheet_setting, true);
                            $this->setData($sheet_setting['values'], $row_values, $language_id, $main_sheet);
                        }
                    }
                }

                $this->updateProgress($sheet_index, $this->reader->key());

                $this->reader->next();
            } while ($this->reader->valid());
        }
    }

    public function prepareValues($main_data, $values, $sheet_setting, $clear = false)
    {
        $main_columns = $this->getColumns($sheet_setting, $main_data);
        $values_columns = $this->getColumns($sheet_setting['values'], $values);

        if (isset($sheet_setting['values']['table']['related_key'])) {
            $related_key = $sheet_setting['values']['table']['related_key'];
            $related_table = $sheet_setting['values']['table']['name'];
            $values_columns[$related_table][$related_key] = $main_columns[$sheet_setting['table']['name']][$related_key];
        }

        if (isset($sheet_setting['values']['table']['require_key'])) {
            $require_key = $sheet_setting['values']['table']['require_key'];
            $related_table = $sheet_setting['values']['table']['name'];
            $values_columns[$related_table][$require_key] = $main_columns[$sheet_setting['table']['name']][$require_key];
        }

        if (isset($sheet_setting['table']['related_key'])) {
            $related_key = $sheet_setting['table']['related_key'];
            $related_table = $sheet_setting['values']['table']['name'];
            $values_columns[$related_table][$related_key] = $main_columns[$sheet_setting['table']['name']][$related_key];
        }

        if (!empty($sheet_setting['values']['tables'])) {
            foreach ($sheet_setting['values']['tables'] as $table_setting) {
                if (isset($table_setting['require_key'])) {
                    $require_key = $table_setting['require_key'];
                    $related_table = $table_setting['name'];
                    $values_columns[$related_table][$require_key] = $main_columns[$sheet_setting['table']['name']][$require_key];
                }
            }
        }

        return $values_columns;
    }

    public function validateFile()
    {
        if (!$this->validateSheet($this->module_setting['main_sheet'], 0)) {
            return false;
        }

        if (!empty($this->module_setting['sheets'])) {
            foreach ($this->module_setting['sheets'] as $sheet_index => $sheet_setting) {
                if (!$this->validateSheet($sheet_setting, ($sheet_index + 1))) {
                    return false;
                }
            }
        }

        return true;
    }

    public function validateSheet($sheet_setting, $sheet_index)
    {
        $this->reader->ChangeSheet($sheet_index);
        $this->reader->rewind();

        $header_main_sheet = $this->reader->current();

        $columns = $sheet_setting['columns'];

        if (!empty($sheet_setting['values'])) {
            $columns = array_merge($columns, $sheet_setting['values']['columns']);
        }

        if (count($columns) == count($header_main_sheet)) {
            foreach ($columns as $column_index => $column_setting) {
                if ($column_setting['name'] != $header_main_sheet[$column_index]) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    public function prepareTables($sheet_setting, $sub_values = false)
    {
        $this->tables = array();
        if ($sub_values) {
            $setting = $sheet_setting['values'];
        } else {
            $setting = $sheet_setting;
        }
        $this->tables[$setting['table']['name']] = $setting['table'];

        $this->value_key = null;
        $this->value_key_name = null;

        if ($sub_values) {
            $this->main_key_name = $setting['table']['related_key'];
            $this->value_key_name = $setting['table']['key'];
            $this->main_table_name = $setting['table']['name'];
        } elseif (isset($setting['table']['related_key'])) {
            $this->main_key_name = $setting['table']['related_key'];
            $this->main_table_name = $setting['table']['name'];
        } else {
            $this->main_key_name = $setting['table']['key'];
            $this->main_table_name = $setting['table']['name'];
        }

        if (!empty($setting['tables'])) {
            foreach ($setting['tables'] as $table_setting) {
                $this->tables[$table_setting['name']] = $table_setting;
            }
        }
    }

    public function getColumns($sheet_setting, $values)
    {
        $table_data = array();

        $values = array_map(function ($item) {
            return htmlentities($item, ENT_QUOTES, 'UTF-8');
        }, $values);

        foreach ($values as $column_index => $column_value) {
            if ($column_index == count($sheet_setting['columns'])) {
                break;
            }
            $table_name = $sheet_setting['columns'][$column_index]['table'];
            $column_name = $sheet_setting['columns'][$column_index]['column'];
            $table_data[$table_name][$column_name] = $column_value;
        }

        return $table_data;
    }

    public function setData($sheet_setting, $table_data, $language_id, $main = false)
    {
        if (!$main) {
            $this->previous_main_key = $this->main_key;
        }

        $this->main_key = $table_data[$this->main_table_name][$this->main_key_name];

        if (!empty($this->value_key_name) && isset($table_data[$sheet_setting['table']['name']][$this->value_key_name])) {
            $this->value_key = $table_data[$sheet_setting['table']['name']][$this->value_key_name];
        } else {
            $this->value_key = null;
            $this->value_key_name = null;
        }

        foreach ($table_data as $table_name => $columns) {
            $table_setting = $this->tables[$table_name];

            if (!isset($table_setting['concat']) || (isset($table_setting['concat']) && $table_setting['concat'] != '1')) {
                $status = $this->checkIsset($main, $table_setting, $language_id);

                if ($status) {
                    $sql = "UPDATE `" . DB_PREFIX . $this->tables[$table_name]['full_name'] . "` SET ";

                    $implode = array();

                    if (!empty($this->value_key_name)) {
                        $main_key_name = $this->value_key_name;
                    } elseif (isset($table_setting['related_key'])) {
                        $main_key_name = $table_setting['related_key'];
                    } else {
                        $main_key_name = $this->main_key_name;
                    }

                    if (!empty($this->value_key)) {
                        $main_key = $this->value_key;
                    } else {
                        $main_key = $this->main_key;
                    }

                    foreach ($columns as $column_name => $column_value) {
                        if ($column_name == $main_key_name) {
                            continue;
                        }

                        $implode[] = "`" . $column_name . "` = '" . $this->db->escape($column_value) . "'";
                    }

                    if (count($implode) > 0) {
                        $sql .= implode(' , ', $implode) . " WHERE `" . $main_key_name . "` = '";

                        if (!empty($table_setting['prefix'])) {
                            $sql .= $table_setting['prefix'];
                        }
                        $sql .= $main_key;

                        if (!empty($table_setting['postfix'])) {
                            $sql .= $table_setting['postfix'];
                        }

                        $sql .= "'";

                        if (isset($table_setting['multi_language']) && $table_setting['multi_language'] == '1') {
                            $sql .= ' AND `language_id` = ' . (int)$language_id;
                        }

                        $this->db->query($sql);
                    }
                } else {
                    $sql = "INSERT INTO `" . DB_PREFIX . $table_setting['full_name'] . "` SET ";

                    $implode = array();

                    if (!empty($this->value_key_name)) {
                        $main_key_name = $this->value_key_name;
                    } elseif (isset($table_setting['related_key'])) {
                        $main_key_name = $table_setting['related_key'];
                    } else {
                        $main_key_name = $this->main_key_name;
                    }

                    if (!empty($this->value_key)) {
                        $main_key = $this->value_key;
                    } else {
                        $main_key = $this->main_key;
                    }

                    foreach ($columns as $column_name => $column_value) {
                        if (!empty($table_setting['not_empty']) && empty($column_value)) {
                            continue;
                        }
                        if ($column_name != $main_key_name) {
                            $implode[] = "`" . $column_name . "` = '" . $this->db->escape($column_value) . "'";
                        }
                    }

                    $implode[] = "`" . $main_key_name . "` = '" . (isset($table_setting['prefix']) ? $table_setting['prefix'] : '') . $main_key . (isset($table_setting['postfix']) ? $table_setting['postfix'] : '') . "'";

                    if (count($implode) > 1) {
                        $sql .= implode(' , ', $implode);

                        if (isset($table_setting['multi_language']) && $table_setting['multi_language'] == '1') {
                            $sql .= ', `language_id` = ' . (int)$language_id;
                        }

                        $this->db->query($sql);
                    }
                }
            } else {
                $sql = " DELETE FROM `" . DB_PREFIX . $table_setting['full_name'] . "` WHERE `" . $this->main_key_name . "` = " . $this->main_key;

                $this->db->query($sql);

                $rows = array();

                foreach ($columns as $column_name => $column_value) {
                    if (empty($column_value) && !strlen($column_value)) {
                        continue;
                    }
                    $explode = explode(',', $column_value);

                    if (!empty($rows) && count($explode) != count($rows)) {
                        continue;
                    }

                    foreach ($explode as $row_index => $row) {
                        if (!isset($rows[$row_index])) {
                            $rows[$row_index] = array();
                        }

                        $rows[$row_index][$column_name] = $row;
                    }
                }

                foreach ($rows as $columns) {
                    $sql = "INSERT INTO `" . DB_PREFIX . $this->tables[$table_name]['full_name'] . "` SET ";

                    $implode = array();

                    foreach ($columns as $column_name => $column_value) {
                        $implode[] = "`" . $column_name . "` = '" . $this->db->escape($column_value) . "'";
                    }

                    $implode[] = "`" . $this->main_key_name . "` = '" . $this->main_key . "'";

                    if (count($implode) > 1) {
                        $sql .= implode(' , ', $implode);
                        $this->db->query($sql);
                    }
                }
            }
        }
    }

    public function checkIsset($main, $table_setting, $language_id)
    {
        if ($main) {
            if (!empty($this->value_key_name)) {
                $main_key_name = $this->value_key_name;
            } elseif (isset($table_setting['related_key'])) {
                $main_key_name = $table_setting['related_key'];
            } else {
                $main_key_name = $this->main_key_name;
            }

            if (!empty($this->value_key)) {
                $main_key = $this->value_key;
            } else {
                $main_key = $this->main_key;
            }

            if (!empty($table_setting['clear'])) {
                $sql = sprintf("DELETE FROM `" . DB_PREFIX . "%s` WHERE %s = '%s%s%s'", $table_setting['full_name'], $main_key_name, isset($table_setting['prefix']) ? $table_setting['prefix'] : '', $this->main_key, isset($table_setting['postfix']) ? $table_setting['postfix'] : '');
                if (isset($table_setting['multi_language']) && $table_setting['multi_language'] == '1') {
                    $sql .= ' AND `language_id` = ' . (int)$language_id;
                }
                $this->db->query($sql);
                return false;
            }

            if (!empty($table_setting['prefix']) || !empty($table_setting['postfix'])) {
                $sql = sprintf("SELECT * FROM `" . DB_PREFIX . "%s` %s WHERE %s.%s = '%s%s%s'", $table_setting['full_name'], $table_setting['name'], $table_setting['name'], $main_key_name, isset($table_setting['prefix']) ? $table_setting['prefix'] : '', $main_key, isset($table_setting['postfix']) ? $table_setting['postfix'] : '');
            } else {
                $sql = sprintf("SELECT * FROM `" . DB_PREFIX . "%s` %s WHERE %s.%s = '%s'", $table_setting['full_name'], $table_setting['name'], $table_setting['name'], $main_key_name, $main_key);
            }

            if (isset($table_setting['multi_language']) && $table_setting['multi_language'] == '1') {
                $sql .= ' AND ' . $table_setting['name'] . '.language_id = ' . (int)$language_id;
            }

            $query = $this->db->query($sql);
            if ($query->num_rows) {
                return true;
            }
        } else {
            if ($this->previous_main_key != $this->main_key) {
                $sql = "DELETE FROM `" . DB_PREFIX . $table_setting['full_name'] . "` WHERE `" . $this->main_key_name . "` = " . $this->main_key;

                if (isset($table_setting['multi_language']) && $table_setting['multi_language'] == '1') {
                    $sql .= ' AND `language_id` = ' . (int)$language_id;
                }

                $this->db->query($sql);
            }
        }

        return false;
    }

    public function getCountInCurrentSheet()
    {
        $count = 0;
        while ($this->reader->next()) {
            $count++;
            $progress_data = array(
                'progress' => $count,
                'memory_usaged' => $this->getUsageMemory()
            );
            file_put_contents(DIR_APPLICATION . 'view/javascript/' . $this->codename . '/progress_info.json', json_encode($progress_data));
        }
        return $count;
    }

    public function convert($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    public function getUsageMemory()
    {
        return $this->convert(memory_get_peak_usage(true));
    }

    public function updateProgress($current_sheet, $current_item)
    {
        if ($this->count_files) {
            $progress = $this->current_file / $this->count_files;

            $progress += (1 / $this->count_files) * ($current_sheet / $this->count_sheets);

            $progress_data = array(
                'progress' => round($progress * 100, 3),
                'current_file' => $this->current_file,
                'count_files' => $this->count_files,
                'current_sheet' => $current_sheet,
                'count_sheets' => $this->count_sheets,
                'current' => $current_item,
                'memory_usaged' => $this->getUsageMemory()
            );
        } else {
            $progress_data = $this->language->get('text_prepare_import');
        }

        try {
            if (file_exists(DIR_APPLICATION . 'view/javascript/' . $this->codename . '/progress_info.json')) {
                if (is_writable(DIR_APPLICATION . 'view/javascript/' . $this->codename . '/progress_info.json')) {
                    file_put_contents(DIR_APPLICATION . 'view/javascript/' . $this->codename . '/progress_info.json', json_encode($progress_data));
                }
            } else {
                file_put_contents(DIR_APPLICATION . 'view/javascript/' . $this->codename . '/progress_info.json', json_encode($progress_data));
            }
        } catch (Extension $e) {
        }
    }

    public static function customErrorHandler($errno, $errstr, $errfile, $errline)
    {
        $json = array();
        $json['error'] = $errstr . ' ' . $errfile . ' ' . $errline;

        self::clearFolderStatic(DIR_CACHE . "d_export_import");

        header('Content-Type: application/json');
        echo json_encode($json);
        exit();
    }


    public static function fatal_error_shutdown_handler()
    {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
            ModelExtensionDExportImportImport::customErrorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
            exit();
        }
    }

    public function truncateTable($language_id)
    {
        $tables = array();

        $multi_language_tables = array();

        $tables[] = $this->module_setting['main_sheet']['table']['full_name'];

        if (!empty($this->module_setting['main_sheet']['tables'])) {
            foreach ($this->module_setting['main_sheet']['tables'] as $table_setting) {
                if (!empty($table_setting['multi_language']) && !isset($table_setting['prefix']) && !isset($table_setting['postfix'])) {
                    $multi_language_tables[] = $table_setting['full_name'];
                } elseif (!empty($table_setting['prefix']) || !empty($table_setting['postfix'])) {
                    $this->truncateTableWithPrefix($table_setting, $language_id);
                } else {
                    $tables[] = $table_setting['full_name'];
                }
            }
        }

        if (!empty($this->module_setting['main_sheet']['values']['tables'])) {
            foreach ($this->module_setting['main_sheet']['values']['tables'] as $table_setting) {
                if (!empty($table_setting['multi_language']) && !isset($table_setting['prefix']) && !isset($table_setting['postfix'])) {
                    $multi_language_tables[] = $table_setting['full_name'];
                } elseif (!empty($table_setting['prefix']) || !empty($table_setting['postfix'])) {
                    $this->truncateTableWithPrefix($table_setting, $language_id);
                } else {
                    $tables[] = $table_setting['full_name'];
                }
            }
        }
        if (!empty($this->module_setting['sheets'])) {
            foreach ($this->module_setting['sheets'] as $sheet_setting) {

                if (!empty($sheet_setting['table']['multi_language']) && !isset($sheet_setting['table']['prefix']) && !isset($sheet_setting['table']['postfix'])) {
                    $multi_language_tables[] = $sheet_setting['table']['full_name'];
                } elseif (!empty($sheet_setting['table']['prefix']) || !empty($sheet_setting['table']['postfix'])) {
                    $this->truncateTableWithPrefix($sheet_setting['table'], $language_id);
                } else {
                    $tables[] = $sheet_setting['table']['full_name'];
                }
                if (!empty($sheet_setting['tables'])) {
                    foreach ($sheet_setting['tables'] as $table_setting) {
                        if (!empty($table_setting['multi_language']) && !isset($table_setting['prefix']) && !isset($table_setting['postfix'])) {
                            $multi_language_tables[] = $table_setting['full_name'];
                        } elseif (!empty($table_setting['prefix']) || !empty($table_setting['postfix'])) {
                            $this->truncateTableWithPrefix($table_setting, $language_id);
                        } else {
                            $tables[] = $table_setting['full_name'];
                        }
                    }
                }

                if (!empty($sheet_setting['values']['tables'])) {
                    foreach ($sheet_setting['values']['tables'] as $table_setting) {
                        if (!empty($table_setting['multi_language']) && !isset($table_setting['prefix']) && !isset($table_setting['postfix'])) {
                            $multi_language_tables[] = $table_setting['full_name'];
                        } elseif (!empty($table_setting['prefix']) || !empty($table_setting['postfix'])) {
                            $this->truncateTableWithPrefix($table_setting, $language_id);
                        } else {
                            $tables[] = $table_setting['full_name'];
                        }
                    }
                }
            }
        }

        if (!empty($tables)) {
            foreach ($tables as $table) {
                $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . $table . "`");
            }
        }

        if (!empty($multi_language_tables)) {
            foreach ($multi_language_tables as $table) {
                $this->db->query("DELETE FROM `" . DB_PREFIX . $table . "` WHERE `language_id` = '" . (int)$language_id . "'");
            }
        }
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

    public static function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    protected function truncateTableWithPrefix($table_setting, $language_id)
    {
        if (!empty($table_setting['prefix'])) {
            $prefix = $table_setting['prefix'];
        } else {
            $prefix = '';
        }

        if (!empty($table_setting['postfix'])) {
            $postfix = $table_setting['postfix'];
        } else {
            $postfix = '';
        }

        $sql = "DELETE FROM `" . DB_PREFIX . $table_setting['full_name'] . "` WHERE `" . $table_setting['related_key'] . "` LIKE '" . $prefix . '%' . $postfix . "'";

        if (!empty($table_setting['multi_language'])) {
            $sql .= " AND `language_id` = '" . (int)$language_id . "'";
        }

        $this->db->query($sql);
    }
}
