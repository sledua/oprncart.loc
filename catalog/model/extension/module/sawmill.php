<?php
class ModelExtensionModuleSawmill extends Model {
    private $codename = 'sawmill';
    /**
     * Get all Vue templates
     * @return array
     */
    public function getVueTemplates()
    {
        $result = array();
        $files = glob(DIR_TEMPLATE . 'default/template/extension/' . $this->codename . '/**/*.vue', GLOB_BRACE);
        foreach ($files as $key => $file) {
            $result[] = str_replace(array(DIR_TEMPLATE.'default/template/', '.vue'), '', $file);
        }
        $files = glob(DIR_TEMPLATE . 'default/template/extension/' . $this->codename . '/**/**/*.vue', GLOB_BRACE);
        foreach ($files as $key => $file) {
            $result[] = str_replace(array(DIR_TEMPLATE.'default/template/', '.vue'), '', $file);
        }
        $files = glob(DIR_TEMPLATE . 'default/template/extension/' . $this->codename . '/**/**/**/*.vue', GLOB_BRACE);
        foreach ($files as $key => $file) {
            $result[] = str_replace(array(DIR_TEMPLATE.'default/template/', '.vue'), '', $file);
        }
        $files = glob(DIR_TEMPLATE . 'default/templatee/xtension/' . $this->codename . '/*.vue');
        foreach ($files as $key => $file) {
            $result[] = str_replace(array(DIR_TEMPLATE.'default/template/', '.vue'), '', $file);
        }
        return $result;
    }
    
    public function getProductType($product_id) {
        $query = $this->db->query("SELECT *FROM `".DB_PREFIX."sawmill_product_type` pt LEFT JOIN `".DB_PREFIX."sawmill_type` t ON pt.type_id = t.type_id WHERE `pt`.`product_id` = '".(int)$product_id."'");
        return $query->row;
    }

    public function getProductsByType($type_id) {
        $query = $this->db->query("SELECT *FROM `".DB_PREFIX."sawmill_product_type` pt LEFT JOIN `".DB_PREFIX."sawmill_type` t ON pt.type_id = t.type_id WHERE `pt`.`type_id` = '".(int)$type_id."'");
        return $query->rows;
    }

}