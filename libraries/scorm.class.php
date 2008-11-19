<?php
class EfrontScorm
{
    public static function parseManifest($data) {
        $parser   = xml_parser_create(); 
    
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
        xml_parse_into_struct($parser, $data, $tagContents, $tags); 
        xml_parser_free($parser);   
    
        $currentParent = array(0 => 0);

        for ($i = 0; $i < sizeof($tagContents); $i++) {
            if ($tagContents[$i]['type'] != 'close') {
                $tagArray[$i] = array('parent_index' => end($currentParent), 
                                      'tag'          => $tagContents[$i]['tag'],
                                      'value'        => $tagContents[$i]['value'],
                                      'attributes'   => $tagContents[$i]['attributes'],
                                      'children'     => array()
                                );
                array_push($tagArray[end($currentParent)]['children'], $i);
            }
            if ($tagContents[$i]['type'] == 'open') {
                array_push($currentParent, $i);
            } else if ($tagContents[$i]['type'] == 'close') {
                array_pop($currentParent);
            }
            
        }
        return $tagArray;   
    }    
}

?>