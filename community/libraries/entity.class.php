<?php
/**
 * 
 * @author user
 *
 */

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}


/**
 * 
 * @author user
 *
 */
class EfrontEntityException extends Exception
{
    const INVALID_ID = 2001;
    const ENTITY_NOT_EXIST = 2002;
    const INVALID_PARAMETER = 2003;
}

/**
 * 
 * @author user
 *
 */
abstract class EfrontEntity
{
    /**
     * 
     * @var string
     */
    public $entity;
    
    /**
     * 
     * @param $entity
     * @param $name
     * @return unknown_type
     */
    public function __construct($param) {
        if (!$this -> entity) {
            $this -> entity = strtolower(str_replace('Efront', '', get_class($this)));
        }
            
        if (!is_array($param)) {
	        if (!eF_checkParameter($param, 'id')) {
	            throw new EfrontEntityException(_INVALIDID.': '.$param, EfrontEntityException :: INVALID_ID);
	        }
	        $result = eF_getTableData($this -> entity, "*", "id=$param");
	        if (sizeof($result) == 0) {
	            throw new EfrontEntityException(_ENTITYNOTFOUND.': '.htmlspecialchars($param), EfrontEntityException :: ENTITY_NOT_EXIST);
	        }
	        $this -> {$this -> entity} = $result[0];
        } else {
            $this -> {$this -> entity} = $param;
        }
        
    }
    
    /**
     * 
     * @return unknown_type
     */
    public function delete() {
        eF_deleteTableData($this -> entity, "id=".$this -> {$this -> entity}['id']);
    }
    
    /**
     * 
     * @param $fields
     * @return unknown_type
     */
    public abstract static function create($fields = array());
    
    /**
     * 
     * @return unknown_type
     */
    public function persist() {
        eF_updateTableData($this -> entity, $this -> {$this -> entity}, "id=".$this -> {$this -> entity}['id']);
    }
    
    /**
     * 
     * @return unknown_type
     */
    public function activate() {
        $this -> {$this -> entity}['active'] = 1;
        $this -> persist();
    } 

    /**
     * 
     * @return unknown_type
     */
    public function deactivate() {
        $this -> {$this -> entity}['active'] = 0;
        $this -> persist();
    } 
    
    /**
     * 
     * @return unknown_type
     */
    public static function getAll($name, $returnObjects = false) {
       $result = eF_getTableData($name, "*");
       $entity = array();
       foreach ($result as $value) {
           if ($returnObjects) {
               $entity[$value['id']] = new $name($value);
           } else {
               $entity[$value['id']] = $value;
           }
       } 
       return $entity;
    }
    
    /**
     * 
     * @param $form
     * @return unknown_type
     */
    public abstract function getForm($form);
    
    /**
     * 
     * @param $form
     * @return unknown_type
     */
    public abstract function handleForm($form);
    
    
}

?>
