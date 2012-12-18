{**}
{*Smarty template*}

{capture name = 't_all_events'}

{if $smarty.get.add_event || $smarty.get.edit_event}
 {eF_template_printForm form = $T_MODULE_OUTLOOK_INVITATION_FORM}

  {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location;</script>
  {/if}
{else}
 {capture name='t_events_list_code'}

<!--ajax:outlookInvitationsTable-->
                        <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "3" id = "outlookInvitationsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_BASEURL}&">
                            <tr class = "defaultRowHeight">
                                <td class = "topTitle" name = "courses_ID">{$smarty.const._COURSE}</td>
                                <td class = "topTitle centerAlign" name = "location">{$smarty.const._LOCATION}</td>
                                <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                            </tr>
                    {foreach name = 'events_list' key = "key" item = "event" from = $T_DATA_SOURCE}
                            <tr id="row_{$key}" class = "{cycle name = "imports" values = "oddRowColor, evenRowColor"}">
                                <td><a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=courses&course={$event.courses_ID}&op=course_scheduling">{$T_MODULE_OUTLOOK_INVITATION_DIRECTION_PATHS[$event.directions_ID]}&rarr;{$event.name}</a></td>
                                <td class = "centerAlign">{$event.location}</td>
                                <td class = "centerAlign">
                                    <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteEvent(this, '{$event.courses_ID}')"/>
                                </td>
                             </tr>
                    {foreachelse}
                            <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory" colspan = "3">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
                        </table>
<!--/ajax:outlookInvitationsTable-->
 {/capture}
 <div class="tabber">
  <div class="tabbertab" title="{$smarty.const._MODULE_OUTLOOK_INVITATION_EVENTS}">
  {eF_template_printBlock title=$smarty.const._MODULE_OUTLOOK_INVITATION_EVENTS data=$smarty.capture.t_events_list_code absoluteImagePath=1 image=$T_MODULE_OUTLOOK_INVITATION_BASELINK|cat:'img/outlook.png'}
  </div>
 </div>

{/if}


{/capture}

{eF_template_printBlock title=$smarty.const._MODULE_OUTLOOK_INVITATION_OUTLOOK_INVITATION data=$smarty.capture.t_all_events absoluteImagePath=1 image=$T_MODULE_OUTLOOK_INVITATION_BASELINK|cat:'img/outlook.png'}
