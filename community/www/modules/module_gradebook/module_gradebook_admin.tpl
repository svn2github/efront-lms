{* Smarty Template for GradeBook module (Administrator) *}

{if $T_GRADEBOOK_MESSAGE}
 <script>
  re = /\?/;
  !re.test(parent.location) ? parent.location = parent.location+'?message={$T_GRADEBOOK_MESSAGE}&message_type=success' : parent.location = parent.location+'&message={$T_GRADEBOOK_MESSAGE}&message_type=success';
 </script>
{/if}

{if $smarty.get.add_range || $smarty.get.edit_range}
{capture name = 't_add_edit_range_code'}
 {$T_GRADEBOOK_ADD_EDIT_RANGE_FORM.javascript}
<form {$T_GRADEBOOK_ADD_EDIT_RANGE_FORM.attributes}>
 {$T_GRADEBOOK_ADD_EDIT_RANGE_FORM.hidden}
 <table style="margin-left:100px">
  <tr>
   <td class="labelCell">{$T_GRADEBOOK_ADD_EDIT_RANGE_FORM.range_from.label}:&nbsp;</td>
   <td class="elementCell">{$T_GRADEBOOK_ADD_EDIT_RANGE_FORM.range_from.html}</td>
  </tr>
  <tr>
   <td class="labelCell">{$T_GRADEBOOK_ADD_EDIT_RANGE_FORM.range_to.label}:&nbsp;</td>
   <td class="elementCell">{$T_GRADEBOOK_ADD_EDIT_RANGE_FORM.range_to.html}</td>
  </tr>
  <tr>
   <td class="labelCell">{$T_GRADEBOOK_ADD_EDIT_RANGE_FORM.grade.label}:&nbsp;</td>
   <td class="elementCell">{$T_GRADEBOOK_ADD_EDIT_RANGE_FORM.grade.html}</td>
  </tr>
  <tr>
   <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
   <td></td>
   <td class="elementCell">{$T_GRADEBOOK_ADD_EDIT_RANGE_FORM.submit.html}</td>
  </tr>
 </table>
</form>
{/capture}

{if $smarty.get.add_range}
{eF_template_printBlock title=$smarty.const._GRADEBOOK_ADD_RANGE data=$smarty.capture.t_add_edit_range_code image=$T_GRADEBOOK_BASELINK|cat:'images/gradebook_logo.png' absoluteImagePath = 1}
{else}
{eF_template_printBlock title=$smarty.const._GRADEBOOK_EDIT_RANGE data=$smarty.capture.t_add_edit_range_code image=$T_GRADEBOOK_BASELINK|cat:'images/gradebook_logo.png' absoluteImagePath = 1}
{/if}

{else}
{capture name = 't_gradebook_code'}

 <div class = "headerTools">
  <span>
   <img src={$T_GRADEBOOK_BASELINK|cat:'images/add.png'} alt="{$smarty.const._GRADEBOOK_ADD_RANGE}" title="{$smarty.const._GRADEBOOK_ADD_RANGE}" style="vertical-align:middle">
   <a href="{$T_GRADEBOOK_BASEURL}&add_range=1&popup=1" target="POPUP_FRAME" onclick="eF_js_showDivPopup('{$smarty.const._GRADEBOOK_ADD_RANGE}', 0)">{$smarty.const._GRADEBOOK_ADD_RANGE}</a>
  </span>
 </div>
<table class="sortedTable" style="width:100%">
 <tr>
  <td class="topTitle centerAlign">{$smarty.const._GRADEBOOK_RANGE_FROM}</td>
  <td class="topTitle centerAlign">{$smarty.const._GRADEBOOK_RANGE_TO}</td>
  <td class="topTitle centerAlign">{$smarty.const._GRADEBOOK_GRADE}</td>
  <td class="topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
 </tr>
{foreach name = 'ranges_loop' key = "id" item = "range" from = $T_GRADEBOOK_RANGES}
 <tr id="row_{$range.id}" class="{cycle values = "oddRowColor, evenRowColor"}">
  <td class = "centerAlign">{$range.range_from}</td>
  <td class = "centerAlign">{$range.range_to}</td>
  <td class = "centerAlign">{$range.grade}</td>
  <td class="centerAlign">
   <a href="{$T_GRADEBOOK_BASEURL}&edit_range={$range.id}&popup=1" target="POPUP_FRAME" onclick="eF_js_showDivPopup('{$smarty.const._GRADEBOOK_EDIT_RANGE}', 0)"><img src="{$T_GRADEBOOK_BASELINK}images/edit.png" alt="{$smarty.const._EDIT}" title="{$smarty.const._EDIT}" border="0"></a>
   <a href="javascript:void(0)" onclick="if(confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteRange(this, {$range.id});"><img src="{$T_GRADEBOOK_BASELINK}images/delete.png" alt="{$smarty.const._DELETE}" title="{$smarty.const._DELETE}" border="0"></a>
  </td>
 </tr>
{foreachelse}
 <tr class="defaultRowHeight oddRowColor">
  <td class="emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td>
 </tr>
{/foreach}
</table>

{/capture}

{eF_template_printBlock title=$smarty.const._GRADEBOOK_NAME data=$smarty.capture.t_gradebook_code image=$T_GRADEBOOK_BASELINK|cat:'images/gradebook_logo.png' absoluteImagePath = 1}

<script>
{literal}
 function deleteRange(el, id){

  Element.extend(el);
  url = '{/literal}{$T_GRADEBOOK_BASEURL}{literal}&delete_range='+id;

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
 }
{/literal}
</script>

{/if}
