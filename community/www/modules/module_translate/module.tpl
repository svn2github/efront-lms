{*Smarty template*}

{capture name = 't_module_translate_code}

<div id="translate_main" style = "width:100%">
  	<form class="translate_query-box" style = "width:100%" onsubmit="return translate_submitChange();">
  	<table style = "width:100%">	
  <tr style = "width:100%"><td>
  	<textarea class="translate_query-input" style = "width:100%" rows="10" id="source" type="text">{$smarty.const._TRANSLATE_HELLOWORLD}</textarea>
</td></tr>
<tr><td>
  		<select name="src" id="src"></select>
  		>>
  		<select name="dst" id="dst"></select>

  		<input class="translate_button" type="submit" value="{$smarty.const._TRANSLATE_TRANSLATE}"/>
</td></tr>	
	</table>
  	</form>
</div>

<div id="translate_results">
  <div id="translate_results_title">{$smarty.const._TRANSLATE_TRANSLATION}</div>
  <div id="translate_results_body"></div>
</div>

<script>
var sessionlanguage = '{$smarty.session.s_language}';
var errortranslating= '{$smarty.const._TRANSLATE_ERRORTRANSLATING}';
</script>
{/capture}


{eF_template_printBlock title=$smarty.const._TRANSLATE_TRANSLATE data=$smarty.capture.t_module_translate_code image=$T_MODULE_BASELINK|cat:'images/planet32.png' absoluteImagePath=1}
