
{capture name = "moduleArchive"}
 <tr><td class = "moduleCell">
 {if !$smarty.get.type}

  {eF_template_printBlock title=$smarty.const._ARCHIVE columns=3 links=$T_ARCHIVE_OPTIONS image='32x32/options.png' help = 'Archive'}

 {else}

  {capture name = 't_archive_list_code'}
<!--ajax:archiveTable-->
             <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "archiveTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=archive&type={$smarty.get.type}&">
                 <tr class = "topTitle defaultRowHeight">
                     <td class = "topTitle">{$smarty.const._NAME}</td>
                     <td class = "topTitle">{$smarty.const._ARCHIVEDON}</td>
    {if $_change_}
                     <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                     <td class = "topTitle centerAlign">{$smarty.const._SELECT}</td>
             {/if}
                 </tr>
                 {foreach name = 'entities_list' key = 'key' item = 'entity' from = $T_DATA_SOURCE}
                 <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                  <td>
                  {if $smarty.get.type == 'users'}
                   {assign var = "identifier" value = $entity.login}
                   #filter:login-{$entity.login}#
                  {else}
                   {assign var = "identifier" value = $entity.id}
                   {$entity.name}
                  {/if}
                  </td>
                  <td>#filter:timestamp_time-{$entity.archive}#</td>
    {if $_change_}
                  <td class = "centerAlign">
                <img class = "ajaxHandle" src = "images/16x16/undo.png" title = "{$smarty.const._RESTORE}" alt = "{$smarty.const._RESTORE}" onclick = "restoreArchive(this, '{$identifier}');">
                <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "deleteArchive(this, '{$identifier}');">
                  </td>
                  <td class = "centerAlign">
                  {if !isset($T_CURRENT_USER->coreAccess.archive) || $T_CURRENT_USER->coreAccess.archive== 'change'}
                         <input class = "inputCheckbox" type = "checkbox" id = "check_{$entity.login}" value = "{$identifier}"/>
      {/if}
      </td>
    {/if}
                 </tr>
     {foreachelse}
                 <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
     {/foreach}
    </table>
<!--/ajax:archiveTable-->
   {if $_change_}
             <div class = "horizontalSeparatorAbove">
              <span style = "vertical-align:middle">{$smarty.const._WITHSELECTED}:</span>
              <img class = "ajaxHandle" src = "images/16x16/undo.png" title = "{$smarty.const._RESTORESELECTED}" alt = "{$smarty.const._RESTORESELECTED}" onclick = "restoreSelected('archiveTable');">
              <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETESELECTED}" alt = "{$smarty.const._DELETESELECTED}" onclick = "deleteSelected('archiveTable');">
             </div>
         {/if}
  {/capture}
  {eF_template_printBlock title = $smarty.const._ARCHIVE data = $smarty.capture.t_archive_list_code image='32x32/generic.png'}

 {/if}


 </td></tr>
{/capture}
