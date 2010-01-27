{capture name = 't_inner_table_code}

	<script type="text/javascript" src="{$T_MODULE_BASEDIR}translate_jsapi.js"></script>
		   
      <div id="translate_main_small">
      <form class="translate_query-box" onsubmit="return translate_submitChange();">
      <table width=100%>
      	<tr>
      		<td>
      			<input class="translate_query-input" id="source" type="text" value="{$smarty.const._TRANSLATE_HELLOWORLD}" onclick = "if (firstClick) {ldelim}firstClick=false;this.value = ''{rdelim}"/>
      		</td>
      		<td>
      			<select name="dst" id="dst"></select>
      		</td>
      		<td>
      			<input class="translate_button_innertable" type="submit" value="{$smarty.const._TRANSLATE_TRANSLATE}"/>
      		</td> 
      	</tr>
      </table>
    
      </form>
    </div>
    
    <div id="translate_results_small">
      <div id="translate_results_title">{$smarty.const._TRANSLATE_TRANSLATION}</div>
      <div id="translate_results_body"></div>
    </div>
<script>
var sessionlanguage = '{$smarty.session.s_language}';
var errortranslating= '{$smarty.const._TRANSLATE_ERRORTRANSLATING}';
</script>
  
 
{/capture}

{eF_template_printBlock title = $smarty.const._TRANSLATE_TRANSLATE data = $smarty.capture.t_inner_table_code image = $T_MODULE_BASELINK|cat:'images/planet32.png' absoluteImagePath=1 options = $T_TRANSLATE_INNERTABLE_OPTIONS}
