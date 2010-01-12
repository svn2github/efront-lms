
{*moduleCopyContent: Content tree to change order*}
{capture name = "moduleCopyContent"}
	<tr><td class = "moduleCell">
	    {capture name = 't_copy_content_code'}
	        <table>
	            <tr><td class = "labelCell">{$smarty.const._SELECTLESSONTOCOPYFROM}:&nbsp;</td>
	                <td class = "elementCell">
	                <select name = 'user_lessons' onchange = "document.location='{$smarty.server.PHP_SELF}?ctg=copy&from='+this.options[this.selectedIndex].value">
	                    <option value = "0">{$smarty.const._SELECTLESSON}</option>
	                {foreach name = 'directions_list' key = key1 item = direction from = $T_USER_LESSONS}
	                    {foreach name = "lessons_list" key = key2 item = lesson from = $direction}
	                    <option value = "{$lesson.id}" {if $lesson.id == $smarty.get.from}selected{/if}>{$key1} -> {$lesson.name}</option>
	                    {/foreach}
	                {/foreach}
	                </select>
	            </td></tr>
	        </table>
			<br/>
        {if $smarty.get.from}
        	<script>var TransferedNodes = "";</script>
	        <table class = "copyContent">
	            <tr><td style = "width:50%">{eF_template_printBlock title = $smarty.const._DRAGAUNITTOCOPY data = $T_SOURCE_TREE image = "32x32/content.png" alt = '<span class = "emptyCategory">'|cat:$smarty.const._NOCONTENTFOUND|cat:'</span>'}</td>
	            	<td style = "width:50%">{eF_template_printBlock title = $smarty.const._DROPAUNITTOCOPY data = $T_CONTENT_TREE image = "32x32/content.png" alt = '<span class = "emptyCategory">'|cat:$smarty.const._NOCONTENTFOUND|cat:'</span>'}</td></tr>
	            <tr><td></td><td><input id = "save_button" class = "flatButton" type = "button" onclick = "saveTree(this)" value = "{$smarty.const._SAVECHANGES}" /></td></tr>
	        </table>	        

	        <fieldset class = "fieldsetSeparator">
            	<legend>{$smarty.const._COPYOTHERENTITIES}</legend>
                {if $T_CONFIGURATION.disable_glossary != 1}
				<table>
					<tr><td class = "labelCell">{$smarty.const._COPYGLOSSARY}:&nbsp;</td>
						<td class = "elementCell"><input type = "submit" class = "flatButton" value = "{$smarty.const._COPY}" onclick = "copyLessonEntity(this, 'glossary')"></td></tr>
					<tr><td class = "labelCell">{$smarty.const._COPYSURVEYS}:&nbsp;</td>
						<td class = "elementCell"><input type = "submit" class = "flatButton" value = "{$smarty.const._COPY}" onclick = "copyLessonEntity(this, 'questions')"></td></tr>
					<tr><td class = "labelCell">{$smarty.const._COPYQUESTIONS}:&nbsp;</td>
						<td class = "elementCell"><input type = "submit" class = "flatButton" value = "{$smarty.const._COPY}" onclick = "copyLessonEntity(this, 'surveys')"></td></tr>
				</table>
                {/if}
			</fieldset>
            
		{/if}
        {/capture}
        {eF_template_printBlock title=$smarty.const._COPYFROMANOTHERLESSON data=$smarty.capture.t_copy_content_code image='32x32/lesson_copy.png'}
	</td></tr>
{/capture}
                                