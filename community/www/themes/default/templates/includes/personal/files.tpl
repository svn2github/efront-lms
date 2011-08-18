{capture name = 't_file_record_code'}
 {$T_FILE_MANAGER}
 <div class = "infoCell">{$smarty.const._PLEASENOTEFILESARESHAREDWITHSUPERVISOR}</div>
{/capture}

{eF_template_printBlock title = $smarty.const._FILES data = $smarty.capture.t_file_record_code image = '32x32/folders.png'}
