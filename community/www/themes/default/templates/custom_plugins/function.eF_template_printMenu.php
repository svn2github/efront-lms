<?php
/**
* Smarty plugin: eF_template_printMenu function
*/
function smarty_function_eF_template_printMenu($params, &$smarty) {
    //if (mb_strlen($params['lesson_name']) > 23) {
    //    $params['lesson_name'] = mb_substr($params['lesson_name'], 0, 20).'...';
    //}
echo "!!!!!!!!!!!!!!!!!!!!!!!";
    $str = '
    <script type = "text/javascript">
    var ie_str;
    var detect = navigator.userAgent.toLowerCase();
    detect.indexOf("msie") > 0 ? ie_str = "?ie=1" :ie_str = "";
    ';
    //if($params['user_type']=='student')
    //      $str .= "var active_id = 'control_panel'";
    //else if($params['user_type']=='professor')
    //      $str .= "var active_id = 'control_panel'";
    //else
    //      $str .= "var active_id = 'control'";
    $str .= "var active_id = 'something improbable';";
    $str .= '
        function changeTDcolor(id) {
        alert(\'asdf\');
            var body_tag = document.getElementsByTagName(\'body\');                 //The body tag controls the current ctg colors. i.e. body_lessons sets the color to ctg="lessons" colors
            body_tag[0].id = "body_"+id;
            alert(active_id + " "+ id);
                if(active_id != id)
                {
                        if(document.getElementById(active_id))
                        {
                                document.getElementById(active_id).className = "menuTableInactive";
                        }

                        if(document.getElementById(active_id+"_a"))
                        {
                                document.getElementById(active_id+"_a").className = "menuLinkInactive";
                        }
                        active_id = id;

                        if(document.getElementById(id))
                                document.getElementById(id).className = "topTitle rightAlign";
                        if(document.getElementById(id+"_a"))
                        {
                                document.getElementById(active_id+"_a").className = "menuLinkActive";
                        }

                }
        }

      function changeColorOnRefresh() {
        ';
        if($params['user_type']=='student')
        {
              $str .= "var temp_id = 'control_panel';
                ";
        }
        else if($params['user_type']=='professor')
        {
              $str .= "var temp_id = 'control_panel';
                ";
        }
        else
        {
              $str .= "var temp_id = 'control';
                ";
        }

        if($params['ctg']=='lessons')
        {
            $str .= '
                changeTDcolor(temp_id);
                ';
        }
        else
        {
            $str .= '
                changeTDcolor("'.$params['ctg'].'");
                ';
        }

      $str .= '
        }
        </script>
        <div class="sdmenu">
        <table width = "100%" >';

    foreach ($params['menu'] as $category => $submenu) {
        switch ($category) {
            case 'lesson' : $title = $params['lesson_name'];  break;
            case 'general': $title = _MYOPTIONS; break;
            default: break;
        }
        $idstr = $category == 'lesson' ? 'id = "lessonid" ' : '';

            $str .= '
            <tr><td '.$idstr.'align = "right" nowrap class = "horizontalSeparator">';

        $maxlen = 16;
        $maxlen_title = 14;

        $i_length = mb_substr_count($title,"i") + mb_substr_count($title,"é") + mb_substr_count($title,"ß");

        if (mb_strlen($title) - $i_length > $maxlen_title) {   // manos lines. Afairoume ta i,é,ß
            $str .= "<span class = 'title' title = '".$title."'><b>";
            $str .= "<img align = 'left' src = 'images/others/blank.gif' class='minus arrow' title='".$title."' alt='".$title."'/>";
            $str .= mb_substr($title, 0, $maxlen_title - 3 - mb_strlen($title) + $i_length)."...</b></span>";
            } else {
            $str .= "<span class = 'title' title = ''><b>";
            $str .= "<img align = 'left' src = 'images/others/blank.gif' class='minus arrow' title='".$title."' alt='".$title."'/>";
                  $str .= $title;
            $str .= "</b></span>";
            }


        $str .= '</td></tr>

        <tr><td>
            <div class="submenu">
                <table border = "0" width = "100%" cellspacing = "0" cellpadding = "0">';

            foreach ($submenu as $key => $menu) {
                if ($key == $params['ctg']) {
                    $class = "menu"; //was "top_title... Afhnw to if mipws xreiastei na gyrisoume shn palia version. manos lines
                } else {
                    $class = "menu";
                }

                if (isset($menu['num'])) {
                    $str_num = '('.$menu['num'].')';
                }


                $str .= '
                    <tr height = "20">
                        <td id = "'.$key.'" class = "menuTableInactive" >
                            <a id = "'.$key.'_a" href = "'.$menu['link'].'" class = "menuLinkInactive" target = "mainframe"';
                    $i_length = mb_substr_count($menu['title'],"i") + mb_substr_count($menu['title'],"é") + mb_substr_count($menu['title'],"ß");
                    if (mb_strlen($menu['title']) + (isset($menu['num'])? 2+mb_strlen($menu['num']) : 0 ) - $i_length> $maxlen) {
                        $str .= 'title = "'.$menu['title'].$str_num.'">'.mb_substr($menu['title'], 0, $maxlen - 3 - mb_strlen($menu['title']) + $i_length).'...';
                        } else {
                              $str .= ' title="'.$str_num.'">'.$menu['title'];
                        }
                    $str .= '</a>';
                        //<!--<td width = "8" nowrap bgcolor = "'.$_SESSION['COLOR'][$key].'">&nbsp;</td></tr>-->

            unset($str_num);
            }

            $str .= '
                </table>
            </div>
            </td></tr>
            <tr><td>&nbsp;</td></tr>';
    }
    $str .= '</table>
         </div>';

    return $str;
}

?>