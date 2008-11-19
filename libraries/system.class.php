<?php
/**
* Efront System Classes file
*
* @package eFront
* @version 3.5.0
*/


class EfrontException extends Exception
{
	
	public function toHTML() {
		
	}
}

class EfrontSystemException extends Exception
{
	const INCOMPATIBLE_VERSIONS = 10; 
	const ILLEGAL_CSV      = 11;
}

/**
 * System class
 * 
 * This class incorporates system-wise static functions
 *
 * @since 3.5.0
 * @package eFront
 */
class EfrontSystem 
{
	/**
	 * Backup system
	 * 
	 * This function is used to backup the system. There are 2 types of backup, database only and full.
	 * In the first case (the default), only the database is backed up, while in the second case, files 
	 * are backed up as well.
	 * <br/>Example:
	 * <code>
	 * $backupFile = EfrontSystem :: backup('13_3_2007');			//Backup database only 
	 * </code>
	 *
	 * @param string $backupName The name of the backup 
	 * @param int $backupType Can be either 0 or 1, where 0 siginifies database only backup and 1 is for including backup files as well
	 * @return EfrontFile The compressed file of the backup
	 * @since 3.5.0
	 * @access public
	 */
	public static function backup($backupName, $backupType = 0) {
		$tempDir     = G_BACKUPPATH.'temp/';
		if (is_dir($tempDir)) {
		    $dir = new EfrontDirectory($tempDir);
		    $dir -> delete();
		}
		mkdir($tempDir, 0755);
	    mkdir($tempDir.'db_backup', 0755);
	    $directory = new EfrontDirectory($tempDir);
	    
	    $tables    = $GLOBALS['db'] -> GetCol("show tables");                                              //Get the database tables
		
        foreach ($tables as $table) {
            $data   = eF_getTableData($table, "count(*)");
            $unfold = 2000;
            $limit  = ceil($data[0]['count(*)'] / $unfold);
            for ($i = 0; $i < $limit; $i++) {
                $data = eF_getTableData($table, "*", "", "'' limit $unfold offset ".($i*$unfold));
        
                file_put_contents($tempDir.'db_backup/'.$table.'.'.$i, serialize($data), FILE_APPEND);
            }
            $result       = eF_ExecuteNew("show create table $table");
            $temp         = $result -> GetAll();
            $definition[] = "drop table ".$temp[0]['Table'];
            $definition[] = $temp[0]['Create Table'];
        }   

/*	    
		foreach ($tables as $table) {
			$data = eF_getTableData($table);
			file_put_contents($tempDir.'db_backup/'.$table, serialize($data));
			$result       = eF_ExecuteNew("show create table $table");
			$temp         = $result -> GetAll();
			$definition[] = "drop table ".$temp[0]['Table'];
			$definition[] = $temp[0]['Create Table'];
		}
*/
		file_put_contents($tempDir.'db_backup/sql.txt', implode(";\n", $definition));
		file_put_contents($tempDir.'db_backup/version.txt', G_VERSION_NUM);

		if ($backupType == 1) {
			$lessonsDir = new EfrontDirectory(G_LESSONSPATH);
			$lessonsDir -> copy($tempDir.'lessons');
			$uploadsDir = new EfrontDirectory(G_UPLOADPATH);
			$uploadsDir -> copy($tempDir.'upload');
		}
		$compressedFile = $directory -> compress($backupName, false);
		$directory -> delete();
		
		return $compressedFile;
	}
	
	/**
	 * Restore system
	 * 
	 * This function is used to restore a backup previously taken
	 * <br/>Example:
	 * <code>
	 * </code> 
	 *
	 * @param unknown_type $restoreFile
	 */
	public static function restore($restoreFile) {
		if (!($restoreFile instanceof EfrontFile)) {
			$restoreFile = new EfrontFile($restoreFile);
		}

		$tempDir     = G_BACKUPPATH.'temp/';
		if (is_dir($tempDir)) {
		    $dir = new EfrontDirectory($tempDir);
		    $dir -> delete();
		}
		mkdir($tempDir, 0755);

		$restoreFile  = $restoreFile -> copy($tempDir.'/');
		$restoreFile -> uncompress(false);

		$filesystem  = new FileSystemTree($tempDir);

    	$iterator    = new EfrontFileOnlyFilterIterator(new RecursiveIteratorIterator($filesystem -> tree, RecursiveIteratorIterator :: SELF_FIRST));
		foreach ($iterator as $key => $value) {
			if (strpos($key, 'version.txt') !== false) {
				$backupVersion = file_get_contents($key);
			}
		}
		if (version_compare($backupVersion, G_VERSION_NUM) != 0) {
			throw new Exception (_INCOMPATIBLEVERSIONS.'<br/> '._BACKUPVERSION.':'.$backupVersion.' / '._CURRENTVERSION.': '.G_VERSION_NUM, EfrontSystemException::INCOMPATIBLE_VERSIONS);
		}

		$sql  = file_get_contents($tempDir.'db_backup/sql.txt');
		$sql  = explode(";", $sql);
		$node = $filesystem -> seekNode($tempDir.'db_backup');
		
		for ($i = 0; $i < sizeof($sql); $i+=2) {
		    preg_match("/drop table (.+)/", $sql[$i], $matches);
		    if ($matches[1]) {
		        $temp[$matches[1]] = array($sql[$i], $sql[$i + 1]);
		    }
			//eF_executeNew($query);
		}
		$sql = $temp;

		$iterator = new EfrontFileOnlyFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($node), RecursiveIteratorIterator :: SELF_FIRST));
		foreach ($iterator as $file => $value) {
		    $tableName = preg_replace("/\.\d+/", "", basename($file));
		    if (isset($sql[$tableName])) {
		        eF_executeNew($sql[$tableName][0]);
		        eF_executeNew($sql[$tableName][1]);
		        unset($sql[$tableName]);
		    }
		    if (strpos($file, 'sql.txt') === false && strpos($file, 'version.txt') === false) {	
		        $data = unserialize(file_get_contents($file));
		        eF_insertTableDataMultiple($tableName, $data);
		    }
		}

		if (is_dir(G_BACKUPPATH.'temp/upload')) {
		    $dir = new EfrontDirectory(G_BACKUPPATH.'temp/upload');
		    $dir -> copy(G_ROOTPATH.'upload', true);		    
		}
		if (is_dir(G_BACKUPPATH.'temp/lessons')) {
		    $dir = new EfrontDirectory(G_BACKUPPATH.'temp/lessons');
		    $dir -> copy(G_CONTENTPATH.'lessons', true);		    
		}

		$dir = new EfrontDirectory($tempDir);
		$dir -> delete();

		return true;
	}
	
	/**
	 * Import users
	 * 
	 * This function is used to import users from the given CSV
	 * file.
	 * <br/>Example:
	 * <code>
	 * $file = new EfrontFile(/var/www/efront/upload/admin/temp/users.csv);
	 * EfrontSystem :: importUsers($file);
	 * </code>
	 *
	 * @param mixed $file The CVS file with the users, either an EfrontFile object or the full path to the file
	 * @param boolean $replaceUsers Whether to replace existing users having the same name as the ones imported
	 * @return array The imported users in an array of EfrontUser objects
	 * @since 3.5.0
	 * @access public 
	 */
	public static function importUsers($file, $replaceUsers = false) {
	    if (!($file instanceof EfrontFile)) {
	        $file = new EfrontFile($file);
	    }
        $usersTable  = eF_getTableData("users", "*", "");
        $tableFields = array_keys($usersTable[0]);
	    
        // Get user types to check if they exist
        $userTypesTable = eF_getTableData("user_types", "*", "");
        // Set the userTypesTable to find in O(1) the existence or not of a user-type according to its name
        foreach($userTypesTable as $key => $userType) {
            $userTypesTable[$userType['name']] = $userType;    
        }

        // If we work on the Enterprise version we need to distinguish between users and module_hcd_employees tables fields 
        $userFields = array('login', 'password','email','languages_NAME','name','surname','active','comments','user_type','timestamp','avatar','pending','user_types_ID');
        
        $existingUsers = eF_getTableDataFlat("users", "login");
	    $fileContents = file_get_contents($file['path']);
	    $fileContents = explode("\n", trim($fileContents));
        $separator    = ";";
	    $fields       = explode($separator, trim($fileContents[0]));
	    if (sizeof($fields) == 1) {
	        $separator = ",";
	        $fields    = explode($separator, $fileContents[0]);
	        if (sizeof($fields) == 1) {
	            throw new Exception (_UNKNOWNSEPARATOR, EfrontSystemException::ILLEGAL_CSV);
	        }
	    }
	    foreach ($fields as $key => $value) {
	        if (empty($value)) { 
    	        $unused = $key;
    	        unset($fields[$key]);
	        }
	    }
	    $inserted = 0;
	    $matched  = array_intersect($fields, $tableFields);

	    $newUsers = array();
	    $messages = array();
	    // The check here is removed to offer interoperability between Enterprise and Educational versions
	    // throw new Exception (_PLEASECHECKYOURCSVFILEFORMAT, EfrontSystemException::ILLEGAL_CSV);
        for ($i = 1; $i < sizeof($fileContents); $i++) {
            $csvUser = explode($separator, $fileContents[$i]);
                unset($csvUser[$unused]);
                
            if (sizeof($csvUser) != sizeof($fields)) {
                    throw new Exception (_PLEASECHECKYOURCSVFILEFORMAT.': '._NUMBEROFFIELDSMUSTBE.' '.sizeof($fields).' '._BUTFOUND.' '.sizeof($csvUser), EfrontSystemException::ILLEGAL_CSV);
                }
            $csvUser = array_combine($fields, $csvUser);

            if (in_array($csvUser['login'], $existingUsers['login']) && $replaceUsers) {
                $existingUser  = EfrontUserFactory :: factory($csvUser['login']);
                $existingUser -> delete();
            }  
            if (!in_array($csvUser['login'], $existingUsers['login']) || $replaceUsers) {
             
                if (!isset($csvUser['password']) || !$csvUser['password']) {
                    $csvUser['password'] = $csvUser['login'];
                }
                
                // Check the user-type existence by name
                if ($csvUser['user_type_name'] != "" && isset($userTypesTable[$csvUser['user_type_name']])) {
                    // If there is a mismatch between the imported custom type basic type and the current basic type 
                    // then set no custom type
                    if ($userTypesTable[$csvUser['user_type_name']]['basic_user_type'] != $csvUser['user_type']) {
                        $csvUser['user_types_ID'] = 0;
                    } else {
                        $csvUser['user_types_ID'] = $userTypesTable[$csvUser['user_type_name']]['id'];
                    }    
                    
                } else {
                    $csvUser['user_types_ID'] = 0;
                }
                unset($csvUser['user_type_name']);
                
                // If we are not in Enterprise version then $csvEmployeeProperties is used as a buffer
                // This is done to enable Enterprise <-> Enteprise, Educational <-> Educational, Enterprise <-> Educational imports/exports
                $csvEmployeeProperties = $csvUser;
                if (MODULE_HCD_INTERFACE) {
                    // Copy all fields and remove the user ones -> leaving only employee related fields
                    $csvEmployeeProperties['users_login'] = $csvUser['login'];
                } 
                    
                    // Delete and recreate $csvUser to keep only the fields in userFields
                    unset($csvUser);
                    foreach($userFields as $field) {
                        $csvUser[$field] = $csvEmployeeProperties[$field]; 
                        if (MODULE_HCD_INTERFACE) {
                            unset($csvEmployeeProperties[$field]);
                        }    
                    }
                
                try {	                
                    
                    if (MODULE_HCD_INTERFACE) {
                       $user =  EfrontUser :: createUser($csvUser);
                       $user -> aspects['hcd'] = EfrontHcdUser::createUser($csvEmployeeProperties);
                       $newUsers[] = $user; 
                    } else {
                        $newUsers[] = EfrontUser :: createUser($csvUser);
                    }
                } catch (Exception $e) {
                    $messages[] = '&quot;'.$csvUser['login'].'&quot;: '.$e -> getMessage().' ('.$e -> getCode().')';
                }
            }
	    }

	    return array($newUsers, $messages);
	}
	
    /**
     * Export users
     * 
     * This function is used to produce a CSV file with the system
     * users.
     * <br/>Example:
     * <code>
     * EfrontSystem :: exportUsers(";");		Create a semicolon-delimited CSV file with system users
     * </code> 
     *
     * @param string $separator The separator to use for the csv file
     * @return EfrontFile The exported CSV file
     * @since 3.5.0
     * @access public
     */	
	public static function exportUsers($separator) {
	    if (MODULE_HCD_INTERFACE) {
	        $users   = eF_getTableData("users LEFT OUTER JOIN user_types ON users.user_types_ID = user_types.id LEFT OUTER JOIN module_hcd_employees ON module_hcd_employees.users_login = users.login", "users.*, user_types.name as user_type_name, module_hcd_employees.*");
	    } else {
	        $users   = eF_getTableData("users LEFT OUTER JOIN user_types ON users.user_types_ID = user_types.id", "users.*, user_types.name as user_type_name");
	    }
	    foreach ($users as $user) {
	        unset($user['password']);
	        unset($user['user_types_ID']);
	        if (MODULE_HCD_INTERFACE) {
	            unset($user['users_login']);
	        }
	        $lines[] = implode($separator, $user);
	    }

	    array_unshift($lines, implode($separator, array_keys($user)));
	    
	    if (!is_dir($GLOBALS['currentUser'] -> user['directory']."/temp")) {
	        mkdir($GLOBALS['currentUser'] -> user['directory']."/temp", 0755);
	    }
	    file_put_contents($GLOBALS['currentUser'] -> user['directory']."/temp/efront_users.csv", implode("\n", $lines));
	    
	    $file = new EfrontFile($GLOBALS['currentUser'] -> user['directory']."/temp/efront_users.csv");
	    
	    return $file;
	}

    /**
     * Export chat conversation
     * 
     * This function is used to produce a txt file with a selected 
     * conversation.
     * <br/>Example:
     * <code>
     * EfrontSystem :: exportChat($messages);		Create a semicolon-delimited CSV file with system users
     * </code> 
     *
     * @param array $messages with fields 'timestamp', 'content', 'users_LOGIN' for each chat message record
     * @return EfrontFile The exported txt file
     * @since 3.5.2
     * @access public
     */	
	public static function exportChat($messages) {
        $lines = array();
        foreach ($messages as $msg) {
            $lines[] = date("j M Y, G:i:s",$msg['timestamp']) . ", ". $msg['users_LOGIN'] . ": " . $msg['content'];
        }

	    if (!is_dir($GLOBALS['currentUser'] -> user['directory']."/temp")) {
	        mkdir($GLOBALS['currentUser'] -> user['directory']."/temp", 0755);
	    }
	    file_put_contents($GLOBALS['currentUser'] -> user['directory']."/temp/chat_conversation.txt", implode("\n", $lines));
	    
	    $file = new EfrontFile($GLOBALS['currentUser'] -> user['directory']."/temp/chat_conversation.txt");
	    
	    return $file;
	}
		
	/**
	 * Get system languages
	 * 
	 * This function is used to get the languages installed to the system
	 * <br/>Example:
	 * <code>
	 * $languages = EfrontSystem :: getLanguages();	Returns a 2-dimensional array, with complete information on each language
	 * $languages = EfrontSystem :: getLanguages(true);	Returns a 1-dimensional array of active languages, with name => translation pairs
	 * </code>
	 *
	 * @param boolean $reduced Whether to return only active languages, in a single-dimensional array
	 * @return array The languages array
	 * @since 3.5.0
	 * @access public
	 */
	public static function getLanguages($reduced = false) {
	    $languages = array();
	    $result    = eF_getTableData("languages", "*");
	    foreach ($result as $value) {
	        if (is_file(G_ROOTPATH.'libraries/language/lang-'.$value['name'].'.php.inc')) {	             
	            $value['file_path']        = G_ROOTPATH.'libraries/language/lang-'.$value['name'].'.php.inc';
	            $languages[$value['name']] = $value;
	        } else {
	            eF_deleteTableData("languages", "name='".$value['name']."'");
	        }
	    }
	    if ($reduced) {
	        $reduced = array();
	        foreach ($languages as $key => $value) {
	            $value['translation'] ? $reduced[$key] = $value['translation'] : $reduced[$key] = $key;
	        }
	        return $reduced;
	    } else {
	        return $languages;
	    }
	}
	
}


?>