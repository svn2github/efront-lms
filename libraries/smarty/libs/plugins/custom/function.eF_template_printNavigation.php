<?php
/**
* Smarty plugin: smarty_function_eF_template_printNavigation function. 
*
*/
function smarty_function_eF_template_printNavigation($params, &$smarty) {
    $previous = $params['previous'];
    $next     = $params['next'];

    if ($previous AND $previous['id'] != 0) {
        //($previous['ctg_type'] == 'tests') ? $link = basename($_SERVER['PHP_SELF']).'?ctg=tests&show_test_content_ID='.$previous['id'] : $link = basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$previous['id'];
        ($previous['ctg_type'] == 'tests') ? $link = basename($_SERVER['PHP_SELF']).'?ctg=tests&view_unit='.$previous['id'] : $link = basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$previous['id'];

        $str = '
                <b>
                <a href="'.$link.'" title="'.$previous['name'].'">
                    <img border = "0" src = "images/24x24/navigate_left.png" title = "'.$previous['name'].'" alt = "'.$previous['name'].'" /></a>&nbsp</b>';
        $connector = "<img src = \"images/others/spacer.gif\" />";
    }
    
    if ($next AND $next['id'] != 0) {
        //($next['ctg_type'] == 'tests') ? $link = basename($_SERVER['PHP_SELF']).'?ctg=tests&show_test_content_ID='.$next['id'] : $link = basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$next['id'];
        ($next['ctg_type'] == 'tests') ? $link = basename($_SERVER['PHP_SELF']).'?ctg=tests&view_unit='.$next['id'] : $link = basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$next['id'];

        $str .= $connector.'
                <b>
                <a href="'.$link.'" title="'.$next['name'].'">
                    <img border = "0" src = "images/24x24/navigate_right.png" title = "'.$next['name'].'" alt = "'.$next['name'].'" /></a>&nbsp;</b>';
    } 
    
    return $str;

}

?>