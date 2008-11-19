<?php
/**
* Export resources metadata
* 
* This is used to accumulate metadata representation of efront resources, which can be used, for example, in an indexer engine
* Usage:
* xml_indexer.php&type=<type>
* where type can be 'content', 'file', etc
* 
* @package eFront
* @version 3.5.0
*/

$path = "../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

//debug();

$xmlString = '';
switch ($_GET['type']) {
    case 'content':
        $result = eF_getTableDataFlat("content", "id, metadata");
        break;
    case 'file':
        $result = eF_getTableDataFlat("files", "id, metadata");
        break;
    case 'lesson':
        $result = eF_getTableDataFlat("lessons", "id, metadata");
        break;
    case 'course':
        $result = eF_getTableDataFlat("courses", "id, metadata");
        break;
    default:
        exit; 
        break;
}

$result = array_combine($result['id'], $result['metadata']);

foreach ($result as $id => $value) {
    if ($value && unserialize($value)) {
        $metadata   = unserialize($value);
        $dc         = new DublinCoreMetadata($metadata);
        $xmlString .= '
	<efront_resource>
		'.($dc -> toXML('dc:')).'
		<dc:identifier xsi:type="dcterms:URI">'.G_SERVERNAME.'view_resource.php?type='.$_GET['type'].'&amp;id='.$id.'</dc:identifier>
	</efront_resource>';
    }
}
$xmlString = '
<efront_resources>'.$xmlString.'</efront_resources>';

    
header("Content-type: text/xml;charset=UTF-8");
echo '<?xml version="1.0" encoding="UTF-8"?>
	<metadata xmlns="http://example.org/myapp/" 
			  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
			  xsi:schemaLocation="http://example.org/myapp/ 
			  http://example.org/myapp/schema.xsd" 
			  xmlns:dc="http://purl.org/dc/elements/1.1/" 
			  xmlns:dcterms="http://purl.org/dc/terms/">
	'.$xmlString.'</metadata>';
?>