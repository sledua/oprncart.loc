<?php
/*
 *  location: admin/model
 */

class ModelExtensionModuleSawmill extends Model {

    public function installDatabase(){

        $this->db->query("CREATE TABLE `".DB_PREFIX."sawmill_type` ( `type_id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(256) NOT NULL , `description` TEXT NOT NULL , PRIMARY KEY (`type_id`)) COLLATE='utf8_general_ci' ENGINE=MyISAM;");

        $this->db->query("CREATE TABLE `".DB_PREFIX."sawmill_product_type` (
            `product_type_id` INT(11) NOT NULL AUTO_INCREMENT,
            `product_id` INT(11) NOT NULL,
            `type_id` INT(11) NOT NULL,
            PRIMARY KEY (`product_type_id`)
        )  COLLATE='utf8_general_ci' ENGINE=MyISAM;");
        //install your own tables. never alter the core tables

    }

    public function uninstallDatabase(){

        $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."sawmill_type`");
        $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."sawmill_product_type`");
        //is your module is uninstalled, alwways remove your tables.
    }

    public function getProductType($product_id) {
        $query = $this->db->query("SELECT *FROM `".DB_PREFIX."sawmill_product_type` pt LEFT JOIN `".DB_PREFIX."sawmill_type` t ON pt.type_id = t.type_id WHERE `pt`.`product_id` = '".(int)$product_id."'");
        return $query->row;
    }
    public function editProductType($type_id, $product_id) {
        $this->db->query("DELETE FROM `".DB_PREFIX."sawmill_product_type` WHERE `product_id` = '".(int)$product_id."'");
        $this->db->query("INSERT INTO `".DB_PREFIX."sawmill_product_type` SET `product_id` = '".(int)$product_id."', `type_id`= '".(int)$type_id."'");
    }

    public function addType($data){
        $this->db->query("INSERT INTO ".DB_PREFIX."sawmill_type SET 
            name='".$data['name']."',
            description='".$this->db->escape($data['description'])."'
        ");
        $type_id = $this->db->getLastId();
        return $type_id;
    }
    /**
     * Edit type
     * @param $type_id
     * @param $data
     */
    public function editType($type_id, $data){
        $this->db->query("UPDATE ".DB_PREFIX."sawmill_type SET 
        name='".$data['name']."',
        description='".$this->db->escape($data['description'])."'
        WHERE type_id='".$type_id."'");
    }
    /**
     * Delete type
     * @param $type_id
     */
    public function deleteType($type_id){
        $this->db->query("DELETE FROM ".DB_PREFIX."sawmill_type WHERE type_id='".$type_id."'");
    }
    /**
     * Get all templates
     * @param array $data
     * @return array
     */
    public function getTypes($data=array()){
        $sql = "SELECT * FROM ".DB_PREFIX."sawmill_type  t ";

        $sort_data = array(
			'name',
			'description',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
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
        $type_data = array();
        if($query->num_rows){
            foreach ($query->rows as $row) {
                $type_data[] = array(
                    'type_id' => $row['type_id'],
                    'name' => $row['name'],
                    'description' => $row['description']
                );
            }
        }
        
        return $type_data;
    }
    /**
     * Get type by type_id
     * @param $type_id
     * @return mixed
     */
    public function getType($type_id){
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."sawmill_type t WHERE t.type_id='".$type_id."'");
        return $query->row;
    }
    /**
     * Get total types
     * @return int
     */
    public function getTotalTypes(){
        $query = $this->db->query("SELECT count(*) as total FROM ".DB_PREFIX."sawmill_type t ");
        return $query->row['total'];
    }

}
