<?php
/**

* Replaces images paths with correct ones for current theme

*/
function smarty_outputfilter_eF_template_applyImageMap($compiled, &$smarty) {
 ini_set("pcre.backtrack_limit", "1000000");
    //First, match all the existing classes of the images, for example <img class = "close"> and replace them with <img ###close%%% >
    $compiled = preg_replace('/(<img )(([^>])*)(class\s*=\s*[\'"](.*)[\'"])(.*>)/U', "$1###$5%%%$2$6", $compiled);
    //Now, replace image src tag with transparent.gif and add the image map classes, 'spriteXX spriteXX-imagename'
    $matches = array('/(<img .*)(?<!\/)\Wimages\/16x16(\/.*)?\/((.*)\.\w{3}\W)/U',
         '/(<img .*)(?<!\/)\Wimages\/32x32(\/.*)?\/((.*)\.\w{3}\W)/U');
    $replacements = array("$1'images/others/transparent.gif' class = 'sprite16 sprite16-$4'",
           "$1'images/others/transparent.gif' class = 'sprite32 sprite32-$4'");
    $new = preg_replace($matches, $replacements, $compiled);
    //Now, reinsert preexisting classes inside the new classes, making it <img class = 'spriteXX spriteXX-imagename close'
    $new = preg_replace('/(<img (###([^>]*)%%%)([^>]*))class = \'(.*)\'\s*(.*>)/U', '<img $4 class = \'$3 $5\'$6', $new);
    //Finally, for all the images that do not belong to an image map, restore classes to their position, from ###close%%% to class = 'close'
    $new = preg_replace('/(<img (###([^>]*)%%%)([^>]*))(.*>)/U', '<img $4 class = \'$3\'$5', $new);
    return $new;
}
?>
