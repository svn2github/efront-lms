<?php 

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}


class EfrontCacheException
{
}

class Cache
{
	//const cacheTimeout = 60;
	
	
	public static function getCache($parameters) {
		$key = self :: encode($parameters);
		
		$result = eF_getTableData("cache", "value", "cache_key='".$key."'");
		if (sizeof($result) > 0) {
			return $result[0]['value'];
		} else {
			return false;
		}
	}

	public static function setCache($parameters, $data) {
		$key = self :: encode($parameters);
		
		if (self :: getCache($parameters)) {
			$result = eF_updateTableData("cache", array("cache_key" => $key, "value" => $data), "cache_key='$key'");
		} else {
			$result = eF_insertTableData("cache", array("cache_key" => $key, "value" => $data));
		}
		
		return $result;
	}
	
	public static function resetCache($parameters) {
		$key = self :: encode($parameters);
		
		eF_deleteTableData("cache", "cache_key='".$key."'");
	}
/*	
	public static function cleanupCache() {
		eF_deleteTableData("cache", "");
	}
*/	
	private static function encode($parameters) {
		$key = hash('sha256', $parameters);
		return $key;
	}
	
}

?>