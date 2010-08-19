{*Smarty template*}

{if $smarty.session.s_type == "administrator"}
    {capture name = 't_BBB_server'}
                {$T_BBB_FORM.javascript}
                <form {$T_BBB_FORM.attributes}>
                    {$T_BBB_FORM.hidden}
                    <table class = "formElements">
                        <tr><td class = "labelCell">{$smarty.const._BBB_BBBSERVERNAME}:&nbsp;</td>
                            <td class = "elementCell">{$T_BBB_FORM.server.html}</td>
                            <td class = "elementCell" align="left" width="100%">&nbsp;<a href="javascript:void(0)" onClick="document.getElementById('server_input').value = ''" ><img src="images/16x16/go_into.png" title="{$smarty.const._BBB_RESETDEFAULTSERVER}" alt="{$smarty.const._BBB_RESETDEFAULTSERVER}" border =0 style="vertical-align:middle"/></a> </td>
       <td class = "formError">{$T_BBB_FORM.server.error}</td></tr>
       <td class = "labelCell">{$smarty.const._BBB_SECURITYSALT}:&nbsp;</td>
       <td class = "elementCell">{$T_BBB_FORM.salt.html}</td>
      <tr><td></td><td >&nbsp;</td></tr>
                        <tr><td></td><td class = "submitCell">{$T_BBB_FORM.submit_BBB_server.html}</td></tr>
                    </table>
                </form>
    {/capture}

    {eF_template_printBlock title=$smarty.const._BBB_BBBSERVER data=$smarty.capture.t_BBB_server absoluteImagePath=1 image=$T_BBB_MODULE_BASELINK|cat:'images/BBB32.png'}

{else}
    {if $smarty.get.add_BBB || $smarty.get.edit_BBB}
        {capture name = 't_insert_BBB_code'}
                    {$T_BBB_FORM.javascript}
                    <form {$T_BBB_FORM.attributes}>
                        {$T_BBB_FORM.hidden}
                        <table class = "formElements">
                            <tr><td class = "labelCell">{$smarty.const._BBB_NAME}:&nbsp;</td>
                                <td class = "elementCell">{$T_BBB_FORM.name.html}</td>
                                <td class = "formError">{$T_BBB_FORM.name.error}</td></tr>
                            <tr><td class = "labelCell">{$smarty.const._BBB_DATE}:&nbsp;</td>
                                <td class = "elementCell"><table><tr><td>{$T_BBB_FORM.day.html}</td>
                                                                     <td>{$T_BBB_FORM.month.html}</td>
                                                                     <td>{$T_BBB_FORM.year.html}</td>
                                                                     </tr></table>
                            <tr><td class = "labelCell">{$smarty.const._BBB_TIME}:&nbsp;</td>
                                <td class = "elementCell"><table><tr><td>{$T_BBB_FORM.hour.html}</td>
                                                                     <td>{$T_BBB_FORM.minute.html}</td>
                                                                     </tr></table>
                            <tr><td class = "labelCell">{$smarty.const._BBBDURATION}:&nbsp;</td>
                                <td class = "elementCell"><table><tr><td>{$T_BBB_FORM.duration_hours.html}</td>
                                                                     <td>{$T_BBB_FORM.duration_minutes.html}</td>
                                                                     </tr></table>
                            <tr><td></td><td >&nbsp;</td></tr>

                            <tr><td></td><td class = "submitCell">{$T_BBB_FORM.submit_BBB.html}</td></tr>
                        </table>
                    </form>

        {/capture}

        {capture name = 't_BBB_users'}
                            {literal}
                            <script>
                            function ajaxSendMails() {
                                var url = '{/literal}{$T_BBB_MODULE_BASEURL}&edit_BBB={$smarty.get.edit_BBB}&mail_users=1{literal}&postAjaxRequest=1';
                                if ($('BBBUsersTable_currentFilter')) {
                     url = url+'&filter='+$('BBBUsersTable_currentFilter').innerHTML;
                  }
                                $('mail_image').writeAttribute('src', 'images/others/progress1.gif').show();
                                new Ajax.Request(url, {
                                    method:'get',
                                    asynchronous:true,
                                    onSuccess: function (transport) {

                                    alert(transport.responseText + " {/literal}{$smarty.const._BBB_EMAILSENTSUCCESFFULLY}{literal}");
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
                        <td align="left">{$smarty.const._BBB_NOTIFYUSERSVIAEMAIL}</td>
                    </tr>
                    </table>
<!--ajax:BBBUsersTable-->
                    <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "BBBUsersTable" useAjax = "1" rowsPerPage = "20" url = "{$T_BBB_MODULE_BASEURL}&edit_BBB={$smarty.get.edit_BBB}&">
                        <tr class = "topTitle">
                            <td class = "topTitle" name="login">{$smarty.const._LOGIN}</td>
                            <td class = "topTitle" name="name">{$smarty.const._NAME}</td>
                            <td class = "topTitle" name="surname">{$smarty.const._SURNAME}</td>
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
                                    <span style="display:none" id="check_row{$user.login}">{if $user.meeting_ID == $smarty.get.edit_BBB}1{else}0{/if}</span>
                                    <input class = "inputCheckBox" type = "checkbox" onclick="javascript:ajaxPost('{$user.login}', this);" name = "check_{$user.login}" id = "check_row{$user.login}"
                                    {if $user.meeting_ID == $smarty.get.edit_BBB}
                                     checked
                                    {/if}
                                    >
                                </td>
                            </tr>
                        {foreachelse}
                            <tr><td colspan="5" class = "emptyCategory">{$smarty.const._NOUSERSFOUND}</td></tr>
                        {/foreach}
                        </table>
<!--/ajax:BBBUsersTable-->
                    </form>
                {* Script for posting ajax requests regarding skill to employees assignments *}
                {literal}
                <script>
                // Wrapper function for any of the 2-3 points where Ajax is used in the module personal
                function ajaxPost(id, el, table_id) {
                     Element.extend(el);

                     var baseUrl = '{/literal}{$T_BBB_MODULE_BASEURL}{literal}&edit_BBB={/literal}{$smarty.get.edit_BBB}{literal}&postAjaxRequest=1';

                     if (id) {
                         var url = baseUrl + '&user=' + id + '&insert='+el.checked;
                         var img_id = 'img_'+ id;
      if ($(table_id+'_currentFilter')) {
                   url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
               }
                     } else if (table_id && table_id == 'BBBUsersTable') {
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

                                         if (sortedTables[i].id == 'BBBUsersTable') {

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
        {capture name = 't_BBB_tabber'}
            <div class="tabber" >
               <div class="tabbertab">
                    <h3>{$smarty.const._BBB_SCHEDULEMEETING}</h3>
                    {eF_template_printBlock title = $smarty.const._BBB_SCHEDULEMEETING data = $smarty.capture.t_insert_BBB_code image = '32x32/calendar.png'}
                </div>
                {if isset($smarty.get.edit_BBB)}
                    <div class="tabbertab{if $smarty.get.tab == "users" } tabbertabdefault {/if}">
                        <h3>{$smarty.const._BBB_MEETINGATTENDANTS}</h3>
                        {eF_template_printBlock title = $smarty.const._BBB_MEETINGATTENDANTS data = $smarty.capture.t_BBB_users image = '32x32/users.png'}
                    </div>
                {/if}
            </div>
        {/capture}
        {eF_template_printBlock title=$smarty.const._BBB_BBBMEETINGDATA data=$smarty.capture.t_BBB_tabber absoluteImagePath=1 image=$T_BBB_MODULE_BASELINK|cat:'images/BBB32.png'}
    {else}
        {capture name = 't_BBB_list_code'}
            {if $T_BBB_CURRENTLESSONTYPE == "professor"}
            <table>
                <tr><td>
                    <a href = "{$T_BBB_MODULE_BASEURL}&add_BBB=1"><img src = "images/16x16/add.png" alt = "{$smarty.const._BBB_ADDBBB}" title = "{$smarty.const._BBB_ADDBBB}" border = "0" /></a>
                </td><td>
                    <a href = "{$T_BBB_MODULE_BASEURL}&add_BBB=1" title = "{$smarty.const._BBB_ADDBBB}">{$smarty.const._BBB_ADDBBB}</a>
                </td></tr>
            </table>
            {/if}
            <table class="sortedTable" id = "module_BBB_sortedTable" border = "0" width = "100%" sortBy = "0">
                <tr class = "topTitle">
                    <td class = "topTitle">{$smarty.const._BBB_NAME}</td>
                    <td class = "topTitle" width="20%">{$smarty.const._BBB_DATE}</td>
                    <td class = "topTitle" width="20%">{$smarty.const._BBBDURATION}</td>
                    <td class = "topTitle" width="20%">{$smarty.const._BBB_STATUS}</td>
                    <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                </tr>

                {foreach name =BBB item=meeting from = $T_BBB}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>{if $T_BBB_CURRENTLESSONTYPE != "student"}<a href = "{$T_BBB_MODULE_BASEURL}&edit_BBB={$meeting.id}" class = "editLink">{$meeting.name}</a>{else}{$meeting.name}{/if}</td>
                    <td>#filter:timestamp_time-{$meeting.timestamp}#</td>
                    <td>{$meeting.durationHours}:{if $meeting.durationMinutes == 0}00{else}{$meeting.durationMinutes}{/if}</td>
                    <td >{if $meeting.status == "0"}{$smarty.const._BBBNOTSTARTED}{elseif $meeting.status == "1"}{$smarty.const._BBBSTARTED}{else}{$smarty.const._BBBFINISHED}{/if}</td>
                    <td align = "center">
                        {if $T_BBB_CURRENTLESSONTYPE == "professor"}
                         <table>
                             <tr>
                             {if $meeting.status != "2"}
                             <td width="30%">
                                 <a href = "{$T_BBB_MODULE_BASEURL}&edit_BBB={$meeting.id}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                             </td>
                             <td width="30%">
                                 {if $meeting.status == "0" && !$meeting.mayStart}<img border = "0" src = "{$T_BBB_MODULE_BASELINK}images/server_client_exchange.png" class = "inactiveImage" title = "{$smarty.const._BBBJOINMEETING}" alt = "{$smarty.const._BBBJOINMEETING}" />{elseif $meeting.mayStart}<a href = "{$T_BBB_CREATEMEETINGURL}" onClick="return confirm('{$smarty.const._BBB_AREYOUSUREYOUWANTTOSTARTTHECONFERENCE}')" class = "editLink"><img border = "0" src = "{$T_BBB_MODULE_BASELINK}images/server_client_exchange.png" title = "{$smarty.const._BBBSTARTMEETING}" alt = "{$smarty.const._BBBSTARTMEETING}" /></a>{/if}
                             </td>
                             <td width="30%">
                             {else}
                             <td align="center">
                             {/if}
                                 <a href = "{$T_BBB_MODULE_BASEURL}&delete_BBB={$meeting.id}" onclick = "return confirm('{$smarty.const._BBBAREYOUSUREYOUWANTTODELETEEVENT}')" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                             </td>
                             </tr>
                          </table>
                         {else}
                            {if $meeting.status == "0"}
                             <img border = "0" src = "{$T_BBB_MODULE_BASELINK}images/server_client_exchange.png" class = "inactiveImage" title = "{$smarty.const._BBBJOINMEETING}" alt = "{$smarty.const._BBBJOINMEETING}" />
                            {elseif $meeting.status == "1" }
                             <a href = "{$meeting.joiningUrl}" class = "editLink"><img border = "0" src = "{$T_BBB_MODULE_BASELINK}images/server_client_exchange.png" title = "{$smarty.const._BBBJOINMEETING}" alt = "{$smarty.const._BBBJOINMEETING}" /></a>
                            {/if}
                         {/if}
                    </td>
                </tr>
                {foreachelse}
                <tr><td colspan="5" class = "emptyCategory">{$smarty.const._BBBNOMEETINGSCHEDULED}</td></tr>
                {/foreach}
            </table>
        {/capture}


        {eF_template_printBlock title=$smarty.const._BBB_BBBLIST data=$smarty.capture.t_BBB_list_code absoluteImagePath=1 image=$T_BBB_MODULE_BASELINK|cat:'images/BBB32.png'}
    {/if}
{/if}
