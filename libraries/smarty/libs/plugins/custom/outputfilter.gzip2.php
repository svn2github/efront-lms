<?php

function smarty_outputfilter_gzip2($tpl_source, &$smarty)
{
    ob_start ("ob_gzhandler");
    
	return $tpl_source;
}
?>