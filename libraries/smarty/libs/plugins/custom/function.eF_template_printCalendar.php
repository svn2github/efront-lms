<?php
/**
* Smarty plugin: smarty_function_eF_template_printCalendar function. Prints inner table
*
*/
function smarty_function_eF_template_printCalendar($params, &$smarty) {

    $events = $params['events'];
    isset($params['ctg']) ? $current_ctg = $params['ctg'] : $current_ctg = 'control_panel';             //If a ctg is defined (e.g. ctg=calendar), use this as the links target. Otherwise, use control_panel (default)
    foreach ($events as $key => $event) {        
        $temp = getdate($key);
        foreach ($event['data'] as $key2 => $value) {
            $event['data'][$key2] = str_replace("&#039;","&amp;#039;", $event['data'][$key2]);
        }
        $events_per_day[mktime(0, 0, 0, $temp['mon'], $temp['mday'], $temp['year'])][$key] = $event['data'];
    }
//pr($events_per_day);
//pr($events);    
    if (!isset($params['timestamp'])) {
        $params['timestamp'] = time();
    }
    $timestamp_info = getdate($params['timestamp']);    

    $previous_month = mktime(0, 0, 0, $timestamp_info['mon'] - 1, 1, $timestamp_info['year']);
    $next_month     = mktime(0, 0, 0, $timestamp_info['mon'] + 1, 1, $timestamp_info['year']);
    $previous_year  = mktime(0, 0, 0, $timestamp_info['mon']    , 1, $timestamp_info['year'] - 1);
    $next_year      = mktime(0, 0, 0, $timestamp_info['mon']    , 1, $timestamp_info['year'] + 1);

    $firstday = mktime(0, 0, 0, $timestamp_info['mon']    , 1, $timestamp_info['year']);
    $lastday  = mktime(0, 0, 0, $timestamp_info['mon'] + 1, 0, $timestamp_info['year']);
    $firstday_info = getdate($firstday);
    if ($firstday_info['wday'] == 0) {
        $firstday_info['wday'] = 7;
    }

    $lastday_info  = getdate($lastday);
    if ($lastday_info['wday'] == 0) {
        $lastday_info['wday'] = 7;
    }
    $today = getdate(time());
    $today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);

    isset($_GET['view_calendar']) && eF_checkParameter($_GET['view_calendar'], 'timestamp') ? $view_calendar = $_GET['view_calendar'] : $view_calendar = $today;
    isset($_GET['show_interval']) ? $show_interval_link = '&show_interval='.$_GET['show_interval'] : $show_interval_link = '';

    if (MODULE_HCD_INTERFACE) {
        isset($_GET['type'])? $type = "&type=".$_GET['type'] : $type = "&type=0";
    } else {
        $type = "";
    }

    $str = '
    <table>
        <tr><td style = "padding: 0px">
            <table cellpadding = "0" width = "100%">
                <tr class = "calendar">
                    <td class = "calendar">
                        <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.$current_ctg.'&view_calendar='.$previous_month.$show_interval_link.$type.'">&laquo; </a>
                        '.iconv(_CHARSET, 'UTF-8', strftime('%B', $params['timestamp'])).'
                        <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.$current_ctg.'&view_calendar='.$next_month.$show_interval_link.$type.'">&raquo; </a>
                    </td>
                    <td class = "calendar" style = "text-align:right;border-left:0px">
                        <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.$current_ctg.'&view_calendar='.$previous_year.$show_interval_link.$type.'">&laquo; </a>
                        '.$timestamp_info['year'].'
                        <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.$current_ctg.'&view_calendar='.$next_year.$show_interval_link.$type.'"> &raquo;</a>
                    </td></tr>
            </table>
        </td></tr>
        <tr><td style = "padding: 0px">
            <table class = "calendar" cellpadding = "0" width = "100%">
                <tr><td class = "calendar">&nbsp;'._MON.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._TUE.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._WED.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._THU.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._FRI.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._SAT.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._SUN.'&nbsp;</td>
                </tr><tr>';

    //                echo $firstday_info['wday'] + $lastday_info['mday'];

    $weeks = ceil(($firstday_info[wday] + $lastday_info[mday] - 1) / 7);
    $count = 1;

    for ($i = 1; $i < $weeks + 1; $i++) {
        $str .= '
            <tr>';
        for ($j = 1; $j <= 7; $j++) {

            if ($count >= $firstday_info['wday'] && $count < $lastday_info['mday'] + $firstday_info['wday']) {
                $day = $count - $firstday_info['wday'] + 1;
            }  else {
                $day = '';
            }
            
            $day_timestamp = mktime(0, 0, 0, $timestamp_info['mon'], $day, $timestamp_info['year']);
            $count++;

            if (!empty($events_per_day[$day_timestamp])) {
                $td_style  = 'background-color: #A3E0CC;';
                $dayEvents = array();
                foreach ($events_per_day[$day_timestamp] as $key => $value) {
                    //pr($key);
                    $dayEvents[] = '#filter:timestamp_time_only_nosec-'.$key.'# '.implode(", ", $value);                        
                }
                $dayEvents = implode("<br>", $dayEvents);
                //$events[$day_timestamp] = str_replace("&#039;","&amp;#039;", $events[$day_timestamp]);
                    $day_str = '
                <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg=calendar&view_calendar='.$day_timestamp.$show_interval_link.$type.'" onmouseover = "this.T_PADDING = 5; this.T_TEXTALIGN = \'left\'; this.T_TITLE = \'#filter:timestamp-'.$day_timestamp.'#\'; return escape(\''.$dayEvents.'\')">'.$day.'</a>';//This requires wz_tooltip.js See outputfilter.eF_template_includeScripts.php for when this gets loaded 
            } else {
                        $day_str = '
                    <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg=calendar&view_calendar='.$day_timestamp.$show_interval_link.$type.'">'.$day.'</a>';
            }

            if ($day_timestamp == $today) {
                $td_style = 'background-color: #889977;';
            }

            if ($day_timestamp == $view_calendar) {
                $td_style .= 'border:1px solid #FF8C00;';
            }

            $str .= '
                <td class = "calendar" style = "'.$td_style.' text-align:center;height:18px;width:20px;">'.$day_str.'</td>';
            $td_style = '';
        }

        $str .= '
            </tr>';
    }

    $str .= '
                </table>
        </td></tr>
    </table>';

    return $str;
}

?>