{* Smarty template for personal messages *}
{* 2007/03/09: CSS safe, except for user_table table right below and some deprecated html properties (height, width etc)*}

{**
* READ THIS CAREFULLY!
* This description applies to all main pages: professor.tpl, student.tpl, administrator.tpl, chat_index.tpl, forum_index.tpl and messages_index.tpl
*
* The structure of this file is as follows:
* The first part contains initialization code, javascript functions etc.
* The second part contains the module list (seee below)
* The third part consists of the basic HTML layout of the page (see below)
* The fourth part contains any code that needs to be executed after the page layout has completed.
*
* Module list: Each part of the page, is assigned to a {capture} block. These blocks can then be used
    anywhere and as many times we need in the page. These {capture} blocks are listed one after another but not affecting each other.
    The format is as follows:

        {capture name = "moduleXXX"}
                            <tr><td class = "moduleCell">
                                <XXX code here>
                            </td></tr>
        {/capture}
    If the module is to be used as a right side menu (e.g. like content tree), the above declaratin changes as follows:
        {capture name = "sideXXX"}
                            <XXX code here>
        {/capture}
    as it is obvious, the only difference is the lack of the outer table elements...
    If there is an if clause specifying whether this module will be parsed, for example depending on the current ctg, the {capture} blocks are nested inside the clause
    Use modules for anything you think that comprises an individual function.
*
* Page layout: The system pages can have one of the following layout:
*   1 or 2 main columns: 1 main column layout is, for example, the "users" page at the administrator account. 2 main columns is administrator "control panel"
*   with or without side menu: With side menu are, for example, the specific content pages and the chat page, while without are most of the other pages
*
*   By default, a 1 main column layout is used. In order to use 2 main columns, we specify so right above the line with LEFT MAIN COLUMN
*   Also by default, pages do not include a side menu. In order for the side menu to appear, we specify so right above RIGHT SIDE MENU
*   For each module we create, we simply add it in the corresponding place, LEFT COLUMN, RIGHT COLUMN, SINGLE COLUMN or SIDE MENU. No if clauses are
*   put here; Any if clauses are put along with the module declaration
*   There is a special case, where we need both 1 and 2 main columns, for example the personal messages page, or the lessons page. In this case, we apply
*   minor modifications in the page layout. See the corresponding cases for an example of doing so.
*
*   SEE THE CODE BELOW FOR EXAMPLES!
*   AVOID OVER-NESTING TABLES!
*}

{include file = "includes/header.tpl"}

<script>
<!--
{literal}
function markAll() {
    var elements = document.getElementsByTagName('input');
    for (var i = 0; i < elements.length; i++) {
        if (elements[i].type == "checkbox" && elements[i].name.length != 0) {
            elements[i].checked == true ? elements[i].checked = false : elements[i].checked = true;
        }
    }
}
{/literal}

if(top.sideframe)
    top.sideframe.changeTDcolor('{$T_MENUCTG}');
//-->
</script>

{*-------------------------------End of Part 1: initialization etc-----------------------------------------------*}




{assign var = "title" value = '<a class = "titleLink" href =$smarty.session.s_type|cat:".php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}


{*-------------------------------Part 2: Modules List ---------------------------------------------*}

{**moduleFolders: The folders list*}
{capture name = "moduleFolders"}
<script src="js/scriptaculous/effects.js" type="text/javascript"></script>
<script src="js/scriptaculous/dragdrop.js" type="text/javascript"></script>

                            <tr><td class = "moduleCell">

                            {capture name = "t_folders_code"}
                                                    <table width="100%">
                                                        {section name = "folders_loop" loop = $T_FOLDERS}
                                                            {if $T_FOLDERS[folders_loop].name == 'Incoming' && !$incoming}
                                                                {assign var = "folder_name" value = $smarty.const._INCOMING} {assign var = "incoming" value = "true"}
                                                            {elseif $T_FOLDERS[folders_loop].name == 'Drafts' && !$drafts}
                                                                {assign var = "folder_name" value = $smarty.const._DRAFTS} {assign var = "drafts" value = "true"}
                                                            {elseif $T_FOLDERS[folders_loop].name == 'Sent' && !$sent}
                                                                {assign var = "folder_name" value = $smarty.const._SENT} {assign var = "sent" value = "true"}
                                                            {else}
                                                                {assign var = "folder_name" value = $T_FOLDERS[folders_loop].name}
                                {capture name = "t_edit_code"}
                                                        <a href = "forum/manage_folders.php?action=edit&id={$T_FOLDERS[folders_loop].id}" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', new Array('500px', '300px'))" target = "POPUP_FRAME"><img src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" border = "0"/></a>
                                                        <a href = "forum/manage_folders.php?action=delete&id={$T_FOLDERS[folders_loop].id}" onclick = "return confirm('{$smarty.const._AREYOUSURETODELETEFOLDER}&quot;{$folder_name}&quot;{$smarty.const._ANDALLCONTENTS}')" target = "POPUP_FRAME" ><img src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" border = "0"/></a>
                                {/capture}
                                                            {/if}

                                                            <tr id="div_folder_id_{$T_FOLDERS[folders_loop].id}"><td>
                                                                    <span class = "counter">{$smarty.section.folders_loop.iteration}.</span>
                                                                    {if $T_VIEWINGMESSAGE}
                                                                        <a href = "{$smarty.server.PHP_SELF}?folder={$T_FOLDERS[folders_loop].id}" {if $T_FOLDERS[folders_loop].id == $T_FOLDER}class = "selectedLink"{/if}>{$folder_name}&nbsp;({$T_FOLDERS[folders_loop].count})</a>
                                                                    {else}
                                                                        <a href = "javascript:void(0);" id="folder_id_{$T_FOLDERS[folders_loop].id}" onclick="loadMessagesFolder('{$T_FOLDERS[folders_loop].id}');" {if $T_FOLDERS[folders_loop].id == $T_FOLDER}class = "selectedLink"{/if}>{$folder_name}&nbsp;(<span id="folder_count_{$T_FOLDERS[folders_loop].id}">{$T_FOLDERS[folders_loop].count}</span>)</a>
                                                                    {/if}
                                                                </td>
                                                                <td>{$smarty.capture.t_edit_code}</td>
                                                            </tr>
                                                            <script type="text/javascript">Droppables.add('div_folder_id_{$T_FOLDERS[folders_loop].id}', {literal}{hoverclass:'messageFolderHoverclass',onDrop: function(element,dropon){document.getElementById(element.id).style.visibility = "hidden";moveMessageAjax(element.id, '{/literal}{$T_FOLDERS[folders_loop].id}{literal}', dropon);}})</script>{/literal}
                                                            {assign var = "folders_options" value = $folders_options|cat:'<option value = "'|cat:$T_FOLDERS[folders_loop].id|cat:'">'|cat:$folder_name|cat:'</option>'} {*This builds an <options> list containing the name of the available folders*}
                                                        {/section}

                                                    </table>
                                                    {literal}
                            <script type="text/javascript">
                            // Variable holding record of the current messages folder that is reviewd
                            var _current_folder;

                            // Function triggering the Ajax call to move a message
                            function moveMessageAjax(messageId, folderId, folderElement) {
                                url = 'forum/messages_index.php?postAjaxRequest=1&message='+messageId+'&move_message_folder='+folderId;
//alert(document.getElementById("folder_count_" + folderId).innerHTML);
                                newFolder = document.getElementById("folder_count_" + folderId);
                                previousFolder = document.getElementById("folder_count_" + _current_folder);
                                newFolder.innerHTML = parseInt(newFolder.innerHTML) + 1;
                                previousFolder.innerHTML = parseInt(previousFolder.innerHTML) -1;
/*
                                var img_id   = 'img_'+ folderElement.id;
                                var img_position = eF_js_findPos(folderElement);
                                var img = document.createElement("img");

                                img.style.position = 'absolute';
                                img.style.top      = Element.positionedOffset(Element.extend(folderElement)).top  + 'px';
                                img.style.left     = Element.positionedOffset(Element.extend(folderElement)).left + 6 + Element.getDimensions(Element.extend(folderElement)).width + 'px';

                                img.setAttribute("id", img_id);
                                img.setAttribute('src', 'images/others/progress1.gif');

                                folderElement.next().appendChild(img);
*/
                                new Ajax.Request(url, {
                                    method:'get',
                                    asynchronous:true,
                                    onSuccess: function (transport) {
                                    //alert('to folder inei to ' + _current_folder);
                                        loadMessagesFolder(_current_folder);
/*
                                        img.style.display = 'none';
                                        img.setAttribute('src', 'images/16x16/check.png');
                                        new Effect.Appear(img_id);
                                        window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
*/
                                        }
                                    });
                            }
                            </script>{/literal}
                            {/capture}

                            {capture name = "t_folders_nav_code"}
                            <!--                         <a href = "javascript:void(0)" onclick = "popUp('manage_folders.php?action=add', 400, 200, 1)" class = "optionsLink">{$smarty.const._NEWFOLDER}</a>-->
                            {/capture}

                            {eF_template_printInnerTable title = $smarty.const._FOLDERS data = $smarty.capture.t_folders_code image = "32x32/folders.png" navigation = $smarty.capture.t_folders_nav_code options = $T_FOLDERS_OPTIONS}

                            </td></tr>
{* Script for changing messages *}
{literal}
<script>
var active_folder_id = "folder_id_{/literal}{$T_FOLDER}{literal}";
// Wrapper function for any of the 2-3 points where Ajax is used in the module personal
function loadMessagesFolder(folder_id) {
    var tables = sortedTables.size();
    var i;
//alert('mesa sto loadmsg: ' + folder_id + ' ola einai ' + tables);


    ajaxUrl[1] = 'forum/messages_index.php?ajax=messagesTable&limit=20&offset=0&sort=null&order=desc&folder='+folder_id+'&';
    eF_js_rebuildTable('1', 0, null, 'desc');

/*
    alert(tables);
    for (i = 0; i < tables; i++) {
        if (sortedTables[i].id == 'messagesTable') {
            ajaxUrl[i] = 'forum/messages_index.php?ajax=messagesTable&limit=20&offset=0&sort=null&order=desc&folder='+folder_id+'&';
            eF_js_rebuildTable(i, 0, null, 'desc');
            break;

            //eF_js_rebuildTable(tableIndex, offset, column_name, order, other, noDiv)


        }
    }
*/
    //eF_js_rebuildTable(1, 0, 'null', 'desc');
    $(active_folder_id).setStyle("font-weight:normal;");

    active_folder_id = "folder_id_"+folder_id;
    $(active_folder_id).setStyle("font-weight:bold;");
}
</script>
{/literal}
{/capture}

{**moduleVolume: Folders volume information*}
{capture name = "moduleVolume"}
                            <tr><td class = "moduleCell">

                            {capture name = "t_volume_code"}
                                                    {if $smarty.session.s_type == 'student'}
                                                        {if ($T_TOTAL_MESSAGES_PERCENTAGE > 0.8*$T_QUOTA_NUM_OF_MESSAGES) & ($T_TOTAL_MESSAGES_PERCENTAGE < 0.9*$T_QUOTA_NUM_OF_MESSAGES)}
                                                            {assign var = "msgClassName" value = "plainWarning"}
                                                            {assign var = "msg_warn"  value = $smarty.const._APPROACHINGMESSAGEQUOTA|cat:$smarty.const._DELETEOLDMESSAGES}
                                                        {elseif ($T_TOTAL_MESSAGES_PERCENTAGE > 0.9*$T_QUOTA_NUM_OF_MESSAGES)}
                                                            {assign var = "msgClassName" value = "severeWarning"}
                                                            {assign var = "msg_warn"  value = $smarty.const._MESSAGEBOXSIZECRITICAL|cat:$smarty.const._ERASEMESSAGESNOTRECEIVENEW}
                                                        {else}
                                                            {assign var = "msgClassName" value = "noWarning"}
                                                            {assign var = "msg_warn"  value = ""}
                                                        {/if}

                                                        {if ($T_TOTAL_FILES_PERCENTAGE > 0.8*$T_QUOTA_KILOBYTES) & ($T_TOTAL_FILES_PERCENTAGE < 0.9*$T_QUOTA_KILOBYTES)}
                                                            {assign var = "fileClassName" value = "plainWarning"}
                                                            {assign var = "file_warn"  value = $smarty.const._APPROACHINGMESSAGEQUOTA|cat:$smarty.const._DELETEOLDMESSAGES}
                                                        {elseif ($T_TOTAL_FILES_PERCENTAGE > 0.9*$T_QUOTA_KILOBYTES)}
                                                            {assign var = "fileClassName" value = "severeWarning"}
                                                            {assign var = "file_warn"  value = $smarty.const._MESSAGEBOXSIZECRITICAL|cat:$smarty.const._ERASEMESSAGESNOTRECEIVENEW}
                                                        {else}
                                                            {assign var = "fileClassName" value = "noWarning"}
                                                            {assign var = "file_warn"  value = ""}
                                                        {/if}
                                                        <table>
                                                            <tr><td><span class = "{$msgClassName} boldFont" id = "messages_number"> {$T_TOTAL_MESSAGES}</span> {$smarty.const._OFTOTAL} <span class = "boldFont">{$T_QUOTA_NUM_OF_MESSAGES}</span>&nbsp;{$smarty.const._MESSAGES}   (<span class = "{$msgClassName}" id ="messages_number_percentage">{$T_TOTAL_MESSAGES_PERCENTAGE}</span>%)</td></tr>
                                                            <tr><td><span class = "{$fileClassName} boldFont" id = "messages_size">{$T_TOTAL_SIZE}    </span> {$smarty.const._OFTOTAL} <span class = "boldFont">{$T_QUOTA_KILOBYTES}      </span>{$smarty.const._KBYTESUSED} (<span class = "{$fileClassName}" id ="messages_size_percentage">{$T_TOTAL_FILES_PERCENTAGE}   </span>%)</td></tr>
                                                        </table>
                                                    {else}
                                                        <table cellpadding = "2" border = "0" width = "100%">
                                                            <tr><td><span class = "boldFont" id = "messages_number">{$T_TOTAL_MESSAGES}</span> {$smarty.const._MESSAGES}   </td></tr>
                                                            <tr><td><span class = "boldFont" id = "messages_size">{$T_TOTAL_SIZE}    </span> {$smarty.const._KBYTESUSED} </td></tr>
                                                        </table>
                                                    {/if}
                            {/capture}

                            {capture name = "t_usage_nav_code"}
                                                            <!--<a href = "javascript:void(0)" onclick = "popUp('manage_folders.php?action=statistics', 600, 400, 1)" class = "optionsLink">{$smarty.const._VIEWFOLDERSTATISTICS}</a>-->
                                                            {if $msg_warn != "" }
                                                                <br/><br/><span class = "{$msgClassName}">{$msg_warn}</span>
                                                            {/if}
                                                            {if $file_warn != "" }
                                                                <br/><span class = "{$fileClassName}">{$file_warn}</span>
                                                            {/if}
                            {/capture}
                            {eF_template_printInnerTable title = $smarty.const._SPACEUSAGE data = $smarty.capture.t_volume_code image = "32x32/battery.png" navigation = $smarty.capture.t_usage_nav_code options = $T_VOLUME_OPTIONS}


                            </td></tr>
{/capture}

{if !$T_VIEWINGMESSAGE}
{**moduleMessages: The personal messages list*}
    {capture name = "moduleMessages"}
                            <tr><td class = "moduleCell">

                            {capture name = "t_messages_code"}
                                    <table>
                                        <tr><td>
                                                <a href = "forum/new_message.php" target = "_self" title = "{$smarty.const._NEWMESSAGE}">
                                                <img src="images/16x16/add2.png" title="{$smarty.const._NEWMESSAGE}" alt="{$smarty.const._NEWMESSAGE}"/ border="0" valign="center"></a>
                                            </td><td>
                                                <a href = "forum/new_message.php" target = "_self" title = "{$smarty.const._NEWMESSAGE}">{$smarty.const._NEWMESSAGE}</a>
                                            </td></tr>
                                    </table>
                                            {assign var = "mailbox_img" value = "mailbox_full.png"}
<!--ajax:messagesTable-->
                                            <table class = "sortedTable" width = "100%" size = "{$T_MESSAGES_SIZE}" sortBy = "0" useAjax = "1" id = "messagesTable" rowsPerPage="20" limit="100" url="forum/messages_index.php?folder={$T_FOLDER}&p_message={$T_VIEWINGMESSAGE}&" style="white-space:nowrap;">
                                                <tr class = "defaultRowHeight">
                                                    <td class = "topTitle" name="priority" style = "width:7%;text-align:center;">{$smarty.const._FLAG}</td>
                                                    <td class = "topTitle" name="viewed" style = "width:7%;text-align:center;">{$smarty.const._STATUS}</td>
                                                    <td class = "topTitle" name="title" >{$smarty.const._SUBJECT}</td>
                                                    <td class = "topTitle" name="sender" style="width:11%">{$smarty.const._FROM}</td>
                                                    <td class = "topTitle" name="recipient" style="width:20%">{$smarty.const._TOFORUM}</td>
                                                    <td class = "topTitle" name="timestamp" style = "width:13%">{$smarty.const._DATE}</td>
                                                    <td class = "topTitle centerText noSort" style="width:10%">{$smarty.const._MARK}</td>
                                                 </tr>
                                        {section name = "messages_list" loop = $T_MESSAGES}
                                        {assign var = "number_messages" value = $smarty.section.messages_list.max}
                                            {if $T_MESSAGES[messages_list].viewed == 1}
                                                {assign var = "image" value = "mail.png"}
                                                {assign var = "viewedClass"  value = ""}
                                            {else}
                                                {assign var = "image" value = "mail2.png"}
                                                {assign var = "viewedClass"  value = "boldFont"}
                                            {/if}
                                            {if $T_MESSAGES[messages_list].attachments}
                                                {assign var = "image" value = "paperclip.png"}
                                            {/if}
                                                 <tr class = "{cycle values = "oddRowColor, evenRowColor"}" id="row_of_message_{$T_MESSAGES[messages_list].id}">

                                                    {* Set email priority *}
                                                    <td style = "width:5%;text-align:center;"><span style = "display:none">{$T_MESSAGES[messages_list].priority}</span> {*For sorting purposes*}
                                                        <a href = "javascript:void(0);" onclick = "flag_unflag(this, '{$T_MESSAGES[messages_list].id}')">
                                                {if !$T_MESSAGES[messages_list].priority}
                                                            <img src = "images/16x16/flag_green.png" alt = "{$smarty.const._NORMAL}" title = "{$smarty.const._SETHIGHPRIORITY}" border = "0"/>
                                                {else}
                                                            <img src = "images/16x16/flag_red.png" alt = "{$smarty.const._HIGH}" title = "{$smarty.const._SETNORMALPRIORITY}" border = "0"/>
                                                {/if}
                                                        </a>
                                                    </td>

                                                    {* Set email read/unread *}
                                                    <td style = "width:5%;text-align:center;"><span style = "display:none">{$T_MESSAGES[messages_list].viewed}</span> {*For sorting purposes*}

                                                        <a href = "javascript:void(0);" onclick = "mark_read_unread(this, '{$T_MESSAGES[messages_list].id}');">
                                                {if $T_MESSAGES[messages_list].viewed == 1}
                                                            <img src = "images/16x16/mail.png" alt = "{$smarty.const._MARKASUNREAD}" title = "{$smarty.const._MARKASUNREAD}" border = "0"/>
                                                {else}
                                                            <img src = "images/16x16/mail2.png" alt = "{$smarty.const._MARKASREAD}" title = "{$smarty.const._MARKASREAD}" border = "0"/>
                                                {/if}
                                                        </a>

                                                    </td>


                                                    <td>{if $T_MESSAGES[messages_list].attachments}<table><tr><td width="5%"><img src = "images/16x16/paperclip.png" alt = "{$smarty.const._HIGH}" title = "{$smarty.const._SETNORMALPRIORITY}" border = "0"/></td><td>{/if}
                                                        <a href = "{$smarty.server.PHP_SELF}?folder={$T_FOLDER}&p_message={$T_MESSAGES[messages_list].id}" class = "{$viewedClass}">{$T_MESSAGES[messages_list].title}</a>
                                                        {if $T_MESSAGES[messages_list].attachments}</td></tr></table>{/if}
                                                    </td>

                                                    <td>{$T_MESSAGES[messages_list].sender}</td>
                                                    <td>{$T_MESSAGES[messages_list].recipient}</td>
                                                    <td><span style = "display:none">{$T_MESSAGES[messages_list].timestamp}</span>#filter:timestamp_time-{$T_MESSAGES[messages_list].timestamp}#</td>
                                                    <td style = "text-align:center">
                                                        <table align="center">
                                                            <tr><td width="45%"><img id="{$T_MESSAGES[messages_list].id}" src = "images/16x16/folder_into.png" border = "0" alt="{$smarty.const._DRAGTOMOVEMAILTOFOLDER}" title="{$smarty.const._DRAGTOMOVEMAILTOFOLDER}"/></td>
                                                                <td width="45%"><a id="message_{$T_MESSAGES[messages_list].id}" href = "javascript:void(0);" onclick="if (confirm('{$smarty.const._AREYOUSURETODELETEMESSAGE}')) delMessage(this, '{$T_MESSAGES[messages_list].id}');" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                {if $smarty.const.MSIE_BROWSER == 1}
                                                    <img style="display:none" src="images/16x16/pens.png" onLoad="javascript:_current_folder = '{$T_FOLDER}';new Draggable('{$T_MESSAGES[messages_list].id}', {literal}{revert:true}{/literal});" />
                                                {else}
                                                    <script type="text/javascript">_current_folder = "{$T_FOLDER}";new Draggable('{$T_MESSAGES[messages_list].id}', {literal}{revert:true}{/literal})</script>
                                                {/if}
                                        {sectionelse}
                                                {assign var = "mailbox_img" value = "mailbox_empty.png"}
                                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                                    <td align = "center" colspan = "100%" class = "emptyCategory">{$smarty.const._NOMESSAGESINFOLDER}</td></tr>
                                        {/section}
                                            </table>
<!--/ajax:messagesTable-->

                            {/capture}

                                            <table border = "0" width = "100%" height = "100%" cellpadding = "2">
                                                <tr><td colspan = "2">
                                                    {eF_template_printInnerTable title = $smarty.const._PERSONALMESSAGES data = $smarty.capture.t_messages_code image = "32x32/"|cat:$mailbox_img}
                                                </td></tr>
                                                {if $number_messages >= 10}
                                                    <tr><td><a href = "#"><img border = "0" src = "images/24x24/navigate_up.png" title = "{$smarty.const._BACKTOTOP}" /></a></td>
                                                {/if}
                                                </tr>
                                            </table>
                            </td></tr>
    {/capture}
{/if}

{* Script for posting ajax requests regarding messages*}
<script>
{literal}
function showMessage(folder_id, p_message_id) {
            ajaxUrl[1] = 'forum/messages_index.php?ajax=messagesTable&limit=20&offset=0&sort=null&order=desc&folder='+folder_id+'&';
            eF_js_rebuildTable('1', 0, null, 'desc');
            /*
    var tables = sortedTables.size();
    var i;
    for (i = 0; i < tables; i++) {
        if (sortedTables[i].id == 'messagesTable') {
            ajaxUrl[i] = 'forum/messages_index.php?folder='+folder_id+'&p_message=' + p_messages_id+ '&';
            eF_js_rebuildTable(i, 0, null, 'desc',false);

            //eF_js_rebuildTable(i, 0, 'null', 'desc');
        }
    }
*/
    //eF_js_rebuildTable(1, 0, 'null', 'desc');
}

function flag_unflag(el, field) {
    Element.extend(el);
    if (el.down().src.match('red')) {
        url = 'forum/messages_index.php?unflag='+field;
        newSource = 'images/16x16/flag_green.png';
    } else {
        url = 'forum/messages_index.php?flag='+field;
        newSource = 'images/16x16/flag_red.png';
    }

    el.down().src = 'images/others/progress1.gif';

    new Ajax.Request(url, {
        method:'get',
        asynchronous:true,
        onSuccess: function (transport) {
            el.down().src = newSource;
            new Effect.Appear(el.down(), {queue:'end'});
            }
        });
}

function mark_read_unread(el, field) {
    Element.extend(el);
    if (el.down().src.match('mail.png')) {
        url = 'forum/messages_index.php?unread='+field;
        newSource = 'images/16x16/mail2.png';
    } else {
        url = 'forum/messages_index.php?read='+field;
        newSource = 'images/16x16/mail.png';
    }

    el.down().src = 'images/others/progress1.gif';

    new Ajax.Request(url, {
        method:'get',
        asynchronous:true,
        onSuccess: function (transport) {
            el.down().src = newSource;
            new Effect.Appear(el.down(), {queue:'end'});
            }
        });
}

//delete row
function delete_msg_row(el, id) {
    Element.extend(el);
    var msgTable = document.getElementById('messagesTable');

    var noOfRows = msgTable.rows.length;

    for (i = 1; i < noOfRows; i++) {
        if (msgTable.rows[i].id == ("row_of_"+el.id)) {

            msgTable.deleteRow(i);
            break;
        }
    }

    // If no job descriptions remain then show the "No jobs assigned" message
    if (msgTable.rows.length == 2) {
        var x = msgTable.insertRow(2);
        var newCell = x.insertCell(0);
        var newCellHTML = '{/literal}{$smarty.const._NOMESSAGESINFOLDER}{literal}';
        newCell.innerHTML= newCellHTML;
        newCell.setAttribute("id", "no_msgs_found");
        newCell.setAttribute("colspan", "8");
        newCell.setAttribute("class", "emptyCategory centerAlign");
    }
    return false;
}

function delMessage(el, id) {
    Element.extend(el);
    msg_deleted = 0;

    el.down().src = 'images/others/progress1.gif';

    new Ajax.Request('forum/messages_index.php?postAjaxRequest=1&delete_message='+id, {
        method:'get',
        asynchronous:true,
        onSuccess: function (transport) {
            folderMsgs = document.getElementById("folder_count_" + _current_folder);
            folderMsgs.innerHTML = parseInt(folderMsgs.innerHTML) -1;
            loadMessagesFolder(_current_folder);

                new Ajax.Request('forum/messages_index.php?postAjaxRequest=1&message_size_quota=1', {
                    method:'get',
                    asynchronous:false,
                    onSuccess: function (transport) {
                        allMsgs = document.getElementById("messages_number");
                        newMessagesNumber = parseInt(allMsgs.innerHTML) - 1;
                        allMsgs.innerHTML = newMessagesNumber;

                        {/literal}
                        {if $smarty.session.s_type != 'administrator'}
                        tempVal = 100*100*newMessagesNumber / {$T_QUOTA_NUM_OF_MESSAGES}; //the first 100* is used to truncate decimal digits
                        tempVal = parseInt(tempVal);
                        tempVal = tempVal / 100;
                        document.getElementById("messages_number_percentage").innerHTML = tempVal;
                        {/if}
                        {literal}

                        newMessagesSize = parseInt(transport.responseText);
                        document.getElementById("messages_size").innerHTML = newMessagesSize;

                        {/literal}
                        {if $smarty.session.s_type != 'administrator'}
                        tempVal = 100*100*newMessagesSize / {$T_QUOTA_KILOBYTES};
                        tempVal = parseInt(tempVal);
                        tempVal = tempVal / 100;
                        document.getElementById("messages_size_percentage").innerHTML = tempVal;
                        {/if}
                        {literal}

                        msg_deleted = 1;
                        }
                 });
            }
        });
}


{/literal}
</script>

{if $T_VIEWINGMESSAGE}
{**moduleMessageBody: A personal message content*}
    {capture name = "moduleMessageBody"}
                            <tr><td class = "moduleCell">

                            {capture name = "t_messagesbody_code"}
                                    <table>
                                        <tr><td>
                                                <a href = "forum/new_message.php" target = "_self" title = "{$smarty.const._NEWMESSAGE}">
                                                <img src="images/16x16/add2.png" title="{$smarty.const._NEWMESSAGE}" alt="{$smarty.const._NEWMESSAGE}"/ border="0" valign="center"></a>
                                            </td><td>
                                                <a href = "forum/new_message.php" target = "_self" title = "{$smarty.const._NEWMESSAGE}">{$smarty.const._NEWMESSAGE}</a>
                                            </td></tr>
                                    </table>
                                            <table class = "messagesTable" >
                                                <tr><td class = "topTitle" colspan = "3">{$smarty.const._PERSONALMESSAGE}</th></tr>
                                                <tr class = "oddRowColor">
                                                    <td width = "20%">{$smarty.const._FROM2} <span class = "boldFont">{$T_PERSONALMESSAGE.sender}</span><br/>{$smarty.const._TO2} <span class = "boldFont">{$T_PERSONALMESSAGE.recipient}</span><br/>{$smarty.const._DATE}: <span class = "boldFont">#filter:timestamp_time-{$T_PERSONALMESSAGE.timestamp}#</span></td>
                                                    <td>{$smarty.const._SUBJECT2} <span class = "boldFont">{$T_PERSONALMESSAGE.title}</span></td>
{*
                                            {if $T_ATTACHMENTS_FILENAMES}
                                                    <td width = "20%">{$smarty.const._ATTACHMENTS}<br/>
                                                    {section name = "attachments_loop" loop = $T_ATTACHMENTS_FILENAMES}
                                                        <span class = "counter">[{$smarty.section.attachments_loop.iteration}]</span> <a href = "view_file.php?file={$T_ATTACHMENTS_NAMES[attachments_loop]}&action=download&folder={$T_FOLDER}">{$T_ATTACHMENTS_FILENAMES[attachments_loop]}</a><br/>
                                                    {/section}
                                                    </td>
                                            {/if}
*}
                                            {if $T_ATTACHMENT}
                                                    <td width = "20%">{$smarty.const._ATTACHMENTS}<br/>
                                                        <a href = "view_file.php?file={$T_ATTACHMENT.id}&action=download">{$T_ATTACHMENT.name}</a>
                                                    </td>
                                            {/if}

                                                    </tr>
                                                <tr class = "evenRowColor">
                                                    <td colspan = "3"><br/>{$T_PERSONALMESSAGE.body}<br/><br/></td></tr>
                                                <tr class = "oddRowColor">
                                                    <td colspan = "3" style = "vertical-align:middle">
                                                        <a href = "forum/new_message.php?reply={$T_PERSONALMESSAGE.id}" target = "_self" title = "{$smarty.const._NEWMESSAGE}" style = "vertical-align:middle">
                                                        <img src = "images/16x16/mail_forward.png" title = "{$smarty.const._REPLY}" alt = "{$smarty.const._REPLY}"/ border = "0" valign = "center" style = "vertical-align:middle"></a>
                                                        <a href = "javascript:void(0)" onclick = "if (confirm('{$smarty.const._AREYOUSURETODELETEMESSAGE}')) document.getElementById('delete_message_submit').click();" title = "{$smarty.const._DELETE}" style = "vertical-align:middle">
                                                        <img src = "images/16x16/mail_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}"/ border = "0" valign = "center" style = "vertical-align:middle"></a>
                                                        <input type = "submit" name = "delete" id = "delete_message_submit" style = "display:none"/>
                                                        <a href = "javascript:void(0)" onclick = "document.getElementById('move_messages_submit').click();" title = "{$smarty.const._MOVETOFOLDER}" style = "vertical-align:middle">
                                                        <img src = "images/16x16/folder_into.png" title = "{$smarty.const._MOVETOFOLDER}" alt = "{$smarty.const._MOVETOFOLDER}"/ border = "0" valign = "center" style = "vertical-align:middle"></a>
                                                        <select name = "move_message" style = "vertical-align:middle">{$folders_options}</select>
                                                        <input type = "submit" name = "move_messages_submit"  id = "move_messages_submit" style = "display:none"/>
                                                        <input type = "hidden" name = "check[0]" value = "{$T_PERSONALMESSAGE.id}" />
                                                    </td></tr>
                                            </table>
                            {/capture}
                                            <form name = "personal_message_form" method = "post" action = "{$smarty.server.PHP_SELF}">
                                            <table border = "0" width = "100%" height = "100%" cellpadding = "2">
                                                <tr><td colspan = "2">
                                                    {eF_template_printInnerTable title = $smarty.const._VIEWMESSAGE data = $smarty.capture.t_messagesbody_code image = "32x32/mail.png"}
                                                </td></tr>
                                                <tr><td><a href = "{$smarty.server.PHP_SELF}?folder={$T_FOLDER}"><img border = "0" src = "images/24x24/navigate_left.png" title = "{$smarty.const._RETURNFOLDERSLIST}'" /></a></td></tr>

                                            </table>
                                            </form>
                                            {$T_MESSAGESBODY}

                            </td></tr>
    {/capture}

{/if}

{*----------------------------End of Part 2: Modules List------------------------------------------------*}



{*-----------------------------Part 3: Display table-------------------------------------------------*}


<table class = "mainTable">
    <tr>
        <td class = "centerTable">

            <table class = "centerTable" >
                <tr class = "topTitle">
                    <td colspan = "2" class = "topTitle">
                            <a class = "titleLink" href ="{$smarty.session.s_type}.php?ctg=control_panel">{$smarty.const._HOME}</a>&nbsp;&raquo;&nbsp;<a class = "titleLink"  href ="forum/messages_index.php" >{$smarty.const._PERSONALMESSAGES}</a>
                    </td>
               </tr>
{if $T_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_MESSAGE type=$T_MESSAGE_TYPE}</td>        {*Display Message, if any*}
                </tr>
{/if}
{if $smarty.get.message}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$smarty.get.message type=$smarty.get.message_type}</td>        {*Display Message passed through get, if any*}
                </tr>
{/if}
{if $T_SEARCH_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_SEARCH_MESSAGE}</td>        {*Display Search Message, if any*}
                </tr>
{/if}
                <tr>
{*LEFT MAIN COLUMN*}
                    <td class = "leftColumn" id = "leftColumn">
                        <table class = "leftColumnData">
                            {$smarty.capture.moduleFolders}
                        </table>
                    </td>
{*RIGHT MAIN COLUMN*}
                    <td class = "rightColumn" id = "rightColumn">
                        <table class = "rightColumnData">
                            {$smarty.capture.moduleVolume}
                        </table>
                    </td>
                </tr>
{*SINGLE MAIN COLUMN*}
                <tr height = "100%">
                    <td class = "singleColumn" id = "singleColumn" colspan = "2">
                        <table class = "singleColumnData">
                            {$smarty.capture.moduleMessages}
                            {$smarty.capture.moduleMessageBody}
                        </table>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
{if $T_SHOWFOOTER}
    {include file = "includes/footer.tpl"}
{/if}
</table>

{*-----------------------------End of Part 3: Display table-------------------------------------------------*}


{*-----------------------------Part 4: Finalization data etc-------------------------------------------------*}
{include file = "includes/closing.tpl"}

</body>
</html>
