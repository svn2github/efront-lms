<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

define("_CACHEENTRYNOTFOUND", "Cache entry not found");
define("_CACHEENTRYEXPIRED", "Cache entry expired");
define("_CACHEENTRYINVALID", "Cache entry is invalid");

class Cache
{
 public static $cacheTimeout = 604800; //3600*24*7, 1 week	

 public static function getCache($parameters) {
  $key = self :: encode($parameters);

  $result = eF_getTableData("cache", "value, timestamp", "cache_key='".$key."'");
  if (sizeof($result) > 0 || time() - $result['timestamp'] <= self :: $cacheTimeout) {
   return $result[0]['value'];
  } else {
   return false;
  }
 }

 public static function setCache($parameters, $data) {
  $key = self :: encode($parameters);
  $values = array("cache_key" => $key, "value" => $data, "timestamp" => time());

  if (self :: getCache($parameters)) {
   $result = eF_updateTableData("cache", $values, "cache_key='$key'");
  } else {
   $result = eF_insertTableData("cache", $values);
  }

  return $result;
 }

 public static function resetCache($parameters) {
  $key = self :: encode($parameters);

  eF_deleteTableData("cache", "cache_key='".$key."'");
 }


 private static function encode($parameters) {
  $key = hash('sha256', $parameters);
  return $key;
 }

}

class EfrontCacheException extends Exception
{
    const KEY_NOT_FOUND = 1401;
    const KEY_EXPIRED = 1402;
    const ENTRY_INVALID = 1403;
}

abstract class EfrontCache
{
    public $cacheTimeout = 604800; //3600*24*7, 1 week

    public abstract function setCache($key, $entity, $timeout);
    public abstract function getCache($key);
    public abstract function deleteCache($key);
}

class CacheFactory
{
    public static function factory() {
        switch ($GLOBALS['configuration']['cache_method']) {
            case 'apc': $cache = new EfrontCacheAPC(); break;
            case 'memcache': $cache = new EfrontCacheMemcache(); break;
            case 'db':
            default: $cache = new EfrontCacheDB(); break;
        }
    }
}

class EfrontCacheDB extends EfrontCache
{
    //public $keys = array()

    public function setCache($key, $entity, $timeout) {

  $values = array("cache_key" => $key, "value" => serialize($entity), "timestamp" => time());

  if ($this -> get($parameters)) {
   $result = eF_updateTableData("cache", $values, "cache_key='$key'");
  } else {
   $result = eF_insertTableData("cache", $values);
  }

  return $result;

    }

    public function deleteCache($key) {
        eF_deleteTableData("cache", "cache_key='".$key."'");
    }

    public function getCache($key) {
  $result = eF_getTableData("cache", "value, timestamp", "cache_key='".$key."'");
  if (sizeof($result) > 0 || time() - $result['timestamp'] <= $this -> cacheTimeout) {
   if ($result[0]['value'] !== serialize(false)) {
       $result[0]['value'] = unserialize($result[0]['value']);
       if ($result[0]['value'] !== false) {
           return $result[0]['value'];
       } else {
           $this -> delete($key);
           throw new EfrontCacheException(_CACHEENTRYINVALID, EfrontCacheException::ENTRY_INVALID);
       }
   } else {
       return false; //This means that the serialized value was "false" 
   }
  } elseif (time() - $result['timestamp'] <= $this -> cacheTimeout) {
      $this -> delete($key);
      throw new EfrontCacheException(_CACHEENTRYEXPIRED, EfrontCacheException::KEY_EXPIRED);
  } else {
      throw new EfrontCacheException(_CACHEENTRYNOTFOUND, EfrontCacheException::KEY_NOT_FOUND);
  }
    }

    public function deleteCacheBasedOnKeyFilter($filter) {
        //eF_deleteTableData("cache")
    }
}
/*

class EfrontCacheAPC implements iCache

{

    public function __construct($method) {

        

    }

    

    public function setCache($key, $entity, $timeout) {

        

    }

    

    public function deleteCache($key) {

        

    }

    

    public function getCache($key) {

        

    }

    

}

class EfrontCacheMemcache implements iCache

{

    public function __construct($method) {

        

    }

    

    public function setCache($key, $entity, $timeout) {

        

    }

    

    public function deleteCache($key) {

        

    }

    

    public function getCache($key) {

        

    }

    

}

*/
?>
