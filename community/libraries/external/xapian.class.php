<?php

class EfrontXapian
{
 const RESULTS_LIMIT = 20;
 const PARSE_LIMIT = 20;

 protected static $_instance = null;
 protected static $_database_path = null;
 protected static $_accepted_formats = array('pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx');

 /**
	 * @return EfrontXapian 
	 */
 public static function getInstance() {
  if (is_null(self::$_instance)) {
   self::$_instance = new self();
  }
  return self::$_instance;
 }

 public function __construct() {
  if (!extension_loaded('xapian')) {
   throw new Exception ('Xapian extension is not loaded');
  }
  require_once "xapian.php";
  self::$_database_path = dirname(__FILE__).'/xapian_database';
 }

 public function addFilesToIndex($paths) {
  //$file = new EfrontFile($path);
  foreach ($paths as $path) {
   $lines = $this->_parseFile(trim($path));
   $this->_index($lines, trim($path));
  }
 }

 public function removeFilesFromIndex($paths) {
  foreach ($paths as $key => $path) {
   $paths[$key] = trim($path);
  }
  $database = new XapianWritableDatabase(self::$_database_path, Xapian::DB_CREATE_OR_OPEN);
     $limit =$database->get_value_freq('file');

     $iterator = $database->valuestream_begin('file');

     $count = 1;
     while($limit > $count++) {
      $iterator->next();
      if (in_array($iterator->get_value(), $paths)) {
       $database->delete_document($iterator->get_docid());
      }
     }
 }

 protected function _parseFile($path) {
  $lines = array();
  if (pathinfo(trim($path), PATHINFO_EXTENSION) == 'pdf') {
   $filename = str_replace(" ", "_", tempnam(sys_get_temp_dir(), "XAPIAN_".(trim(basename($path)))));
   exec("pdftotext ".escapeshellarg(trim($path))." {$filename}");
   $lines = file($filename);
  } else if (in_array(pathinfo(trim($path), PATHINFO_EXTENSION), self::$_accepted_formats)) {
   $filename = str_replace(" ", "_", tempnam(sys_get_temp_dir(), "XAPIAN_".(trim(basename($path)))));
   exec("unoconv -f pdf -o ".escapeshellarg($filename).".pdf ".escapeshellarg(trim($path)).";pdftotext ".escapeshellarg($filename).".pdf ".escapeshellarg($filename));
   $lines = file($filename);
  }
  return $lines;
 }

 /**
	 * Index file contents
	 * 
	 * @param array $lines The array of the file contents, each entry corresponds to a new line (included) 
	 */
 protected function _index($lines, $file_path) {
  if (empty($lines)) {
   return false;
  }
  // Open the database for update, creating a new database if necessary.
  $database = new XapianWritableDatabase(self::$_database_path, Xapian::DB_CREATE_OR_OPEN);

  $indexer = new XapianTermGenerator();
  $stemmer = new XapianStem("english");
  $indexer->set_stemmer($stemmer);

  $para = '';
  //$lines = file($path);

  foreach ($lines as $line) {
   $line = rtrim($line);
   if ($line == "" && $para != "") {
    // We've reached the end of a paragraph, so index it.
    $doc = new XapianDocument();
    $doc->set_data($para);

    $doc->add_value('file', $file_path); //add meta-information to the entry

    $indexer->set_document($doc);
    $indexer->index_text($para);

    // Add the document to the database.
    $database->add_document($doc);

    $para = "";
   } else {
    if ($para != "") {
     $para .= " ";
    }
    $para .= $line;
   }
  }

  // Set the database handle to Null to ensure that it gets closed
  // down cleanly or uncommitted changes may be lost.
  $database = Null;

 }

 public function search($query_string) {
  $database = new XapianDatabase(self::$_database_path);
  // Start an enquire session.
  $enquire = new XapianEnquire($database);


  $qp = new XapianQueryParser();
  $stemmer = new XapianStem("english");
  $qp->set_stemmer($stemmer);
  $qp->set_database($database);
  $qp->set_stemming_strategy(XapianQueryParser::STEM_SOME);
  $query = $qp->parse_query($query_string);

  // Find the top 10 results for the query.
  $enquire->set_query($query);
  $enquire->set_collapse_key(0, 1); //index '0' holds the file path, so we're collapsing on that value in order for a single value to be returned by the system
  $matches = $enquire->get_mset(0, $database->get_doccount());

  $i = $matches->begin();
  $results = array();
  while (!$i->equals($matches->end())) {
   $n = $i->get_rank() + 1;
   try {
    $fileobj = new EfrontFile($i->get_document()->get_value('file'));
    $results[] = array('id' => $fileobj['id'],
      'path' => str_replace(G_ROOTPATH, '', $fileobj['path']),
      'login' => $fileobj['users_LOGIN'] ? $fileobj['users_LOGIN'] : '',
      'date' => formatTimestamp(filemtime($fileobj['path']), 'time_nosec'),
      'name' => $fileobj['name'],
      'extension' => $fileobj['extension'],
      'score' => $i->get_percent(),
      'content' => $i->get_document()->get_data(),
      'icon' => $fileobj -> getTypeImage());
   } catch (Exception $e) {
    //don't halt for missing files
   }

   $i->next();
  }

  return $results;
 }

 public static function cron() {
  $hash_file_name = 'xapian_list_hash.txt';
  $list_file_name = 'xapian_list.txt';
  $new_file_name = 'temp.txt';
  $parse_root_path = realpath(dirname(__FILE__).'/../../www/content/lessons');
  //$parse_root_path = "xapian_tests";

  if (is_file($list_file_name)) {
   $lines = file($list_file_name);
   if (is_file($hash_file_name)) {
    $hashes = file($hash_file_name, FILE_IGNORE_NEW_LINES);
   } else {
    foreach ($lines as $key => $value) {
     if (is_file($value)) {
      $hashes[$key] = sha1_file(trim($value));
     } else {
      $hashes[$key] = '';
     }
    }
   }
  } else {
   $lines = $hashes = array();
  }

  $find_str = array();
  foreach (self::$_accepted_formats as $value) {
   $find_str[] = "\*.{$value}";
  }
  $find_str = implode(" -o -name ", $find_str);
  exec("find {$parse_root_path} -name {$find_str} > {$new_file_name}");

  $newlines = file($new_file_name);
  $newFiles = array_slice(array_diff($newlines, $lines), 0, self::PARSE_LIMIT); //only parse 20 files at a time
  $deletedFiles = array_diff($lines, $newlines);

  EfrontXapian::getInstance()->addFilesToIndex($newFiles);
  EfrontXapian::getInstance()->removeFilesFromIndex($deletedFiles);

  //Remove deleted files from the list
  foreach ($deletedFiles as $value) {
   if (($key = array_search($value, $lines)) !== false) {
    unset($lines[$key]);
    unset($hashes[$key]);
   }
  }
  $lines = array_merge($lines, $newFiles);
  foreach ($newFiles as $value) {
   $hashes[] = sha1_file(trim($value));
  }

  foreach ($lines as $key => $value) {
   $hash = sha1_file(trim($value));
   if ($hashes[$key] != $hash) { //meaning the file has changed
    unset($lines[$key]);
    unset($hashes[$key]);
    }
  }
  $lines = array_values($lines); //Re-index $lines in case values where unset

  file_put_contents($list_file_name, implode("", $lines));
  file_put_contents($hash_file_name, implode("\n", $hashes));
 }
}

?>
