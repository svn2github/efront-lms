<?php
/**
* Replaces occurences of the form #filter:timestamp-1132843907# with the current date
*/
function smarty_outputfilter_eF_template_formatTimestamp($compiled, &$smarty) {
    switch ($GLOBALS['configuration']['date_format']) {
        case "YYYY/MM/DD": $format = '%Y %b %d'; break;
        case "MM/DD/YYYY": $format = '%b %d %Y'; break;
        case "DD/MM/YYYY": default: $format = '%d %b %Y'; break;
    }
    $new = preg_replace("/#filter:timestamp-(\d{9,10})#/e", "iconv(_CHARSET, 'UTF-8', strftime('$format', '\$1'))", $compiled);
    $new = preg_replace("/#filter:timestamp_time-(\d{9,10})#/e", "iconv(_CHARSET, 'UTF-8', strftime('$format, %H:%M:%S', '\$1'))", $new);
    $new = preg_replace("/#filter:timestamp_time_nosec-(\d{9,10})#/e", "iconv(_CHARSET, 'UTF-8', strftime('$format, %H:%M', '\$1'))", $new);
    $new = preg_replace("/#filter:timestamp_interval-(\d{9,10})#/e", "eF_convertIntervalToTime(time() - \$1, true)", $new);    
    $new = preg_replace("/#filter:timestamp_time_only_nosec-(\d{9,10})#/e", "iconv(_CHARSET, 'UTF-8', strftime('%H:%M', '\$1'))", $new);    
    /*If filter is found without timestamp, erase filter*/
    $new = preg_replace("/#filter:timestamp-#/e", "", $new);           
    $new = preg_replace("/#filter:timestamp_time-#/e", "", $new);
    $new = preg_replace("/#filter:timestamp_time_nosec-#/e", "", $new);
    $new = preg_replace("/#filter:timestamp_interval-#/e", "", $new);
    $new = preg_replace("/#filter:timestamp_time_only_nosec-#/e", "", $new);
    return $new;
}

?>