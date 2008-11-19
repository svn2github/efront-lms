<?php
/**
* Smarty plugin: eF_template_printIconMenu function
*/
function smarty_function_eF_template_printIconMenu($params, &$smarty) {
    //if (mb_strlen($params['lesson_name']) > 23) {
    //    $params['lesson_name'] = mb_substr($params['lesson_name'], 0, 20).'...';
    //}

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
            var body_tag = document.getElementsByTagName(\'body\');                 //The body tag controls the current ctg colors. i.e. body_lessons sets the color to ctg="lessons" colors
            body_tag[0].id = "body_"+id;

            if(active_id != id)
            {
                if(document.getElementById(active_id)) {
                    document.getElementById(active_id).className = "menuTableInactive";
                }
                if(document.getElementById(active_id+"_a")) {
                    document.getElementById(active_id+"_a").className = "menuLinkInactive";
                }
                active_id = id;

                if(document.getElementById(id)) {
                    document.getElementById(id).className = "topTitle leftAlign";
                }
                if(document.getElementById(id+"_a")) {
                        document.getElementById(active_id+"_a").className = "menuLinkActive";
                }
            }
        }

        function changeColorOnRefresh() {
                changeTDcolor("control_panel")
        }
        </script>
        <ul class="outerList">
        ';

     $maxlen = 16;
     $maxlen_title = 14;

    foreach ($params['menu'] as $category => $submenu) {
        switch ($category) {
            case 'lesson' : $title = $params['lesson_name'];  break;
            case 'general': $title = _MYOPTIONS; break;
            default: break;
        }
        //$idstr = $category == 'lesson' ? 'id = "lessonid" ' : '';

        $str .= '<li class="title"><img src = "images/others/blank.gif" class="minus" onClick="toggleVisibility(document.getElementById(\''.$category.'_ul\'),this)" title="'._SHOWHIDE.'" alt="'._SHOWHIDE.'" style="vertical-align:middle;position:relative;left:-2px;float:right;"/>'.$title.'</li>
        <li class="container"><ul id="'.$category.'_ul" class="innerList">
            ';

        foreach ($submenu as $key => $menu) {
            if (isset($menu['num'])) {
                $str_num = '('.$menu['num'].')';
            }
            if(isset($menu['image'])) {
                $str_img = '<img style="border:0; float: left;" src="images/16x16/'.$menu['image'].'.png" alt="" />';
            }

            $str .= '<li id="'.$key.'">';
            $str .= '<a href="'.$menu['link'].'"';
            if (isset($menu['target'])) {
               $str .= ' target="'.$menu['target'].'" id="'.$key.'_a"';
            } else {
               $str .= ' target="mainframe" id="'.$key.'_a"';
            }
			$str .= 'title = "'.$menu['title'].'">';
            $i_length = mb_substr_count($menu['title'],"i") + mb_substr_count($menu['title'],"é") + mb_substr_count($menu['title'],"ß");
            if (mb_strlen($menu['title']) + (isset($menu['num'])? 2+mb_strlen($menu['num']) : 0 ) - $i_length > $maxlen) {
                $str .= $str_img.mb_substr($menu['title'], 0, $maxlen - 3 - mb_strlen($menu['title']) + $i_length).'...';
            } else {
                $str .= $str_img.$menu['title'];
            }

            $str .= '</a></li>
            ';
            unset($str_num);
        }

        $str .= '
        </ul></li>
    ';

    }

    $str .= '</ul>
    ';

    return $str;
}

?>