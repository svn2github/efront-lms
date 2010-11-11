<table cellpadding="3" cellspacing="0" style="width:100%;">

 <tr>
  <td>
   <fieldset class="fieldsetSeparator">
   <legend>{$smarty.const._MOD_JAM_SETTINGS_EMAILS_TITLE}</legend>
   <form action="{$MOD_JOBS_MANAGER_BASEURL}&action=save_settings_emails&tab=settings" method="post">
   <!--
   <table cellpadding="0" cellspacing="0">
    {assign var=emails_per_row value=4}
    {foreach name='email_loop' key='email_id' item='email' from=$_MOD_JAM_SETTINGS_EMAILS}
     {assign var=row value=$email_id%$emails_per_row}
     {if !$row}<tr>{/if}
      <td style="width:200px;">
       <input type="checkbox" id="settings_email_{$email_id}" name="settings_emails[]" value="{$email.email}" {if $email.selected}checked{/if}><label for="settings_email_{$email_id}">{$email.full_name}</label>
      </td>
     {assign var=id_ahead value=$email_id+1}
     {assign var=row value=$id_ahead%$emails_per_row}
     {if !$row}</tr>{/if}
    {/foreach}
    {if $row}</tr>{/if}
   </table>
   -->
   <table cellpadding="0" cellspacing="0">
    <tr>
     <td>
      {$smarty.const._MOD_JAM_SETTINGS_EMAILS_TEXT}
     </td>
    </tr>
    <tr>
     <td>
      <input type="text" style="width:600px;" name="settings_emails" value="{$_MOD_JAM_SETTINGS_EMAILS}"/>
     </td>
    </tr>
   </table>
   <table cellpadding="0" cellspacing="0">
    <tr>
     <td style="padding-top:20px;">
      <label for="mod_jam_settings_emails_content">{$smarty.const._MOD_JAM_SETTINGS_EMAILS_CONTENT_TITLE}</label>
     </td>
    </tr>
    <tr>
     <td>
      <textarea class="inputContentTextarea simpleEditor" style="width:600px;height:200px;" id="mod_jam_settings_emails_content" name="mod_jam_email_content_admin">{$_MOD_JAM_SETTINGS_EMAIL_ALERT_CONTENT}</textarea>
     </td>
    </tr>
    <tr>
     <td style="padding-top:px;padding-bottom:10px;color:#888;">
      {$smarty.const._MOD_JAM_SETTINGS_EMAILS_CONTENT_TEXT}
     </td>
    </tr>
    <!--
    <tr>
     <td style="padding-top:10px;padding-bottom:5px;">
      {$smarty.const._MOD_JAM_SETTINGS_EMAIL_EXTRA_TEXT}
     </td>
    </tr>
    <tr>
     <td style="padding-top:0px;padding-bottom:15px;">
      <table cellpadding="0" cellspacing="0" width="100%">
       <tr>
        <td>
         <input type="checkbox" name="mod_jam_form_extras[]" value="send_name" id="mod_jam_form_send_name"{if $_MOD_JAM_SETTINGS_EMAIL_EXTRAS_NAME} checked{/if}><label for="mod_jam_form_send_name">{$smarty.const._MOD_JAM_SETTINGS_EMAIL_EXTRA_USER_NAME}</label>
        </td>
        <td>
         <input type="checkbox" name="mod_jam_form_extras[]" value="send_email" id="mod_jam_form_send_email"{if $_MOD_JAM_SETTINGS_EMAIL_EXTRAS_EMAIL} checked{/if}><label for="mod_jam_form_send_email">{$smarty.const._MOD_JAM_SETTINGS_EMAIL_EXTRA_USER_EMAIL}</label>
        </td>
        <td>
         <input type="checkbox" name="mod_jam_form_extras[]" value="send_phone" id="mod_jam_form_send_phone"{if $_MOD_JAM_SETTINGS_EMAIL_EXTRAS_PHONE} checked{/if}><label for="mod_jam_form_send_phone">{$smarty.const._MOD_JAM_SETTINGS_EMAIL_EXTRA_USER_PHONE}</label>
        </td>
        <td>
         <input type="checkbox" name="mod_jam_form_extras[]" value="send_cv_url" id="mod_jam_form_send_cv_url"{if $_MOD_JAM_SETTINGS_EMAIL_EXTRAS_CV_URL} checked{/if}><label for="mod_jam_form_send_cv_url">{$smarty.const._MOD_JAM_SETTINGS_EMAIL_EXTRA_USER_CV_URL}</label>
        </td>
       </tr>
      </table>
     </td>
    </tr>
    -->
    <tr>
     <td style="padding-top:20px;">
      <label for="mod_jam_settings_emails_content_reply">{$smarty.const._MOD_JAM_SETTINGS_EMAILS_CONTENT_REPLY_TITLE}</label>
     </td>
    </tr>
    <tr>
     <td>
      <textarea class="inputContentTextarea simpleEditor" style="width:600px;height:200px;" id="mod_jam_settings_emails_content_reply" name="mod_jam_email_content_reply">{$_MOD_JAM_SETTINGS_EMAIL_REPLY_CONTENT}</textarea>
     </td>
    </tr>
    <tr>
     <td style="padding-top:0px;padding-bottom:10px;color:#888;">
      {$smarty.const._MOD_JAM_SETTINGS_EMAILS_CONTENT_REPLY_TEXT}
     </td>
    </tr>
   </table>
   <input type="submit" name="submit" value="{$smarty.const._SUBMIT}"/>
   </form>
   </fieldset>
  </td>
 </tr>

 <tr>
  <td>
   <fieldset class="fieldsetSeparator">
   <legend>{$smarty.const._MOD_JAM_SETTINGS_PAGE_SETTINGS_TITLE}</legend>
   <form action="{$MOD_JOBS_MANAGER_BASEURL}&action=save_settings_page&tab=settings" method="post">
   <table cellpadding="0" cellspacing="0">
    <tr>
     <td>
      <label for="mod_jam_settings_page_about">{$smarty.const._MOD_JAM_SETTINGS_ABOUT_TITLE}</label>
     </td>
    </tr>
    <tr>
     <td>
      <textarea class="inputContentTextarea simpleEditor" style="width:600px;height:200px;" id="mod_jam_settings_page_about" name="mod_jam_settings_page_about">{$_MOD_JAM_SETTINGS_ABOUT}</textarea>
     </td>
    </tr>
    <tr>
     <td style="padding-top:px;padding-bottom:10px;color:#888;">
      {$smarty.const._MOD_JAM_SETTINGS_ABOUT_TEXT}
     </td>
    </tr>
    <tr>
     <td style="padding-top:5px;">
      <label for="mod_jam_settings_page_list_location">{$smarty.const._MOD_JAM_SETTINGS_LIST_LOCATION_TITLE}</label>
     </td>
    </tr>
    <tr>
     <td>
      <select id="mod_jam_settings_page_list_location" name="mod_jam_settings_page_list_location">
       <option value="LEFT"{if $_MOD_JAM_SETTINGS_LIST_LOCATION == 'LEFT'} selected{/if}>{$smarty.const._MOD_JAM_SETTINGS_LIST_LOCATION_LEFT}</option>
       <option value="RIGHT"{if $_MOD_JAM_SETTINGS_LIST_LOCATION == 'RIGHT'} selected{/if}>{$smarty.const._MOD_JAM_SETTINGS_LIST_LOCATION_RIGHT}</option>
      </select>
     </td>
    </tr>
    <tr>
     <td style="padding-top:0px; padding-bottom:10px; color:#888;">
      {$smarty.const._MOD_JAM_SETTINGS_LIST_LOCATION_TEXT}
     </td>
    </tr>
    <tr>
     <td style="padding-top:5px;">
      <label for="mod_jam_settings_page_list_type">{$smarty.const._MOD_JAM_SETTINGS_LIST_TYPE_TITLE}</label>
     </td>
    </tr>
    <tr>
     <td>
      <select id="mod_jam_settings_page_list_type" name="mod_jam_settings_page_list_type">
       <option value="LIST"{if $_MOD_JAM_SETTINGS_LIST_TYPE == 'LIST'} selected{/if}>{$smarty.const._MOD_JAM_SETTINGS_LIST_TYPE_LIST}</option>
       <option value="SELECT"{if $_MOD_JAM_SETTINGS_LIST_TYPE == 'SELECT'} selected{/if}>{$smarty.const._MOD_JAM_SETTINGS_LIST_TYPE_SELECT}</option>
      </select>
     </td>
    </tr>
    <tr>
     <td style="padding-top:0px; padding-bottom:10px; color:#888;">
      {$smarty.const._MOD_JAM_SETTINGS_LIST_TYPE_TEXT}
     </td>
    </tr>
   </table>
   <input type="submit" name="submit" value="{$smarty.const._SUBMIT}"/>
   </form>
   </fieldset>
  </td>
 </tr>

 <tr>
  <td>
   <fieldset class="fieldsetSeparator">
   <legend>{$smarty.const._MOD_JAM_SETTINGS_LOGO_SECTION}</legend>
   {php}
    //echo '['.$_SERVER['DOCUMENT_ROOT'].']<br/>'; $settings = new Settings(); echo '['.$settings->getUploadPathLocal().']<br/>'; echo '['.$settings->getUploadPathWeb().']';
   {/php}
   <form action="{$MOD_JOBS_MANAGER_BASEURL}&action=save_settings_logo&tab=settings" enctype="multipart/form-data" method="post">
   <input type="hidden" name="MAX_FILE_SIZE" value="1048576"/><!-- 1MB MAXIMUM FILESIZE -->
   <table cellpadding="0" cellspacing="0">
    <tr>
     <td>
      <div style="border: solid 1px #aaa; width:800px; height:120px;{if $_MOD_JAM_SETTINGS_LOGO}background:url({$_MOD_JAM_SETTINGS_LOGO}) no-repeat;{/if}">&nbsp;</div>
     </td>
    </tr>
    <tr>
     <td style="padding-top:px;padding-bottom:10px;color:#888;">
      {$smarty.const._MOD_JAM_SETTINGS_LOGO_PREVIEW}
     </td>
    </tr>
    <tr>
     <td style="padding-top:20px;">
      <label for="mod_jam_settings_logo_file">{$smarty.const._MOD_JAM_SETTINGS_LOGO_TITLE}</label>
     </td>
    </tr>
    <tr>
     <td>
      <input type="file" name="mod_jam_settings_logo_file" id="mod_jam_settings_logo_file" size="100" style="width:200px;">
     </td>
    </tr>
    <tr>
     <td style="padding-top:px;padding-bottom:10px;color:#888;">
      {$smarty.const._MOD_JAM_SETTINGS_LOGO_TEXT}
     </td>
    </tr>
   </table>
   <input type="submit" name="submit" value="{$smarty.const._SUBMIT}"/>
   </form>
   </fieldset>
  </td>
 </tr>

</table>
