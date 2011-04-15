{capture name = 't_users_to_certificates_code'}
   <table width = "100%" id = "certificatesTable" class = "sortedTable" >
    <tr class = "topTitle">
     <td class = "topTitle" name = "course_name">{$smarty.const._COURSE}</td>
     <td class = "topTitle centerAlign" name = "score">{$smarty.const._COURSESCORE}</td>
     <td class = "topTitle" name = "certificate_key">{$smarty.const._CERTIFICATEKEY}</td>
     <td class = "topTitle" name = "issue_date">{$smarty.const._CERTIFICATEISSUEDON}</td>
     <td class = "topTitle" name = "expiry_date">{$smarty.const._CERTIFICATEEXPIRESON}</td>
     <td class = "topTitle centerAlign noSort" >{$smarty.const._FUNCTIONS}</td>
    </tr>

   {foreach name = 'users_to_certificates_list' key = 'key' item = 'certificate' from = $T_USER_TO_CERTIFICATES}
    {strip}
    <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$certificate.active}deactivatedTableElement{/if}">
     <td>{$certificate.course_name}</td>
     <td class = "centerAlign">{$certificate.grade}</td>
     <td>{$certificate.serial_number}</td>
     <td>#filter:timestamp-{$certificate.issue_date}#</td>
     <td>{if !is_numeric($certificate.expiration_date)}{$smarty.const._NEVER}{else}#filter:timestamp-{$certificate.expiration_date}#{/if}</td>
     <td class = "centerAlign">
      {if $T_EDITEDUSER->user.login == $smarty.session.s_login}
      <img src = "images/16x16/certificate.png" title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}" class = "ajaxHandle" onclick = "window.open('{$smarty.server.PHP_SELF}?ctg=lessons&course={$certificate.courses_ID}&export={$certificate.export_method}&user={$T_EDITEDUSER->user.login}')"/>
      {else}
      <img src = "images/16x16/certificate.png" title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}" class = "ajaxHandle" onclick = "window.open('{$smarty.server.PHP_SELF}?ctg=courses&op=course_certificates&export={$certificate.export_method}&user={$T_EDITEDUSER->user.login}&course={$certificate.courses_ID}')"/>
      {/if}
      {if $_change_certificates_}
      <a href = "{$smarty.server.PHP_SELF}?ctg=courses&op=course_certificates&revoke_certificate={$T_EDITEDUSER->user.login}&course={$certificate.courses_ID}" title = "{$smarty.const._REVOKECERTIFICATE}">
       <img src = "images/16x16/error_delete.png" title = "{$smarty.const._REVOKECERTIFICATE}" alt = "{$smarty.const._REVOKECERTIFICATE}" class = "handle"/>
      </a>
      {/if}
     </td>
    </tr>
    {/strip}
   {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
   </table>
{/capture}
{eF_template_printBlock title = $smarty.const._CERTIFICATES data = $smarty.capture.t_users_to_certificates_code image = '32x32/certificate.png'}
