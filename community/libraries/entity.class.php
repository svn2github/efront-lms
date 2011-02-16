<?php
/**

 * General Entity Class file

 * @author eFront

 *

 */
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * Entity exceptions

 * @author eFront

 *

 */
class EfrontEntityException extends Exception
{
    const INVALID_ID = 2001;
    const ENTITY_NOT_EXIST = 2002;
    const INVALID_PARAMETER = 2003;
}
/**

 * General Entity Class

 *

 * @author eFront

 *

 */
abstract class EfrontEntity
{
    /**

     * The entity variable

     * @var string

     */
    public $entity;
    /**

     * Instantiate entity

     *

     * @param $param The parameters to instantiate entity with

     * @since 3.6.0

     * @access public

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

     * Delete entity

     *

     * @since 3.6.0

     * @access public

     */
    public function delete() {
        eF_deleteTableData($this -> entity, "id=".$this -> {$this -> entity}['id']);
    }
    /**

     * Create entity

     *

     * @param $fields The fields to create the entity with

     * @since 3.6.0

     * @access public

     * @static

     */
    public abstract static function create($fields = array());
    /**

     * Persist entity

     *

     * @since 3.6.0

     * @access public

     */
    public function persist() {
        eF_updateTableData($this -> entity, $this -> {$this -> entity}, "id=".$this -> {$this -> entity}['id']);
    }
    /**

     * Activate entity

     *

     * @since 3.6.0

     * @access public

     */
    public function activate() {
        $this -> {$this -> entity}['active'] = 1;
        $this -> persist();
    }
    /**

     * Deactivate entity

     *

     * @since 3.6.0

     * @access public

     */
    public function deactivate() {
        $this -> {$this -> entity}['active'] = 0;
        $this -> persist();
    }
    /**

     * Archive entity (if applicable)

     *

     * @since 3.6.0

     * @access public

     */
    public function archive() {
     if (isset($this -> {$this -> entity}['archive'])) {
      $this -> {$this -> entity}['archive'] = 1;
      $this -> persist();
     }
    }
    /**

     * Export entity

     *

     * @param string $type The type of the exported data, for example 'xml' or 'csv'

     * @return string The string corresponding to the exported data

     * @since 3.6.0

     * @access public

     */
    public function export($type) {
     $result = eF_getTableData($this -> entity, "*");
     switch ($type) {
      case 'csv':
       break;
      case 'json':
       break;
      case 'xml':
      default:
       $export = "<?xml version = \"1.0\" encoding=\"UTF-8\" >";
       foreach ($result as $value) {
        unset($value['id']);
        $export .= "<entry>";
        foreach ($value as $k => $v) {
         $export .= "<$k>".htmlentities($v)."</$k>";
        }
        $export .= "</entry>";
       }
      break;
     }
     return $export;
    }
    /**

     * Import entity

     *

     * @param string $type The type of the imported data, for example 'xml' or 'csv'

     * @since 3.6.0

     * @access public

     */
    public function import($type) {
     switch ($type) {
      case 'csv':
       break;
      case 'json':
       break;
      case 'xml':
      default:
       break;
     }
    }
    public static function createDateElement($form, $elementName, $elementLabel, $options = array()) {
     $options = array_merge(array('format' => getDateFormat().' H:i',
             'minYear' => date("Y") - 4,
             'maxYear' => date("Y") + 3), $options);
     $el = $form -> createElement("date", $elementName, $elementLabel, $options);
     for ($i = 0; $i < 12; $i++) {
      //$el -> _locale['en']['months_long'][$i] = iconv(_CHARSET, 'UTF-8', strftime("%B", mktime(0, 0, 0, $i+1, 1, 2000)));
      //$el -> _locale['en']['months_short'][$i] = iconv(_CHARSET, 'UTF-8', strftime("%b", mktime(0, 0, 0, $i+1, 1, 2000)));
      $el -> _locale['en']['months_short'][$i] = $GLOBALS['_monthNames'][(int)date("m", mktime(0, 0, 0, $i+1, 1, 2000))];
     }
     for ($i = 0; $i < 7; $i++) {
      //$el -> _locale['en']['weekdays_long'][$i] = iconv(_CHARSET, 'UTF-8', strftime("%A", mktime(0, 0, 0, 1, $i+2, 2000)));
      //$el -> _locale['en']['weekdays_short'][$i] = iconv(_CHARSET, 'UTF-8', strftime("%a", mktime(0, 0, 0, 1, $i+2, 2000)));
     }
     return $el;
    }
    /**

     * Get all entity entries

     *

     * @param string $name The name of the entity

     * @param boolean $returnObjects whether to return an array of arrays or objects

     * @since 3.6.0

     * @access public

     * @static

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

     * Produce the creation form for this entity

     *

     * @param $form The initial form object

     * @since 3.6.0

     * @access public

     * @static

     */
    public abstract function getForm($form);
    /**

     * Handle the posted form values

     *

     * @param $form The form object

     * @since 3.6.0

     * @access public

     * @static

     */
    public abstract function handleForm($form);
}
