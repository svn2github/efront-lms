<?php

require_once dirname(__FILE__) . '/../../common.php';
require_once dirname(__FILE__) . '/../../Converter.php';

// -----------------------------------------------------------------------------

define('PATH_BASE', dirname(__FILE__) );

//if (isset($_GET['fileName'])){
	//$fileName = $_GET['fileName'];
//}
//$inputFilename = PATH_BASE . DIRECTORY_SEPARATOR . $fileName.'.rtf';  // convert this file
//$outputFormat  = 'pdf';                                             // into this format

$inputFilename = PATH_BASE . DIRECTORY_SEPARATOR . $fileName.'.rtf';  // convert this file
$outputFormat  = 'pdf';   

// -----------------------------------------------------------------------------

$outputFilename = Converter::getFilename($inputFilename, $outputFormat);

//printf('Converting %s to %s... ', basename($inputFilename), basename($outputFilename));

$convertedDocument = Converter::convert($inputFilename, $outputFormat);
if (false !== $convertedDocument) {
    file_put_contents($outputFilename, $convertedDocument);
    print("DONE.\n");
} else {
    print("ERROR.\n");
}

// -----------------------------------------------------------------------------
