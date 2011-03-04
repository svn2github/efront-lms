{capture name="table_title"}
 {$smarty.const._JOBS_MANAGER}
{/capture}

{if $_JOB_MANAGER_APP}
 {capture name = 'mod_jobs_manager_app'}
 <table>
  <tr>
   <td>
    &larr;
    <a href="{$MOD_JOBS_MANAGER_BACK}&tab=apps">{$smarty.const._JOBS_MANAGER_BACK}</a>
   </td>
   <td>&nbsp;|&nbsp;</td>
   <td>
    <img src="{$MOD_JOBS_MANAGER_BASELINK|cat:'images/milky_delete.png'}" alt="{$smarty.const._JOBS_MANAGER_DELETE_APP}" title="{$smarty.const._JOBS_MANAGER_DELETE_JOB}" style="vertical-align:middle">
    <a href="javascript:void(0);" onclick="if(confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) window.location='{$MOD_JOBS_MANAGER_BASEURL}&action=remove_app&app_id={$_JOB_MANAGER_APP.id}&job_id={$_JOB_MANAGER_APP.job_id}';">{$smarty.const._JOBS_MANAGER_DELETE_APP}</a>
    <!--<a href="{$MOD_JOBS_MANAGER_BASEURL}&action=remove_app&job_id={$_JOB_MANAGER_APP.job_id}&app_id={$_JOB_MANAGER_APP.id}">{$smarty.const._JOBS_MANAGER_DELETE_APP}</a>-->
   </td>
  </tr>
 </table>
 <hr/>
 <h1>{$_JOB_MANAGER_JOB.title}</h1>
 <table cellpadding="0" cellspacing="0" style="width:100%;">
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_APP_NAME}
   </td>
   <td>{$_JOB_MANAGER_APP.name}</td>
  </tr>
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_APP_EMAIL}
   </td>
   <td>{$_JOB_MANAGER_APP.email}</td>
  </tr>
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_APP_PHONE}
   </td>
   <td>{$_JOB_MANAGER_APP.phone}</td>
  </tr>
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_APP_CITY}
   </td>
   <td>{$_JOB_MANAGER_APP.city}</td>
  </tr>
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_APP_COUNTRY}
   </td>
   <td>{$_JOB_MANAGER_APP.country}</td>
  </tr>
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_APP_CREATED_ON}
   </td>
   <td>{$_JOB_MANAGER_APP.date_added|date_format:"%A, %d %b %Y"}</td>
  </tr>
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_APP_CV}
   </td>
   <td><a href="{$MOD_JOBS_MANAGER_BASEURL}&action=download_file&app_id={$_JOB_MANAGER_APP.id}" target="_blank">{$smarty.const._JOBS_MANAGER_APP_CV}</a></td>
  </tr>
  <tr valign="top">
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_APP_COVER}
   </td>
   <td>
    <div style="width:100%; max-height:150px; overflow:auto; margin-bottom:10px;">{$_JOB_MANAGER_APP.cover|replace:"\n":"<br/>"}</div>
   </td>
  </tr>

 </table>
 {/capture}

 {eF_template_printBlock title=$smarty.const._JOBS_MANAGER data=$smarty.capture.mod_jobs_manager_app image=$MOD_JOBS_MANAGER_BASELINK|cat:'images/logo_32.png' absoluteImagePath = 1}

{else}
 <div style="width:100%;">
  <div style="margin:0 auto;"><h2>NO DATA FOUND FOR THIS APP</h2></div>
 </div>
{/if}
