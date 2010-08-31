<?php
/**

 * Smarty plugin: smarty_function_eF_template_printCalendar function. Prints inner table

 *

 */
function smarty_function_eF_template_printCalendar($params, &$smarty) {
 $events = $params['events'];
 //isset($params['ctg']) ? $current_ctg = $params['ctg'] : $current_ctg = 'control_panel';             //If a ctg is defined (e.g. ctg=calendar), use this as the links target. Otherwise, use control_panel (default)
 foreach ($events as $key => $event) {
  $temp = getdate($key);
  foreach ($event['data'] as $key2 => $value) {
   $event['data'][$key2] = str_replace("&#039;","&amp;#039;", $event['data'][$key2]);
  }
  $events_per_day[mktime(0, 0, 0, $temp['mon'], $temp['mday'], $temp['year'])][$key] = $event['data'];
 }
 if (!isset($params['timestamp'])) {
  $params['timestamp'] = time();
 }
 $timestamp_info = getdate($params['timestamp']);

 $previous_month = mktime(0, 0, 0, $timestamp_info['mon'] - 1, 1, $timestamp_info['year']);
 $next_month = mktime(0, 0, 0, $timestamp_info['mon'] + 1, 1, $timestamp_info['year']);
 $previous_year = mktime(0, 0, 0, $timestamp_info['mon'] , 1, $timestamp_info['year'] - 1);
 $next_year = mktime(0, 0, 0, $timestamp_info['mon'] , 1, $timestamp_info['year'] + 1);

 $firstday = mktime(0, 0, 0, $timestamp_info['mon'] , 1, $timestamp_info['year']);
 $lastday = mktime(0, 0, 0, $timestamp_info['mon'] + 1, 0, $timestamp_info['year']);
 $firstday_info = getdate($firstday);
 if ($firstday_info['wday'] == 0) {
  $firstday_info['wday'] = 7;
 }

 $lastday_info = getdate($lastday);
 if ($lastday_info['wday'] == 0) {
  $lastday_info['wday'] = 7;
 }
 $today = getdate(time());
 $today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);

 isset($_GET['view_calendar']) && eF_checkParameter($_GET['view_calendar'], 'timestamp') ? $view_calendar = $_GET['view_calendar'] : $view_calendar = $today;
 isset($_GET['show_interval']) ? $show_interval_link = '&show_interval='.$_GET['show_interval'] : $show_interval_link = '';

 $str = '
    <table>
        <tr><td>
            <table class = "calendarHeader" >
                <tr class = "calendar">
                    <td class = "calendarHeader">
                        <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.($_GET['ctg'] ? $_GET['ctg'] : 'calendar').'&view_calendar='.$previous_month.$show_interval_link.'">&laquo; </a>
                        '.iconv(_CHARSET, 'UTF-8', strftime('%B', $params['timestamp'])).'
                        <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.($_GET['ctg'] ? $_GET['ctg'] : 'calendar').'&view_calendar='.$next_month.$show_interval_link.'">&raquo; </a>
                    </td>
                    <td class = "calendarHeader">
                        <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.($_GET['ctg'] ? $_GET['ctg'] : 'calendar').'&view_calendar='.$previous_year.$show_interval_link.'">&laquo; </a>
                        '.$timestamp_info['year'].'
                        <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.($_GET['ctg'] ? $_GET['ctg'] : 'calendar').'&view_calendar='.$next_year.$show_interval_link.'"> &raquo;</a>
                    </td></tr>
            </table>
        </td></tr>
        <tr><td>
            <table class = "calendar">
                <tr><td class = "calendar">&nbsp;'._MON.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._TUE.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._WED.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._THU.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._FRI.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._SAT.'&nbsp;</td>
                    <td class = "calendar">&nbsp;'._SUN.'&nbsp;</td>
                </tr><tr>';

 $weeks = ceil(($firstday_info[wday] + $lastday_info[mday] - 1) / 7);
 $count = 1;

 for ($i = 1; $i < $weeks + 1; $i++) {
  $str .= '
            <tr>';
  for ($j = 1; $j <= 7; $j++) {

   if ($count >= $firstday_info['wday'] && $count < $lastday_info['mday'] + $firstday_info['wday']) {
    $day = $count - $firstday_info['wday'] + 1;
   } else {
    $day = '';
   }

   $day_timestamp = mktime(0, 0, 0, (int)$timestamp_info['mon'], (int)$day, (int)$timestamp_info['year']);
   $count++;

   if (!empty($events_per_day[$day_timestamp])) {
    $className = 'eventCalendar';
    $dayEvents = array();

    foreach ($events_per_day[$day_timestamp] as $key => $value) {
     if (date("His",$key) == '0') {
      $dayEvents[] = rawurlencode(implode(", ", strip_tags($value)));
     } else {
      $dayEvents[] = '#filter:timestamp_time_only_nosec-'.$key.'# '.rawurlencode(strip_tags(implode(", ", $value)));
     }
    }
    $dayEvents = implode("<br>", $dayEvents);
//This requires wz_tooltip.js See outputfilter.eF_template_includeScripts.php for when this gets loaded
    $day_str = '
                 <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.($_GET['ctg'] ? $_GET['ctg'] : 'calendar').'&view_calendar='.$day_timestamp.'" onmouseover = "this.T_PADDING = 5; this.T_TEXTALIGN = \'left\'; this.T_TITLE = \'#filter:timestamp-'.$day_timestamp.'#\'; return escape(decodeURI(\''.($dayEvents).'\'))">'.$day.'</a>';
   } else {
    $day_str = '
                    <a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.($_GET['ctg'] ? $_GET['ctg'] : 'calendar').'&view_calendar='.$day_timestamp.'">'.$day.'</a>';
   }

   if ($day_timestamp == $today) {
    $className = 'todayCalendar';
   }

   if ($day_timestamp == $view_calendar) {
    $className .= ' viewCalendar';
   }

   $str .= '
                <td class = "calendar '.$className.'">'.$day_str.'</td>';
   $className = '';
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
