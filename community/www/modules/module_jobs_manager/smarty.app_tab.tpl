  <table class="sortedTable" style="width:100%;">
   <tr>
    <td class="topTitle" style="width:250px;">{$smarty.const._JOBS_MANAGER_APP_NAME}</td>
    <td class="topTitle">{$smarty.const._JOBS_MANAGER_APP_EMAIL}</td>
    <td class="topTitle centerAlign" style="width:150px;">{$smarty.const._JOBS_MANAGER_APP_JOB_CODE}</td>
    <td class="topTitle centerAlign" style="width:150px;">{$smarty.const._JOBS_MANAGER_APP_CREATED_ON}</td>
    <td class="topTitle centerAlign noSort" style="width:150px;">{$smarty.const._JOBS_MANAGER_JOB_TOOLS}</td>
   </tr>
   {foreach name='apps_loop' key='app_id' item="app" from=$_JOB_MANAGER_JOB_APPS}
    <tr id="row_{$app.id}" class="{cycle values = "oddRowColor, evenRowColor"}" {if !$app.read}style="font-weight:bold;"{/if}>
     <td><a href="{$MOD_JOBS_MANAGER_BASEURL}&action=show_app&app_id={$app.id}">{$app.name}</a></td>
     <td>{$app.email}</td>
     <td class="centerAlign"><a href="{$MOD_JOBS_MANAGER_BASEURL}&action=show_job&job_id={$app.job_id}&back=2">{$app.job_code}</a></td>
     <td class="centerAlign">{$app.date_added|date_format:"%a, %d %b %Y"}</td>
     <td class="centerAlign">
      <a href="{$MOD_JOBS_MANAGER_BASEURL}&action=show_app&app_id={$app.id}"><img src="{$MOD_JOBS_MANAGER_BASELINK}images/milky_view.png" alt="{$smarty.const._MOD_JOB_APP_VIEW}" title="{$smarty.const._MOD_JOB_APP_VIEW}" border="0"></a>
      <a href="{$app.cv_filename}" target="_blank"><img src="{$MOD_JOBS_MANAGER_BASELINK}images/milky_download.png" alt="{$smarty.const._MOD_JOB_APP_DOWNLOAD}" title="{$smarty.const._MOD_JOB_APP_DOWNLOAD}" border="0"></a>
      <a href="javascript:void(0);" onclick="if(confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) window.location='{$MOD_JOBS_MANAGER_BASEURL}&action=remove_app&app_id={$app.id}&job_id={$app.job_id}';"><img src="{$MOD_JOBS_MANAGER_BASELINK}images/milky_delete.png" alt="{$smarty.const._DELETE}" title="{$smarty.const._DELETE}" border="0"></a>
     </td>
    </tr>
   {foreachelse}
    <tr class="defaultRowHeight oddRowColor">
     <td class="emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td>
    </tr>
   {/foreach}
  </table>
