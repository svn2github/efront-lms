<?php
/**
 * Smarty plugin: smarty_function_eF_template_printProjects function. Prints projects data
 *
 */
function smarty_function_eF_template_printProjects($params, &$smarty) {
    $max_title_size = 50;                                           //The maximum length of the title, after which it is cropped with ...
    $list_fold_size = 5;                                            //The folding occurs in this number of lines

    if (isset($params['limit'])) {
        $limit = min($params['limit'], sizeof($params['data']));
    } else {
        $limit = sizeof($params['data']);
    }

    $params['data'] = array_values($params['data']);

    $str = '
        <table class = "cpanelTable">';
    for ($i = 0; $i < $list_fold_size && $i < $limit; $i++) {
        $title_message = $params['data'][$i]['title'];
        if (mb_strlen($params['data'][$i]['title']) > $max_title_size) {
            $params['data'][$i]['title'] = mb_substr($params['data'][$i]['title'], 0, $max_title_size).'...';                                 //If the project title is large, cut it and append ...
        }

        $str .= '
	            <tr><td>
	                    <span class = "counter">'.($i + 1).'. </span>'; 
        if ($_SESSION['s_type'] == "student") {
            // Students may view a project
            $str .= '<a title="'.$title_message.'" href = "student.php?ctg=projects&view_project='.$params['data'][$i]['id'];

            // Students and professors may have to change lesson session - using the new_lessons_ID parameter for this purpose
            if (isset($params['data'][$i]['show_lessons_id']) && $params['data'][$i]['show_lessons_id'] != 0 && isset($params['data'][$i]['show_lessons_name'])) {
                $str .= '&new_lessons_ID='.$params['data'][$i]['show_lessons_id'].'&sbctg=exercises"><b>'. $params['data'][$i]['show_lessons_name'] . '</b>: ' . $params['data'][$i]['title'].'</a></td>';
            } else {
                $str .= '">'.$params['data'][$i]['title'].'</a></td>';
            }

            $str .= '<td class = "cpanelTime">#filter:user_login-'.$params['data'][$i]['creator_LOGIN'].'#, ';
            $title2 = _DEADLINE.': #filter:timestamp_time-'.$params['data'][$i]['deadline'].'#';
            if ($params['data'][$i]['deadline'] > time()) {
                $str .= '<span title = "'.$title2.'">'._EXPIRESIN.' '.eF_convertIntervalToTime($params['data'][$i]['deadline'] - time(), true)."</span>";
            } else {
                $str .= '<span title = "'.$title2.'">'._EXPIREDBEFORE.' '.eF_convertIntervalToTime(time() - $params['data'][$i]['deadline'], true)."</span>";

            }
            $str .= '</td></tr>';
        } else {
            $str .= '<a title="'.$title_message.'" href = "professor.php?ctg=projects&project_results='.$params['data'][$i]['id'].'">'.$params['data'][$i]['users_LOGIN'].' ('.$params['data'][$i]['title'].')</a></td>
	                <td class = "cpanelTime">';

            $str .= '<span> '.eF_convertIntervalToTime(time() - $params['data'][$i]['upload_timestamp'], true)."&nbsp;"._AGO."</span>";
            $str .= '</td></tr>';

        }
    }
    if ($i == 0) {
        $str .= '
            <tr><td class = "emptyCategory">'._NOPROJECTS.'</td></tr>
        </table>';
    } else {
        $str .= '
            </table>';
    }

     
    return $str;
}

?>