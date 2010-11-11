{capture name="table_title"}
 {$smarty.const._JOBS_MANAGER}
{/capture}

{capture name="mod_jobs_manager_job_new"}
 <table>
  <tr>
   <td>
    <a href="{$MOD_JOBS_MANAGER_BACK}">&larr;&nbsp;{$smarty.const._JOBS_MANAGER_BACK}</a>
   </td>
  </tr>
 </table>
 <div style="width:100%; background: #EEEEEE; border: solid 1px #CCCCCC; margin-top:10px; position:relative;">
  <div style="padding:15px; position:relative;">
   <form method="post" action="{$MOD_JOBS_MANAGER_BASEURL}{$MOD_JAM_FORM_POSTTARGET}">
    <table cellpadding="5" cellpsacing="0">
     <tr>
      <td>
       <div style="width:700px; float:left; position:relative;">
        <div style="width:140px; float:left; text-align:right; padding-right:10px;">
         <label for="mod_jam_new_job_code"><sup style="color:#f00;">*</sup>&nbsp;{$smarty.const._JOBS_MANAGER_JOB_CODE}:</label>
        </div>
        <div style="width:550px; float:right;">
         <input type="text" id="mod_jam_new_job_code" name="mod_jam_new_job_code" style="width:200px;" value="{$_MOD_JOBS_FORM_POPULATE_CODE}"/>
        </div>
       </div>
      </td>
     </tr>
     <tr>
      <td>
       <div style="width:700px; float:left; position:relative;">
        <div style="width:140px; float:left; text-align:right; padding-right:10px;">
         <label for="mod_jam_new_job_title"><sup style="color:#f00;">*</sup>&nbsp;{$smarty.const._JOBS_MANAGER_JOB_TITLE}:</label>
        </div>
        <div style="width:550px; float:right;">
         <input type="text" id="mod_jam_new_job_title" name="mod_jam_new_job_title" style="width:550px;" value="{$_MOD_JOBS_FORM_POPULATE_TITLE}"/>
        </div>
       </div>
      </td>
     </tr>
     <tr>
      <td>
       <div style="width:700px; float:left; position:relative;">
        <div style="width:140px; float:left; text-align:right; padding-right:10px;">
         <label for="mod_jam_new_job_type"><sup style="color:#f00;">*</sup>&nbsp;{$smarty.const._JOBS_MANAGER_FORM_JOB_TYPE}:</label>
        </div>
        <div style="width:550px; float:right;">
         <select id="mod_jam_new_job_type" name="mod_jam_new_job_type" style="width:550px;">
          {foreach name='job_type_loop' key='type_id' item="type" from=$_JOB_MANAGER_FORM_NEW_TYPES}
           <option value="{$type.id}"{if $type.selected} SELECTED{/if}>{$type.name}</option>
          {/foreach}
         </select>
        </div>
       </div>
      </td>
     </tr>
     <tr>
      <td>
       <div style="width:700px; float:left; position:relative;">
        <div style="width:140px; float:left; text-align:right; padding-right:10px;">
         <label for="mod_jam_new_job_exp"><sup style="color:#f00;">*</sup>&nbsp;{$smarty.const._JOBS_MANAGER_FORM_EXPERIENCE}:</label>
        </div>
        <div style="width:550px; float:right;">
         <select id="mod_jam_new_job_exp" name="mod_jam_new_job_exp" style="width:550px;">
          {foreach name='xp_loop' key='xp_id' item="xp" from=$_JOB_MANAGER_FORM_NEW_EXP}
           <option value="{$xp.id}"{if $xp.selected} SELECTED{/if}>{$xp.name}</option>
          {/foreach}
         </select>
        </div>
       </div>
      </td>
     </tr>
     <tr>
      <td>
       <div style="width:700px; float:left; position:relative;">
        <div style="width:140px; float:left; text-align:right; padding-right:10px;">
         <label for="mod_jam_new_job_remuneration"><sup style="color:#f00;">*</sup>&nbsp;{$smarty.const._JOBS_MANAGER_FORM_REMUNERATION}:</label>
        </div>
        <div style="width:550px; float:right;">
         <input type="text" id="mod_jam_new_job_remuneration" name="mod_jam_new_job_remuneration" style="width:200px;" value="{$_MOD_JOBS_FORM_POPULATE_REMUNERATION}"/>
        </div>
       </div>
      </td>
     </tr>
     <tr>
      <td>
       <div style="width:700px; float:left; position:relative;">
        <div style="width:140px; float:left; text-align:right; padding-right:10px;">
         <label for="mod_jam_new_job_functions"><sup style="color:#f00;">*</sup>&nbsp;{$smarty.const._JOBS_MANAGER_FORM_FUNCTIONS}:</label>
        </div>
        <div style="width:550px; float:right;">
         <select id="mod_jam_new_job_functions" name="mod_jam_new_job_functions[]" style="width:550px;" multiple="multiple" size="6">
          {foreach name='function_loop' key='function_id' item="function" from=$_JOB_MANAGER_FORM_NEW_FUNCTION}
           <option value="{$function.id}"{if $function.selected} SELECTED{/if}>{$function.name}</option>
          {/foreach}
         </select>
        </div>
       </div>
      </td>
     </tr>

     <tr>
      <td>
       <div style="width:700px; float:left; position:relative;">
        <div style="width:140px; float:left; text-align:right; padding-right:10px;">
         <label for="mod_jam_new_job_desc"><sup style="color:#f00;">*</sup>&nbsp;{$smarty.const._JOBS_MANAGER_JOB_DESC}:</label>
        </div>
        <div style="width:550px; float:right;">
         <textarea class="inputContentTextarea simpleEditor" id="mod_jam_new_job_desc" name="mod_jam_new_job_desc" style="width:550px; height:200px;">{$_MOD_JOBS_FORM_POPULATE_DESC}</textarea>
        </div>
       </div>
      </td>
     </tr>
     <tr>
      <td>
       <div style="width:700px; float:left; position:relative;">
        <div style="width:140px; float:left; text-align:right; padding-right:10px;">
         <label for="mod_jam_new_job_skills">{$smarty.const._JOBS_MANAGER_FORM_SKILLS}:</label>
        </div>
        <div style="width:550px; float:right;">
         <textarea class="inputContentTextarea simpleEditor" id="mod_jam_new_job_skills" name="mod_jam_new_job_skills" style="width:550px; height:150px;">{$_MOD_JOBS_FORM_POPULATE_SKILLS}</textarea>
        </div>
       </div>
      </td>
     </tr>
     <tr>
      <td>
       <div style="width:700px; float:left; position:relative;">
        <div style="width:140px; float:left; text-align:right; padding-right:10px;">
         <label for="mod_jam_new_job_company_desc">{$smarty.const._JOBS_MANAGER_FORM_COMPANY_DESC}:</label>
        </div>
        <div style="width:550px; float:right;">
         <textarea class="inputContentTextarea simpleEditor" id="mod_jam_new_job_company_desc" name="mod_jam_new_job_company_desc" style="width:550px; height:150px;">{$_MOD_JOBS_FORM_POPULATE_COMPANY_DESC}</textarea>
        </div>
       </div>
      </td>
     </tr>
     <tr>
      <td>
       <div style="width:700px; float:left; position:relative;">
        <div style="width:140px; float:left; text-align:right; padding-right:10px;">&nbsp;</div>
        <div style="width:550px; float:right;">
         <input type="submit" name="submit" value="{$smarty.const._SUBMIT}"/>
        </div>
       </div>
      </td>
     </tr>

    </table>
   </form>
  </div>
 </div>
{/capture}

{eF_template_printBlock title=$smarty.capture.table_title data=$smarty.capture.mod_jobs_manager_job_new image=$MOD_JOBS_MANAGER_BASELINK|cat:'images/logo_32.png' absoluteImagePath = 1}
