<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

class EfrontBenchmark
{
 public $times;
 public $dbtimes;

 public $defaultValues = array('start', 'init', 'script', 'smarty', 'end');

 public function __construct($time = false) {
  if (!$time) {
   $time = microtime(true);
  }
  $this -> times = array('start' => $time);
 }

 public function set($label, $time = false) {
  if (!$time) {
   $time = microtime(true);
  }
  $this -> times[$label] = $time;
 }

 public function stop() {
  $this -> times['end'] = microtime(true);
  $this -> dbtimes = array('time' => $GLOBALS['db'] -> databaseTime,
         'queries' => $GLOBALS['db'] -> databaseQueries);
 }

 public function display() {

     $GLOBALS['db'] -> queries = eF_multisort($GLOBALS['db'] -> queries, 'times', 'asc');

     $str = "
  <div onclick = 'this.style.display=\"none\"' style = 'position:absolute;top:0px;right:0px;background-color:lightblue;border:1px solid black' >
     <table>
         <tr><th colspan = '100%'>Benchmarking info (click to remove)</th></tr>
         <tr><td>Initialization time: </td><td>".round($this -> times['init'] - $this -> times['start'], 5)." sec</td></tr>
         <tr><td>Script time: </td><td>".round($this -> times['script'] - $this -> times['init'], 5)." sec</td></tr>
         <tr><td>Database time (".$this -> dbtimes['queries']." q): </td><td>".($this -> dbtimes['time'] > 100 ? 0 : round($this -> dbtimes['time'], 5))." sec (<a href = 'javascript:void(0)' onclick = 'eF_js_showDivPopup(\"Queries\", 2, \"queries_table\");return false;'>show queries</a>)</td></tr>
         <tr><td>Smarty time: </td><td>".round($this -> times['smarty'] - $this -> times['script'], 5)." sec</td></tr>
         <tr><td colspan = \"2\" class = \"horizontalSeparator\"></td></tr>
         <tr><td>Total execution time: </td><td>".round($this -> times['end'] - $this -> times['start'], 5)." sec</td></tr>
         <tr><td>Peak memory usage: </td><td>".round(memory_get_peak_usage(true)/1024)." KB</td></tr>";
  if (sizeof($this -> defaultValues) != sizeof($this -> times)) {
   $current = 'start';
   foreach ($this -> times as $key => $value) {
    if (!in_array($key, $this -> defaultValues)) {
     $str .= "<tr><td>Time from ".$current." to ".$key.": </td><td>".round($this -> times[$key] - $this -> times[$current], 5)." sec</td></tr>";
     $current = $key;
    }
   }
   $str .= "<tr><td>Time from ".$current." to end: </td><td>".round($this -> times['end'] - $this -> times[$current], 5)." sec</td></tr>";
  }
  $str .= "
   </table>
   <table style = 'display:none;background-color:white;width:100%' id = 'queries_table'>
    <tr><td><pre>".print_r($GLOBALS['db'] -> queries, true)."</pre></td></tr>
   </table>
      </div>";


  return $str;
 }
}
?>
