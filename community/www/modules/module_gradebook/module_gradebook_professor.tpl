{* Smarty Template for GradeBook module (Professor) *}

{if $T_GRADEBOOK_MESSAGE}
 <script>
  re = /\?/;
  !re.test(parent.location) ? parent.location = parent.location+'?message={$T_GRADEBOOK_MESSAGE}&message_type=success' : parent.location = parent.location+'&message={$T_GRADEBOOK_MESSAGE}&message_type=success';
 </script>
{/if}

{if $smarty.get.add_column}
{capture name = 't_add_column_code'}
 {$T_GRADEBOOK_ADD_COLUMN_FORM.javascript}
<form {$T_GRADEBOOK_ADD_COLUMN_FORM.attributes}>
 {$T_GRADEBOOK_ADD_COLUMN_FORM.hidden}
 <table style="margin-left:60px;">
  <tr>
   <td class="labelCell">{$T_GRADEBOOK_ADD_COLUMN_FORM.column_name.label}:&nbsp;</td>
   <td class="elementCell">{$T_GRADEBOOK_ADD_COLUMN_FORM.column_name.html}</td>
  </tr>
  <tr>
   <td class="labelCell">{$T_GRADEBOOK_ADD_COLUMN_FORM.column_weight.label}:&nbsp;</td>
   <td class="elementCell">{$T_GRADEBOOK_ADD_COLUMN_FORM.column_weight.html}</td>
  </tr>
  <tr>
   <td class="labelCell">{$T_GRADEBOOK_ADD_COLUMN_FORM.column_refers_to.label}:&nbsp;</td>
   <td class="elementCell">{$T_GRADEBOOK_ADD_COLUMN_FORM.column_refers_to.html}</td>
  </tr>
  <tr>
   <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
   <td></td>
   <td class="elementCell">{$T_GRADEBOOK_ADD_COLUMN_FORM.submit.html}</td>
  </tr>
 </table>
</form>
{/capture}
{eF_template_printBlock title=$smarty.const._GRADEBOOK_ADD_COLUMN data=$smarty.capture.t_add_column_code image=$T_GRADEBOOK_BASELINK|cat:'images/gradebook_logo.png' absoluteImagePath = 1 help = 'Gradebook'}

{else}
{capture name = 't_gradebook_professor_code'}

<table>
 <tr>
  <td>
   <img src="{$T_GRADEBOOK_BASELINK|cat:'images/add.png'}" alt="{$smarty.const._GRADEBOOK_ADD_COLUMN}" title="{$smarty.const._GRADEBOOK_ADD_COLUMN}" style="vertical-align:middle">
   <a href="{$T_GRADEBOOK_BASEURL}&add_column=1&popup=1" target="POPUP_FRAME" onclick="eF_js_showDivPopup('{$smarty.const._GRADEBOOK_ADD_COLUMN}', 0)">{$smarty.const._GRADEBOOK_ADD_COLUMN}</a>&nbsp;
  </td>
  <td style="border-right: 1px solid #333333;"></td>
  <td>
   &nbsp;<img src="{$T_GRADEBOOK_BASELINK|cat:'images/compute_score.png'}" alt="{$smarty.const._GRADEBOOK_COMPUTE_SCORE_GRADE}" title="{$smarty.const._GRADEBOOK_COMPUTE_SCORE_GRADE}" style="vertical-align:middle">
   <a href="{$T_GRADEBOOK_BASEURL}&compute_score_grade=1">{$smarty.const._GRADEBOOK_COMPUTE_SCORE_GRADE}</a>&nbsp;
  </td>
  <td style="border-right: 1px solid #333333;"></td>
  <td>
   &nbsp;<img src="{$T_GRADEBOOK_BASELINK|cat:'images/xls.png'}" alt="{$smarty.const._GRADEBOOK_EXPORT_EXCEL}" title="{$smarty.const._GRADEBOOK_EXPORT_EXCEL}" style="vertical-align:middle">
   <a href="javascript:void(0)" onclick="location=('{$T_GRADEBOOK_BASEURL}&export_excel='+Element.extend(this).next().options[this.next().options.selectedIndex].value)">{$smarty.const._GRADEBOOK_EXPORT_EXCEL}</a>
   <select id="excel" name="excel">
    <option value="one">{$smarty.const._GRADEBOOK_EXPORT_EXCEL_ONE}</option>
    <option value="all">{$smarty.const._GRADEBOOK_ALL_LESSONS}</option>
   </select>&nbsp;
  </td>
{if sizeof($T_GRADEBOOK_GRADEBOOK_LESSONS) != 0}
  <td style="border-right: 1px solid #333333;"></td>
  <td>
   &nbsp;<img src="{$T_GRADEBOOK_BASELINK|cat:'images/arrow_right.png'}" alt="{$smarty.const._GRADEBOOK_SWITCH_TO}" title="{$smarty.const._GRADEBOOK_SWITCH_TO}" style="vertical-align:middle">
   <a href="javascript:void(0)" onclick="location=('{$T_GRADEBOOK_BASEURL}&switch_lesson='+Element.extend(this).next().options[this.next().options.selectedIndex].value)">{$smarty.const._GRADEBOOK_SWITCH_TO}</a>
   <select id="switch_lesson" name="switch_lesson">
{foreach name = 'lessons_loop' key = "id" item = "lesson" from = $T_GRADEBOOK_GRADEBOOK_LESSONS}
    <option value="{$lesson.id}">{$lesson.name}</option>
{/foreach}
   </select>
  </td>
{/if}
 </tr>
</table>

<div style="clear: both; height: 5px;"></div>

<table class="sortedTable" style="width:100%">
 <tr>
  <td class="topTitle">{$smarty.const._GRADEBOOK_STUDENT_NAME}</td>
{foreach name = 'columns_loop' key = "id" item = "column" from = $T_GRADEBOOK_LESSON_COLUMNS}
  <td class="topTitle rightAlign">{$column.name} ({$smarty.const._GRADEBOOK_COLUMN_WEIGHT_DISPLAY}: {$column.weight})</td>
  <td class="topTitle leftAlign noSort" style="width:16px;">
   <a href="{$T_GRADEBOOK_BASEURL}&delete_column={$column.id}" onclick="return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src="{$T_GRADEBOOK_BASELINK}images/delete.png" alt="{$smarty.const._GRADEBOOK_DELETE_COLUMN}" title="{$smarty.const._GRADEBOOK_DELETE_COLUMN}" border="0"></a>
  </td>
{if $column.refers_to_type != 'real_world'}
  <td class="topTitle leftAlign noSort" style="width:16px;">
   <a href="{$T_GRADEBOOK_BASEURL}&import_grades={$column.id}" onclick="return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src="{$T_GRADEBOOK_BASELINK}images/import.png" alt="{$smarty.const._GRADEBOOK_IMPORT_GRADES}" title="{$smarty.const._GRADEBOOK_IMPORT_GRADES}" border="0"></a>
  </td>
{else}
  <td class="topTitle leftAlign noSort">&nbsp;</td>
{/if}
{/foreach}
  <td class="topTitle centerAlign">{$smarty.const._GRADEBOOK_SCORE}</td>
  <td class="topTitle centerAlign noSort">{$smarty.const._GRADEBOOK_GRADE}</td>
  <td class="topTitle centerAlign noSort">{$smarty.const._GRADEBOOK_PUBLISH}</td>
 </tr>
{foreach name = 'users_loop' key = "id" item = "user" from = $T_GRADEBOOK_LESSON_USERS}
 <tr id="row_{$user.uid}" class="{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
  <td>#filter:login-{$user.users_LOGIN}#</td>
{foreach name = 'grades_loop' key = "id_" item = "grade" from = $user.grades}
  <td class="rightAlign">
   <input type="text" id="grade_{$grade.gid}" value="{$grade.grade}" size="5" maxlength="5" />
   <img class="ajaxHandle" src="{$T_GRADEBOOK_BASELINK|cat:'images/success.png'}" title="{$smarty.const._SAVE}" alt="{$smarty.const._SAVE}" onclick="changeGrade('{$grade.gid}', this)"/>
  </td>
  <td class="leftAlign">&nbsp;</td>
  <td class="leftAlign">&nbsp;</td>
{/foreach}
  <td class="centerAlign">{$user.score}</td>
  <td class="centerAlign">{$user.grade}</td>
  <td class="centerAlign">
   <input class="inputCheckbox" type="checkbox" name="checked_{$user.uid}" id="checked_{$user.uid}" onclick="publishGradebook('{$user.uid}', this);" {if ($user.publish == 1)} checked="checked"{/if} />
  </td>
 </tr>
{foreachelse}
 <tr class="defaultRowHeight oddRowColor">
  <td class="emptyCategory" colspan="100%">{$smarty.const._NODATAFOUND}</td>
 </tr>
{/foreach}
</table>
{/capture}

{eF_template_printBlock title=$smarty.const._GRADEBOOK_NAME data=$smarty.capture.t_gradebook_professor_code image=$T_GRADEBOOK_BASELINK|cat:'images/gradebook_logo.png' absoluteImagePath = 1 help = 'Gradebook'}

<script>
{literal}
 /*function deleteColumn(el, id){

		Element.extend(el);
		url = '{/literal}{$T_GRADEBOOK_BASEURL}{literal}&delete_column='+id;

		var img = new Element('img', {id:'img_'+id, src:'{/literal}{$T_GRADEBOOK_BASELINK}{literal}images/progress1.gif'}).setStyle({position:'absolute'});
		img_id = img.identify();
		el.up().insert(img);

		new Ajax.Request(url, {
			method: 'get',
			asynchronous: true,
			onFailure: function(transport){
				img.writeAttribute({src:'{/literal}{$T_GRADEBOOK_BASELINK}{literal}images/delete.png', title:transport.responseText}).hide();
				new Effect.Appear(img_id);
				window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
			},
			onSuccess: function(transport){
				img.hide();
				new Effect.Fade(el.up().up(), {queue:'end'});
			}
		});
	}*/

 /*function importGrades(el, id){

		Element.extend(el);
		url = '{/literal}{$T_GRADEBOOK_BASEURL}{literal}&import_grades='+id;

		var img = new Element('img', {id:'img_'+id, src:'{/literal}{$T_GRADEBOOK_BASELINK}{literal}images/progress1.gif'}).setStyle({position:'absolute'});
		img_id = img.identify();
		el.up().insert(img);

		new Ajax.Request(url, {
			method: 'get',
			asynchronous: true,
			onFailure: function(transport){
				img.writeAttribute({src:'{/literal}{$T_GRADEBOOK_BASELINK}{literal}images/import.png', title:transport.responseText}).hide();
				new Effect.Appear(img_id);
				window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
			},
			onSuccess: function(transport){
				img.hide();
				new Effect.Appear(el.up(), {queue:'end'});
			}
		});
	}*/

 function publishGradebook(uid, el){

  var url = '{/literal}{$T_GRADEBOOK_BASEURL}{literal}&edit_publish=1&uid='+uid;
  var checked = $('checked_'+uid).checked;
  checked ? url += '&publish=1' : url += '&publish=0';

  var img_id = 'img_'+uid;
  var position = eF_js_findPos(el);
  var img = document.createElement("img");

  img.style.position = 'absolute';
  img.style.top = Element.positionedOffset(Element.extend(el)).top + 'px';
  img.style.left = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

  img.setAttribute("id", img_id);
  img.setAttribute('src', '{/literal}{$T_GRADEBOOK_BASELINK}{literal}images/progress1.gif');
  el.parentNode.appendChild(img);

  new Ajax.Request(url, {
   method: 'get',
   asynchronous: true,
   onSuccess: function (transport) {
    img.style.display = 'none';
    img.setAttribute('src', '{/literal}{$T_GRADEBOOK_BASELINK}{literal}images/success.png');
    new Effect.Appear(img_id);
    window.setTimeout('Effect.Fade("'+img_id+'")', 1500);
   }
  });
 }

 function changeGrade(gid, el){

  Element.extend(el);
  var grade = $('grade_'+gid).value;
  var url = '{/literal}{$T_GRADEBOOK_BASEURL}{literal}&change_grade='+gid+'&grade='+grade;

  var img = new Element('img', {id:'img_'+gid, src:'{/literal}{$T_GRADEBOOK_BASELINK}{literal}images/progress1.gif'}).setStyle({position:'absolute'});
  img_id = img.identify();
  el.up().insert(img);

  new Ajax.Request(url, {
   method: 'get',
   asynchronous: true,
   onFailure: function(transport){
    img.hide();
    alert(decodeURIComponent(transport.responseText));
   },
   onSuccess: function(transport){
    img.hide();
    new Effect.Appear(el.up(), {queue:'end'});
   }
  });
 }

 /*function exportExcel(el){

		element = document.getElementById(el);
		var selected = element.options[element.selectedIndex].value;
		var url = '{/literal}{$T_GRADEBOOK_BASEURL}{literal}&export_excel='+selected;

		new Ajax.Request(url, {
			method: 'get',
			asynchronous: true,
			onFailure: function(transport){
				alert(decodeURIComponent(transport.responseText));
			},
			onSuccess: function(transport){
			}
		});
	}*/

{/literal}
</script>

{/if}
