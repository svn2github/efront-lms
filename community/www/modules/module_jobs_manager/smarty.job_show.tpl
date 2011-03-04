{* Smarty Template for Jobs Manager module (Administrator) *}

{if $_JOB_MANAGER_JOB}

 {capture name = 't_gradebook_code'}
 <table>
  <tr>
   <td>
    &larr;
    <a href="{$MOD_JOBS_MANAGER_BACK}">{$smarty.const._JOBS_MANAGER_BACK}</a>
   </td>
   <td>&nbsp;|&nbsp;</td>
   <td>
    <img src="{$MOD_JOBS_MANAGER_BASELINK|cat:'images/milky_pencil.png'}" alt="{$smarty.const._JOBS_MANAGER_EDIT_JOB}" title="{$smarty.const._JOBS_MANAGER_EDIT_JOB}" style="vertical-align:middle;">
    <a href="{$MOD_JOBS_MANAGER_BASEURL}&action=edit_job&job_id={$_JOB_MANAGER_JOB.id}&back=2">{$smarty.const._JOBS_MANAGER_EDIT_JOB}</a>
   </td>
   <td>&nbsp;|&nbsp;</td>
   <td>
    <img src="{$MOD_JOBS_MANAGER_BASELINK|cat:'images/milky_delete.png'}" alt="{$smarty.const._JOBS_MANAGER_DELETE_JOB}" title="{$smarty.const._JOBS_MANAGER_DELETE_JOB}" style="vertical-align:middle;">
    <a href="{$MOD_JOBS_MANAGER_BASEURL}&action=remove_job&job_id={$_JOB_MANAGER_JOB.id}">{$smarty.const._JOBS_MANAGER_DELETE_JOB}</a>
   </td>
  </tr>
 </table>
 <hr/>
 <h1>{$_JOB_MANAGER_JOB.title}</h1>
 <table cellpadding="0" cellspacing="0" style="width:100%;">
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_JOB_CODE}
   </td>
   <td>{$_JOB_MANAGER_JOB.code}</td>
  </tr>
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_JOB_CREATED_ON}
   </td>
   <td>{$_JOB_MANAGER_JOB.date_added|date_format:"%A, %d %b %Y"}</td>
  </tr>
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_JOB_REMUNERATION}
   </td>
   <td>
    {$_JOB_MANAGER_JOB.remuneration}
   </td>
  </tr>
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_JOB_TYPE}
   </td>
   <td>
    {$_JOB_MANAGER_JOB.type}
   </td>
  </tr>
  <tr>
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_JOB_EXPERIENCE}
   </td>
   <td>
    {$_JOB_MANAGER_JOB.experience}
   </td>
  </tr>
  <tr valign="top">
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_JOB_DESC}
   </td>
   <td>
    <div style="width:100%; max-height:150px; overflow:auto; margin-bottom:10px;">{$_JOB_MANAGER_JOB.description|replace:"\n":"<br/>"}</div>
   </td>
  </tr>
  <tr valign="top">
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_JOB_SKILLS}
   </td>
   <td>
    <div style="width:100%; max-height:150px; overflow:auto; margin-bottom:10px;">{$_JOB_MANAGER_JOB.skills|replace:"\n":"<br/>"}</div>
   </td>
  </tr>
  <tr valign="top">
   <td style="width:150px;">
    {$smarty.const._JOBS_MANAGER_JOB_COMPANY_DESC}
   </td>
   <td>
    <div style="width:100%; max-height:150px; overflow:auto; margin-bottom:10px;">{$_JOB_MANAGER_JOB.company_desc|replace:"\n":"<br/>"}</div>
   </td>
  </tr>

 </table>
 <h3>{$smarty.const._JOBS_MANAGER_JOB_APPS}</h3>

 <table class="sortedTable" style="width:100%;">
  <tr>
   <td class="topTitle" style="width:200px;">{$smarty.const._JOBS_MANAGER_APP_NAME}</td>
   <td class="topTitle">{$smarty.const._JOBS_MANAGER_APP_EMAIL}</td>
   <td class="topTitle centerAlign" style="width:120px;">{$smarty.const._JOBS_MANAGER_APP_CREATED_ON}</td>
  </tr>
  {foreach name='apps_loop' key='app_id' item="app" from=$_JOB_MANAGER_JOB.app_data}
   <tr id="row_{$app.id}" class="{cycle values = "oddRowColor, evenRowColor"}">
    <td><a href="{$app.link}&back={$_JOB_MANAGER_JOB.id}">{$app.name}</a></td>
    <td><a href="{$app.link}&back={$_JOB_MANAGER_JOB.id}">{$app.email}</a></td>
    <td class="centerAlign"><a href="{$app.link}&back={$_JOB_MANAGER_JOB.id}">{$app.date_added|date_format:"%a, %d %b %Y"}</a></td>
   </tr>
  {foreachelse}
   <tr class="defaultRowHeight oddRowColor">
    <td class="emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td>
   </tr>
  {/foreach}
 </table>

 {/capture}

 {eF_template_printBlock title=$smarty.const._JOBS_MANAGER data=$smarty.capture.t_gradebook_code image=$MOD_JOBS_MANAGER_BASELINK|cat:'images/logo_32.png' absoluteImagePath = 1}

{else}
 <div style="width:100%;">
  <div style="margin:0 auto;"><h2>NO DATA FOUND FOR THIS JOB</h2></div>
 </div>
{/if}
