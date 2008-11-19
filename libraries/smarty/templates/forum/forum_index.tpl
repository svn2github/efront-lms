{include file = "includes/header.tpl"}
{assign var = "current_forum" value = $smarty.get.forum}
<script>
<!--
if (top.sideframe) 

  {*  {if ($T_FORUMS.$current_forum.lessons_ID == 0 && $T_USER != 'administrator' && isset($smarty.get.forum)) || empty($smarty.get)}
		top.sideframe.changeTDcolor('forum_general');
	{elseif !isset($smarty.get.topic)}
		top.sideframe.changeTDcolor('forum_a');   
	{/if} *}
	
	{if $T_USER != 'administrator' && empty($smarty.get)}
		top.sideframe.changeTDcolor('forum_general');
		{assign var = "highlight" value = 'general'}
	{elseif $T_USER == 'administrator' || $T_FORUMS.$current_forum.lessons_ID == $smarty.session.s_lessons_ID || $T_FIRSTNODE == $smarty.session.s_lessons_ID}
		top.sideframe.changeTDcolor('forum_a');
		{assign var = "highlight" value = 'lesson'}
	{else}
		if (top.sideframe.$('forum_a').className == 'selectedTopTitle')
			{assign var = "highlight" value = 'lesson'}
		else
			{assign var = "highlight" value = 'general'}
	{/if}

-->
</script>

{if $T_USER == 'administrator'}
	{assign var = "title" value = '<a class = "titleLink" href ="administrator.php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
	{assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'">'|cat:$smarty.const._FORUMS}
{else}
	{if $T_USER == 'professor'}
		 {assign var = "title" value = '<a class = "titleLink" href ="professor.php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
	{else}
		{assign var = "title" value = '<a class = "titleLink" href ="student.php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
	{/if}
	{if $highlight == 'lesson'}
		{assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:$smarty.const._FORUMOFLESSON} 
	{else}
		{assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'">'|cat:$smarty.const._FORUMS}	
	{/if}
{/if}

{*
{if $T_USER == 'professor'}
    {assign var = "title" value = '<a class = "titleLink" href ="professor.php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{elseif $T_USER == 'administrator'}
    {assign var = "title" value = '<a class = "titleLink" href ="administrator.php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{else}
    {assign var = "title" value = '<a class = "titleLink" href ="student.php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{/if}

		{if (($T_FORUMS.$current_forum.lessons_ID == 0 && !isset($smarty.get.topic))  || ($T_FIRSTNODE == 0 && isset($smarty.get.topic)) || $T_USER == 'administrator')}
			{assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'">'|cat:$smarty.const._FORUMS}	
		{else}
			{assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:$smarty.const._FORUMOFLESSON} 
		{/if} 
*}
	{foreach name = 'title_loop' item = "item" key = "key" from = $T_FORUM_PARENTS}
    {if $smarty.foreach.title_loop.first && $highligt == 'lesson' && $T_USER != 'administrator'}
		{assign var = "title" value = $title|cat:'&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?forum='|cat:$key|cat:'">'|cat:$item|cat:'</a>'}
	{else}
		{assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?forum='|cat:$key|cat:'">'|cat:$item|cat:'</a>'}
	{/if}
	{/foreach}


	{if $smarty.get.topic}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?topic='|cat:$smarty.get.topic|cat:'">'|cat:$T_TOPIC.title|cat:'</a>'}
    {capture name = "moduleTopics"}
                            <tr><td class = "moduleCell">
                                {capture name = 't_topic_code'}
                                {if !$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change'}
                                            <table style = "margin-bottom:5px">
                                                <tr>
                                                    <td><img src = "images/16x16/add2.png" alt = "{$smarty.const._NEWMESSAGE}" title = "{$smarty.const._NEWMESSAGE}"/></td>
                                    {if $T_TOPIC.status == 'locked'}
                                                    <td><a href = "javascript:void(0)" onclick = "alert('{$smarty.const._NONEWMESSAGELOCKED}')" class = "inactiveLink" >{$smarty.const._NEWMESSAGE}</a>&nbsp;</td>
                                    {else}
                                                    <td><a href = "forum/forum_add.php?add_message=1&topic_id={$smarty.get.topic}" onclick = "eF_js_showDivPopup('{$smarty.const._NEWMESSAGE}', 1)" target = "POPUP_FRAME" >{$smarty.const._NEWMESSAGE}</a>&nbsp;</td>
                                    {/if}
                                                </tr>
                                            </table>                              
                                 {/if}              
                                            <table class = "forumTable">
                                    {section name = 'messages_list' loop = $T_POSTS}
                                        {assign var = "message_user" value = $T_POSTS[messages_list].users_LOGIN}
                                                <tr class = "{cycle values = "oddRowColorNoHover, evenRowColorNoHover"}">
                                                    <td>
                                                        <table style = "width:100%">
                                                            <tr><td class = "blockHeader">{$T_POSTS[messages_list].title}</td></tr>
                                                            <tr><td class="infoCellSmall">{$smarty.const._POSTEDBY}<span class="boldFont"> #filter:user_loginNoIcon-{$T_POSTS[messages_list].users_LOGIN}# </span>{$smarty.const._ON} #filter:timestamp_time-{$T_POSTS[messages_list].timestamp}# {if $T_POSTS[messages_list].replyto}{$smarty.const._INREPLYTO}: <a href = "{$smarty.server.PHP_SELF}?topic={$smarty.get.topic}&view_message={$T_POSTS[messages_list].replyto}#{$T_POSTS[messages_list].replyto}">{$T_POSTS[messages_list].reply_title}</a>{/if}</td></tr>
                                                            <tr><td>{$T_POSTS[messages_list].body}</td></tr>
                                    {if !$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change'}                                    
                                                            <tr><td style = "border-top:1px dotted white">
	                                    {if $T_TOPIC.status != 'locked'}
                                                        <a href = "forum/forum_add.php?add_message=1&topic_id={$smarty.get.topic}&replyto={$T_POSTS[messages_list].id}" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._REPLY}', new Array('650px', '450px'))"><img border = "0" src = "images/16x16/document_plain.png" title = "{$smarty.const._REPLY}" alt = "{$smarty.const._REPLY}"/></a>
                                                        <a href = "forum/forum_add.php?add_message=1&topic_id={$smarty.get.topic}&replyto={$T_POSTS[messages_list].id}&quote=1"  target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._REPLYWITHQUOTE}', new Array('650px', '450px'))"><img border = "0" src = "images/16x16/document_plain_new.png" title = "{$smarty.const._REPLYWITHQUOTE}" alt = "{$smarty.const._REPLYWITHQUOTE}"/></a>
    	                                {/if}
        
        	                            {if $smarty.session.s_type == 'administrator' || $smarty.session.s_login == $T_POSTS[messages_list].users_LOGIN}
                                                        <a href = "forum/forum_add.php?edit_message={$T_POSTS[messages_list].id}&topic_id={$smarty.get.topic}" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', new Array('650px', '450px'))" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" /></a>
                                                        <a href = "forum/forum_add.php?delete_message={$T_POSTS[messages_list].id}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWNATTODELETEMESSAGE} {$T_POSTS[messages_list].title}?')" target = "POPUP_FRAME"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" /></a>
            	                        {/if}
                                                            </td></tr>
                                    {/if}
                                                        </table>
                                                    </td>
                                                    <td style = "width:10%;border-left:1px dotted white;padding:0px 10px 0px 10px">
                                                        <table style = "width:100%">
                                                            <tr><td style = "text-align:center">
                                                            	<img src = {if $T_POSTS[messages_list].avatar}"view_file.php?file={$T_POSTS[messages_list].avatar}"{else}"images/avatars/system_avatars/unknown_small.png"{/if} title = "{$T_POSTS[messages_list].users_LOGIN}" alt = "{$T_POSTS[messages_list].users_LOGIN}"/>
                                                            </td></tr>
                                                            <tr><td style = "white-space:nowrap">
                                                                #filter:user_loginNoIcon-{$T_POSTS[messages_list].users_LOGIN}#<br/>
                                                                {$smarty.const._POSITION}: {$T_POSTS[messages_list].user_type}<br/>
                                                                {$smarty.const._JOINED}: #filter:timestamp-{$T_POSTS[messages_list].timestamp}#<br/>
                                                                {$smarty.const._POSTS}: {$T_USER_POSTS.$message_user}<br/>
                                                                </td></tr>
                                                        </table>
                                                    </td>
                                                    </tr>
                                    {sectionelse}
                                                    <tr><td align = "center" colspan = "8" class = "{cycle values = "oddRowColor, evenRowColor"}">{$smarty.const._NOMESSAGESFOUNDINTHISTOPIC}</td></tr>
                                    {/section}
                                                </table>
                                {/capture}
                                {eF_template_printInnerTable title = $smarty.const._TOPICS data = $smarty.capture.t_topic_code image = '/32x32/messages.png'}
    {/capture}
{elseif $smarty.get.poll}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?poll='|cat:$smarty.get.poll|cat:'">'|cat:$T_POLL.title|cat:'</a>'}
    {capture name = "modulePoll"}
                        <tr><td class = "moduleCell">     
                            {capture name = 't_poll_code'}
                                {if $T_ACTION == 'view' || !$T_POLL.isopen}
                                    <table style = "width:100%">
                                        <tr><td class = "blockHeader" colspan = "100%">{$T_POLL.title}</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td style = "text-align:left" colspan = "100%"><b>{$T_POLL.question}</b></td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        {section name = 'votes_list' loop = $T_POLL_VOTES}
                                            <tr><td style = "text-align:left" width="20%">{$T_POLL_VOTES[votes_list].text}</td>
                                                <td style="text-align=left" width="30%">
                                                    <img src="images/others/bar.jpg" width="{$T_POLL_VOTES[votes_list].width}" height="15"/>
                                                </td>
                                                <td style="text-align=left">{$T_POLL_VOTES[votes_list].perc*100}% </td>
                                            </tr>       
                                        {/section}
                                    </table>
                                {else}
                                    {$T_POLL_FORM.javascript}
                                    <form {$T_POLL_FORM.attributes}>
                                    {$T_POLL_FORM.hidden}
                                    <table class = "formElements" style = "width:100%">
                                        <tr><td class = "blockHeader" >{$T_POLL.title}</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td style = "font-weight:bold">{$T_POLL.question}</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td class = "elementCell">{$T_POLL_FORM.options.html}</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td class = "submitCell">{$T_POLL_FORM.submit_poll.html}</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td>{$smarty.const._TOTALVOTES}: {$T_POLL_TOTALVOTES}</td></tr>
                                        <tr><td><a href="forum/forum_index.php?poll={$smarty.get.poll}&action=view">{$smarty.const._VIEWRESULTS}</a></td></tr>
                                    </table>
                                    </form>
                                {/if}
                            {/capture}
                            {eF_template_printInnerTable title = $smarty.const._POLL data = $smarty.capture.t_poll_code image = '/32x32/column-chart.png'}
                        </td></tr>
    {/capture}
{else}
    {capture name = "moduleForum"}
                            <tr><td class = "moduleCell">
        {capture name = 't_forums_code'}                                                        
                {assign var = "current_forum" value = $smarty.get.forum}
                    
                                    <table>
                                        <tr><td class = "blockHeader">{$T_FORUMS.$current_forum.title}</td></tr>
                                        {if $T_FORUMS.$current_forum.comments}<tr><td class = "infoCell" style = "padding-bottom:5px">{$T_FORUMS.$current_forum.comments}</td></tr>{/if}
                                    </table>
		{if !$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change'}                                    
                                    <table>
                                        <tr>
            {if $smarty.get.forum}
				
				{if $T_FORUMS.$current_forum.status != 'public'}
                                            <td><img src = "images/16x16/add2.png" alt = "{$smarty.const._NEWTOPIC}" title = "{$smarty.const._NEWTOPIC}"/></td>
                                            <td><a href = "javascript:void(0)" onclick = "alert('{$smarty.const._NONEWMESSAGELOCKED}')" class = "inactiveLink" >{$smarty.const._NEWTOPIC}</a>&nbsp;</td>
                {else}
											<td><img src = "images/16x16/add2.png" alt = "{$smarty.const._NEWTOPIC}" title = "{$smarty.const._NEWTOPIC}"/></td>
                                            <td><a href = "forum/forum_add.php?add_topic=1&forum_id={$T_PARENT_FORUM}" onclick = "eF_js_showDivPopup('{$smarty.const._NEWTOPIC}', 1)" target = "POPUP_FRAME">{$smarty.const._NEWTOPIC}</a>&nbsp;</td>
          
				{/if}
				{if !isset($T_FORUM_CONFIG.polls) || $T_FORUM_CONFIG.polls}
								{if $T_FORUMS.$current_forum.status != 'public'}
											<td style = "border-left:1px solid black">&nbsp;<img src = "images/16x16/add2.png" alt = "{$smarty.const._NEWPOLL}" title = "{$smarty.const._NEWPOLL}"/></td>
                                            <td><a href = "javascript:void(0)" onclick = "alert('{$smarty.const._NONEWPOLLLOCKED}')" class = "inactiveLink" >{$smarty.const._NEWPOLL}</a>&nbsp;</td>
								{else}
											<td style = "border-left:1px solid black">&nbsp;<img src = "images/16x16/add2.png" alt = "{$smarty.const._NEWPOLL}" title = "{$smarty.const._NEWPOLL}"/></td>
                                            <td><a href = "forum/forum_add.php?add_poll=1&forum_id={$T_PARENT_FORUM}" onclick = "eF_js_showDivPopup('{$smarty.const._NEWPOLL}', 1)" target = "POPUP_FRAME">{$smarty.const._NEWPOLL}</a>&nbsp;</td>
								{/if}
				
				{/if}
            {/if}
            {if $smarty.session.s_type == 'administrator'}
                                            <td {if $smarty.get.forum}style = "border-left:1px solid black"{/if}>&nbsp;<img src = "images/16x16/edit.png" title = "{$smarty.const._FORUMCONFIGURATIONPANEL}" alt = "{$smarty.const._FORUMCONFIGURATIONPANEL}"/ ></td>
                                            <td><a href = "forum/forum_admin.php" onclick = "eF_js_showDivPopup('{$smarty.const._FORUMCONFIGURATIONPANEL}', 1)" target = "POPUP_FRAME">{$smarty.const._FORUMCONFIGURATIONPANEL}</a>&nbsp;</td>
            {/if}
                                        </tr>
                                    </table>
		{/if}
            
            {if $smarty.get.forum}
                {capture name = 'subforums_list_code'}
                    {foreach name = "subforums_list" item = "subforum" key = "key2" from = $T_FORUMS}
                        {if $subforum.parent_id == $T_FORUMS.$current_forum.id && $subforum.status != 'invisible'}
                            {assign var = "has_subforums" value = true}
							
                                        <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
                                            <td style = "width:1%;padding:5px 15px 5px 5px">
                                            	<a href = "{$smarty.server.PHP_SELF}?forum={$subforum.id}"><img src = "images/32x32/pen_blue.png" alt = "{$smarty.const._FORUM}" title = "{$smarty.const._FORUM}" border = "0"/></a>
                                            </td>
                                            <td style = "vertical-align:middle;" class = "infoCell">
                                               <a href = "{$smarty.server.PHP_SELF}?forum={$subforum.id}" >{$subforum.title}</a><br/>
                                               <span style = "white-space:normal">{$subforum.comments}</span>
                                            </td>
											{if $subforum.polls !=0} 
												<td>{$subforum.topics} {$smarty.const._TOPICS}, {$subforum.messages} {$smarty.const._MESSAGES}, {$subforum.polls} {$smarty.const._POLLS}</td>
                                            {else}
												<td>{$subforum.topics} {$smarty.const._TOPICS}, {$subforum.messages} {$smarty.const._MESSAGES}</td>
											{/if}
											<td>
                            {if $subforum.last_post}
                                            #filter:timestamp_time-{$subforum.last_post.timestamp}#
                                            <br/> {$smarty.const._BY} #filter:user_loginNoIcon-{$subforum.last_post.users_LOGIN}# 
                                            <a href = "forum/forum_index.php?topic={$subforum.last_post.f_topics_ID}&view_message={$subforum.last_post.id}">&raquo;</a></td>
                            {else}
                                            {$smarty.const._NOFORUMPOSTSYET}
                            {/if}
                                            </td>
											<td style = "text-align:center">
                            {if $subforum.status == 'locked'}
                                                <img src = "images/16x16/lock.png" alt = "{$smarty.const._LOCKED}" title = "{$smarty.const._LOCKED}"/><span style = "display:none">{$smarty.const._LOCKED}</span>  {*span is used for sorting*}
                            {elseif $subforum.status == 'invisible'}
                                                <img src = "images/16x16/ghost.png" alt = "{$smarty.const._INVISIBLE}" title = "{$smarty.const._INVISIBLE}"/><span style = "display:none">{$smarty.const._INVISIBLE}</span>
                            {else}
                                                <img src = "images/16x16/lock_open.png" alt = "{$smarty.const._PUBLIC}" title = "{$smarty.const._PUBLIC}"/><span style = "display:none">{$smarty.const._PUBLIC}</span>
                            {/if}
                                            </td>											
					{if (!$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change') && $smarty.session.s_type != 'student'}
                          
						  <td style = "text-align:center">
                        {if $smarty.session.s_type == 'administrator' || ($smarty.session.s_login == $subforum.users_LOGIN && $subforum.status != 'locked')}
                                                <a href = "forum/forum_add.php?edit_forum={$subforum.id}&forum_id={$T_PARENT_FORUM}"   onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', new Array('600px', '100px'))" class = "editLink"   target = "POPUP_FRAME"><img border = "0" src = "images/16x16/edit.png"   title = "{$smarty.const._EDIT}"   alt = "{$smarty.const._EDIT}"   /></a>
                                                <a href = "forum/forum_add.php?delete_forum={$subforum.id}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"          class = "deleteLink" target = "POPUP_FRAME"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                        {/if}
                                            </td>
                    {/if}
                                        </tr>
                        {/if}
                    {foreachelse}
                                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOSUBFORUMSFOUND}</td></tr>
                    {/foreach}
                {/capture}
                {if $has_subforums}
                                    <table class = "forumTable">
                                        <tr>
                                            <td colspan = "2" class = "topTitle smallHeader" style = "width:30%;text-align:left;vertical-align:middle">{$smarty.const._SUBFORUMS}</td>
                                            <td class = "topTitle">{$smarty.const._ACTIVITY}</td>
                                            <td class = "topTitle">{$smarty.const._LASTPOST}</td>
											<td class = "topTitle" style = "text-align:center">{$smarty.const._STATUS}</td>
                                        {if $smarty.session.s_type != 'student'}   
											<td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
										{/if}	
                                        </tr>
                                        {$smarty.capture.subforums_list_code}
                                    </table>
                                    <br/>
                {/if}
                {if $T_FORUM_POLLS}
                                    {*Polls list*}
                                    <table class = "forumTable">
                                        <tr>
                                            <td colspan = "2" class = "topTitle smallHeader" style = "width:30%;text-align:left;vertical-align:middle">{$smarty.const._POLLS}</td>
                                            <td class = "topTitle">{$smarty.const._AUTHOR}</td>
                                            <td class = "topTitle">{$smarty.const._ISVALID}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._VOTES}</td>
                                        {if !$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change'}
                                        	<td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                                        {/if}
                                        </tr>
                    {foreach name = "polls_list" item = "poll" key = "key2" from = $T_FORUM_POLLS}
                                        <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
                                            <td style = "width:1%;padding:5px 15px 5px 5px"><img src = "images/32x32/column-chart.png" alt = "{$smarty.const._POLL}" title = "{$smarty.const._POLL}"/></td>
                                            <td style = "vertical-align:middle;" class = "infoCell">
                                               <a href = "{$smarty.server.PHP_SELF}?poll={$poll.id}" class = "smallHeader" style = "white-space:normal">{$poll.title}</a><br/>
                                               <span style = "white-space:normal">{$poll.question|eF_truncate:50}</span>
                                            </td>
                                            <td>#filter:user_loginNoIcon-{$poll.users_LOGIN}#</td>
                                            <td>{$smarty.const._FROM} <b>#filter:timestamp_time-{$poll.timestamp_start}#</b> <br/>{$smarty.const._TO} <b>#filter:timestamp_time-{$poll.timestamp_end}#</b></td>
                                            <td style = "text-align:center">{$poll.votes}</td>
					{if !$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change'}
                                            <td style = "text-align:center">
                        {if $smarty.session.s_type == 'administrator' || $smarty.session.s_login == $poll.users_LOGIN}
                                                <a href = "forum/forum_add.php?edit_poll={$poll.id}"   onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 1)" class = "editLink"   target = "POPUP_FRAME"><img border = "0" src = "images/16x16/edit.png"   title = "{$smarty.const._EDIT}"   alt = "{$smarty.const._EDIT}"   /></a>
                                                <a href = "forum/forum_add.php?delete_poll={$poll.id}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"          class = "deleteLink" target = "POPUP_FRAME"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                        {/if}
                                            </td>
                    {/if}
                                        </tr>
                    {foreachelse}
                                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOPOLLSSFOUNDINTHISFORUM}</td></tr>
                    {/foreach}
                                    </table>
                                    <br/>
                {/if}
                {if $T_FORUM_TOPICS || (!$T_HAS_SUBFORUMS && !$T_FORUM_POLLS)}                      {*Do not display topics block, unless nothing else exists*}
                                    <table class = "forumTable">
                                        <tr>
                                            <td colspan = "2" class = "topTitle smallHeader" style = "width:30%;text-align:left;vertical-align:middle">{$smarty.const._TOPICS}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._MESSAGES}</td>
                                            <td class = "topTitle">{$smarty.const._LASTPOST}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                                        {if !$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change'}
                                            <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                                        {/if}
                                        </tr>
					{assign var = "novisible" value = 0}
                    {foreach name = "topics_list" item = "topic" key = "key2" from = $T_FORUM_TOPICS}
                        {if $smarty.session.s_type == 'administrator' || $topic.status != 'invisible'}
                                        <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
                                            <td style = "width:1%;padding:5px 15px 5px 5px"><img src = "images/32x32/message.png" alt = "{$smarty.const._TOPIC}" title = "{$smarty.const._TOPIC}"/></td>
                                            <td style = "vertical-align:middle;" class = "infoCell">
                                               <a href = "{$smarty.server.PHP_SELF}?topic={$topic.id}" class = "smallHeader" style = "white-space:normal">{$topic.title}</a><br/>
                                               <span style = "white-space:normal">{$topic.first_message|eF_truncate:50}</span>
                                            </td>
                                            <td style = "text-align:center">{$topic.messages}</td>
                                            <td>{if $topic.last_post}
                                                #filter:timestamp_time-{$topic.last_post.timestamp}#
                                                <br/> {$smarty.const._BY} #filter:user_loginNoIcon-{$topic.last_post.users_LOGIN}# 
                                                <a href = "forum/forum_index.php?topic={$topic.last_post.id}&view_message={$topic.last_post.id}">&raquo;</a></td>
                                                {/if}
                                            </td>
                                            <td style = "text-align:center">
                            {if $topic.status == 'locked'}
                                                <img src = "images/16x16/lock.png" alt = "{$smarty.const._LOCKED}" title = "{$smarty.const._LOCKED}"/><span style = "display:none">{$smarty.const._LOCKED}</span>  {*span is used for sorting*}
                            {elseif $topic.status == 'invisible'}
                                                <img src = "images/16x16/ghost.png" alt = "{$smarty.const._INVISIBLE}" title = "{$smarty.const._INVISIBLE}"/><span style = "display:none">{$smarty.const._INVISIBLE}</span>
                            {else}
                                                <img src = "images/16x16/lock_open.png" alt = "{$smarty.const._PUBLIC}" title = "{$smarty.const._PUBLIC}"/><span style = "display:none">{$smarty.const._PUBLIC}</span>
                            {/if}
                                            </td>
                        {if !$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change'}
                                            <td style = "text-align:center">
                            {if $smarty.session.s_type == 'administrator' || ($smarty.session.s_login == $topic.users_LOGIN && $T_FORUMS.$current_forum.status != 'locked')}
                                                <a href = "forum/forum_add.php?edit_topic={$topic.id}"   onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', new Array('600px', '100px'))" class = "editLink"   target = "POPUP_FRAME"><img border = "0" src = "images/16x16/edit.png"   title = "{$smarty.const._EDIT}"   alt = "{$smarty.const._EDIT}"   /></a>
                                                <a href = "forum/forum_add.php?delete_topic={$topic.id}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"          class = "deleteLink" target = "POPUP_FRAME"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                            {/if}
                                            </td>
                        {/if}
                                        </tr>
                        {else} 
								{assign var = "novisible" value = $novisible+1}
						{/if}
                    {foreachelse}
                                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOTOPICSFOUNDINTHISFORUM}</td></tr>
                    {/foreach}
						{if $novisible == $smarty.foreach.topics_list.total && $smarty.foreach.topics_list.total != 0}
								<tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOVISIBLETOPICSFOUNDINTHISFORUM}</td></tr>
                
						{/if}
                                    </table>
                {/if}
                
            {else}
                                    <table class = "forumTable">
                                        <tr>
                                            <td colspan = "2" class = "topTitle smallHeader" style = "width:30%;text-align:left;vertical-align:middle">{$smarty.const._FORUMS}</td>
                                            <td class = "topTitle">{$smarty.const._ACTIVITY}</td>
                                            <td class = "topTitle">{$smarty.const._LASTPOST}</td>
											<td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                            {if $smarty.session.s_type == 'administrator' && (!$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change')}
                                            <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                            {/if}
                                        </tr>
                    {foreach name = "subforums_list" item = "subforum" key = "key2" from = $T_FORUMS}
                        {if $subforum.parent_id == 0 && ($subforum.status != 'invisible' || $smarty.session.s_type == 'administrator')}
                                        <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
                                            <td style = "width:1%;padding:5px 15px 5px 5px">
                                            	<a href = "{$smarty.server.PHP_SELF}?forum={$subforum.id}"><img src = "images/32x32/pen_blue.png" alt = "{$smarty.const._FORUM}" title = "{$smarty.const._FORUM}" border = "0"/></a></td>
                                            <td style = "vertical-align:middle;" class = "infoCell">
                                               <a href = "{$smarty.server.PHP_SELF}?forum={$subforum.id}">{$subforum.title}</a><br/>
                                               <span style = "white-space:normal">{$subforum.comments}</span>
                                            </td>
											{if $subforum.polls !=0}
												<td>{$subforum.topics} {$smarty.const._TOPICS}, {$subforum.messages} {$smarty.const._MESSAGES},{$subforum.polls} {$smarty.const._POLLS}</td>
                                            {else}
												<td>{$subforum.topics} {$smarty.const._TOPICS}, {$subforum.messages} {$smarty.const._MESSAGES}</td>
											{/if}
											<td>
                            {if $subforum.last_post}
                                            #filter:timestamp_time-{$subforum.last_post.timestamp}#
                                            <br/> {$smarty.const._BY} #filter:user_loginNoIcon-{$subforum.last_post.users_LOGIN}# 
                                            <a href = "forum/forum_index.php?topic={$subforum.last_post.f_topics_ID}&view_message={$subforum.last_post.id}">&raquo;</a></td>
                            {else}
                                            {$smarty.const._NOFORUMPOSTSYET}
                            {/if}
                                            </td>
											<td style = "text-align:center">
                            {if $subforum.status == 'locked'}
                                                <img src = "images/16x16/lock.png" alt = "{$smarty.const._LOCKED}" title = "{$smarty.const._LOCKED}"/><span style = "display:none">{$smarty.const._LOCKED}</span>  {*span is used for sorting*}
                            {elseif $subforum.status == 'invisible'}
                                                <img src = "images/16x16/ghost.png" alt = "{$smarty.const._INVISIBLE}" title = "{$smarty.const._INVISIBLE}"/><span style = "display:none">{$smarty.const._INVISIBLE}</span>
                            {else}
                                                <img src = "images/16x16/lock_open.png" alt = "{$smarty.const._PUBLIC}" title = "{$smarty.const._PUBLIC}"/><span style = "display:none">{$smarty.const._PUBLIC}</span>
                            {/if}
                                            </td>
                        {if !$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change'}
                            {if $smarty.session.s_type == 'administrator'}
                                            <td style = "text-align:center">
                                                <a href = "forum/forum_add.php?edit_forum={$subforum.id}&forum_id={$T_PARENT_FORUM}"   onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', new Array('600px', '100px'))" class = "editLink"   target = "POPUP_FRAME"><img border = "0" src = "images/16x16/edit.png"   title = "{$smarty.const._EDIT}"   alt = "{$smarty.const._EDIT}"   /></a>
                                                <a href = "forum/forum_add.php?delete_forum={$subforum.id}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"          class = "deleteLink" target = "POPUP_FRAME"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                            </td>
                            {/if}
                        {/if}
                                        </tr>
                        {/if}
                    {foreachelse}
                                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOSUBFORUMSFOUND}</td></tr>
                    {/foreach}
                                    </table>
                                    <br/>                
            {/if}
        {/capture}

        {eF_template_printInnerTable title = $smarty.const._FORUMS data = $smarty.capture.t_forums_code image = '/32x32/messages.png' options = $T_FORUM_OPTIONS}
    {/capture}
{/if}

{*----------------------------End of Part 2: Modules List------------------------------------------------*}



{*-----------------------------Part 3: Display table-------------------------------------------------*}

<table class = "mainTable">
    <tr>
        <td style = "vertical-align: top;">
            <table class = "centerTable">
                <tr class = "topTitle">
                    <td colspan = "2" class = "topTitle">
                        {$title}
                    </td>
               </tr>
{if $T_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_MESSAGE type=$T_MESSAGE_TYPE}</td>        {*Display Message, if any*}
                </tr>
{/if}
{if $T_SEARCH_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_SEARCH_MESSAGE}</td>        {*Display Search Message, if any*}
                </tr>                                        
{/if}                                    
                <tr>
                    <td class = "singleColumn" id = "singleColumn">
                        <table class = "singleColumnData">
                            {$smarty.capture.moduleForum}
                            {$smarty.capture.moduleTopics}
                            {$smarty.capture.modulePoll}
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
