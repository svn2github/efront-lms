{* Smarty Template for Journal Module (Administrator) *}

{if isset($smarty.get.add_rule) || isset($smarty.get.edit_rule)}

{capture name = 't_add_edit_rule_code'}
 {$T_JOURNAL_ADD_EDIT_RULE_FORM.javascript}
<form {$T_JOURNAL_ADD_EDIT_RULE_FORM.attributes}>
 {$T_JOURNAL_ADD_EDIT_RULE_FORM.hidden}
        <table class="formElements">
  <tr>
   <td class="labelCell">{$smarty.const._TITLE}:&nbsp;</td>
   <td class="elementCell">{$T_JOURNAL_ADD_EDIT_RULE_FORM.title.html}</td>
  </tr>
  <tr>
   <td class="labelCell">{$smarty.const._DESCRIPTION}:&nbsp;</td>
   <td class="elementCell">{$T_JOURNAL_ADD_EDIT_RULE_FORM.description.html}</td>
  </tr>
  <tr>
   <td></td>
   <td class="submitCell">{$T_JOURNAL_ADD_EDIT_RULE_FORM.submit.html}</td>
  </tr>
 </table>
</form>
{/capture}

{capture name = 't_journal_tab_code'}
<div class="tabber">
 <div class="tabbertab">
{if isset($smarty.get.add_rule)}
         <h3>{$smarty.const._JOURNAL_ADD_RULE2}</h3>
  {eF_template_printBlock title=$smarty.const._JOURNAL_ADD_RULE_FORM data=$smarty.capture.t_add_edit_rule_code image=$T_JOURNAL_BASELINK|cat:'images/journal_logo.png' absoluteImagePath=1}
{else}
         <h3>{$smarty.const._JOURNAL_EDIT_RULE}</h3>
  {eF_template_printBlock title=$smarty.const._JOURNAL_EDIT_RULE_FORM data=$smarty.capture.t_add_edit_rule_code image=$T_JOURNAL_BASELINK|cat:'images/journal_logo.png' absoluteImagePath=1}
{/if}
 </div>
</div>
{/capture}
{eF_template_printBlock title=$smarty.const._JOURNAL_NAME data=$smarty.capture.t_journal_tab_code image=$T_JOURNAL_BASELINK|cat:'images/journal_logo.png' absoluteImagePath=1 help = 'Journal'}

{else}
{capture name = 't_journal_code'}

<table>
 <tr>
  <td>
   <img src="{$T_JOURNAL_BASELINK|cat:'images/add.png'}" alt="{$smarty.const._JOURNAL_ADD_RULE}" title="{$smarty.const._JOURNAL_ADD_RULE}" style="vertical-align:middle" />&nbsp;<a href="{$T_JOURNAL_BASEURL}&add_rule=1">{$smarty.const._JOURNAL_ADD_RULE}</a>&nbsp;
  </td>
  <td style="border-right: 1px solid #333333;"></td>
  <td>
   &nbsp;&nbsp;<img src="{$T_JOURNAL_BASELINK|cat:'images/export.png'}" alt="{$smarty.const._JOURNAL_ALLOW_PRINT_EXPORT}" title="{$smarty.const._JOURNAL_ALLOW_PRINT_EXPORT}" style="vertical-align:middle" />&nbsp;{$smarty.const._JOURNAL_ALLOW_PRINT_EXPORT}
   <input class="inputCheckbox" type="checkbox" name="allow_print_export" id="allow_print_export" style="border:0px;" onclick="allowPrintExport(this);" {if ($T_JOURNAL_ALLOW_EXPORT == 1)} checked="checked"{/if} />
  </td>
  <td style="border-right: 1px solid #333333;"></td>
  <td>
   &nbsp;&nbsp;<img src="{$T_JOURNAL_BASELINK|cat:'images/analysis.png'}" alt="{$smarty.const._JOURNAL_ALLOW_PROFESSOR_PREVIEW}" title="{$smarty.const._JOURNAL_ALLOW_PROFESSOR_PREVIEW}" style="vertical-align:middle" />&nbsp;{$smarty.const._JOURNAL_ALLOW_PROFESSOR_PREVIEW}
   <input class="inputCheckbox" type="checkbox" name="allow_professor_preview" id="allow_professor_preview" style="border:0px;" onclick="allowProfessorPreview(this);" {if ($T_JOURNAL_ALLOW_PROFESSOR_PREVIEW == 1)} checked="checked"{/if} />
  </td>
 </tr>
</table>

<div style="clear: both; height: 5px;"></div>

<table class="sortedTable" style="width:100%">
 <tr>
  <td class="topTitle">{$smarty.const._TITLE}</td>
  <td class="topTitle">{$smarty.const._DESCRIPTION}</td>
  <td class="topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
 </tr>
{foreach name = 'rules_loop' key = "id" item = "rule" from = $T_JOURNAL_RULES}
 <tr id="row_{$rule.id}" class="{cycle values = "oddRowColor, evenRowColor"}">
  <td>{$rule.title}</td>
  <td>{$rule.description}</td>
  <td class="centerAlign">
   <a href="{$T_JOURNAL_BASEURL}&edit_rule={$rule.id}"><img src="{$T_JOURNAL_BASELINK|cat:'images/edit.png'}" alt="{$smarty.const._EDIT}" title="{$smarty.const._EDIT}"/></a>
   <a href="javascript:void(0);" onclick="if(confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteRule(this, {$rule.id});"><img src="{$T_JOURNAL_BASELINK|cat:'images/delete.png'}" alt="{$smarty.const._DELETE}" title="{$smarty.const._DELETE}" border="0"/></a>
{if $rule.active == 1}
   <a href="{$T_JOURNAL_BASEURL}&deactivate_rule={$rule.id}"><img src="{$T_JOURNAL_BASELINK|cat:'images/trafficlight_green.png'}" alt="{$smarty.const._DEACTIVATE}" title="{$smarty.const._DEACTIVATE}" border="0"></a>
{else}
   <a href="{$T_JOURNAL_BASEURL}&activate_rule={$rule.id}"><img src="{$T_JOURNAL_BASELINK|cat:'images/trafficlight_red.png'}" alt="{$smarty.const._ACTIVATE}" title="{$smarty.const._ACTIVATE}" border="0"></a>
{/if}
  </td>
 </tr>
{foreachelse}
 <tr class="defaultRowHeight oddRowColor">
  <td class="emptyCategory" colspan="100%">{$smarty.const._JOURNAL_NO_RULES_FOUND}</td>
 </tr>
{/foreach}
</table>

{/capture}
{eF_template_printBlock title=$smarty.const._JOURNAL_NAME data=$smarty.capture.t_journal_code image=$T_JOURNAL_BASELINK|cat:'images/journal_logo.png' absoluteImagePath=1 help = 'Journal'}

<script>
{literal}
 function deleteRule(el, id){

  Element.extend(el);
  url = '{/literal}{$T_JOURNAL_BASEURL}{literal}&delete_rule='+id;

  var img = new Element('img', {id:'img_'+id, src:'{/literal}{$T_JOURNAL_BASELINK}{literal}images/progress.gif'}).setStyle({position:'absolute'});
  img_id = img.identify();
  el.up().insert(img);

  new Ajax.Request(url, {
   method: 'get',
   asynchronous: true,
   onFailure: function(transport){
    img.writeAttribute({src:'{/literal}{$T_JOURNAL_BASELINK}{literal}images/delete.png', title:transport.responseText}).hide();
    new Effect.Appear(img_id);
    window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
   },
   onSuccess: function(transport){
    img.hide();
    new Effect.Fade(el.up().up(), {queue:'end'});
   }
  });
 }

 function allowPrintExport(el){

  var url = '{/literal}{$T_JOURNAL_BASEURL}{literal}&edit_allow_export=1';
  var checked = $('allow_print_export').checked;
  checked ? url += '&allow=1' : url += '&allow=0';

  new Ajax.Request(url, {
   method: 'get',
   asynchronous: true,
   onSuccess: function (transport){
    ;
   }
  });
 }

 function allowProfessorPreview(el){

  var url = '{/literal}{$T_JOURNAL_BASEURL}{literal}&edit_professor_preview=1';
  var checked = $('allow_professor_preview').checked;
  checked ? url += '&preview=1' : url += '&preview=0';

  new Ajax.Request(url, {
   method: 'get',
   asynchronous: true,
   onSuccess: function (transport){
    ;
   }
  });
 }
{/literal}
</script>

{/if}
