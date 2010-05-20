<?php
/**

 * File for filesystem classes

 *

 * @package eFront

 */
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * EfrontFileException class

 *

 * This class extends Exception class and is used to issue errors regarding files and filesystem

 *

 * @package eFront

 * @since 3.5.0

 */
class EfrontFileException extends Exception
{
    //Note: values from 1 to 8 are upload errors
    const NO_ERROR = 0;
    const ILLEGAL_FILE_NAME = 101;
    const FILE_NOT_EXIST = 102;
    const ILLEGAL_PATH = 103;
    const FILE_IN_BLACK_LIST = 104;
    const FILE_NOT_IN_WHITE_LIST = 105;
    const GENERAL_ERROR = 106;
    const FILE_ALREADY_EXISTS = 107;
    const DIRECTORY_ALREADY_EXISTS = 108;
    const FILE_DELETED = 109;
    const ERROR_CREATE_ZIP = 110;
    const ERROR_OPEN_ZIP = 111;
    const UNKNOWN_COMPRESSION = 112;
    const DIRECTORY_NOT_EXIST = 113;
    const NOT_LESSON_FILE = 114;
    const UNAUTHORIZED_ACTION = 115;
    const NOT_APPROPRIATE_TYPE = 116;
    const ERROR_ZIP_PROCESSING = 117;
    const CANNOT_CREATE_DIR = 118;
    const UNKNOWN_ERROR = 199;
    const DATABASE_ERROR = 301;
}
/**

 * Class for files in Efront file system

 *

 * @since 3.5.0

 * @package eFront

 */
class EfrontFile extends ArrayObject
{
    /**

     * An array of mime types

     *

     * @since 3.5.0

     * @var array

     * @access public

     * @static

     */
    public static $mimeTypes = array (
     'bmp' => 'image/bmp',
        'cgm' => 'image/cgm',
        'djv' => 'image/vnd.djvu',
        'djvu' => 'image/vnd.djvu',
        'flv' => 'application/flv',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'ief' => 'image/ief',
        'jp2' => 'image/jp2',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'mac' => 'image/x-macpaint',
        'pbm' => 'image/x-portable-bitmap',
        'pct' => 'image/pict',
        'pgm' => 'image/x-portable-graymap',
        'pic' => 'image/pict',
        'pict' => 'image/pict',
        'png' => 'image/png',
        'pnm' => 'image/x-portable-anymap',
        'pnt' => 'image/x-macpaint',
        'pntg' => 'image/x-macpaint',
        'ppm' => 'image/x-portable-pixmap',
        'qti' => 'image/x-quicktime',
        'qtif' => 'image/x-quicktime',
        'ras' => 'image/x-cmu-raster',
        'rgb' => 'image/x-rgb',
        'svg' => 'image/svg+xml',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'wbmp' => 'image/vnd.wap.wbmp',
        'xbm' => 'image/x-xbitmap',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'asc' => 'text/plain',
        'css' => 'text/css',
        'etx' => 'text/x-setext',
        'htm' => 'text/html',
        'html' => 'text/html',
        'ics' => 'text/calendar',
        'ifb' => 'text/calendar',
        'rtf' => 'text/rtf',
        'rtx' => 'text/richtext',
        'sgm' => 'text/sgml',
        'sgml' => 'text/sgml',
        'tsv' => 'text/tab-separated-values',
        'txt' => 'text/plain',
        'wml' => 'text/vnd.wap.wml',
        'wmls' => 'text/vnd.wap.wmlscript',
     'kar' => 'audio/midi',
        'm3u' => 'audio/x-mpegurl',
        'm4a' => 'audio/mp4a-latm',
        'm4b' => 'audio/mp4a-latm',
        'm4p' => 'audio/mp4a-latm',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'mpga' => 'audio/mpeg',
        'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'snd' => 'audio/basic',
        'wav' => 'audio/x-wav',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'au' => 'audio/basic',
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp4' => 'video/mp4',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'm4u' => 'video/vnd.mpegurl',
        'm4v' => 'video/x-m4v',
        'dif' => 'video/x-dv',
        'dv' => 'video/x-dv',
        'mxu' => 'video/vnd.mpegurl',
        'qt' => 'video/quicktime',
        'wmv' => 'video/x-ms-wmv',
        'iges' => 'model/iges',
        'igs' => 'model/iges',
        'mesh' => 'model/mesh',
        'msh' => 'model/mesh',
        'silo' => 'model/mesh',
        'vrml' => 'model/vrml',
        'wrl' => 'model/vrml',
        'xyz' => 'chemical/x-xyz',
        'pdb' => 'chemical/x-pdb',
        'ice' => 'x-conference/x-cooltalk',
        'ai' => 'application/postscript',
        'atom' => 'application/atom+xml',
        'bcpio' => 'application/x-bcpio',
        'bin' => 'application/octet-stream',
        'cdf' => 'application/x-netcdf',
        'class' => 'application/octet-stream',
        'cpio' => 'application/x-cpio',
        'cpt' => 'application/mac-compactpro',
        'csh' => 'application/x-csh',
        'csv' => 'application/text',
     'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'dll' => 'application/octet-stream',
        'dmg' => 'application/octet-stream',
        'dms' => 'application/octet-stream',
        'doc' => 'application/msword',
        'dtd' => 'application/xml-dtd',
        'dvi' => 'application/x-dvi',
        'dxr' => 'application/x-director',
        'eps' => 'application/postscript',
        'exe' => 'application/octet-stream',
        'ez' => 'application/andrew-inset',
        'gram' => 'application/srgs',
        'grxml' => 'application/srgs+xml',
        'gtar' => 'application/x-gtar',
        'hdf' => 'application/x-hdf',
        'hqx' => 'application/mac-binhex40',
        'jnlp' => 'application/x-java-jnlp-file',
        'js' => 'application/x-javascript',
        'latex' => 'application/x-latex',
        'lha' => 'application/octet-stream',
        'lzh' => 'application/octet-stream',
        'man' => 'application/x-troff-man',
        'mathml' => 'application/mathml+xml',
        'me' => 'application/x-troff-me',
        'mif' => 'application/vnd.mif',
        'ms' => 'application/x-troff-ms',
        'nc' => 'application/x-netcdf',
        'oda' => 'application/oda',
        'ogg' => 'application/ogg',
        'pdf' => 'application/pdf',
        'pgn' => 'application/x-chess-pgn',
        'ppt' => 'application/vnd.ms-powerpoint',
        'ps' => 'application/postscript',
        'rdf' => 'application/rdf+xml',
        'rm' => 'application/vnd.rn-realmedia',
        'roff' => 'application/x-troff',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'sit' => 'application/x-stuffit',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'so' => 'application/octet-stream',
        'spl' => 'application/x-futuresplash',
        'src' => 'application/x-wais-source',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'swf' => 'application/x-shockwave-flash',
        't' => 'application/x-troff',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tr' => 'application/x-troff',
        'ustar' => 'application/x-ustar',
        'vcd' => 'application/x-cdlink',
        'vxml' => 'application/voicexml+xml',
        'wbmxl' => 'application/vnd.wap.wbxml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'xht' => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
        'xls' => 'application/vnd.ms-excel',
        'xml' => 'application/xml',
        'xsl' => 'application/xml',
        'xslt' => 'application/xslt+xml',
        'xul' => 'application/vnd.mozilla.xul+xml',
        'zip' => 'application/zip');
    /**

     * Class constructor

     *

     * The class constructor instantiates the object based on the $file parameter.

     * $file may be either:

     * - an array with file attributes

     * - a file id

     * - the full path to a physical file

     * - The full path to a file, even if it doesn't have a corresponding database representation

     * <br/>Example:

     * <code>

     * $result = eF_getTableData("files", "*", "id=43");

     * $file = new EfrontFile($result[0]);                          //Instantiate object using array of values

     * $file = new EfrontFile(43);                                  //Instantiate object using id

     * $file = new EfrontFile('/var/www/test.txt');                 //Instantiate object using path

     * </code>

     *

     * @param mixed $file The file information, either an array, an id or a path string

     * @since 3.5.0

     * @access public

     */
    function __construct($file) {
        if (is_array($file)) { //Instantiate object based on the given array
            $file['path'] = EfrontDirectory :: normalize($file['path']);
            if (strpos($file['path'], G_ROOTPATH) === false) {
                $file['path'] = G_ROOTPATH.$file['path'];
            }
            $fileArray = $file;
        } else {
            if (eF_checkParameter($file, 'id')) { //Instantiate object based on id
                $result = eF_getTableData("files", "*", "id=".$file);
            } elseif (eF_checkParameter($file, 'path')) { //id-based instantiation failed; Check if the full path is specified
                $result = eF_getTableData("files", "*", "path='".str_replace(G_ROOTPATH, '', EfrontDirectory :: normalize($file))."'");
            } else {
                throw new EfrontFileException(_ILLEGALPATH.': '.$file, EfrontFileException :: ILLEGAL_PATH);
            }
            if (sizeof($result) > 0) {
                if (sizeof($result) > 1) { //if for some reason there is more than 1 database entries for the same file, keep only the latest (based on id)
                    for ($i = 0; $i < sizeof($result) - 1; $i++) {
                        eF_deleteTableData("files", "id=".$result[$i]['id']);
                        EfrontSearch :: removeText('files', $result[$i]['id'], 'data');
                        //unlink($result[$i]['file']);
                    }
                    $fileArray = $result[$i];
                } else {
                    $fileArray = $result[0];
                }
                $fileArray['path'] = G_ROOTPATH.$fileArray['path'];
            } else {
                if (is_file($file) && strpos($file, G_ROOTPATH) !== false) { //Create object without database information
                    $fileArray = array('id' => -1, //Set 'id' to -1, meaning this file has not a database representation
                                       'path' => $file);
                } else if (strpos($file, G_ROOTPATH) === false) {
                    throw new EfrontFileException(_ILLEGALPATH.': '.$file, EfrontFileException :: ILLEGAL_PATH);
                } else {
                    throw new EfrontFileException(_FILEDOESNOTEXIST.': '.$file, EfrontFileException :: FILE_NOT_EXIST);
                }
            }
        }
        //Append extra useful (derived) information to the array: name, extension, size, mime type
        $fileArray['name'] = EfrontFile :: decode(basename($fileArray['path']));
        $fileArray['directory'] = dirname($fileArray['path']);
        $fileArray['extension'] = pathinfo($fileArray['path'], PATHINFO_EXTENSION);
        $fileArray['size'] = round(filesize($fileArray['path'])/1024, 2);
        $fileArray['timestamp'] = filemtime($fileArray['path']);
        $fileArray['type'] = 'file';
        $fileArray['physical_name'] = basename($fileArray['path']);
        foreach ($pathParts = explode("/", $fileArray['path']) as $key => $value) {
            $pathParts[$key] = urlencode($value);
        }
        $fileArray['url_path'] = implode("/", $pathParts);
        //$fileArray['original_name'] != $fileArray['physical_name'] ? $fileArray['renamed'] = true : $fileArray['renamed'] = false;        //If the physical file name is different than the original name, it means that the file is renamed
        isset(EfrontFile :: $mimeTypes[strtolower($fileArray['extension'])]) ? $fileArray['mime_type'] = EfrontFile :: $mimeTypes[strtolower($fileArray['extension'])] : $fileArray['mime_type'] = 'application/'.$fileArray['extension'];
        parent :: __construct($fileArray); //Create an ArrayObject from the given array
        if (!is_file($this['path'])) { //If the file does not actually exist, then delete it from database and issue exception
            if ($this['id'] != -1) {
                eF_deleteTableData("files", "id=".$this['id']);
                EfrontSearch :: removeText('files', $this['id'], 'data');
            }
            throw new EfrontFileException(_FILEDOESNOTEXIST.': '.$this['path'], EfrontFileException :: FILE_DELETED);
        } elseif ( strpos($this['path'], G_ROOTPATH) === false ) {
            throw new EfrontFileException(_ILLEGALPATH.': '.$this['path'], EfrontFileException :: ILLEGAL_PATH); //The file must be inside root path, otherwise it is illegal
        }
    }
    /**

     * Delete file

     *

     * This function deletes the file. It first unlinks (if it exists)

     * the physical file, and then deletes its entry from the database.

     * <br/>Example:

     * <code>

     * $file = new EfrontFile(34);                          //Instantiate file

     * $file -> delete();                                   //Delete file

     * </code>

     *

     * @return boolean True if the file was deleted

     * @since 3.5.0

     * @access public

     */
    public function delete() {
        if (is_file($this['path']) && !unlink($this['path'])) { //If the file exists but could not be deleted, throw an exception. This way, even files that their equivalent physical file does not exist, may be deleted.
            throw new EfrontFileException(_CANNOTDELETEFILE, EfrontFileException :: GENERAL_ERROR);
        }
        if ($this['id'] != -1) {
            eF_deleteTableData("files", "path = '".str_replace(G_ROOTPATH, '', $this['path'])."' or id=".$this['id']); //Delete database representation of the file
            EfrontSearch :: removeText('files', $this['id'], 'data');
        }
        return true;
    }
    /**

     * Copy file

     *

     * This function is used to copy the current file to a new

     * destination. If a file with the same name exists in the

     * destination and $overwrite is true, it will be overwritten

     * <br/>Example:

     * <code>

     * $file = new EfrontFile(43);                                  //Instantiate file object

     * $file -> copy('/var/www/');                                  //Copy file to /var/www/

     * $file -> copy('/var/www/', true);                            //Copy file to /var/www/ and overwrite if it already exists

     * </code>

     * If the file being copied doesn't have a corresponding database representation,

     * the new file won't have one either. Otherwise, a database entry will be created

     * for the new file (An EfrontFile object corresponds to a file without DB representation

     * when the id is -1)

     *

     * @param string $destinationPath The destination directory

     * @param boolean $overwrite If true, overwrite existing file with the same name

     * @return EfrontFile The copied file

     * @since 3.5.0

     * @access public

     */
    public function copy($destinationPath, $overwrite = true) {
        $destinationPath = EfrontDirectory :: normalize($destinationPath);
        $parentDirectory = new EfrontDirectory(dirname($destinationPath)); //This way we check integrity of destination
        if (is_dir($destinationPath)) { //If $destinationPath is a directory, it means that the target file name was not specified, so append the current
            $destinationPath = $destinationPath.'/'.$this['physical_name'];
        }
        if (!$overwrite && is_file($destinationPath)) {
            throw new EfrontFileException(_CANNOTCOPYFILE.': '.$destinationPath.', '._FILEALREADYEXISTS, EfrontFileException :: FILE_ALREADY_EXISTS);//Use plain Exception rather than EfrontFileException, since the latter is caught right from the following catch block
        }
        if (copy($this['path'], $destinationPath)) {
            if ($this['id'] != -1) {
                $fields = array("path" => str_replace(G_ROOTPATH, '', $destinationPath), //Database entry for copied file
                                "users_LOGIN" => isset($_SESSION['s_login']) ? $_SESSION['s_login'] : $this['users_LOGIN'],
                                "timestamp" => time(),
                                "description" => $this['description'],
                                "groups_ID" => $this['groups_ID'],
                                "access" => $this['access'],
                                "metadata" => $this['metadata']);
                $fileId = eF_insertTableData("files", $fields);
                if ($fileId) {
                    $fileMetadataArray = unserialize($this['metadata']);
                    foreach ($fileMetadataArray as $key => $value) {
                        EfrontSearch :: insertText($value, $fileId, "files", "data");
                    }
                }
            }
            $file = new EfrontFile($destinationPath);
            return $file;
        } else {
            //eF_deleteTableData("files", "id=$fileid");                                                //If copy failed, delete empty table entry
            throw new EfrontFileException(_CANNOTCOPYFILE, EfrontFileException :: UNKNOWN_ERROR);
        }
    }
    /**

     * Move file

     *

     * This function is equivalent to copy(), except that it deletes the original

     * file after copying it.

     * <br/>Example:

     * <code>

     * $file = new EfrontFile(43);                                  //Instantiate file object

     * $file -> rename('/var/www/');                                  //Move file to /var/www/

     * $file -> rename('/var/www/', true);                            //Move file to /var/www/ and overwrite if it already exists

     * </code>

     *

     * @param string $destinationPath The destination directory

     * @param boolean $overwrite If true, overwrite existing file with the same name

     * @return EfrontFile The copied file

     * @since 3.5.0

     * @access public

     */
    public function rename($destinationPath, $overwrite = false) {
        $destinationPath = EfrontDirectory :: normalize($destinationPath);
        $parentDirectory = new EfrontDirectory(dirname($destinationPath)); //This way we check integrity of destination
        FileSystemTree::checkFile($destinationPath);
        if (!$overwrite && (is_file($destinationPath))) {
            throw new EfrontFileException(_CANNOTMOVEFILE.': '.$this['name'].', '._FILEALREADYEXISTS, EfrontFileException :: FILE_ALREADY_EXISTS);//Use plain Exception rather than EfrontFileException, since the latter is caught right from the following catch block
        }
        if ($this['path'] != $destinationPath) {
            if (copy($this['path'], $destinationPath)) { //rename() acts as a move() function as well
                unlink($this['path']);
                $this['path'] = $destinationPath;
                if ($this['id'] != -1) {
                    $this -> persist();
                }
                $this -> refresh();
            } else {
                throw new EfrontFileException(_CANNOTMOVEFILE, EfrontFileException :: UNKNOWN_ERROR);
            }
        }
    }
    /**

     * Persist file values

     *

     * This function is used to persist any changed values

     * of the file.

     * <br/>Example:

     * <code>

     * $file = new EfrontFile(43);                                  //Instantiate file object

     * $file -> file['description'] = 'New description';            //Change a file's property

     * $file -> persist();                                          //Persist changes

     * </code>

     *

     * @return boolean true if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function persist() {
        $fields = array('path' => str_replace(G_ROOTPATH, '', $this['path']),
                        'description' => $this['description'],
                        'groups_ID' => $this['groups_ID'],
                        'access' => $this['access'],
                        'shared' => $this['shared'],
                        'metadata' => $this['metadata']);
        $ok = eF_updateTableData("files", $fields, "id=".$this['id']);
        EfrontSearch :: removeText('files', $this['id'], 'data');
        $fileMetadataArray = unserialize($this['metadata']);
        foreach ($fileMetadataArray as $key => $value) {
            EfrontSearch :: insertText($value, $this['id'], "files", "data");
        }
        return $ok;
    }
    /**

     * Refresh object properties

     *

     * This function is used to refresh the object properties. It is useful

     * for when some function outside the object, has updated the object properties

     * This function does not apply to EfrontFile objects that don't have a database

     * representation

     * <br/>Example:

     * <code>

     * $file = new EfrontFile(432);																//Instantiate object for file with id 432

     * eF_updateTableData("files", array("original_name" => "new_name"), "id=".$file['id']);	//Change the file attributes without using the object. This way, the $file object becomes outdated

     * $file -> refresh();																		//Refresh $file properties

     * </code>

     *

     * @since 3.5.0

     * @access public

     */
    public function refresh() {
        if ($this['id'] != -1) {
            $result = eF_getTableData("files", "*", "id=".$this['id']);
            $this['path'] = G_ROOTPATH.$result[0]['path'];
            $this['description'] = $result[0]['description'];
            $this['groups_ID'] = $result[0]['groups_ID'];
            $this['access'] = $result[0]['access'];
            $this['shared'] = $result[0]['shared'];
            $this['metadata'] = $result[0]['metadata'];
        }
        $this['name'] = EfrontFile :: decode(basename($this['path']));
        $this['directory'] = dirname($this['path']);
        $this['extension'] = pathinfo($this['path'], PATHINFO_EXTENSION);
        $this['size'] = round(filesize($this['path'])/1024, 2);
        $this['timestamp'] = filemtime($this['path']);
        $this['type'] = 'file';
        $this['physical_name'] = basename($this['path']);
        foreach ($pathParts = explode("/", $this['path']) as $key => $value) {
            $pathParts[$key] = urlencode($value);
        }
        $this['url_path'] = implode("/", $pathParts);
    }
 /**

	 * Compress file

	 * 

     * @param string $zipName The name if the compressed file

     * @param boolean $decode Whether the file name should be decoded

     * @return EfrontFile The compressed file

     * @since 3.6.1

     * @access public

	 */
    public function compress($zipName = false, $decode = false) {
     if (!$zipName) {
            $zipName = $this['path'].'.zip';
        } else {
            $zipName = $this['directory'].'/'.(EfrontFile :: encode(basename($zipName)));
        }
        try { //This way we delete the file, if it already exists
            $file = new EfrontFile($zipName);
            $file -> delete();
        } catch (Exception $e) {}
        if ($GLOBALS['configuration']['zip_method'] == 'system') {
            $dir = getcwd();
            chdir($this['directory']);
            $response = exec('zip -r "'.$zipName.'" '.$this['name'].' 2>&1', $output, $code);
            chdir($dir);
            if ($code != 0) {
                throw new EfrontFileException(_COMMANDFAILEDWITHOUTPUT.': '.$response.". "._PERHAPSDONTSUPPORTZIP, EfrontFileException :: ERROR_ZIP_PROCESSING);
            }
            return new EfrontFile($zipName);
        } else {
            $zip = new ZipArchive;
            if ($zip -> open($zipName, ZIPARCHIVE::CREATE ) === true) {
                if ($decode) {
                 $zip -> addFile($this['path'], EfrontFile :: decode($this['name']));
                } else {
                 $zip -> addFile($this['path'], $this['name']);
                }
                $zip -> close();
                return new EfrontFile($zipName);
            } else {
                throw new EfrontFileException(_CANNOTOPENCOMPRESSEDFILE.': '.$this['path'], EfrontFileException :: ERROR_OPEN_ZIP);
            }
        }
    }
    /**

     * Uncompress file

     *

     * This function is used to uncompress the current file.

     * The uncompressed files will have a database representation, unless $addDb is set to false.

     * The function supports zip and tar.gz files

     * <br/>Example:

     * <code>

     * $file = new EfrontFile('/var/www/test.zip');

     * $uncompressedFiles = $file -> uncompress();

     * </code>

     *

     * @param  boolean $addDB Whether to create a database representation for the extracted files

     * @return array An array of EfrontFile objects or file paths (depending on wheter a database representation exists)

     * @since 3.5.0

     * @access public

     */
    public function uncompress($addDB = true) {
        if ($this['extension'] == 'zip') {
            if ($GLOBALS['configuration']['zip_method'] == 'system') {
                if ($GLOBALS['configuration']['file_black_list']) {
                    $blackList = '-x "*.'.implode('" "*.', explode(",", $GLOBALS['configuration']['file_black_list'])).'"';
                } else {
                    $blackList = '';
                }
                if ($GLOBALS['configuration']['file_white_list']) {
                    $whiteList = '"*.'.implode('" "*.', explode(",", $GLOBALS['configuration']['file_white_list'])).'"';
                } else {
                    $whiteList = '';
                }
                $response = exec('unzip "'.$this['path'].'" '.$whiteList.' '.$blackList.' -d "'.$this['directory'].'" 2>&1', $output, $code);
                if (stripos($response, 'caution') === false && $code != 0) {
                    throw new EfrontFileException(_COMMANDFAILEDWITHOUTPUT.': '.$response.". "._PERHAPSDONTSUPPORTZIP, EfrontFileException :: ERROR_ZIP_PROCESSING);
                }
            } else {
                $zip = new ZipArchive;
                if ($zip -> open($this['path']) === true && $zip -> extractTo($this['directory'])) {
                    for ($i = 0; $i < $zip -> numFiles; $i++) {
                        $file = $this['directory'].'/'.$zip -> getNameIndex($i);
                        try { //If the file is not allowed, then append to its extension '.ext'
                            FileSystemTree::checkFile($file);
                        } catch (EfrontFileException $e) {
                            $fileObj = new EfrontFile($file);
                            $fileObj -> rename($this['directory'].'/'.$zip -> getNameIndex($i).'.ext', true);
                            $file = $fileObj['path'];
                        }
                        $zipFiles[] = $file;
                    }
                    if ($this['id'] != -1 && $addDB) {
                        $importedFiles = FileSystemTree :: importFiles($zipFiles, $options);
                        return $importedFiles;
                    } else {
                        return $zipFiles;
                    }
                } else {
                    throw new EfrontFileException(_CANNOTOPENCOMPRESSEDFILE.': '.$this['path'], EfrontFileException :: ERROR_OPEN_ZIP);
                }
            }
        }
    }
    /**

     * List contents of compressed file

     * 

     * This function is used to list the contents of a compressed file. 

     * It returns an array with the file names contained in the archive

     * <br/>Example:

     * <code>

     * $file = new EfrontFile('mydata.zip');

     * $file -> listContents();	//Returns an array where values are the file names, along with their directory offset

     * </code>

     * 

     * @return array The contents of the compressed file 

     * @access public

     * @since 3.6.0

     */
    public function listContents() {
        if ($this['extension'] == 'zip') {
            $zipFiles = array();
            if ($GLOBALS['configuration']['zip_method'] == 'system') {
                //@todo: Implement for system calls as well
                $tempfile = tempnam(dirname($this['path']), time());
                $response = exec('unzip -qql "'.$this['path'].'" | awk \'{print $4}\' > "'.$tempfile.'" 2>&1', $output, $code);
                if ($code != 0) {
                    throw new EfrontFileException(_COMMANDFAILEDWITHOUTPUT.': '.$response.". "._PERHAPSDONTSUPPORTZIP, EfrontFileException :: ERROR_ZIP_PROCESSING);
                }
                foreach (file($tempfile) as $value) {
                    $zipFiles[] = trim($value);
                }
                unlink($tempfile);
                return $zipFiles;
            } else {
                $zip = new ZipArchive;
                if ($zip -> open($this['path'])) {
                    for ($i = 0; $i < $zip -> numFiles; $i++) {
                        $zipFiles[] = $zip -> getNameIndex($i);
                    }
                    return $zipFiles;
                } else {
                    throw new EfrontFileException(_CANNOTOPENCOMPRESSEDFILE.': '.$this['path'], EfrontFileException :: ERROR_OPEN_ZIP);
                }
            }
        } else {
            throw new EFrontFileException(_UNSUPPORTEDFILETYPE.': '.$this['extension'], EfrontFileException :: UNKNOWN_COMPRESSION);
        }
    }
    /**

     * Get the image for the file type

     *

     * This function returns the url to an image representing the current

     * file type.

     * <br/>Example:

     * <code>

     * echo $file -> getTypeImage();			//Returns something like 'images/16x16/zip.png' if it's a zip file

     * </code>

     *

     * @return string The url to the image representing the file type

     * @since 3.5.0

     * @access public

     */
    public function getTypeImage() {
        if (is_file(G_DEFAULTIMAGESPATH.'file_types/'.$this['extension'].'.png') || is_file(G_IMAGESPATH.'file_types/'.$this['extension'].'.png')) {
            $image = 'images/file_types/'.$this['extension'].'.png';
        } else {
            $image = 'images/file_types/unknown.png';
        }
        return $image;
    }
    /**

     * Share file

     *

     * This function is used to make the current file available to the lesson's

     * students. A file can be made available to a single lesson only.

     * <br/>Example:

     * <code>

     * $file = new EfrontFile(43);

     * $file -> share();							//The file is now visible to the shared files list

     * $file -> unshare();							//The file was made hidden again

     * </code>

     *

     * @param int $lessonId A specific lesson to share this file for

     * @since 3.5.0

     * @access public

     */
    public function share($lessonId = false) {
        if (!$lessonId) {
            $lessonId = $_SESSION['s_lessons_ID'];
        }
        if ($lessonId) {
            if ($this['id'] == -1) { //If the file does not have a database representation, create one for it
                $newList = FileSystemTree :: importFiles($this['path']);
                $this['id'] = key($newList);
                $this -> refresh();
            }
            $this['shared'] = $lessonId;
            $this -> persist();
        } else {
            throw new EfrontFileException(_CANNOTSHAREFILE.': '.$this['path'], EfrontFileException :: NOT_LESSON_FILE);
        }
    }
    /**

     * Unshare file

     *

     * This function is used to make the current file unavailable to the lesson's

     * students. It must belong to a lesson (that is, it must have a lesson id)

     * in order to do so.

     * <br/>Example:

     * <code>

     * $file = new EfrontFile(43);

     * $file -> share();							//The file is now visible to the shared files list

     * $file -> unshare();							//The file was made hidden again

     * </code>

     *

     * @since 3.5.0

     * @access public

     */
    public function unshare() {
        $this['shared'] = 0;
        $this -> persist();
    }
    /**

     * Print a link with tooltip

     *

     * This function is used to print a file link with a popup tooltip

     * containing information on this file. The link must be provided

     * and optionally the information.

     * <br/>Example:

     * <code>

     * $link = 'view_file.php?file=23';

     * echo $file -> toHTMLTooltipLink($link);

     * </code>

     *

     * @param string $link The link to print

     * @param boolean $preview Whether to display link in a preview panel

     * @since 3.5.0

     * @access public

     */
    public function toHTMLTooltipLink($link, $preview = true) {
        $classes[] = 'info'; //This array holds the link css classes
        if (!$link) {
            $link = 'javascript:void(0)';
            $classes[] = 'inactiveLink';
        }
        $tooltipString = '
            <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;" '.($preview ? 'onclick = "eF_js_showDivPopup(\''._PREVIEW.'\', 2, \'preview_table_'.$tableId.'\')" target = "PREVIEW_FRAME"' : '').'>
                '.$this -> offsetGet('name').'
                <img class = "tooltip" border = "0" src="images/others/tooltip_arrow.gif"/><span class = "tooltipSpan">';
        foreach ($this as $key => $value) {
            if ($value) {
                switch ($key) {
                    //case 'path'        : $tooltipString .= '<div style = "white-space:nowrap"><strong>'._PHYSICALNAME."</strong>: ".basename($value)."<br/></div>";  break;
                    case 'users_LOGIN' : $tooltipString .= '<strong>'._USER."</strong>: $value<br/>"; break;
                    case 'timestamp' : $tooltipString .= '<strong>'._LASTMODIFIED."</strong>: ".formatTimestamp($value, 'time_nosec')."<br/>"; break;
                    //case 'shared'      : $tooltipString .= '<strong>'._SHARED."</strong>: $value<br/>";    break;
                    case 'mime_type' : $tooltipString .= '<strong>'._MIMETYPE."</strong>: $value<br/>"; break;
                    default: break;
                }
            }
        }
        $tooltipString .= '</span></a>';
        return $tooltipString;
    }
    /**

     * Encode file name

     *

     * This function is used to encode the given name, based on the current

     * configuration options.

     * <br/>Example:

     * <code>

     * $name    = 'some name';							//The name to encode

     * $newName = EfrontFile :: encode($name);			//Encodeded version of name

     * </code>

     * A little word about the need of encoding:

     * Throughut eFront UTF-8 is used as encoding. When uploading a file, for example, its name is encoded

     * in UTF-8, and with this name is stored in the filesystem. This does not cause any problems, when the

     * OS is UTF8-aware, for example in most Linux distributions. However, for Windows installations, this

     * causes major side-effects: The file name is messed up. On the other hand, when trying to access the file,

     * the encoding is still in UTF8, so that many browsers, for example FireFox, have no problem in accessing

     * the file, using its initial, UTF8-encoded, correct name. Unfortunately, Internet Explorer (6,7) cannot access

     * the file at all.

     * So when using a windows server, we must encode non-latin characters in order to be able to access any

     * uploaded files with international characters. The most (if not the only) convenient encoding is UTF7-IMAP, which is a

     * version of UTF-7 without the filesystem incompatible characters (see http://tools.ietf.org/html/rfc3501#section-5.1.3)

     * If we are sure that only the native windows language will be used for file names, there is a somewhat better solution

     * than using UTF7-IMAP (which scrambles the file names in the file system). We could use the native windows encoding.

     * For example, for greek, the native windows encoding is windows-1253 (ISO-8859-7 is also supported). So, instead of

     * UTF7-IMAP, we select this encoding and everything works like a charm. Except for filenames with characters other

     * than latin and greek, of course.

     *

     * @param string $name The filename to encode

     * @return string The encoded file name

     * @since 3.5.0

     * @access public

     * @static

     */
    public static function encode($name) {
        $newName = $name;
        if ($GLOBALS['configuration']['file_encoding']) {
            if (in_array($GLOBALS['configuration']['file_encoding'], mb_list_encodings())) {
                $newName = mb_convert_encoding($name, $GLOBALS['configuration']['file_encoding'], "UTF-8");
            } else {
                $newName = mb_convert_encoding($name, "UTF7-IMAP", "UTF-8");
            }
        }
        return $newName;
    }
    /**

     * Decode filename

     *

     * This function is the opposite of encode() and is used to convert a file name

     * back to UTF8

     * <br/>Example:

     * <code>

     * $name = EfrontFile :: decode($encodedName);

     * </code>

     *

     * @param string $name The encoded name

     * @return string The decoded name

     * @since 3.5.0

     * @access public

     * @static

     * @see EfrontFile :: encode()

     */
    public static function decode($name) {
        $newName = $name;
        if ($GLOBALS['configuration']['file_encoding']) {
            if (in_array($GLOBALS['configuration']['file_encoding'], mb_list_encodings())) {
                $newName = mb_convert_encoding($name, "UTF-8", $GLOBALS['configuration']['file_encoding']);
            } else {
                $newName = mb_convert_encoding($name, "UTF-8", "UTF7-IMAP");
            }
        }
        return $newName;
    }
}
/**

 * Class for directories in Efront file system

 *

 * @since 3.5.0

 * @package eFront

 */
class EfrontDirectory extends ArrayObject
{
    /**

     * Class constructor

     *

     * The class constructor instantiates the object based on the $directory parameter.

     * $directory may be either:

     * - an array with directory attributes

     * - a directory id

     * - the full path to a physical directory

     * - the full path to a directory using its original directory name

     * <br/>Example:

     * <code>

     * $result = eF_getTableData("files", "*", "id=43");

     * $file = new EfrontDirectory($result[0]);                          //Instantiate object using array of values

     * $file = new EfrontDirectory(43);                                  //Instantiate object using id

     * $file = new EfrontDirectory('/var/www/32/');                      //Instantiate object using path

     * </code>

     *

     * @param mixed $directory The directory information, either an array, an id or a path string

     * @since 3.5.0

     * @access public

     */
    function __construct($directory) {
        $directory = EfrontDirectory :: normalize($directory);
        if (is_dir($directory) && strpos($directory, rtrim(G_ROOTPATH, "/")) !== false) { //Create object without database information
            $directoryArray = array('path' => $directory,
                                    'name' => EfrontFile :: decode(basename($directory)),
                                    'directory' => dirname($directory),
                                    'timestamp' => filemtime($directory),
                                    'type' => 'directory',
                                    'physical_name' => basename($directory));
            foreach ($pathParts = explode("/", $directoryArray['path']) as $key => $value) {
                $pathParts[$key] = urlencode($value);
            }
            $directoryArray['url_path'] = implode("/", $pathParts);
        } else if (strpos($directory, rtrim(G_ROOTPATH, "/")) === false) {
            throw new EfrontFileException(_ILLEGALPATH.': '.$directory, EfrontFileException :: ILLEGAL_PATH);
        } else {
            throw new EfrontFileException(_DIRECTORYDOESNOTEXIST.': '.$directory, EfrontFileException :: DIRECTORY_NOT_EXIST);
        }
        parent :: __construct($directoryArray); //Create an ArrayObject from the given array
    }
    /**

     * Normalize path

     *

     * This function is used to normalize a path string. It applies

     * realpath() to translate relevant paths, converts \ to / and trims

     * trailing /

     * <br/>Example:

     * <code>

     * $path = '..\..\..\test.php\';

     * echo EfrontDirectory :: normalize($path);	//Outputs c:/test.php

     * </code>

     *

     * @param string $path The path to normalize

     * @return string The normalized path

     * @since 3.5.0

     * @access public

     */
    public static function normalize($path) {
        $realPath = realpath($path);
        if ($realPath && file_exists($realPath)) {
            if (DIRECTORY_SEPARATOR == "\\") {
                return rtrim(str_replace("\\", "/", $realPath), "/");
            } else {
                return rtrim($realPath, "/");
            }
        } else {
            if (DIRECTORY_SEPARATOR == "\\") {
                return rtrim(str_replace("\\", "/", $path), "/");
            } else {
                return rtrim($path, "/");
            }
        }
    }
    /**

     * Rcursively delete directory

     *

     * This function recursively deletes the directory.

     * <br/>Example:

     * <code>

     * $directory = new EfrontDirectory(34);                     //Instantiate directory

     * $directory -> delete();                                   //Delete directory

     * </code>

     *

     * @since 3.5.0

     * @access public

     */
    public function delete() {
        $it = new EfrontREFilterIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this['path']), RecursiveIteratorIterator :: SELF_FIRST), array('/.svn/'), false);
        $files = array();
        $result = eF_getTableData("files", "*", "path like '".str_replace(G_ROOTPATH, '', $this['path'])."%'");
        foreach ($result as $file) {
            $files[G_ROOTPATH.$file['path']] = $file;
        }
        foreach ($it as $node => $value) {
            if ($value -> isFile()) {
                $current = str_replace("\\", "/", $node);
                if (isset($files[$current])) {
                    $current = new EfrontFile($files[$current]);
                } else {
                    $fileArray = array('id' => -1, //Set 'id' to -1, meaning this file has not a database representation
                                       'path' => $value -> getPathName());
                    $current = new EfrontFile($fileArray);
                }
                $current -> delete();
            }
        }
        $directories[] = $this['path']; //Append current directory to the beginning of the array
        foreach ($it as $node => $value) {
            if ($value -> isDir()) {
                $directories[] = str_replace("\\", "/", $node);
            }
        }
        unset($it); //The iterator keeps an open handle to the directory, so it must be unset beore we delete the current directory
        $directories = array_reverse($directories); //Reverse directories order, so that they are deleted from the innermost to the outermost
        foreach ($directories as $key => $value) {
            rmdir($value);
        }
    }
    /**

     * Copy directory

     *

     * This function is used to recursively copy the current directory to a new destination.

     * <br/>Example:

     * <code>

     * $directory = new EfrontDirectory(43);                            //Instantiate directory object

     * $directory -> copy('/var/www/');                                 //Copy directory to /var/www/

     * </code>

     *

     * @param string $destinationPath The destination directory

     * @param boolean $overwrite Whether to overwrite existing files/directories in the destination

     * @return EfrontDirectory The copied directory

     * @since 3.5.0

     * @access public

     */
    public function copy($destinationPath, $overwrite = false) {
        $destinationPath = EfrontDirectory :: normalize($destinationPath);
        $parentDirectory = new EfrontDirectory(dirname($destinationPath)); //This way we check integrity of destination
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755);
        } elseif (!$overwrite) {
            throw new EfrontFileException(_CANNOTCOPYDIRECTORY.': '.$destinationPath.', '._FILEALREADYEXISTS, EfrontFileException :: DIRECTORY_ALREADY_EXISTS);
        }
        $it = new EfrontREFilterIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this['path']), RecursiveIteratorIterator :: SELF_FIRST), array('/.svn/'), false);
        foreach ($it as $node => $value) {
            if ($value -> isDir()) {
                $current = str_replace("\\", "/", $node);
                $newDir = str_replace($this['path'], $destinationPath, $current);
                mkdir($newDir, 0755);
            }
        }
        foreach ($it as $node => $value) {
            if ($value -> isFile()) {
                $current = str_replace("\\", "/", $node);
                $newPath = str_replace($this['path'], $destinationPath, $current);
                $file = new EfrontFile($node);
                $file -> copy($newPath, $overwrite);
            }
        }
        $newDirectory = new EfrontDirectory($destinationPath);
        return $newDirectory;
    }
    /**

     * Rename / Move directory

     *

     * This function is used to rename and/or move the directory. The destinationPath must contain the same name

     * if it is going to be moved only, or a new name if it is going to be renamed also

     * directory after copying it.

     * <br/>Example:

     * <code>

     * $directory = new EfrontDirectory(43);                             //Instantiate directory object

     * $directory -> rename('/var/www/');                                  //Move directory to /var/www/

     * </code>

     *

     * @param mixed $destinationPath The destination directory

     * @return EfrontDirectory The renamed/moved directory

     * @since 3.5.0

     * @access public

     * @see copy()

     */
    public function rename($destinationPath, $overwrite = false) {
        $destinationPath = EfrontDirectory :: normalize($destinationPath);
        $parentDirectory = new EfrontDirectory(dirname($destinationPath)); //This way we check integrity of destination
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755);
        } elseif (!$overwrite) {
            throw new EfrontFileException(_CANNOTCOPYDIRECTORY.': '.$destinationPath.', '._FILEALREADYEXISTS, EfrontFileException :: DIRECTORY_ALREADY_EXISTS);
        }
        $it = new EfrontREFilterIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this['path']), RecursiveIteratorIterator :: SELF_FIRST), array('/.svn/'), false);
        foreach ($it as $node => $value) {
            if ($value -> isDir()) {
                $current = str_replace("\\", "/", $node);
                $newDir = str_replace($this['path'], $destinationPath, $current);
                mkdir($newDir, 0755);
            }
        }
        foreach ($it as $node => $value) {
            if ($value -> isFile()) {
                $current = str_replace("\\", "/", $node);
                $newPath = str_replace($this['path'], $destinationPath, $current);
                $file = new EfrontFile($node);
                $file -> rename($newPath, $overwrite);
            }
        }
        unset($it);
        rmdir($this['path']);
        $directory = new EfrontDirectory($destinationPath);
        $this['name'] = $directory['name'];
        $this['path'] = $directory['path'];
        $this['directory'] = $directory['directory'];
        $this['timestamp'] = $directory['timestamp'];
        $this['physical_name'] = $directory['physical_name'];
    }
    /**

     * Compress directory

     *

     * This function is used to compress (to zip format) the current directory.

     * It creates a zip file with the specified name, or the same

     * name as the directory iteself, if the parameter $zipName is

     * ommited

     * <br/>Example:

     * <code>

     * $directory = new EfrontDirectory('/var/www/efront/www/content/lessons/32/test_folder');

     * $file 	  = $directory -> compress();						//This will create a file named 'test_folder.zip' inside the directory

     * </code>

     *

     * @param string $zipName The name if the compressed file

     * @param boolean $includeSelf Whether to include itself to the zip file

     * @param boolean $decode Whether the file name should be decoded

     * @return EfrontFile The compressed file

     * @since 3.5.0

     * @access public

     */
    public function compress($zipName = false, $includeSelf = true, $decode = false) {
        if (!$zipName) {
            $zipName = $this['path'].'.zip';
        } else {
            $zipName = $this['directory'].'/'.(EfrontFile :: encode(basename($zipName)));
        }
        try { //This way we delete the file, if it already exists
            $file = new EfrontFile($zipName);
            $file -> delete();
        } catch (Exception $e) {}
        if ($GLOBALS['configuration']['zip_method'] == 'system') {
            $dir = getcwd();
            chdir($this['path']);
            $response = exec('zip -r "'.$zipName.'" ./* 2>&1', $output, $code);
            chdir($dir);
            if ($code != 0) {
                throw new EfrontFileException(_COMMANDFAILEDWITHOUTPUT.': '.$response.". "._PERHAPSDONTSUPPORTZIP, EfrontFileException :: ERROR_ZIP_PROCESSING);
            }
            return new EfrontFile($zipName);
        } else {
            $zip = new ZipArchive;
            if ($zip -> open($zipName, ZIPARCHIVE::CREATE ) === true) {
                $count = 0;
                $it = new EfrontREFilterIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this['path']), RecursiveIteratorIterator :: SELF_FIRST), array('/.svn/'), false);
                foreach ($it as $node => $value) {
                    if ($value -> isFile()) {
                        $current = str_replace("\\", "/", $node);
                        if ($includeSelf) {
                            $nameInZip = ltrim(str_replace(dirname($this['path']), '', $current), '/');
                        } else {
                            $nameInZip = ltrim(str_replace($this['path'], '', $current), '/');
                        }
                        //pr($current);pr($nameInZip);
                        if ($decode) {
                            $zip -> addFile($node, EfrontFile :: decode($nameInZip));
                        } else {
                            $zip -> addFile($node, $nameInZip);
                        }
                        if ($count++ > 500) { //See bug http://pecl.php.net/bugs/bug.php?id=8714
                            $zip -> close();
                            $zip -> open($zipName, ZIPARCHIVE::CREATE);
                            $count = 0;
                        }
                    }
                }
                $zip -> close();
                return new EfrontFile($zipName);
            } else {
                throw new EfrontFileException(_CANNOTOPENCOMPRESSEDFILE.': '.$this['path'], EfrontFileException :: ERROR_OPEN_ZIP);
            }
        }
    }
    /**

     * Create directory

     *

     * This function is used to create a new directory along with its

     * database representation (as long as $addDB is not false)

     * <br/>Example:

     * <code>

     * EfrontDirectory :: createDirectory('/var/www/efront/www/content/lessons/32/new directory');

     * </code>

     *

     * @param string $fullPath The full path to the new directory

     * @param string $addDB Whether to create database represenation for the new directory, defaults to true

     * @return boolean true if everything is ok

     * @since 3.5.0

     * @access public

     */
    public static function createDirectory($fullPath) {
        $fullPath = EfrontFile :: encode(EfrontDirectory :: normalize($fullPath));
        $parentDirectory = new EfrontDirectory(dirname($fullPath));
        if (is_dir($fullPath)) {
            throw new Exception(_COULDNOTCREATEDIRECTORY.': '.$fullPath.', '._DIRECTORYALREADYEXISTS, EfrontFileException :: DIRECTORY_ALREADY_EXISTS);
        }
        if (mkdir($fullPath, 0755)) {
            $newDirectory = new EfrontDirectory($fullPath);
            return $newDirectory;
        } else {
            throw new Exception(_COULDNOTCREATEDIRECTORY.': '.$fullPath, EfrontFileException :: CANNOT_CREATE_DIR);
        }
    }
    /**

     * Get the image for the directory

     *

     * This function returns the url to an image representing the directory.

     * similar to EfrontFile :: getTypeImage().

     * <br/>Example:

     * <code>

     * echo $directory -> getTypeImage();			//Returns something like 'images/16x16/file_explorer.png'

     * </code>

     *

     * @return string The url to the image representing the directory

     * @since 3.5.0

     * @access public

     */
    public function getTypeImage() {
        $image = 'images/file_types/folder.png';
        return $image;
    }
    /**

     * Print a link with tooltip

     *

     * This function is used to print a directory link with a popup tooltip

     * containing information on this directory. The link must be provided

     * and optionally the information.

     * <br/>Example:

     * <code>

     * echo $directory -> toHTMLTooltipLink();

     * </code>

     *

     * @param string $link The link to print

     * @since 3.5.0

     * @access public

     */
    public function toHTMLTooltipLink($link) {
        $classes[] = 'info'; //This array holds the link css classes
        if (!$link) {
            $link = 'javascript:void(0)';
            $classes[] = 'inactiveLink';
        }
        $tooltipString = '
            <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;">
                '.$this -> offsetGet('name').'
                <img class = "tooltip" border = "0" src="images/others/tooltip_arrow.gif"/><span class = "tooltipSpan">';
        foreach ($this as $key => $value) {
            if ($value) {
                switch ($key) {
                    case 'path' : $tooltipString .= '<div style = "white-space:nowrap"><strong>'._FULLPATH."</strong>: $value</div>"; break;
                    case 'users_LOGIN' : $tooltipString .= '<strong>'._USER."</strong>: $value<br/>"; break;
                    case 'timestamp' : $tooltipString .= '<strong>'._LASTMODIFIED."</strong>: ".formatTimestamp($value, 'time_nosec')."<br/>"; break;
                    default: break;
                }
            }
        }
        $tooltipString .= '</span></a>';
        return $tooltipString;
    }
}
/**

 * File system tree

 *

 * This class represents the file system tree, with directories being

 * branches or leafs and files being only leafs

 * @since 3.5.0

 * @package eFront

 * @author Venakis Periklis <pvenakis@efront.gr>

 */
class FileSystemTree extends EfrontTree
{
    /**

     * The tree's root directory

     *

     * @var string

     * @since 3.5.0

     * @access protected

     */
    protected $dir = '';
    /* Initialize tree

     *

     * This function is used to initialize the file system tree

     * <br/>Example:

     * <code>

     * $fileSystemTree = new FileSystemTree();

     * </code>

     *

     * @param string $dir The root directory for the filesystem tree

     * @since 3.5.0

     * @access public

     */
    function __construct($dir = G_ROOTPATH, $shallow = false) {
        //pr($dir);echo "1";
        if (!($dir instanceof EfrontDirectory)) {
            $dir = new EfrontDirectory($dir);
        }
        //pr($dir);echo "2";
        if (!is_dir($dir['path'])) {
            throw new EfrontFileException(_DIRECTORYDOESNOTEXIST.': '.$dir['path'], EfrontFileException :: DIRECTORY_NOT_EXIST);
        }
        $this -> dir = $dir;
        if ($shallow) {
            $this -> iterator = new EfrontREFilterIterator(new RecursiveDirectoryIterator($this -> dir['path']), array('/.svn/', '/.htaccess/'), false);
        } else {
            $this -> iterator = new EfrontREFilterIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this -> dir['path']), RecursiveIteratorIterator :: SELF_FIRST), array('/.svn/', '/.htaccess/'), false);
        }
        $this -> reset();
    }
    /**

     * Insert node to the tree

     *

     * This function is not used by this EfrontTree implementation,

     * so it always returns false

     *

     * @param EfrontFile $node

     * @param int $parentNode

     * @param int $previousNode

     * @return boolean Always false

     * @since 3.5.0

     * @access public

     */
    public function insertNode($node, $parentNode = false, $previousNode = false) {
        return false;
    }
    /**

     * Remove node from tree

     *

     * This function is not used by this EfrontTree implementation,

     * so it always returns false

     *

     * @param EfrontFile $node

     * @return boolean Always false

     * @since 3.5.0

     * @access public

     */
    public function removeNode($node) {
        return false;
    }
    /**

     * Reset filesystem tree

     *

     * This function is used to reset (or initially set) the filesystem

     * tree to its original state. The function is normally called by the

     * constructor.

     * <br/>Example:

     * <code>

     * $tree -> reset();

     * </code>

     *

     * @todo must add a "RECURSIVE" parameter, since the "isFile" fs call is *very* slow. There is no need to call it if we don't need a recursive search (as in delete for example)

     * @since 3.5.0

     * @access public

     */
    public function reset() {
        //Get all files that are within the designated directory
        $result = eF_getTableData("files", "*", "path like '".str_replace(G_ROOTPATH, "", $this -> dir['path'])."%'");
        foreach ($result as $key => $file) {
            $file['path'] = G_ROOTPATH.$file['path'];
            $files[$file['path']] = $file;
            unset($result[$key]); //Releasing memory
        }
        $it = $this -> iterator;
        //$it = new EfrontREFilterIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this -> dir['path']), RecursiveIteratorIterator :: SELF_FIRST), array('/.svn/', '/.htaccess/'), false);
        //$it = (new RecursiveDirectoryIterator($this -> dir['file']));
        $nodes = array();
        foreach ($it as $node => $value) {
            $current = str_replace("\\", "/", $node);
            //Instantiate file/directory object. We are using an approach that doesn't require any database queries
            if (isset($files[$current])) {
                try {
                    $nodes[$current] = new EfrontFile($files[$current]);
                } catch (EfrontFileException $e) {
                    //Don't halt for illegal file arrays; The EfrontFile class constructor handles them properly in the database
                }
            } else {
                if ($value -> isFile()) {
                    $fileArray = array('id' => -1, //Set 'id' to -1, meaning this file/directory has not a database representation
                                       'path' => $current);
                    $nodes[$current] = new EfrontFile($fileArray);
                } else {
                    $nodes[$current] = new EfrontDirectory($current);
                }
            }
        }
        $parentNode = $this -> dir['path'];
        $rejected = array();
        $tree = $nodes;
        $count = 0; //$count is used to prevent infinite loops
        while (sizeof($tree) > 1 && $count++ < 1000) { //We will merge all branches under the main tree branch, the 0 node, so its size will become 1
            foreach ($nodes as $key => $value) {
                if ($value['directory'] == $parentNode || in_array($value['directory'], array_keys($nodes))) { //If the unit parent (directory) is in the $nodes array keys - which are the unit ids- or it is 0, then it is  valid
                    $parentNodes[$value['directory']][] = $value; //Find which nodes have children and assign them to $parentNodes
                    $tree[$value['directory']][$value['path']] = array(); //We create the "slots" where the node's children will be inserted. This way, the ordering will not be lost
                } else {
                    $rejected = $rejected + array($value['path'] => $value); //Append units with invalid parents to $rejected list
                    unset($nodes[$key]); //Remove the invalid unit from the units array, as well as from the parentUnits, in case a n entry for it was created earlier
                    unset($parentNodes[$value['directory']]);
                }
            }
            if (isset($parentNodes)) { //If the unit was rejected, there won't be a $parentNodes array
                $leafNodes = array_diff(array_keys($nodes), array_keys($parentNodes)); //Now, it's easy to see which nodes are leaf nodes, just by subtracting $parentNodes from the whole set
                foreach ($leafNodes as $leaf) {
                    $parent_id = $nodes[$leaf]['directory']; //Get the leaf's parent
                    $tree[$parent_id][$leaf] = $tree[$leaf]; //Append the leaf to its parent's tree branch
                    unset($tree[$leaf]); //Remove the leaf from the main tree branch
                    unset($nodes[$leaf]); //Remove the leaf from the nodes set
                }
                unset($parentNodes); //Reset $parentNodes; new ones will be calculated at the next loop
            }
        }
        if (sizeof($tree) > 0 && !isset($tree[$this -> dir['path']])) { //This is a special case, where only one node exists in the tree
            $tree = array($this -> dir['path'] => $tree);
        }
        if (sizeof($rejected) > 0) { //Append rejected nodes to the end of the tree array, updating their parent/previous information
            foreach ($rejected as $key => $value) {
                //eF_updateTableData("directions", array("parent_direction_ID" => 0), "id=".$key);
                //$value['parent_direction_ID'] = 0;
                //$tree[0][] = $value;
            }
        }
        if (sizeof($tree) > 0) {
            $this -> tree = new RecursiveArrayIterator($tree[$this -> dir['path']]);
        } else {
            $this -> tree = new RecursiveArrayIterator(array());
        }
        //echo"<pre>";debug_print_backtrace();exit;
    }
    /**

     * Get an upload form

     *

     * This function is responsible for creating an "upload file"

     * form, as well as the equivalent HTML code.

     * <br/>Example:

     * <code>

     * $basedir    = G_LESSONSPATH.'test/';

     * $filesystem = new FileSystemTree($basedir);									//Set the base directory that the file manager displayes

     * $url        = 'administrator.php?ctg=file_manager';			//Set the url where file manager resides

     * $uploadForm         = new HTML_QuickForm("upload_file_form", "post", $url, "", "", true);

     * $uploadFormString   = $filesystem -> getUploadForm($uploadForm);

     * echo $uploadFormString;

     * </code>

     *

     * @param HTML_QuickForm $form The form to populate

     * @return string The HTML code of the form

     * @since 3.5.0

     * @access public

     */
    public function getUploadForm(& $form) {
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
        $form -> addElement('file', 'file_upload[0]', null, 'class = "inputText"');
        $form -> addElement('file', 'file_upload[1]', null, 'class = "inputText"');
        $form -> addElement('file', 'file_upload[2]', null, 'class = "inputText"');
        $form -> addElement('file', 'file_upload[3]', null, 'class = "inputText"');
        $form -> addElement('file', 'file_upload[4]', null, 'class = "inputText"');
        $form -> addElement('file', 'file_upload[5]', null, 'class = "inputText"');
        $form -> addElement('file', 'file_upload[6]', null, 'class = "inputText"');
        $form -> addElement('text', 'url_upload', null, 'id = "url_upload" class = "inputText"');
        $form -> addElement('hidden', 'upload_current_directory', null, 'id = "upload_current_directory" class = "inputText"');
        $form -> addElement('submit', 'submit_upload_file', _UPLOAD, 'class = "flatButton" onclick = "$(\'uploading_image\').show()"');
        $form -> setMaxFileSize($this -> getUploadMaxSize() * 1024); //getUploadMaxSize returns size in KB
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> accept($renderer);
        $formArray = $renderer -> toArray();
        $formString = '
        '.$formArray['javascript'].'
        <form '.$formArray['attributes'].'>
        '.$formArray['hidden'].'
        <table width = "100%">
               <tr><td class = "labelCell">'._UPLOADFILE.':&nbsp;</td>
                <td class = "elementCell">'.$formArray['file_upload'][0]['html'].'</td></tr>
               <tr style = "display:none"><td class = "labelCell">'._UPLOADFILE.':&nbsp;</td>
                <td class = "elementCell">'.$formArray['file_upload'][1]['html'].'</td></tr>
               <tr style = "display:none"><td class = "labelCell">'._UPLOADFILE.':&nbsp;</td>
                <td class = "elementCell">'.$formArray['file_upload'][2]['html'].'</td></tr>
               <tr style = "display:none"><td class = "labelCell">'._UPLOADFILE.':&nbsp;</td>
                <td class = "elementCell">'.$formArray['file_upload'][3]['html'].'</td></tr>
               <tr style = "display:none"><td class = "labelCell">'._UPLOADFILE.':&nbsp;</td>
                <td class = "elementCell">'.$formArray['file_upload'][4]['html'].'</td></tr>
               <tr style = "display:none"><td class = "labelCell">'._UPLOADFILE.':&nbsp;</td>
                <td class = "elementCell">'.$formArray['file_upload'][5]['html'].'</td></tr>
               <tr style = "display:none"><td class = "labelCell">'._UPLOADFILE.':&nbsp;</td>
                <td class = "elementCell">'.$formArray['file_upload'][6]['html'].'</td></tr>
               <tr><td></td>
                <td class = "elementCell">
                 <img src = "images/16x16/add.png" alt = "'._ADDFILE.'" title = "'._ADDFILE.'" onclick = "addUploadBox(this)"/></td></tr>
               <tr><td></td>
                <td class = "infoCell"><span id="messageError" class = "severeWarning"></span></td></tr>
      <tr><td></td>
                <td class = "infoCell">'._MAXIMUMUPLOADSIZE.': '.($this -> getUploadMaxSize()).' '._KB.'</td></tr>
               <tr><td class = "labelCell">'._UPLOADFILEFROMURL.':&nbsp;</td>
                <td class = "elementCell">'.$formArray['url_upload']['html'].'</td></tr>
               <tr><td></td>
                <td class = "submitCell">
                 '.$formArray['submit_upload_file']['html'].'
                </td></tr>
              </table>
              </form>
              <img src = "images/others/progress_big.gif" id = "uploading_image" title = "'._UPLOADING.'" alt = "'._UPLOADING.'" style = "display:none;margin-left:auto;margin-right:auto;margin-top:30px;vertical-align:middle;"/>';
        return $formString;
    }
    /**

     * Handle upload form

     *

     * This function is used to perform all the actions necessary for when uploading a file

     * from within the file manager

     * <br/>Example:

     * <code>

     *   $uploadForm       = new HTML_QuickForm("upload_file_form", "post", $url, "", "", true);

     *   if ($uploadForm -> isSubmitted() && $uploadForm -> validate()) {

     *       $uploadedFile = $this -> handleUploadForm($uploadForm);

     *   }

     *

     * </code>

     *

     * @param HTML_QuickForm $form The form used to upload the file

     * @return EfrontFile The uploaded file

     * @since 3.5.0

     * @access public

     */
    public function handleUploadForm(& $form) {
        if ($form -> exportValue('upload_current_directory')) {
            $curDir = EfrontDirectory :: normalize($form -> exportValue('upload_current_directory'));
        } else {
            $curDir = $this -> dir['path'];
        }
        if (strpos($curDir, $this -> dir['path']) !== false) {
            foreach ($_FILES['file_upload']['error'] as $key => $value) {
                if ($value == 0) {
                    $uploadedFile = $this -> uploadFile('file_upload', $curDir, $key);
                }
            }
            $this -> reset();
        }
        $urlUpload = $form -> exportValue('url_upload');
        if ($urlUpload != "" ) {
            $this -> checkFile($urlUpload);
            $urlArray = explode("/", $urlUpload);
            $urlFile = urldecode($urlArray[sizeof($urlArray) - 1]);
            //copy() does not like names with spaces, so we split the $urlUpload to dirname() and basename() and we urlencode() the latter
            if (!copy(dirname($urlUpload).'/'.rawurlencode(basename($urlUpload)), $curDir."/".$urlFile)) {
                throw new Exception(_PROBLEMUPLOADINGFILE);
            }else{
                $uploadedFile = new EfrontFile($curDir."/".$urlFile);
            }
        }
        return $uploadedFile;
    }
    /**

     * Get the create directory form

     *

     * This function is responsible for creating the "create directory"

     * form, as well as the equivalent HTML code.

     *

     * @param HTML_QuickForm $form The form to populate

     * @return string The HTML code of the form

     * @since 3.5.0

     * @access protected

     */
    protected function getCreateDirectoryForm(& $form) {
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
        $form -> addElement('text', 'create_directory', null, 'class = "inputText"');
        $form -> addElement('hidden', 'current_directory', null, 'id = "current_directory" class = "inputText"');
        $form -> addElement('submit', 'submit_create_directory', _CREATE, 'class = "flatButton"');
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> accept($renderer);
        $formArray = $renderer -> toArray();
        $formString = '
        '.$formArray['javascript'].'
        <form '.$formArray['attributes'].'>
        '.$formArray['hidden'].'
        <table width = "100%">
               <tr><td class = "labelCell">'._FOLDERNAME.':&nbsp;</td>
                <td class = "elementCell">'.$formArray['create_directory']['html'].'</td></tr>
               <tr><td></td>
                <td class = "elementCell">
                 '.$formArray['submit_create_directory']['html'].'
                </td></tr>
              </table>
              </form>';
        return $formString;
    }
    /**

     * Handle upload form

     *

     * This function is used to perform all the actions necessary for when creating a directory

     * from within the file manager

     * <br/>Example:

     * <code>

     *   $createFolderForm   = new HTML_QuickForm("create_folder_form", "post", $url, "", null, true);

     *   if ($createFolderForm -> isSubmitted() && $createFolderForm -> validate()) {

     *       $this -> handleCreateDirectoryForm($createFolderForm);

     *   }

     *

     * </code>

     *

     * @param HTML_QuickForm $form The form used to create the directory

     * @return EfrontDirectory The created directory

     * @since 3.5.0

     * @access public

     */
    protected function handleCreateDirectoryForm(& $form) {
        $newDir = basename(EfrontDirectory :: normalize($form -> exportValue('create_directory')));
        if ($form -> exportValue('current_directory')) {
            $curDir = EfrontDirectory :: normalize($form -> exportValue('current_directory'));
        } else {
            $curDir = $this -> dir['path'];
        }
        if (strpos($curDir, $this -> dir['path']) !== false) {
            $createdDirectory = EfrontDirectory :: createDirectory($curDir.'/'.$newDir);
            $this -> reset();
        }
        return $createdDirectory;
    }
    /**

     * Get the copy form

     *

     * This function is responsible for creating the "create directory"

     * form, as well as the equivalent HTML code.

     *

     * @param HTML_QuickForm $form The form to populate

     * @return string The HTML code of the form

     * @since 3.5.0

     * @access protected

     */
    /*

     protected function getCopyForm(& $form) {

     $form -> addElement('hidden', 'copy_files', null, 'class = "inputText" id = "copy_files"');

     $form -> addElement('hidden', 'copy_current_directory', null, 'id = "copy_current_directory" class = "inputText"');

     $form -> addElement('select', 'action', null, array('copy' => _COPY, 'move' => _MOVE));

     $form -> addElement("advcheckbox", "overwrite", _OVERWRITE, null, 'class = "inputCheckBox"', array(0, 1));

     $form -> addElement('submit', 'submit_copy_file', _EXECUTE, 'class = "flatButton" onclick = "getSelected();"');



     $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

     $form -> accept($renderer);

     $formArray = $renderer -> toArray();



     $formString = '

     '.$formArray['javascript'].'

     <form '.$formArray['attributes'].'>

     '.$formArray['hidden'].'

     <table>

     <tr><td class = "labelCell">'._YOUWANTTOBEDONE.':&nbsp;</td>

     <td class = "elementCell">'.$formArray['action']['html'].'</td></tr>

     <tr><td class = "labelCell">'._SELECTEDFILESTO.':&nbsp;</td>

     <td class = "elementCell">'.$formArray['destination']['html'].'</td></tr>

     <tr><td class = "labelCell">'._OVERWRITE.':&nbsp;</td>

     <td class = "elementCell">'.$formArray['overwrite']['html'].'</td></tr>

     <tr><td></td>

     <td class = "elementCell">

     '.$formArray['submit_copy_file']['html'].'

     </td></tr>

     </table>

     </form>';

      

     return $formString;

     }

     */
    /**

     * Handle copy form

     *

     * This function is used to perform all the actions necessary for when creating a directory

     * from within the file manager

     * <br/>Example:

     * <code>

     *   $createFolderForm   = new HTML_QuickForm("create_folder_form", "post", $url, "", null, true);

     *   if ($createFolderForm -> isSubmitted() && $createFolderForm -> validate()) {

     *       $this -> handleCreateDirectoryForm($createFolderForm);

     *   }

     *

     * </code>

     *

     * @param HTML_QuickForm $form The form used to create the directory

     * @return EfrontDirectory The created directory

     * @since 3.5.0

     * @access public

     */
    /*

     protected function handleCopyForm(& $form) {

     $destinationDirectory = EfrontDirectory :: normalize($form -> exportValue('destination'));

     if ($form -> exportValue('copy_current_directory')) {

     $curDir = EfrontDirectory :: normalize($form -> exportValue('copy_current_directory'));

     } else {

     $curDir = $this -> dir['path'];

     }

     if (strpos($destinationDirectory, $this -> dir['path']) !== false) {

     $copyFiles = explode(",", $form -> exportValue("copy_files"));

     unset($copyFiles[0]);                                                //this is always empty, due to a "," in the beginning of the string

     foreach ($copyFiles as $file) {

     $file = new EfrontFile($file);

     if ($form -> exportValue('action') == 'move') {

     $file -> rename($destinationDirectory.'/'.basename($file['path']), $form -> exportValue('overwrite'));

     } else {

     $file -> copy($destinationDirectory.'/'.basename($file['path']), $form -> exportValue('overwrite'));

     }

     }

     } else {

     throw new EfrontFileException(_ILLEGALPATH.': '.$form -> exportValue('destination'), EfrontFileException :: ILLEGAL_PATH);

     }

     return $copiedFile;

     }

     */
    /**

     * Create HTML representation of file system tree

     *

     * This function creates the file manager HTML code. It also handles any AJAX calls,

     * composes and prints upload and create directory forms, as well as makes sure the

     * correct folder contents are displayed.

     * <code>

     * $basedir    = G_LESSONSPATH.'test/';

     * $filesystem = new FileSystemTree($basedir);									//Set the base directory that the file manager displayes

     * $url        = 'administrator.php?ctg=file_manager';			//Set the url where file manager resides

     * echo $filesystem -> toHTML($url); 											//Display file manager

     * </code>

     * The available options are (the default value in parenthesis):

     * - show_type (true)				//Whether to show the "type" column

     * - show_date (true)				//Whether to show the "last modified" column

     * - show_name (true)				//Whether to show the "name" column

     * - show_size (true)				//Whether to show the "size" column

     * - show_tools (true)				//Whether to show the "tools" column

     * - metadata (true)				//Whether to allow for metadata

     * - db_files_only (false) 			//Whether to display only files that have a db representation

     * - delete (true)					//Whether to display delete icon

     * - download (true)				//Whether to display download icon

     * - zip  (true)					//Whether to display zip icon

     * - share (true)					//Whether to display share icon

     * - create_folder (true)			//Whether to display create folder link

     * - upload (true)					//Whether to display upload file link

     * - copy (true)					//Whether to display copy icon

     * - folders (true)					//Whether to display folders in files list

     *

     * The $extraFileTools, $extraHeaderOptions, $extraDirectoryTools paramaters are used to add custom

     * extra tools to various places of the file manager. The format of these parameters is of the form:

     * $extraFileTools = array(array('image' => 'images/16x16/restore.png', 'title' => _RESTORE, 'action' => 'restore'));

     * $extraHeaderOptions = array(array('image' => 'images/16x16/undo.png', 'title' => _BACKUP, 'action' => 'backup'));

     *

     * @param string $url The url where the file manager resides

     * @param string $currentDirectory The directory to use as base directory

     * @param array $ajaxOptions AJAX-specific options: sort, order, limit, offset, filter

     * @param array $options Options for the file manager

     * @param array $extraFileTools Extra tools for files

     * @param array $extraDirectoryTools Extra tools for directories

     * @param array $extraHeaderOptions Extra tools for file manager header

     * @param array $defaultIterator A specific iterator to use for files display

     * @param bool 	$show_tooltip If tooltip is dislayed in name

     * @return string The HTML representation of the file system

     * @since 3.5.0

     * @access public

     */
    public function toHTML($url, $currentDirectory = '', $ajaxOptions = array(), $options, $extraFileTools = array(), $extraDirectoryTools = array(), $extraHeaderOptions = array(), $defaultIterator = false, $show_tooltip = true) {
        //Set default options
        !isset($options['show_type']) ? $options['show_type'] = true : null;
        !isset($options['show_date']) ? $options['show_date'] = true : null;
        !isset($options['show_name']) ? $options['show_name'] = true : null;
        !isset($options['show_size']) ? $options['show_size'] = true : null;
        !isset($options['show_tools']) ? $options['show_tools'] = true : null;
        !isset($options['delete']) ? $options['delete'] = true : null;
        !isset($options['download']) ? $options['download'] = true : null;
        !isset($options['zip']) ? $options['zip'] = true : null;
        !isset($options['share']) ? $options['share'] = true : null;
        !isset($options['edit']) ? $options['edit'] = true : null;
        !isset($options['copy']) ? $options['copy'] = true : null;
        !isset($options['create_folder']) ? $options['create_folder'] = true : null;
        !isset($options['upload']) ? $options['upload'] = true : null;
        !isset($options['folders']) ? $options['folders'] = true : null;
        !isset($options['db_files_only']) ? $options['db_files_only'] = false : null;
        !isset($options['table_id']) ? $tableId = 'filesTable' : $tableId = $options['table_id'];
        //Make sure that current directory is a path
        //$currentDirectory = new EfrontDirectory($currentDirectory);
        if ($currentDirectory instanceof EfrontDirectory) {
            $currentDirectory = $currentDirectory['path'];
        }
        if (isset($_POST['upload_current_directory']) && strpos(EfrontDirectory :: normalize($_POST['upload_current_directory']), rtrim(G_ROOTPATH, "/")) !== false) {
            $currentDirectory = $_POST['upload_current_directory'];
        }
        if (isset($_POST['current_directory']) && strpos(EfrontDirectory :: normalize($_POST['current_directory']), rtrim(G_ROOTPATH, "/")) !== false) {
            $currentDirectory = $_POST['current_directory'];
        }
        if (isset($_POST['copy_current_directory']) && strpos(EfrontDirectory :: normalize($_POST['copy_current_directory']), rtrim(G_ROOTPATH, "/")) !== false) {
            $currentDirectory = $_POST['copy_current_directory'];
        }
        if ($currentDirectory && $currentDirectory != $this -> dir['path']) {
            //Check that the current directory actually exists
            $currentDir = new EfrontDirectory($currentDirectory);
            //Get its parent directory
            $parentDir = new EfrontDirectory($currentDir['directory']);
            //Build a new (shallow) file system tree on the current directory
            $innerFileSystem = new FileSystemTree($currentDir, false);
            //Assign each node as a child to the currentDir, thus creating a new tree with currentDir as parent
            foreach ($innerFileSystem -> tree as $key => $value) {
                $currentDir[$key] = $value;
            }
            //$currentDir = $this -> seekNode($currentDirectory);
            //$parentDir  = new EfrontDirectory($currentDir['directory']);
        } else {
            $currentDirectory = $this -> dir['path'];
            $currentDir = $this -> tree;
        }
        try {
            $uploadForm = new HTML_QuickForm("upload_file_form_$tableId", "post", $url, "", "target = 'POPUP_FRAME'", true);
            $uploadFormString = $this -> getUploadForm($uploadForm);
            if ($uploadForm -> isSubmitted() && $uploadForm -> validate()) {
                $uploadedFile = $this -> handleUploadForm($uploadForm);
                $uploadFormString .= '<script type = "text/javascript" src = "js/scriptaculous/prototype.php"> </script>
                   <script>if (window.name == "POPUP_FRAME") {(parent.eF_js_showDivPopup("", "", "upload_file_table_'.$tableId.'"));parent.eF_js_rebuildTable(parent.$(\'filename_'.$tableId.'\').down().getAttribute(\'tableIndex\'), 0, \'\', \'desc\', \''.urlencode($currentDirectory).'\');parent.$(\'uploading_image\').hide()}</script>';
            }
            $createFolderForm = new HTML_QuickForm("create_folder_form", "post", $url, "", "target = 'POPUP_FRAME'", true);
            $createFolderString = $this -> getCreateDirectoryForm($createFolderForm);
            if ($createFolderForm -> isSubmitted() && $createFolderForm -> validate()) {
                $this -> handleCreateDirectoryForm($createFolderForm);
                $createFolderString .= '<script type = "text/javascript" src = "js/scriptaculous/prototype.php"> </script>
                   <script>if (window.name == "POPUP_FRAME") {(parent.eF_js_showDivPopup("", "", "create_directory_table_'.$tableId.'"));parent.eF_js_rebuildTable(parent.$(\'filename_'.$tableId.'\').down().getAttribute(\'tableIndex\'), 0, \'\', \'desc\', \''.urlencode($currentDirectory).'\');}</script>';
            }
            /*

             $copyForm       = new HTML_QuickForm("copy_file_form", "post", $url, "", "", true);



             foreach ($iterator = new EfrontDirectoryOnlyFilterIterator(new EfrontNodeFilterIterator($currentDir)) as $key => $value) {

             $directories[$key] = str_replace($this -> dir['path'].'/', '', EfrontFile :: decode($value['path']));

             }

             $copyForm -> addElement('select', 'destination', null, $directories, 'class = "inputText"');

             $copyFormString = $this -> getCopyForm($copyForm);



             if ($copyForm -> isSubmitted() && $copyForm -> validate()) {

             $copiedFile = $this -> handleCopyForm($copyForm);

             }

             */
            //pr($currentDirectory);
            if (isset($_POST['copy_files']) && sizeof($_POST['copy_files']) > 0) {
                $copyFiles = explode(",", $_POST["copy_files"]);
                foreach ($copyFiles as $file) {
                    $file = new EfrontFile($file);
                    //pr('copying to '.$currentDirectory.'/'.basename($file['path']));
                    $file -> copy($currentDirectory.'/'.basename($file['path']));
                }
            }
        } catch (Exception $e) {
            echo "<script>if (top && top.mainframe) {w=top.mainframe} else {w=parent;}w.document.getElementById('messageError').innerHTML = '".$e -> getMessage()."';parent.$('uploading_image').hide();</script>";
            //Don't halt for uploading and create directory errors
            $GLOBALS['smarty'] -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $GLOBALS['message'] = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        }
        $files = array();
        $fileArrays = array();
        if ($options['folders']) {
            $iterator = new EfrontDirectoryOnlyFilterIterator((new ArrayIterator($currentDir))); //Plain ArrayIterator so that it iterates only on the current's folder files
            if ($options['db_files_only']) { //Filter out directories without database representation
                $iterator = new EfrontDBOnlyFilterIterator($iterator);
            }
            foreach ($iterator as $key => $value) { //We convert iterator to a complete array of files, so we can apply sorting, filtering etc more easily
                $current = (array)$iterator -> current();
                foreach ($current as $k => $v) { //Remove child elements, such files, directories etc from the array, so we can successfully apply operations on to them, such as filtering
                    if ($v instanceOf ArrayObject) {
                        unset ($current[$k]);
                    }
                }
                $current['size'] = 0;
                $current['extension'] = '';
                $current['shared'] = 10; //Add these 3 parameters, so that sorting below works correctly (10 means nothing, since a folder cannot be shared, but it is handy for sorting)
                $fileArrays[] = (array)$current; //Array representation of directory objects, on which we can apply sorting, filtering, etc
            }
        }
        if ($defaultIterator) {
            $iterator = $defaultIterator;
        } else {
            $iterator = new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new ArrayIterator($currentDir))); //Plain ArrayIterator so that it iterates only on the current folder's files
            if ($options['db_files_only']) { //Filter out directories without database representation
                $iterator = new EfrontDBOnlyFilterIterator($iterator);
            }
        }
        foreach ($iterator as $key => $value) { //We convert iterator to a complete array of files, so we can apply sorting, filtering etc more easily
            $current = (array)$iterator -> current();
            foreach ($current as $k => $v) { //Remove child elements, such files, directories etc from the array, so we can successfully apply operations on to them, such as filtering
                if ($v instanceOf ArrayObject) {
                    unset ($current[$k]);
                }
            }
            $fileArrays[] = (array)$current; //Array representation of file objects, on which we can apply sorting, filtering, etc
        }
        isset($ajaxOptions['order']) && $ajaxOptions['order'] == 'asc' ? $ajaxOptions['order'] = 'asc' : $ajaxOptions['order'] = 'desc';
        !isset($ajaxOptions['sort']) ? $ajaxOptions['sort'] = 'name' : null;
        !isset($ajaxOptions['limit']) ? $ajaxOptions['limit'] = 20 : null;
        !isset($ajaxOptions['offset']) ? $ajaxOptions['offset'] = 0 : null;
        !isset($ajaxOptions['filter']) ? $ajaxOptions['filter'] = '' : null;
        $size = sizeof($fileArrays);
        if ($size) {
         $fileArrays = eF_multiSort($fileArrays, $ajaxOptions['sort'], $ajaxOptions['order']);
         $ajaxOptions['filter'] ? $fileArrays = eF_filterData($fileArrays, $ajaxOptions['filter']) : null;
         $fileArrays = array_slice($fileArrays, $ajaxOptions['offset'], $ajaxOptions['limit']);
        }
        $filesCode = '
                        <table class = "sortedTable" style = "width:100%" size = "'.$size.'" id = "'.$tableId.'" useAjax = "1" rowsPerPage = "20" other = "'.urlencode($currentDirectory).'" url = "'.$url.'&" nomass = "1" currentDir = "'.(isset($currentDir['path']) ? $currentDir['path'] : '').'">
                      <tr>'.($options['show_type'] ? '<td class = "topTitle centerAlign" name = "extension">'._FILETYPE.'</td>' : '').'
                       '.($options['show_name'] ? '<td class = "topTitle" name = "name" id = "filename_'.$tableId.'">'._FILENAME.'</td>' : '').'
                       '.($options['show_size'] ? '<td class = "topTitle" name = "size">'._SIZE.'</td>' : '').'
                       '.($options['show_date'] ? '<td class = "topTitle" name = "timestamp">'._LASTMODIFIED.'</td>' : '').'
                       '.($_SESSION['s_lessons_ID'] && $options['share'] ? '<td class = "topTitle centerAlign" name = "shared">'._SHARE.'</td>' : '').'
                       '.($options['show_tools'] ? '<td class = "topTitle centerAlign">'._OPERATIONS.'</td>' : '').'
                       '.($options['delete'] || ($_SESSION['s_lessons_ID'] && $options['share']) ? '<td class = "topTitle centerAlign">'._SELECT.'</td>' : '').'
                      </tr>';
        if (isset($parentDir)) {
            if ($parentDir['path'] == $this -> dir['path']) {
                $parentDir['path'] = '';
            }
            $filesCode .= '
                     <tr class = "defaultRowHeight oddRowColor">
                      <td class = "centerAlign"><span style = "display:none"></span><img src = "images/16x16/folder_up.png" alt = "'._UPONELEVEL.'" title = "'._UPONELEVEL.'"/></td>
                      <td><a href = "javascript:void(0)" onclick = "eF_js_rebuildTable($(\'filename_'.$tableId.'\').down().getAttribute(\'tableIndex\'), 0, \'\', \'desc\', \''.urlencode($parentDir['path']).'\');">.. ('._UPONELEVEL.')</a></td>
                      <td colspan = "5"></td></tr>';
        }
        $i = 0;
        foreach ($fileArrays as $key => $value) {
            $toolsString = '';
            $sharedString = '';
            if (is_file($value['path'])) {
                $value['id'] == -1 ? $identifier = $value['path'] : $identifier = $value['id']; //The file/directory identifier will be the id, if the entity has a database representation, or the file path otherwise
                $value = new EfrontFile($value); //Restore file/directory representation, so we can use its methods
                $link = $url.'&view='.urlencode($identifier);
                foreach ($extraFileTools as $tool) {
                    //$toolsString .= '<a href = "javascript:void(0)"><img src = "'.$tool['image'].'" alt = "'.$tool['title'].'" title = "'.$tool['title'].'" border = "0" onclick = "'.$tool['action'].'(this, \''.urlencode($identifier).'\')"  /></a>&nbsp;';
                    $toolsString .= '<a href = "javascript:void(0)"><img src = "'.$tool['image'].'" alt = "'.$tool['title'].'" title = "'.$tool['title'].'" border = "0" onclick = "'.$tool['action'].'(this, $(\'span_'.urlencode($identifier).'\').innerHTML)" /></a>&nbsp;';
                }
                if (($value['extension'] == 'zip' || $value['extension'] == 'gz') && $options['zip']) {
                    $toolsString .= '<a href = "javascript:void(0)"><img src = "images/16x16/uncompress.png" alt = "'._UNCOMPRESS.'" title = "'._UNCOMPRESS.'" border = "0" onclick = "uncompressFile(this, $(\'span_'.urlencode($identifier).'\').innerHTML)"  /></a>&nbsp;';
                }
                if ($options['download']) {
                    $toolsString .= '<a href = "'.$url.'&download='.urlencode($identifier).'"><img src = "images/16x16/import.png" alt = "'._DOWNLOADFILE.'" title = "'._DOWNLOADFILE.'" border = "0"/></a>&nbsp;';
                }
                if ($_SESSION['s_lessons_ID'] && $options['share']) {
                    $sharedString = '
                      <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "'._UNSHARE.'" title = "'._UNSHARE.'" onclick = "unshareFile(this, $(\'span_'.urlencode($identifier).'\').innerHTML)" style = "'.(!$value['shared'] ? 'display:none' : null).'" />
                      <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "'._SHARE.'" title = "'._SHARE.'" onclick = "shareFile(this, $(\'span_'.urlencode($identifier).'\').innerHTML)" style = "'.($value['shared'] ? 'display:none' : null).'" />';
                }
                if ($options['metadata']) {
                    $toolsString .= '<a href = "'.$url.'&popup=1&display_metadata='.urlencode($identifier).'" target = "POPUP_FRAME"><img src = "images/16x16/information.png" alt = "'._METADATA.'" title = "'._METADATA.'" onclick = "eF_js_showDivPopup(\''._METADATA.'\', 2)" border = "0"/></a>&nbsp;';
                }
                if ($options['edit']) {
                    $toolsString .= '<img class = "ajaxHandle edit" src = "images/16x16/edit.png" alt = "'._EDIT.'" title = "'._EDIT.'" onclick = "toggleEditBox(this, \''.urlencode($identifier).'\')"/>&nbsp;';
                }
                if ($options['delete'] && !($value['users_LOGIN'] == $_SESSION['s_login'] && isset($value['users_LOGIN']) && $GLOBALS['configuration']['allow_users_to_delete_supervisor_files'] == 0)) {
                    $toolsString .= '<img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "'._DELETE.'" title = "'._DELETE.'" onclick = "if (confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')) {deleteFile(this, $(\'span_'.urlencode($identifier).'\').innerHTML)}"/></a>&nbsp;';
                }
            } else if (is_dir($value['path'])) {
                $identifier = $value['path'];
                $value = new EfrontDirectory($value['path']);
                $link = $url.'&view_dir='.urlencode($identifier);
                foreach ($extraDirectoryTools as $tool) {
                    $toolsString .= '<a href = "javascript:void(0)"><img src = "'.$tool['image'].'" alt = "'.$tool['title'].'" title = "'.$tool['title'].'" border = "0" onclick = "'.$tool['action'].'(this, $(\'span_'.urlencode($identifier).'\').innerHTML)"  /></a>&nbsp;';
                }
                if ($options['edit']) {
                    $toolsString .= '<img class = "ajaxHandle edit" src = "images/16x16/edit.png" alt = "'._EDIT.'" title = "'._EDIT.'" onclick = "toggleEditBox(this, \''.urlencode($identifier).'\')"/>&nbsp;';
                }
                if ($options['delete']) {
                    $toolsString .= '<img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "'._DELETE.'" title = "'._DELETE.'" onclick = "if (confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')) {deleteFolder(this, $(\'span_'.urlencode($identifier).'\').innerHTML)}" />&nbsp;';
                }
            }
            $filesCode .= '<tr class = "defaultRowHeight '.(fmod($i++, 2) ? 'oddRowColor' : 'evenRowColor').'">';
            if ($options['show_type']) {
                $filesCode .= '<td class = "centerAlign"><span style = "display:none">'.(isset($value['extension']) ? $value['extension'] : '').'</span>';
                if ($value['type'] == 'file') {
                    if (strpos($value['mime_type'], "image") !== false ||
                    strpos($value['mime_type'], "text") !== false ||
                    strpos($value['mime_type'], "pdf") !== false ||
                    strpos($value['mime_type'], "html") !== false ||
                    strpos($value['mime_type'], "flash") !== false) {
                        $filesCode .= '<a href = "javascript:void(0);" onclick = "eF_js_showDivPopup(\''._PREVIEW.'\', 2, \'preview_table_'.$tableId.'\');$(\'preview_frame\').src = \''.$link.'\';" ><img src = "'.$value -> getTypeImage().'" alt = "'.$value['mime_type'].'" title = "'.$value['mime_type'].'" border = "0"/></a></td>';
                    } else {
                        $filesCode .= '<a href = "'.$url.'&download='.urlencode($identifier).'"><img src = "'.$value -> getTypeImage().'" alt = "'.$value['mime_type'].'" title = "'.$value['mime_type'].'" border = "0"/></a>';
                    }
                } else {
                    isset($value['mime_type']) ? $mimeType = $value['mime_type'] : $mimeType = '';
                    $filesCode .= '<img src = "'.$value -> getTypeImage().'" alt = "'.$mimeType.'" title = "'.$mimeType.'" border = "0"/></td>';
                }
            }
            if ($options['show_name']) {
                $filesCode .= '<td><span id = "span_'.urlencode($identifier).'" style = "display:none;">'.urlencode($identifier).'</span>';
                if ($value['type'] == 'file') {
                    if ($show_tooltip) {
                        $filesCode .= $value -> toHTMLTooltipLink($link);
                    } else {
                        if (strpos($value['mime_type'], "image") !== false || strpos($value['mime_type'], "text") !== false || strpos($value['mime_type'], "pdf") !== false || strpos($value['mime_type'], "flash") !== false) {
                            $filesCode .= '<a href = "'.$link.'" target = "PREVIEW_FRAME" onclick = "eF_js_showDivPopup(\''._PREVIEW.'\', 2, \'preview_table_'.$tableId.'\');">'.$value['name'].'</a>';
                        } else {
                            $filesCode .= '<a target = "PREVIEW_FRAME" href = "'.$url.'&download='.urlencode($identifier).'">'.$value['name'].'</a>';
                        }
                    }
                } else {
                    $filesCode .= '<a href = "javascript:void(0)" onclick = "eF_js_rebuildTable($(\'filename_'.$tableId.'\').down().getAttribute(\'tableIndex\'), 0, \'\', \'desc\', \''.urlencode($identifier).'\');">'.$value['name'].'</a>';
                }
                $filesCode .= '<span id = "edit_'.urlencode($identifier).'" style = "display:none"><input type = "text" value = "'.$value['name'].'" onkeypress = "if (event.which == 13 || event.keyCode == 13) {Element.extend(this).next().down().onclick(); return false;}"/>&nbsp;<a href = "javascript:void(0)"><img id = "editImage_'.urlencode($identifier).'"src = "images/16x16/success.png" style = "vertical-align:middle" onclick = "editFile(this, $(\'span_'.urlencode($identifier).'\').innerHTML, Element.extend(this).up().previous().value, \''.$value['type'].'\',\''.$value['name'].'\')" border = "0"></a></span></td>';
            }
            $filesCode .= ''.($options['show_size'] ? '<td>'.($value['type'] == 'file' ? $value['size'].' '._KB : '').'</td>' : '').'
                          '.($options['show_date'] ? '<td>'.formatTimestamp($value['timestamp'], 'time_nosec').'</td>' : '').'
                          '.($_SESSION['s_lessons_ID'] && $options['share'] ? '<td class = "centerAlign">'.$sharedString.'</td>' : '').'
                          '.($options['show_tools'] ? '<td class = "centerAlign">'.$toolsString.'</td>' : '').'
                           '.($options['delete'] || ($_SESSION['s_lessons_ID'] && $options['share']) ? '<td class = "centerAlign">'.($value['type'] == 'file' ? '<input type = "checkbox" id = "'.$identifier.'" value = "'.$identifier.'" />' : '').'</td>' : '').'
                         </tr>';
        }
        $massOperationsCode = '';
        if ($size) {
            $filesCode .= '
            </table>';
            if ($options['delete'] || ($_SESSION['s_lessons_ID'] && $options['share'])) {
                $massOperationsCode = '
               <div class = "horizontalSeparatorAbove">
                <span style = "vertical-align:middle">'._WITHSELECTEDFILES.':</span>
                '.($_SESSION['s_lessons_ID'] && $options['share'] ? '<a href = "javascript:void(0)"><img src = "images/16x16/trafficlight_green.png" title = "'._SHARESELECTED.'" alt = "'._SHARESELECTED.'" border = "0" style = "vertical-align:middle" onclick = "shareSelected()"></a><a href = "javascript:void(0)"><img src = "images/16x16/trafficlight_red.png" title = "'._UNSHARESELECTED.'" alt = "'._UNSHARESELECTED.'" border = "0" style = "vertical-align:middle" onclick = "unshareSelected()"></a>' : '');
                if ($options['copy']) {
                    $massOperationsCode .= '
                   <form name = "copy_files_form" id = "copy_files_form" method = "post" style = "display:none;"><input type = "hidden" name = "copy_current_directory" id = "copy_current_directory"><input type = "hidden" name = "copy_files" id = "copy_files" value = "" /></form>
       <img class = "ajaxHandle" src = "images/16x16/copy.png" title = "'._COPYSELECTED.'" alt = "'._COPYSELECTED.'" onclick = "copyFiles(this);">
                            <img style = "display:none" class = "ajaxHandle" src = "images/16x16/paste.png" title = "'._PASTESELECTED.'" alt = "'._PASTESELECTED.'" onclick = "$(\'copy_current_directory\').value = $(\''.$tableId.'\').getAttribute(\'currentDir\');pasteFiles(this);eF_js_rebuildTable($(\'filename_'.$tableId.'\').down().getAttribute(\'tableIndex\'), 0, \'\', \'desc\', $(\'copy_current_directory\').value);">&nbsp;';
                }
                $massOperationsCode .= ($options['delete'] ? '<a href = "javascript:void(0)"><img src = "images/16x16/error_delete.png" title = "'._DELETESELECTED.'" alt = "'._DELETESELECTED.'" border = "0" style = "vertical-align:middle" onclick = "if (confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')) deleteSelected()"></a>' : '').'
               </div>';
            }
        } else {
            $filesCode .= '
                <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">'._NODATAFOUND.'</td></tr>
            </table>';            
        }
        $str = '
         <div class = "headerTools">';
        if ($options['upload']) {
            $str .= '
          <span>
              <img src = "images/16x16/add.png" alt = "'._UPLOADFILE.'" title = "'._UPLOADFILE.'"/>
           <a href = "javascript:void(0)" onclick = "$(\'url_upload\').value = \'\';$$(\'input\').each(function(s)  {if (s.type == \'file\') s.value = \'\'});$(\'upload_current_directory\').value = $(\''.$tableId.'\').getAttribute(\'currentDir\');eF_js_showDivPopup(\''._UPLOADFILE.'\', 0, \'upload_file_table_'.$tableId.'\')">'._UPLOADFILE.'</a>&nbsp;
          </span>';
        }
        if ($options['create_folder']) {
            $str .= '
          <span>
           <img src = "images/16x16/folder_add.png" alt = "'._CREATEFOLDER.'" title = "'._CREATEFOLDER.'">
           <a href = "javascript:void(0)" onclick = "$(\'current_directory\').value = $(\''.$tableId.'\').getAttribute(\'currentDir\');eF_js_showDivPopup(\''._CREATEFOLDER.'\', 0, \'create_directory_table_'.$tableId.'\')">'._CREATEFOLDER.'</a>&nbsp;
          </span>';
        }
        foreach ($extraHeaderOptions as $option) {
            $str .= '
             <span>
           <img src = "'.$option['image'].'" alt = "'.$option['title'].'" title = "'.$option['title'].'">
           <a href = "'.(isset($option['href']) ? $option['href'] : 'javascript:void(0)').'" onclick = "'.$option['action'].'">'.$option['title'].'</a>&nbsp;
          </span>';
        }
        $str .= '
         </div>
         <table style = "width:100%">
          <tr><td>
<!--ajax:'.$tableId.'-->
            '.$filesCode.'
<!--/ajax:'.$tableId.'-->
      '.$massOperationsCode.'
           </td></tr>
         </table>
         <script>
         var url = "'.$url.'";
         var tableId = "'.$tableId.'";
         </script>
         <div id = "upload_file_table_'.$tableId.'" style = "display:none;" class = "filemanagerBlock">'.$uploadFormString.'</div>
         <div id = "create_directory_table_'.$tableId.'" style = "display:none;" class = "filemanagerBlock">'.$createFolderString.'</div>
         <div id = "preview_table_'.$tableId.'" style = "height:100%;display:none" class = "filemanagerBlock">
                <iframe name = "PREVIEW_FRAME" id = "preview_frame" src = "about:blank" style = "border-width:0px;width:100%;height:100%;padding:0px 0px 0px 0px">Sorry, but your browser needs to support iframes to see this</iframe>
            </div>';
/*

        $GLOBALS['smarty'] -> assign("T_BLOCK_DATA", $uploadFormString);

        $GLOBALS['smarty'] -> assign("T_DISPLAY_BLOCK", '<div id = "upload_file_table_'.$tableId.'" style = "display:none;">{eF_template_printBlock title="'._UPLOADFILE.'" data=$T_BLOCK_DATA image="32x32/import.png"}</div>');

        $str .= $GLOBALS['smarty'] -> fetch("display_code.tpl");

        $GLOBALS['smarty'] -> assign("T_BLOCK_DATA", $createFolderString);

        $GLOBALS['smarty'] -> assign("T_DISPLAY_BLOCK", '<div id = "create_directory_table_'.$tableId.'" style = "display:none;">{eF_template_printBlock title="'._CREATEFOLDER.'" data=$T_BLOCK_DATA image="32x32/folder.png"}</div>');

        $str .= $GLOBALS['smarty'] -> fetch("display_code.tpl");

        $GLOBALS['smarty'] -> assign("T_DISPLAY_BLOCK", '<div id = "preview_table_'.$tableId.'" style = "display:none">{eF_template_printBlock title="'._PREVIEW.'" data="<iframe name = \"PREVIEW_FRAME\" id = \"preview_frame\" src = \"about:blank\" style = \"border-width:0px;width:100%;height:100%;padding:0px\">Sorry, but your browser needs to support iframes to see this</iframe>" image="32x32/folder.png"}</div>');

        $str .= $GLOBALS['smarty'] -> fetch("display_code.tpl");



 */
        return $str;
    }
    /**

     * Handle AJAX actions

     *

     * This function is used to perform the necessary ajax actions,

     * that may be fired by the file manager

     * <br/>Example:

     * <code>

     * $basedir    = $currentLesson -> getDirectory();

     * $filesystem = new FileSystemTree($basedir);

     * $filesystem -> handleAjaxActions();

     * </code>

     *

     * @param EfrontUser $currentUser The current user

     * @since 3.5.0

     * @access public

     */
    public function handleAjaxActions($currentUser) {
        if (isset($_GET['delete']) && (eF_checkParameter($_GET['delete'], 'id') || strpos(urldecode($_GET['delete']), $this -> dir['path']) !== false)) {
            try {
                $file = new EfrontFile(urldecode($_GET['delete']));
                //if ($file['users_LOGIN'] == $currentUser -> user['login']) {
                $file -> delete();
                //} else {
                //    throw new EfrontFileException(_YOUDONTHAVEPERMISSION.': '.$file['file'], EfrontFileException :: UNAUTHORIZED_ACTION);
                //}
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } else if (isset($_GET['share']) && (eF_checkParameter($_GET['share'], 'id') || strpos(urldecode($_GET['share']), $this -> dir['path']) !== false)) {
            try {
                $file = new EfrontFile(urldecode($_GET['share']));
                //if ($file['users_LOGIN'] == $currentUser -> user['login']) {
                $file -> share();
                //} else {
                //    throw new EfrontFileException(_YOUDONTHAVEPERMISSION.': '.$file['file'], EfrontFileException :: UNAUTHORIZED_ACTION);
                //}
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } else if (isset($_GET['unshare']) && (eF_checkParameter($_GET['unshare'], 'id') || strpos(urldecode($_GET['unshare']), $this -> dir['path']) !== false)) {
            try {
                $file = new EfrontFile(urldecode($_GET['unshare']));
                //if ($file['users_LOGIN'] == $currentUser -> user['login']) {
                $file -> unshare();
                //} else {
                //    throw new EfrontFileException(_YOUDONTHAVEPERMISSION.': '.$file['file'], EfrontFileException :: UNAUTHORIZED_ACTION);
                //}
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } else if (isset($_GET['uncompress']) && (eF_checkParameter($_GET['uncompress'], 'id') || strpos(urldecode($_GET['uncompress']), $this -> dir['path']) !== false)) {
            try {
                $file = new EfrontFile(urldecode($_GET['uncompress']));
                $file -> uncompress();
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } elseif (isset($_GET['delete_folder']) && (eF_checkParameter($_GET['delete_folder'], 'id') || strpos(urldecode($_GET['delete_folder']), $this -> dir['path']) !== false)) {
            try {
                $directory = new EfrontDirectory(urldecode($_GET['delete_folder']));
                //if ($directory['users_LOGIN'] == $currentUser -> user['login']) {
                $directory -> delete();
                //} else {
                //    throw new EfrontFileException(_YOUDONTHAVEPERMISSION.': '.$directory['file'], EfrontFileException :: UNAUTHORIZED_ACTION);
                //}
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } elseif (isset($_GET['download']) && (eF_checkParameter($_GET['download'], 'id') || strpos(urldecode($_GET['download']), $this -> dir['path']) !== false)) {
            try {
                $file = new EfrontFile(urldecode($_GET['download']));
                //if ($file['users_LOGIN'] == $currentUser -> user['login'] || $file['access'] != 0) {
                header("content-type:".$file['mime_type']);
                if (stripos($_SERVER['HTTP_USER_AGENT'], 'firefox') === false) {
                    header('content-disposition: attachment; filename= "'.urlencode($file['name']).'"');
                } else {
                    header('content-disposition: attachment; filename= "'.($file['name']).'"');
                }
                readfile($file['path']);
                //} else {
                //    throw new EfrontFileException(_YOUDONTHAVEPERMISSION.': '.$file['file'], EfrontFileException :: UNAUTHORIZED_ACTION);
                //}
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } elseif (isset($_GET['view']) && (eF_checkParameter($_GET['view'], 'id') || strpos(urldecode($_GET['view']), $this -> dir['path']) !== false)) {
            try {
                $file = new EfrontFile(urldecode($_GET['view']));
                //if ($file['users_LOGIN'] == $currentUser -> user['login'] || $file['access'] != 0) {
                header("content-type:".$file['mime_type']);
                header('content-disposition: inline; filename= "'.urlencode($file['name']).'"');
                readfile($file['path']);
                //} else {
                //    throw new EfrontFileException(_YOUDONTHAVEPERMISSION.': '.$file['file'], EfrontFileException :: UNAUTHORIZED_ACTION);
                //}
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } elseif (isset($_GET['update']) && (eF_checkParameter($_GET['update'], 'id') || strpos(urldecode($_GET['update']), $this -> dir['path']) !== false)) {
            try {
                $_GET['type'] == 'file' ? $file = new EfrontFile(urldecode($_GET['update'])) : $file = new EfrontDirectory(urldecode($_GET['update']));
                //if ($file['users_LOGIN'] == $currentUser -> user['login'] || $file['access'] != 0) {
                $previousName = $file['name'];
                if ($file['name'] != $_GET['name']) {
                    $file -> rename(dirname($file['path']).'/'.EfrontFile :: encode(urldecode($_GET['name'])));
                }
                echo json_encode(array('previousName' => $previousName, 'name' => $file['name']));
                //} else {
                //    throw new EfrontFileException(_YOUDONTHAVEPERMISSION.': '.$file['file'], EfrontFileException :: UNAUTHORIZED_ACTION);
                //}
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    }
    /**

     * Handle uploaded file

     *

     * This function is used to handle an uploaded file. Given the name of the form field

     * that was used to upload the file, as well as the destination directory, the function

     * creates the corresponding database entry and moves the file to the designated position,

     * using the appropriate name.

     * <br/>Example:

     * <code>

     * $destinationDir = new EfrontDirectory(/path/to/destination/dir);                    //the directory to upload the file to.

     * $filesystem = new FileSystemTree('/path/to/some_dir');								//Create a FileSystemTree instance

     * try {

     *   $uploadedFile = $filesystem -> uploadFile('file_upload', $destinationDir);

     * } catch (EfrontFileException $e) {

     *   echo $e -> getMessage();

     * }

     * </code>

     *

     * @param string $fieldName The form file field name

     * @param mixed $destinationDirectory The destination for the uploaded file, either a string or an EfrontDirectory object

     * @param string $offset If the field name is on the form file[x] (array-like), then specifying specific offset (x) allows for handling of it

     * @return object An object of EfrontFile class, corresponding to the newly uploaded file.

     * @access public

     * @since 3.0

     * @static

     */
    public function uploadFile($fieldName, $destinationDirectory = false, $offset = false) {
        if (!$destinationDirectory) {
            $destinationDirectory = $this -> dir;
        }
        if (!($destinationDirectory instanceof EfrontDirectory)) {
            $destinationDirectory = new EfrontDirectory($destinationDirectory);
        }
        if (strpos($destinationDirectory['path'], $this -> dir['path']) === false) {
            throw new EfrontFileException(_ILLEGALPATH.': '.$destinationDirectory['path'], EfrontFileException :: ILLEGAL_PATH);
        } else {
            if ($offset !== false) {
                $error = $_FILES[$fieldName]['error'][$offset];
                $size = $_FILES[$fieldName]['size'][$offset];
                $name = $_FILES[$fieldName]['name'][$offset];
                $tmp_name = $_FILES[$fieldName]['tmp_name'][$offset];
            } else {
                $error = $_FILES[$fieldName]['error'];
                $size = $_FILES[$fieldName]['size'];
                $name = $_FILES[$fieldName]['name'];
                $tmp_name = $_FILES[$fieldName]['tmp_name'];
            }
            $this -> checkFile($name);
            if ($error) {
                switch ($error) {
                    case UPLOAD_ERR_INI_SIZE :
                        throw new EfrontFileException(_THEFILE." "._MUSTBESMALLERTHAN." ".ini_get('upload_max_filesize'), UPLOAD_ERR_INI_SIZE );
                        break;
                    case UPLOAD_ERR_FORM_SIZE :
                        throw new EfrontFileException(_THEFILE." "._MUSTBESMALLERTHAN." ".sprintf("%.0f", $_POST['MAX_FILE_SIZE']/1024)." "._KILOBYTES, UPLOAD_ERR_FORM_SIZE);
                        break;
                    case UPLOAD_ERR_PARTIAL :
                        throw new EfrontFileException(_FILEWASPARTIALLYUPLOADED, UPLOAD_ERR_PARTIAL);
                        break;
                    case UPLOAD_ERR_NO_FILE :
                        throw new EfrontFileException(_NOFILEUPLOADED, UPLOAD_ERR_NO_FILE);
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR :
                        throw new EfrontFileException(_NOTMPDIR, UPLOAD_ERR_NO_TMP_DIR);
                        break;
                    case UPLOAD_ERR_CANT_WRITE :
                        throw new EfrontFileException(_UPLOADCANTWRITE, UPLOAD_ERR_CANT_WRITE);
                        break;
                    case UPLOAD_ERR_EXTENSION :
                        throw new EfrontFileException(_UPLOADERREXTENSION, UPLOAD_ERR_EXTENSION);
                        break;
                    default:
                        throw new EfrontFileException(_ERRORUPLOADINGFILE, EfrontFileException :: UNKNOWN_ERROR);
                        break;
                }
            } elseif ($size == 0) {
                throw new EfrontFileException(_FILEDOESNOTEXIST, EfrontFileException :: FILE_NOT_EXIST);
            } elseif (!eF_checkParameter($name, 'filename')) {
                throw new EfrontFileException(_ILLEGALFILENAME, EfrontFileException :: ILLEGAL_FILE_NAME);
            } else {
                //$id      = eF_insertTableData("files", array('file' => 'temp'));                        //Insert bogus entry
                /*

                 if (FileSystemTree :: mustTranslate($name)) {

                 $newName = $id;

                 pathinfo($name, PATHINFO_EXTENSION) ? $newName .= '.'.pathinfo($name, PATHINFO_EXTENSION) : null;    //Append the file extension, only if the file has one

                 }

                 */
                $newName = EfrontFile :: encode($name);
                move_uploaded_file($tmp_name, $destinationDirectory['path'].'/'.$newName);
                chmod($destinationDirectory['path'].'/'.$newName, 0644); //because of this http://bugs.php.net/bug.php?id=42291
                $fileMetadata = array('title' => $name,
                                      'creator' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                      'publisher' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                      'contributor' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                      'date' => date("Y/m/d", time()),
                                      'type' => 'file');
                $fields = array('path' => str_replace(G_ROOTPATH, '', $destinationDirectory['path'].'/'.$newName),
                    'users_LOGIN' => $_SESSION['s_login'],
                                'timestamp' => time(),
                                'metadata' => serialize($fileMetadata));
                $id = eF_insertTableData("files", $fields);
                if ($id) {
                    foreach ($fileMetadata as $key => $value) {
                        EfrontSearch :: insertText($value, $id, "files", "data");
                    }
                }
                return new EfrontFile($id);
            }
        }
    }
    /**

     * Check validity of file names

     *

     * This function is used to check against the file black/white lists

     * <br/>Example:

     * <code>

     * FileSystemTree :: checkFile('test.php');	//Will throw an exception

     * </code>

     *

     * @param string $name The file name to check

     * @since 3.5.2

     * @access public

     */
    public static function checkFile($name) {
        $blackList = explode(",", $GLOBALS['configuration']['file_black_list']);
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        foreach ($blackList as $value) {
            if ($extension == trim(mb_strtolower($value))) {
                throw new EfrontFileException(_YOUCANNOTUPLOADFILESWITHTHISEXTENSION.': '.$extension, EfrontFileException::FILE_IN_BLACK_LIST);
            }
        }
        $whiteList = explode(",", $GLOBALS['configuration']['file_white_list']);
        foreach ($whiteList as $key => $value) {
            $value = trim(mb_strtolower($value));
            if ($value) {
                $whiteList[$key] = $value;
            } else {
                unset($whiteList[$key]);
            }
        }
        if (sizeof($whiteList) > 0 && !in_array($extension, $whiteList)) {
            throw new EfrontFileException(_YOUMAYONLYUPLOADFILESWITHEXTENSION.': '.$GLOBALS['configuration']['file_white_list'], EfrontFileException::FILE_NOT_IN_WHITE_LIST);
        }
    }
    /**

     * Get maximum upload size

     *

     * This function is used to calculate the maximum alloweded upload

     * file size (in KB). The size is the smallest among the following:

     * - The 'memory_limit' PHP ini setting

     * - The 'upload_max_filesize' PHP ini setting

     * - The 'post_max_size' PHP ini setting

     * - The maximum file size configuration setting

     * <br/>Example:

     * <code>

     * echo FileSystemTree :: getUploadMaxSize();	//returns something like 10000, which is 10000KB

     * </code>

     *

     * @return int The maximum file size, in Kilobytes

     * @see FileSystemTree :: uploadFile()

     * @since 3.0

     * @access public

     * @static

     */
    public static function getUploadMaxSize() {
        preg_match('/(\d+)/', ini_get('memory_limit'), $memory_limit);
        preg_match('/(\d+)/', ini_get('upload_max_filesize'), $upload_max_filesize);
        preg_match('/(\d+)/', ini_get('post_max_size'), $post_max_size);
        $memory_limit[1] == 1 ? $memory_limit[1] = $upload_max_filesize[1] : null; //In case memory_limit is set to -1 (no limit), then equalize this variable with the upload_max_filesize
        $max_upload = min($memory_limit[1] * 1024, $upload_max_filesize[1] * 1024, $post_max_size[1] * 1024, $GLOBALS['configuration']['max_file_size']);
        return $max_upload;
    }
    /**

     * Get specific file types

     *

     * This function can be used to return extensions and mime types of specific file

     * classes, such as images or media.

     * <br/>Example:

     * <code>

     * $imageMimeTypes = FileSystemTree :: getFileTypes('image');	//$imageMimeTypes now contains the arrays 'jpg' => 'image/png', 'png' => 'image/png', etc

     * </code>

     *

     * @param mixed $type The file classes: 'image', 'media', 'java'. If none is specified, then all file types available are returned

     * @return array The file types in extension => mime type pairs

     * @see EfrontFile :: $mimeTypes

     * @since 3.0

     * @access public

     * @static

     */
    public static function getFileTypes($type = false) {
        $fileTypes = array();
        foreach (EfrontFile :: $mimeTypes as $key => $filetype) {
            switch ($type) {
                case 'image':
                    if (strpos($filetype, 'image/') === 0) {
                        $fileTypes[$key] = $filetype;
                    }
                    break;
                case 'media':
                    if (strpos($filetype, 'audio/') === 0 || strpos($filetype, 'video/') === 0 || $key == 'swf' || $key == 'flv' || strpos($filetype, 'media') !== false) {
                        $fileTypes[$key] = $filetype;
                    }
                    break;
                case 'java':
                    if ($key == 'class') {
                        $fileTypes[$key] = $filetype;
                    }
                    break;
                default:
                    $fileTypes[$key] = $filetype;
                    break;
            }
        }
        return $fileTypes;
    }
    /**

     * Import files to filesystem

     *

     * This function imports the specified files (in $list array) to the filesystem,

     * by creating a corresponding database representation. The $list

     * array should contain full paths to the files. The function returns an array

     * of the same size and contents as $list , but this time the file ids being the keys

     * <br/>Example:

     * <code>

     * $list = array('/var/www/text.txt', '/var/www/user.txt');

     * $newList = FileSystemTree :: importFiles($list);

     * </code>

     *

     * @param array $list The files list

     * @param array $options extra options to set for the files, such as whether they should be renamed, or the proper permissions

     * @return array An array with the new file ids

     * @access public

     * @since 3.0

     * @static

     */
    public static function importFiles($list, $options = array()) {
        if (!is_array($list)) {
            $list = array($list);
        }
        $allFiles = eF_getTableDataFlat("files", "path"); //Get all files, so that if a file already exists, a duplicate entry in the database won't be created
        for ($i = 0; $i < sizeof($list); $i++) {
            $list[$i] = EfrontFile :: encode($list[$i]);
            if (!in_array($list[$i], $allFiles['path']) && strpos(dirname($list[$i]), rtrim(G_ROOTPATH, "/")) !== false) {
                $fileMetadata = array('title' => basename($list[$i]),
                                      'creator' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                      'publisher' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                      'contributor' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                      'date' => date("Y/m/d", time()),
                                      'type' => 'file');
                $fields = array('path' => str_replace(G_ROOTPATH, '', $list[$i]),
                                'users_LOGIN' => isset($_SESSION['s_login']) ? $_SESSION['s_login'] : '',
                                'timestamp' => time(),
                                'metadata' => serialize($fileMetadata));
                isset($options['access']) ? $fields['access'] = $options['access'] : null;
                $fileId = eF_insertTableData("files", $fields);
                if ($fileId) {
                    $newList[$fileId] = $list[$i];
                    foreach ($fileMetadata as $key => $value) {
                        EfrontSearch :: insertText($value, $fileId, "files", "data");
                    }
                }
            }
        }
        return $newList;
    }
}
/**

 * Return directories only

 *

 * @package eFront

 */
class EfrontDirectoryOnlyFilterIterator extends FilterIterator
{
    /**

     * Accept method

     *

     * The accept method returns true only if the current element

     * is a directory

     *

     * @return boolean True if the current element is a directory

     * @since 3.5.0

     * @access public

     */
    function accept() {
        //return is_dir($this -> key());
        //pr($this -> current());
        return is_dir($this -> key());
    }
}
/**

 * Return files only

 *

 * @package eFront

 */
class EfrontFileOnlyFilterIterator extends FilterIterator
{
    /**

     * Accept method

     *

     * The accept method returns true only if the current element

     * is a file

     *

     * @return boolean True if the current element is a file

     * @since 3.5.0

     * @access public

     */
    function accept() {
        return is_file($this -> key());
    }
}
/**

 * Return only files that have a DB representation

 *

 * @package eFront

 */
class EfrontDBOnlyFilterIterator extends FilterIterator
{
    /**

     * Accept method

     *

     * The accept method returns true only if the current element

     * has a db representation (equivalent to having an id different than -1)

     *

     * @return boolean True if the current element has a DB representation

     * @since 3.5.0

     * @access public

     */
    function accept() {
        if ($this -> current() -> offsetGet('id') != -1) {
            return true;
        }
    }
}
/**

 * Filter files based on a Regular Expression

 *

 * @package eFront

 */
class EfrontREFilterIterator extends FilterIterator
{
    /**

     * The Regular Expression to use to filter files

     *

     * @var string

     * @since 3.5.0

     * @access public

     */
    public $re;
    /**

     * Whether to include the filtered files (true) or exclude them (false)

     *

     * @var boolean

     * @since 3.5.0

     * @access public

     */
    public $mode;
    /**

     * Class constructor

     *

     * The class constructor calls the FilterItearator constructor and assigns

     * the $re and $mode parameters

     *

     * @param ArrayIterator $it The iterator

     * @param string $re The regular expression to use

     * @param boolean $mode Whether to include or exclude the filtered files from the data set

     */
    function __construct($it, $re, $mode = true) {
        parent :: __construct($it);
        is_array($re) ? $this -> re = $re : $this -> re = array($re);
        $this -> mode = $mode;
    }
    /**

     * Accept method

     *

     * The accept method filters in or out (based on $mode value) the files

     * from the data set

     *

     * @return boolean True if the current element is eligible

     * @since 3.5.0

     * @access public

     */
    function accept() {
        $result = array();
        foreach ($this -> re as $regExp) {
            $this -> mode ? $result[] = preg_match($regExp, $this -> key()) : $result[] = !preg_match($regExp, $this -> key());
        }
        return array_product($result);
    }
}
/**

 * Filter files based on type

 *

 * @package eFront

 */
class EfrontFileTypeFilterIterator extends FilterIterator
{
    /**

     * The file types to include

     *

     * @var string

     * @since 3.5.0

     * @access public

     */
    public $fileTypes;
    /**

     * Whether to include the filtered files (true) or exclude them (false)

     *

     * @var boolean

     * @since 3.5.0

     * @access public

     */
    public $mode;
    /**

     * Class constructor

     *

     * The class constructor calls the FilterItearator constructor and assigns

     * the $fileTypes and $mode parameters

     *

     * @param ArrayIterator $it The iterator

     * @param string $fileTypes The file types to examine

     * @param boolean $mode Whether to include or exclude the filtered files from the data set

     */
    function __construct($it, $fileTypes, $mode = true) {
        parent :: __construct($it);
        is_array($fileTypes) ? $this -> fileTypes = $fileTypes : $this -> fileTypes = array($fileTypes);
        $this -> mode = $mode;
    }
    /**

     * Accept method

     *

     * The accept method filters in or out (based on $mode value) the files

     * from the data set

     *

     * @return boolean True if the current element matches the criteria

     * @since 3.5.0

     * @access public

     */
    function accept() {
        $result = array();
        if (in_array(mb_strtolower($this -> current() -> offsetGet('extension')), $this -> fileTypes)) {
            $this -> mode ? $return = true : $return = false;
        } else {
            $this -> mode ? $return = false : $return = true;
        }
        return $return;
    }
}
?>
