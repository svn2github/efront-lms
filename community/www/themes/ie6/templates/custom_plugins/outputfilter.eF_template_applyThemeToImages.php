<?php
/**
* Replaces images paths with correct ones for current theme
*/

function smarty_outputfilter_eF_template_applyThemeToImages($compiled, &$smarty) {

    preg_match_all('/(?<!\/)images(\/.*)?\/((.*)\.\w{3})/U', $compiled, $images);            // /U is necessary here
//pr($images);exit;

    $patterns = $replacements = array();
    foreach ($images[0] as $image) {
        //We don't replace modules images
        if (strpos($image, G_MODULESURL) === false) {
	        if (is_file(G_CURRENTTHEMEPATH.$image)) {
	        	$patterns[] 	= $image;
	        	$replacements[] = G_CURRENTTHEMEURL.$image;
	        } else {
	        	$patterns[] 	= $image;
	        	$replacements[] = G_DEFAULTTHEMEURL.$image;
	        }
        } 
    }
	$patterns 	  = array_unique($patterns);
	$replacements = array_unique($replacements);
	    
    $new = str_replace($patterns, $replacements, $compiled);
    return $new;
}
?>