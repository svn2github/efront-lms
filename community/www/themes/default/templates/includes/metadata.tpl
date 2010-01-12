
    {*moduleContentMetadata: Show content metadata*}
    {capture name = "moduleContentMetadata"}
        <tr><td class = "moduleCell">
	        {capture name = 't_content_info_code'}
	            <fieldset class = "fieldsetSeparator">
	                <legend>{$smarty.const._CONTENTMETADATA}</legend>
	                {$T_CONTENT_METADATA_HTML}
	            </fieldset>
	        {/capture}
	        {eF_template_printBlock title = $smarty.const._METADATAFORUNIT|cat:' &quot;'|cat:$T_CURRENT_UNIT.name|cat:'&quot;' data = $smarty.capture.t_content_info_code image = '32x32/information.png'}
		</td></tr>
	{/capture}
