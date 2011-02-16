<?php
/**

* Replaces occurences of the form #filter:timestamp-1132843907# with the current date

*/
function smarty_outputfilter_eF_template_formatTimestamp($compiled, &$smarty) {
    $new = preg_replace("/#filter:timestamp-(\d{9,10})#/e", "formatTimestamp('\$1')", $compiled);
    $new = preg_replace("/#filter:timestamp_time-(\d{9,10})#/e", "formatTimestamp('\$1', 'time')", $new);
    $new = preg_replace("/#filter:timestamp_time_nosec-(\d{9,10})#/e", "formatTimestamp('\$1', 'time_nosec')", $new);
    $new = preg_replace("/#filter:timestamp_interval-(\d{9,10})#/e", "eF_convertIntervalToTime(time() - \$1, true)", $new);
    $new = preg_replace("/#filter:timestamp_time_only_nosec-(\d{9,10})#/e", "formatTimestamp('\$1', 'time_only_nosec')", $new);
    /*If filter is found without timestamp, erase filter*/
    $new = preg_replace("/#filter:timestamp-\d?#/e", "", $new);
    $new = preg_replace("/#filter:timestamp_time-\d?#/e", "", $new);
    $new = preg_replace("/#filter:timestamp_time_nosec-\d?#/e", "", $new);
    $new = preg_replace("/#filter:timestamp_interval-\d?#/e", "", $new);
    $new = preg_replace("/#filter:timestamp_time_only_nosec-\d?#/e", "", $new);
    return $new;
}
?>
