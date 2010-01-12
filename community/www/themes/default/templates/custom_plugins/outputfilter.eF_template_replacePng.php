<?php
/**
* Replaces PNGs with equivalent GIFs
*/

function smarty_outputfilter_eF_template_replacePng($compiled, &$smarty) {

    preg_match_all("/images(\/.*)?\/((.*)\.png)/U", $compiled, $images);            // /U is necessary here

//    if (!defined(G_CURRENTTHEMEPATH)) {
//        define("G_CURRENTTHEMEPATH", "../../");
//    }
    foreach ($images[0] as $image) {
        //echo G_ROOTPATH.'www/'.$image.': '.str_replace(".png", ".gif", $image);
        if (is_file(G_CURRENTTHEMEPATH.$image)) {
            $new_image = str_replace(".png", ".gif", $image);
            if (is_file(G_CURRENTTHEMEPATH.$new_image)) {
                $patterns[]     = $image;
                $replacements[] = $new_image;
            }
        } 
    }
    
    $new = str_replace($patterns, $replacements, $compiled);
    //$new = preg_replace("/\.png/", ".gif", $compiled);
    return $new;
}
?>