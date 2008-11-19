<?php
/**
*/

/**
* Initialization part
*
* This part is used for database initialization.
*/
define("G_MAXIMUMQUERYSIZE", 500000);                                    //Maximum query size is 1M. Lower it in case of query problems
/**ADODB database abstraction class*/
require_once($path.'adodb/adodb.inc.php');

$ADODB_CACHE_DIR = $path."adodb/cache";
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
//global $db;
$db = ADONewConnection(G_DBTYPE);
if (!$db -> Connect(G_DBHOST, G_DBUSER, G_DBPASSWD, G_DBNAME)) {
    echo $db -> ErrorMsg();
    exit;
}
//$db->debug=true;

$databaseTime    = 0;
$databaseQueries = 0;

/**
* Execute mySQL code
*
* This function is used to execute an arbitrary SQL query
* <br>Example:
* <code>
* $result = eF_execute('SELECT * FROM users WHERE id = 1');
* </code>
*
* @param string $sql the SQL query
* @return mixed The result, formed in a MySQL resource
* @version 1.0
*
*/
function eF_executeNew($sql)
{
    /**ADODB exceptions class*/
    //require_once($path.'adodb/adodb-exceptions.inc.php');

    global $db;



    $thisQuery = microtime(true);

    $result = $db -> Execute($sql);
    if (!$result) {
        if (G_DEBUG) {
            eF_printMessage(_PROBLEMQUERYINGDATABASE.": '".$sql."'<br> ".mysql_error());
        }
    }
    if ($db->debug == true) {
        echo '<span style = "color:red">Time spent on this query: '.(microtime(true) - $thisQuery).'</span>';
    }

    $GLOBALS['databaseTime'] = $GLOBALS['databaseTime'] + microtime(true) - $thisQuery;$GLOBALS['databaseQueries']++;

    return $result;
}

function eF_execute($sql)
{



    $db = mysql_connect(G_DBHOST, G_DBUSER, G_DBPASSWD);
    if ( (!$db) | (!mysql_select_db(G_DBNAME, $db)) ) {
        if (G_DEBUG) {
                echo "Problem Connecting to Database. MySQL returned: ".mysql_error();          //If there is a problem with the database, then eF_printMessage and lanugage files may not have loaded, so we echo the error message
        } else {
                echo "Problem Connecting to Database.";
        }
        exit;
    }

    $result = mysql_query($sql);
    if (!$result) {
        if (G_DEBUG) {
            eF_printMessage(_PROBLEMQUERYINGDATABASE.": '".$sql."'<br> ".mysql_error());
        } else {
            eF_printMessage(_PROBLEMQUERYINGDATABASE);
        }
        exit;
    }

    $GLOBALS['databaseTime'] = $GLOBALS['databaseTime'] + microtime(true) - $thisQuery;$GLOBALS['databaseQueries']++;
    return $result;
}

/**
* Insert data to a database table
*
* This function is used to insert data to a database table. The data is formed as an associative
* array, where the keys are column names and the values are the column data. The function returns
* the auto_increment value of the insertion id, if one exists
* <br>Example:
* <code>
* $fields = array('name' => 'john', 'surname' => 'doe');
* $result = eF_insertTableData('users', $fields);
* </code>
* @param string $table The table to insert data into
* @param array $fields An associative array with the table cell data
* @return mixed The id of the insertion, if an AUTO_INCREMENT id field is set. Otherwise, true in success and false on failure
* @version 1.0
*/
function eF_insertTableData($table, $fields)
{
    global $db;

    $thisQuery = microtime(true);

    if (sizeof($fields) < 1) {
        trigger_error(_EMPTYFIELDSLIST, E_USER_WARNING);
        return false;
    }

    $sql       = "INSERT INTO ".$table." SET ";
    $connector = "";
    foreach ($fields as $key => $value) {
        $sql      .= $connector.$key."='".$value."'";
        $connector = ", ";
    }

    $result = $db -> Execute($sql);
    if ($result) {
        if ($db->debug == true) {
            echo '<span style = "color:red">Time spent on this query: '.(microtime(true) - $thisQuery).'</span>';
        }

        $id = $db -> Insert_ID();

        $GLOBALS['databaseTime'] = $GLOBALS['databaseTime'] + microtime(true) - $thisQuery;
        $GLOBALS['databaseQueries']++;

        if ($id == 0) {
            return true;
        } else {
            return $id;
        }
    } else {
        $GLOBALS['databaseTime'] = $GLOBALS['databaseTime'] + microtime(true) - $thisQuery;
        $GLOBALS['databaseQueries']++;
        return false;
    }
}




/**
 * Insert mutiple values
 *
 * This function is used to insert multiple database values at once.
 * The values are specified in an array of arrays.
 * <br/>Example:
 * <code>
 * $data[] = array('users_LOGIN' => 'admin',
 *            'timestamp' => '1111111111',
 *            'action' => 'lastmove',
 *            'comments' => '0',
 *            'session_ip' => '7f000001');
 * $data[] = array('users_LOGIN' => 'admin',
 *            'timestamp' => '2222222222',
 *            'action' => 'lastmove1',
 *            'comments' => '0',
 *            'session_ip' => '7f000001');

 * eF_insertTableDataMultiple('logs', $data);
 * </code>
 *
 * @param string $table The table to insert values into
 * @param array $fields An array of arrays with fields
 * @return boolean True if everything is ok
 * @since 3.5.0
 */
function eF_insertTableDataMultiple($table, $fields) {
    global $db;

    $thisQuery = microtime(true);

    if (sizeof($fields) == 0) {
        return false;
    }
    if (!is_array($fields[0])) {            //If we specified a 1-dimensional array, convert it to 2-dimensional
        $fields = array($fields);
    }

    $count     = 0;
    $sqlArray2 = array();
    $sql       = "INSERT INTO ".$table." (".implode(",", array_keys($fields[0])).") values ";
    $currentLength[$table] = 0;
    foreach ($fields as $value) {
        $valuesString = implode("','", $value);
        $currentLength[$table] += mb_strlen("('".$valuesString."')");
        if ($currentLength[$table] > G_MAXIMUMQUERYSIZE) {
            $count++;
            $currentLength[$table] = 0;
        }
        $sqlArray2[$count][] = "('".$valuesString."')";
        $sqlArray[] = "('".$valuesString."')";
    }


    $bigSqlQuery = $sql.implode(",", $sqlArray);
    foreach ($sqlArray2 as $query) {
        $bigSqlQuery2[] = $sql.implode(",", $query);
    }

    foreach ($bigSqlQuery2 as $value) {
        $result = $db -> Execute($value);
    }

    if ($result) {
        if ($db->debug == true) {
            echo '<span style = "color:red">Time spent on this query: '.(microtime(true) - $thisQuery).'</span>';
        }
        $GLOBALS['databaseTime'] = $GLOBALS['databaseTime'] + microtime(true) - $thisQuery;$GLOBALS['databaseQueries']++;
        return true;
    } else {
        $GLOBALS['databaseTime'] = $GLOBALS['databaseTime'] + microtime(true) - $thisQuery;$GLOBALS['databaseQueries']++;
        return false;
    }
}

/**
* Update table data
*
* This function is used to update data to a database table. The data is formed as an associative
* array, where the keys are column names and the values are the column data.
* <br>Example:
* <code>
* $fields = array('name' => 'john', 'surname' => 'doe');
* $result = eF_updateTableData('users', $fields, 'login=jdoe');
* </code>
* @param string $table The table to update data to
* @param array $fields An associative array with the table cell data
* @param string $where The where clause of the SQL Update.
* @return mixed The query result, usually true or false.
* @version 1.0
*/
function eF_updateTableData($table, $fields, $where)
{
    global $db;



    $thisQuery = microtime(true);

    if(sizeof($fields) < 1) {
        trigger_error(_EMPTYFIELDSLIST, E_USER_WARNING);
        return false;
    }

    $sql       = "UPDATE ".$table." SET ";
    $connector = "";
    foreach($fields as $key => $value) {
        if ($value === null) {
            $sql .= $connector.$key."=NULL";
            $connector = ", ";
        } else {
            $sql .= $connector.$key."='".$value."'";
            $connector = ", ";
        }
    }

    $sql   .= " WHERE ".$where;

    $result = $db -> Execute($sql);

    if ($db->debug == true) {
        echo '<span style = "color:red">Time spent on this query: '.(microtime(true) - $thisQuery).'</span>';
    }

    $GLOBALS['databaseTime'] = $GLOBALS['databaseTime'] + microtime(true) - $thisQuery;$GLOBALS['databaseQueries']++;

    return $result;
}

/**
* Delete database data
*
* This function is used to delete the database data specified by the where clause.
* <br>Example
* <code>
* $result = eF_deleteTableData('users');                  //Equivalent to truncate table "users".
* $result = eF_deleteTableData('users', 'id = 1');        //Delete data from table users, where id = 1.
* </code>
* @param string $table The table to dekete data from
* @param string $where The where clause of the SQL Delete.
* @return mixed The query result, usually true/false.
* @version 1.0
*/
function eF_deleteTableData($table, $where="")
{
    global $db;



    $thisQuery = microtime(true);

    $sql = "DELETE FROM ".$table;
    if($where != "") {
        $sql .= " WHERE ".$where;
    }
    $result = $db -> Execute($sql);
    //echo "<pre>";print_r($result);echo "</pre>";
    if ($db->debug == true) {
        echo '<span style = "color:red">Time spent on this query: '.(microtime(true) - $thisQuery).'</span>';
    }

    $GLOBALS['databaseTime'] = $GLOBALS['databaseTime'] + microtime(true) - $thisQuery;$GLOBALS['databaseQueries']++;
    return $result;
}


/**
* Retrieve database data.
*
* This function is used to perform a SELECT query. Multiple parameters may be used, to
* specify the ordering, length and grouping of the data set. It returns an array of associative arrays,
* where each of these arrays holds the column name and the corresponding value for the result row
* <br>Example:
* <code>
* //Retrieve all data from table users:
* $result = eF_getTableData('users');
* //Retrieve all rows from table users, but only columns "name" and "surname"
* $result = eF_getTableData('users', 'name, surname');
* //Get the "name" and "surname" for user with login "jdoe"
* $result = eF_getTableData('users', 'name, surname', 'login=jdoe');
* //Get the same information, but this time ordered by "name"
* $result = eF_getTableData('users', 'name, surname', 'login=jdoe', 'name');
* //Get the same information, but this time grouped by "surname"
* $result = eF_getTableData('users', 'name, surname', 'login=jdoe', '', 'surname');
* </code>
* @param string $table The table to retrieve data from
* @param string $fields The fields to retrive, comma-separated string, defaults to *.
* @param string $where The where clause of the SQL Select.
* @param string $order The order by clause of the SQL Select.
* @param string $group The group by clause of the SQL Select.
* @return mixed an array holding the query result.
* @version 1.0
*/
function eF_getTableData($table, $fields="*", $where="", $order="", $group="")
{
    global $db;

    $thisQuery = microtime(true);

    $sql = "SELECT ".$fields." FROM ".$table;
    if($where != "") {
        $sql .= " WHERE ".$where;
    }
    if($order != "") {
        $sql .= " ORDER BY ".$order;
    }
    if($group != "") {
        $sql .= " GROUP BY ".$group;
    }

    $result = $db -> GetAll($sql);
//echo $sql."<BR>";
    if ($db->debug == true) {
        echo '<span style = "color:red">Time spent on this query: '.(microtime(true) - $thisQuery).'</span>';
    }

    $GLOBALS['databaseTime'] = $GLOBALS['databaseTime'] + microtime(true) - $thisQuery;
    $GLOBALS['databaseQueries']++;

    if ($result == false) {
        return array();
    } else {
        return $result;
    }
}

/**
* Retrieve table contents Flat
*
* This function, much similar to the eF_getTableData(), retrieves data from the designated
* database table. The main difference lies at the result array format: This time, each
* field in the result set corresponds to an array in the result array.
* <br/>Example:
* <code>
* $result = eF_getTableDataFlat("users", "name, surname");
* print_r($result);
* </code>
* Returns:
* <code>
* Array
* (
*     [name]     => Array
*                   (
*                     [0] => 'john',
*                     [1] => 'joe',
*                     [2] => 'mary'
*                   )
*     [surname]  => Array
*                   (
*                     [0] => 'white',
*                     [1] => 'black',
*                     [2] => 'green'
*                   )
* )
* </code>
*
* @param string $table The database table name.
* @param string $fields Comma separated list of the fields to retrieve, defaults to *.
* @param string $where The where clause of the SQL query.
* @return array The query result table.
* @version 2.0
* @see eF_getTableData()
* Changes from 1.0 to 2.0:
* - Rewritten function in order to accelerate execution. It now uses eF_getTableData()
*/
function eF_getTableDataFlat($table, $fields="*", $where="", $order="", $group="")
{
    global $db;


    $thisQuery = microtime(true);

    $sql = "SELECT ".$fields." FROM ".$table;

    if($where != "") {
        $sql .= " WHERE ".$where;
    }
    if($order != "") {
        $sql .= " ORDER BY ".$order;
    }
    if($group != "") {
        $sql .= " GROUP BY ".$group;
    }


    $result = eF_getTableData($table, $fields, $where, $order, $group);
    $temp = array();

    for ($i = 0; $i < sizeof($result); $i++) {
        foreach ($result[$i] as $key => $value) {
            $temp[$key][] = $value;
        }
    }

    if ($db->debug == true) {
        echo '<span style = "color:red">Time spent on this query: '.(microtime(true) - $thisQuery).'</span>';
    }

    $GLOBALS['databaseTime'] = $GLOBALS['databaseTime'] + microtime(true) - $thisQuery;$GLOBALS['databaseQueries']++;

    return $temp;

}


/**
* Describes a table field
*
* This function returns the description of a table field, or the whole table if
* a field is not specified.
* <br />Example:
* <code>
* $desc = eF_describeTable("logs", array(0 => "id", "comments"));
* print_r($desc);
* //Prints something like:
* Array
* (
*     [0] => Array
*         (
*             [Field] => id
*             [Type] => int(10) unsigned
*             [Null] =>
*             [Key] => PRI
*             [Default] =>
*             [Extra] => auto_increment
*         )
*
*     [1] => Array
*         (
*             [Field] => comments
*             [Type] => varchar(255)
*             [Null] =>
*             [Key] => PRI
*             [Default] => 0
*             [Extra] =>
*         )
*
* )
* </code>
*
* @param string $table The table to describe
* @param array $fields The fields to describe
* @return array The field description
* @version 1.0
*/
function eF_describeTable($table, $fields = false) {
    if (!$fields) {
        $result = eF_execute("describe $table");
        while($temp = mysql_fetch_assoc($result)) {
            $desc[] = $temp;
        }
    } else {
        foreach ($fields as $field) {
            $result = eF_execute("describe $table $field");
            $desc[] = mysql_fetch_assoc($result);
        }
    }

    return $desc;
}

/**
* Get table fields
*
* This function returns the desgnated table's fields
*
* @param string $table The database table
* @return array The table fields
* @version 1.0
*/
function eF_getTableFields($table) {
    global $db;

    $result = $db -> GetCol("describe $table");
    return $result;
}

?>
