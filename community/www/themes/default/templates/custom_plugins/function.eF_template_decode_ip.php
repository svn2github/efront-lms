<?php
/*
{eF_template_decode_ip ip='3e674945'}	
*/
function smarty_function_eF_template_decode_ip($params, &$smarty)
{
	$hex_ip = $params['ip'];
    if (!$hex_ip) {
        return '';
    }

    $dotquad_ip = hexdec(mb_substr($hex_ip,0,2)).'.'.
                  hexdec(mb_substr($hex_ip,2,2)).'.'.
                  hexdec(mb_substr($hex_ip,4,2)).'.'.
                  hexdec(mb_substr($hex_ip,6,2));

    return $dotquad_ip;

}
?>