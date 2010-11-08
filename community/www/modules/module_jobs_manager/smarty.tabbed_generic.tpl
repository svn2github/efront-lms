
{if $T_GRADEBOOK_MESSAGE}
 <script>
  re = /\?/;
  !re.test(parent.location) ? parent.location = parent.location+'?message={$T_GRADEBOOK_MESSAGE}&message_type=success' : parent.location = parent.location+'&message={$T_GRADEBOOK_MESSAGE}&message_type=success';
 </script>
{/if}
{capture name="mod_jobs_manager_job_tab"}
 {include file="$MOD_JOBS_MANAGER_BASEDIR/smarty.job_tab.tpl"}
{/capture}
{capture name="mod_jobs_manager_app_tab"}
 {include file="$MOD_JOBS_MANAGER_BASEDIR/smarty.app_tab.tpl"}
{/capture}
{capture name="mod_jobs_manager_settings_tab"}
 {include file="$MOD_JOBS_MANAGER_BASEDIR/smarty.settings_tab.tpl"}
{/capture}
<div style="position:relative;">
 <div style="position:absolute;top:0px;right:10px;color:#000000;float:right;background:#ffffff;padding:3px;">{$_MOD_JAM_PUBLIC_URL}</div>
 <div class ="tabber">
  <div class="tabbertab" title="{$smarty.const._JOBS_MANAGER_JOB_TAB_TITLE}">
   {eF_template_printBlock title=$smarty.const._JOBS_MANAGER data=$smarty.capture.mod_jobs_manager_job_tab image=$MOD_JOBS_MANAGER_BASELINK|cat:'images/logo_32.png' absoluteImagePath = 1}
     </div>
  <div class="tabbertab {if $smarty.get.tab == 'apps'}tabbertabdefault{/if}" title="{$smarty.const._JOBS_MANAGER_APP_TAB_TITLE}">
   {eF_template_printBlock title=$smarty.const._JOBS_MANAGER data=$smarty.capture.mod_jobs_manager_app_tab image=$MOD_JOBS_MANAGER_BASELINK|cat:'images/logo_32.png' absoluteImagePath = 1}
  </div>
  <div class="tabbertab {if $smarty.get.tab == 'settings'}tabbertabdefault{/if}" title="{$smarty.const._JOBS_MANAGER_SETTINGS_TAB_TITLE}">
   {eF_template_printBlock title=$smarty.const._JOBS_MANAGER data=$smarty.capture.mod_jobs_manager_settings_tab image=$MOD_JOBS_MANAGER_BASELINK|cat:'images/logo_32.png' absoluteImagePath = 1}
  </div>
 </div>
</div>
<script>
{literal}
 function deleteJob(el, id){
  Element.extend(el);
  url = '{/literal}{$MOD_JOBS_MANAGER_BASEURL}{literal}&remove_job='+id;
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

 function toggleJob(job_id) {
  var url = '{/literal}{$MOD_JOBS_MANAGER_BASEURL}{literal}&action=toggle_job&job_id='+job_id;
  new Ajax.Request(url, {
   method:'get',
   asynchronous: true,
   onFailure: function() {
    alert('{/literal}{$smarty.const._MOD_JAM_AJAX_ERROR_JOB_TOGGLE}{literal}');
   },
   onSuccess: function(response) {
    var el = $('status_image_'+job_id);
    var response = response.responseText.evalJSON();
    if (response.success) {
     if (response.new_status) { el.className = 'sprite16 sprite16-trafficlight_green'; }
     else { el.className = 'sprite16 sprite16-trafficlight_red'; }
    }
    else { alert('{/literal}{$smarty.const._MOD_JAM_AJAX_ERROR_JOB_TOGGLE}{literal}'); }
   }
  });
 }
{/literal}
</script>
