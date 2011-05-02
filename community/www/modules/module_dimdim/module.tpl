{*Smarty template*}

{if $smarty.session.s_type == "administrator"}
    {capture name = 't_dimdim_server'}
                {$T_DIMDIM_FORM.javascript}
                <form {$T_DIMDIM_FORM.attributes}>
                    {$T_DIMDIM_FORM.hidden}
                    <table class = "formElements">
                        <tr><td class = "labelCell">{$smarty.const._DIMDIM_DIMDIMSERVERNAME}:&nbsp;</td>
                            <td class = "elementCell">{$T_DIMDIM_FORM.server.html}</td>
                            <td class = "elementCell" align="left" width="100%">&nbsp;<a href="javascript:void(0)" onClick="document.getElementById('server_input').value = 'http://www1.dimdim.com'" ><img src="images/16x16/go_into.png" title="{$smarty.const._DIMDIM_RESETDEFAULTSERVER}" alt="{$smarty.const._DIMDIM_RESETDEFAULTSERVER}" border =0 style="vertical-align:middle"/></a> </td>
                            <td class = "formError">{$T_DIMDIM_FORM.server.error}</td></tr>
                        <tr><td></td><td >&nbsp;</td></tr>
                        <tr><td></td><td class = "submitCell">{$T_DIMDIM_FORM.submit_dimdim_server.html}</td></tr>
                    </table>
                </form>
    {/capture}

    {eF_template_printBlock title=$smarty.const._DIMDIM_DIMDIMSERVER data=$smarty.capture.t_dimdim_server absoluteImagePath=1 image=$T_DIMDIM_MODULE_BASELINK|cat:'images/dimdim32.png'}

{else}
    {if $smarty.get.add_dimdim || $smarty.get.edit_dimdim}
        {capture name = 't_insert_dimdim_code'}
                    {$T_DIMDIM_FORM.javascript}
                    <form {$T_DIMDIM_FORM.attributes}>
                        {$T_DIMDIM_FORM.hidden}
                        <table class = "formElements">
                            <tr><td class = "labelCell">{$smarty.const._DIMDIM_NAME}:&nbsp;</td>
                                <td class = "elementCell">{$T_DIMDIM_FORM.name.html}</td>
                                <td class = "formError">{$T_DIMDIM_FORM.name.error}</td></tr>
                            <tr><td class = "labelCell">{$smarty.const._DIMDIM_DATE}:&nbsp;</td>
                                <td class = "elementCell"><table><tr><td>{$T_DIMDIM_FORM.day.html}</td>
                                                                     <td>{$T_DIMDIM_FORM.month.html}</td>
                                                                     <td>{$T_DIMDIM_FORM.year.html}</td>
                                                                     </tr></table>
                            <tr><td class = "labelCell">{$smarty.const._DIMDIM_TIME}:&nbsp;</td>
                                <td class = "elementCell"><table><tr><td>{$T_DIMDIM_FORM.hour.html}</td>
                                                                     <td>{$T_DIMDIM_FORM.minute.html}</td>
                                                                     </tr></table>
                            <tr><td class = "labelCell">{$smarty.const._DIMDIMDURATION}:&nbsp;</td>
                                <td class = "elementCell"><table><tr><td>{$T_DIMDIM_FORM.duration_hours.html}</td>
                                                                     <td>{$T_DIMDIM_FORM.duration_minutes.html}</td>
                                                                     </tr></table>
                            <tr><td></td><td >&nbsp;</td></tr>
                            <tr><td class = "labelCell">{$T_DIMDIM_FORM.presenterAV.label}:&nbsp;</td>
                                <td class = "elementCell">{$T_DIMDIM_FORM.presenterAV.html}</td>
                                <td class = "formError">{$T_DIMDIM_FORM.presenterAV.error}</td></tr>

                            <tr><td class = "labelCell">{$T_DIMDIM_FORM.maxParticipants.label}:&nbsp;</td>
                                <td class = "elementCell">{$T_DIMDIM_FORM.maxParticipants.html}</td>
                                <td class = "formError">{$T_DIMDIM_FORM.maxParticipants.error}</td></tr>

                            <tr><td class = "labelCell">{$T_DIMDIM_FORM.maxMics.label}:&nbsp;</td>
                                <td class = "elementCell">{$T_DIMDIM_FORM.maxMics.html}</td>
                                <td class = "formError">{$T_DIMDIM_FORM.maxMics.error}</td></tr>

                            <tr><td class = "labelCell">{$T_DIMDIM_FORM.lobby.label}:&nbsp;</td>
                                <td class = "elementCell">{$T_DIMDIM_FORM.lobby.html}</td>
                                <td class = "formError">{$T_DIMDIM_FORM.lobby.error}</td></tr>
                            <tr><td></td><td >&nbsp;</td></tr>

                            <tr><td></td><td class = "submitCell">{$T_DIMDIM_FORM.submit_dimdim.html}</td></tr>
                        </table>
                    </form>

        {/capture}

        {capture name = 't_dimdim_users'}
                            {literal}
                            <script>
                            function ajaxSendMails() {
                                var url = '{/literal}{$T_DIMDIM_MODULE_BASEURL}&edit_dimdim={$smarty.get.edit_dimdim}&mail_users=1{literal}&postAjaxRequest=1';
                                if ($('dimdimUsersTable_currentFilter')) {
                     url = url+'&filter='+$('dimdimUsersTable_currentFilter').innerHTML;
                  }
                                $('mail_image').writeAttribute('src', 'images/others/progress1.gif').show();
                                new Ajax.Request(url, {
                                    method:'get',
                                    asynchronous:true,
                                    onSuccess: function (transport) {

                                    alert(transport.responseText + " {/literal}{$smarty.const._DIMDIM_EMAILSENTSUCCESFFULLY}{literal}");
                                    if (transport.responseText == "0") {
                                        $('mail_image').hide().setAttribute('src', 'images/16x16/error_delete');
                                    } else {
                                        $('mail_image').hide().setAttribute('src', 'images/16x16/success.png');
                                    }
                                    new Effect.Appear($('mail_image'));
                                    window.setTimeout('Effect.Fade("mail_image")', 2500);
                                    window.setTimeout("$('mail_image').writeAttribute('src', 'images/16x16/mail_forward.png')", 3500);
                                    window.setTimeout("new Effect.Appear($('mail_image'))", 3500);

                                    }
                                });
                            }
                            </script>
                            {/literal}

                    <table style = "width:100%">
                    <tr><td width="2%"><a href="javascript:void(0);" onClick="ajaxSendMails()"><img src= "images/16x16/mail_forward.png" id="mail_image" border = 0 /></a></td>
                        <td align="left">{$smarty.const._DIMDIM_NOTIFYUSERSVIAEMAIL}</td>
                    </tr>
                    </table>
<!--ajax:dimdimUsersTable-->
                    <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "dimdimUsersTable" useAjax = "1" rowsPerPage = "20" url = "{$T_DIMDIM_MODULE_BASEURL}&edit_dimdim={$smarty.get.edit_dimdim}&">
                        <tr class = "topTitle">
                            <td class = "topTitle" name="login">{$smarty.const._LOGIN}</td>
                            <td class = "topTitle" name="name">{$smarty.const._FIRSTNAME}</td>
                            <td class = "topTitle" name="surname">{$smarty.const._LASTNAME}</td>
                            <td class = "topTitle" name="email">{$smarty.const._EMAIL}</td>
                            <td class = "topTitle noSort" name="login" align="center">{$smarty.const._CHECK}</td>
                        </tr>

                        {foreach name = 'users_list' key = 'key' item = 'user' from = $T_USERS}
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                <td>
                                {if ($user.pending == 1)}
                                    <span style="color:red;">{$user.login}</span>
                                {else}
                                    {$user.login}
                                {/if}
                                </td>

                                <td>{$user.name}</td>
                                <td>{$user.surname}</td>
                                <td>{$user.email}</td>
                                <td align = "center">
                                    <span style="display:none" id="check_row{$user.login}">{if $user.meeting_ID == $smarty.get.edit_dimdim}1{else}0{/if}</span>
                                    <input class = "inputCheckBox" type = "checkbox" onclick="javascript:ajaxPost('{$user.login}', this);" name = "check_{$user.login}" id = "check_row{$user.login}"
                                    {if $user.meeting_ID == $smarty.get.edit_dimdim}
                                     checked
                                    {/if}
                                    >
                                </td>
                            </tr>
                        {foreachelse}
                            <tr><td colspan="5" class = "emptyCategory">{$smarty.const._NOUSERSFOUND}</td></tr>
                        {/foreach}
                        </table>
<!--/ajax:dimdimUsersTable-->
                    </form>
                {* Script for posting ajax requests regarding skill to employees assignments *}
                {literal}
                <script>
                // Wrapper function for any of the 2-3 points where Ajax is used in the module personal
                function ajaxPost(id, el, table_id) {
                     Element.extend(el);

                     var baseUrl = '{/literal}{$T_DIMDIM_MODULE_BASEURL}{literal}&edit_dimdim={/literal}{$smarty.get.edit_dimdim}{literal}&postAjaxRequest=1';

                     if (id) {
                         var url = baseUrl + '&user=' + id + '&insert='+el.checked;
                         var img_id = 'img_'+ id;
      if ($(table_id+'_currentFilter')) {
                   url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
               }
                     } else if (table_id && table_id == 'dimdimUsersTable') {
                         el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                         var img_id = 'img_selectAll';
                         if ($(table_id+'_currentFilter')) {
                   url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
                }
                     }

                     var position = eF_js_findPos(el);
                     var img = document.createElement("img");

                     img.style.position = 'absolute';
                     img.style.top = Element.positionedOffset(Element.extend(el)).top + 'px';
                     img.style.left = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

                     img.setAttribute("id", img_id);
                     img.setAttribute('src', 'images/others/progress1.gif');

                     el.parentNode.appendChild(img);

                       new Ajax.Request(url, {
                                 method:'get',
                                 asynchronous:true,
                                 onSuccess: function (transport) {
                                     // Update all form tables
                                     /*

                                     var tables = sortedTables.size();

                                     var i;

                                     for (i = 0; i < tables; i++) {

                                         if (sortedTables[i].id == 'dimdimUsersTable') {

                                             eF_js_rebuildTable(i, 0, 'null', 'desc');

                                         }

                                     }

                                     */
                                     img.style.display = 'none';
                                     img.setAttribute('src', 'images/16x16/success.png');
                                     new Effect.Appear(img_id);
                                     window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                     }
                            });
                }
                </script>
                {/literal}
        {/capture}
        {capture name = 't_dimdim_tabber'}
            <div class="tabber" >
               <div class="tabbertab">
                    <h3>{$smarty.const._DIMDIM_SCHEDULEMEETING}</h3>
                    {eF_template_printBlock title = $smarty.const._DIMDIM_SCHEDULEMEETING data = $smarty.capture.t_insert_dimdim_code image = '32x32/calendar.png'}
                </div>
                {if isset($smarty.get.edit_dimdim)}
                    <div class="tabbertab{if $smarty.get.tab == "users" } tabbertabdefault {/if}">
                        <h3>{$smarty.const._DIMDIM_MEETINGATTENDANTS}</h3>
                        {eF_template_printBlock title = $smarty.const._DIMDIM_MEETINGATTENDANTS data = $smarty.capture.t_dimdim_users image = '32x32/users.png'}
                    </div>
                {/if}
            </div>
        {/capture}
        {eF_template_printBlock title=$smarty.const._DIMDIM_DIMDIMMEETINGDATA data=$smarty.capture.t_dimdim_tabber absoluteImagePath=1 image=$T_DIMDIM_MODULE_BASELINK|cat:'images/dimdim32.png'}
    {else}
        {capture name = 't_dimdim_list_code'}
            {if $T_DIMDIM_CURRENTLESSONTYPE == "professor"}
            <table>
                <tr><td>
                    <a href = "{$T_DIMDIM_MODULE_BASEURL}&add_dimdim=1"><img src = "images/16x16/add.png" alt = "{$smarty.const._DIMDIM_ADDDIMDIM}" title = "{$smarty.const._DIMDIM_ADDDIMDIM}" border = "0" /></a>
                </td><td>
                    <a href = "{$T_DIMDIM_MODULE_BASEURL}&add_dimdim=1" title = "{$smarty.const._DIMDIM_ADDDIMDIM}">{$smarty.const._DIMDIM_ADDDIMDIM}</a>
                </td></tr>
            </table>
            {/if}
            <table class="sortedTable" id = "module_dimdim_sortedTable" border = "0" width = "100%" sortBy = "0">
                <tr class = "topTitle">
                    <td class = "topTitle">{$smarty.const._DIMDIM_NAME}</td>
                    <td class = "topTitle" width="20%">{$smarty.const._DIMDIM_DATE}</td>
                    <td class = "topTitle" width="20%">{$smarty.const._DIMDIMDURATION}</td>
                    <td class = "topTitle" width="20%">{$smarty.const._DIMDIM_STATUS}</td>
                    <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                </tr>

                {foreach name =dimdim item=meeting from = $T_DIMDIM}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>{if $T_DIMDIM_CURRENTLESSONTYPE != "student"}<a href = "{$T_DIMDIM_MODULE_BASEURL}&edit_dimdim={$meeting.id}" class = "editLink">{$meeting.name}</a>{else}{$meeting.name}{/if}</td>
                    <td>#filter:timestamp_time-{$meeting.timestamp}#</td>
                    <td>{$meeting.durationHours}:{if $meeting.durationMinutes == 0}00{else}{$meeting.durationMinutes}{/if}</td>
                    <td >{if $meeting.status == "0"}{$smarty.const._DIMDIMNOTSTARTED}{elseif $meeting.status == "1"}{$smarty.const._DIMDIMSTARTED}{else}{$smarty.const._DIMDIMFINISHED}{/if}</td>
                    <td align = "center">
                        {if $T_DIMDIM_CURRENTLESSONTYPE == "professor"}
                         <table>
                             <tr>
                             {if $meeting.status != "2"}
                             <td width="30%">
                                 <a href = "{$T_DIMDIM_MODULE_BASEURL}&edit_dimdim={$meeting.id}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                             </td>
                             <td width="30%">
                                 {if $meeting.status == "0" && !$meeting.mayStart}<img border = "0" src = "{$T_DIMDIM_MODULE_BASELINK}images/server_client_exchange.png" class = "inactiveImage" title = "{$smarty.const._DIMDIMJOINMEETING}" alt = "{$smarty.const._DIMDIMJOINMEETING}" />{elseif $meeting.mayStart}<a href = "{$T_DIMDIM_MODULE_BASEURL}&start_meeting={$meeting.id}" onClick="return confirm('{$smarty.const._DIMDIM_AREYOUSUREYOUWANTTOSTARTTHECONFERENCE}')" class = "editLink"><img border = "0" src = "{$T_DIMDIM_MODULE_BASELINK}images/server_client_exchange.png" title = "{$smarty.const._DIMDIMSTARTMEETING}" alt = "{$smarty.const._DIMDIMSTARTMEETING}" /></a>{/if}
                             </td>
                             <td width="30%">
                             {else}
                             <td align="center">
                             {/if}
                                 <a href = "{$T_DIMDIM_MODULE_BASEURL}&delete_dimdim={$meeting.id}" onclick = "return confirm('{$smarty.const._DIMDIMAREYOUSUREYOUWANTTODELETEEVENT}')" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                             </td>
                             </tr>
                          </table>
                         {else}
                            {if $meeting.status == "0"}
                             <img border = "0" src = "{$T_DIMDIM_MODULE_BASELINK}images/server_client_exchange.png" class = "inactiveImage" title = "{$smarty.const._DIMDIMJOINMEETING}" alt = "{$smarty.const._DIMDIMJOINMEETING}" />
                            {elseif $meeting.status == "1" }
                             <a href = "{$meeting.joiningUrl}" class = "editLink"><img border = "0" src = "{$T_DIMDIM_MODULE_BASELINK}images/server_client_exchange.png" title = "{$smarty.const._DIMDIMJOINMEETING}" alt = "{$smarty.const._DIMDIMJOINMEETING}" /></a>
                            {/if}
                         {/if}
                    </td>
                </tr>
                {foreachelse}
                <tr><td colspan="5" class = "emptyCategory">{$smarty.const._DIMDIMNOMEETINGSCHEDULED}</td></tr>
                {/foreach}
            </table>
        {/capture}


        {eF_template_printBlock title=$smarty.const._DIMDIM_DIMDIMLIST data=$smarty.capture.t_dimdim_list_code absoluteImagePath=1 image=$T_DIMDIM_MODULE_BASELINK|cat:'images/dimdim32.png'}
    {/if}
{/if}
