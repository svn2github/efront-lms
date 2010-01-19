{*moduleCleanup: Clean up old data*}
{capture name = "moduleCleanup"}
	<tr><td class = "moduleCell">
                    
	{capture name = 't_cleanup_code'}
	    <table>
	        <tr><td>{$smarty.const._ORPHANUSERFOLDERSCHECK}:&nbsp;</td>
	            <td>
	            {if $T_ORPHAN_USER_FOLDERS}
	                <img src = "images/16x16/warning.png" title = "{$smarty.const._PROBLEM}" alt = "{$smarty.const._PROBLEM}"/>&nbsp;
	                <img src = "images/16x16/help.png"   title = "{$smarty.const._INFO}"    alt = "{$smarty.const._INFO}"    onclick = "eF_js_showDivPopup('{$smarty.const._ORPHANUSERFOLDERSCHECK}', 0, 'orphan_user_folders')"/>&nbsp;
	                <img src = "images/16x16/error_delete.png"  title = "{$smarty.const._CLEANUP}" alt = "{$smarty.const._CLEANUP}" onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGFOLDERS}:\n\n{$T_ORPHAN_USER_FOLDERS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&cleanup=orphan_user_folders'"/>
	            {else}
	                <img src = "images/16x16/success.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
	            {/if}
	            </td></tr>
	        <tr><td>{$smarty.const._USERSWITHOUTFOLDERSCHECK}:&nbsp;</td>
	            <td>
	            {if $T_ORPHAN_USERS}
	                <img src = "images/16x16/warning.png"    title = "{$smarty.const._PROBLEM}"      alt = "{$smarty.const._PROBLEM}"/>&nbsp;
	                <img src = "images/16x16/help.png"      title = "{$smarty.const._INFO}"         alt = "{$smarty.const._INFO}"         onclick = "eF_js_showDivPopup('{$smarty.const._USERSWITHOUTFOLDERSCHECK}', 0, 'users_without_folders')"/>&nbsp;
	                <img src = "images/16x16/error_delete.png"     title = "{$smarty.const._CLEANUP}"      alt = "{$smarty.const._CLEANUP}"      onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGUSERS}:\n\n{$T_ORPHAN_USERS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&cleanup=users_without_folders'"/>&nbsp;
	                <img src = "images/16x16/folders.png" title = "{$smarty.const._CREATEFOLDER}" alt = "{$smarty.const._CREATEFOLDER}" onclick = "if (confirm('{$smarty.const._CREATEFOLLOWINGUSERFOLDERS}:\n\n{$T_ORPHAN_USERS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&create=user_folders'"/>
	            {else}
	                <img src = "images/16x16/success.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
	            {/if}
	            </td></tr>
	        <tr><td>{$smarty.const._ORPHANLESSONFOLDERSCHECK}:&nbsp;</td>
	            <td>
	            {if $T_ORPHAN_LESSON_FOLDERS}
	                <img src = "images/16x16/warning.png" title = "{$smarty.const._PROBLEM}" alt = "{$smarty.const._PROBLEM}"/>&nbsp;
	                <img src = "images/16x16/help.png"   title = "{$smarty.const._INFO}"    alt = "{$smarty.const._INFO}"    onclick = "eF_js_showDivPopup('{$smarty.const._ORPHANLESSONFOLDERSCHECK}', 0, 'orphan_lesson_folders')"/>&nbsp;
	                <img src = "images/16x16/error_delete.png"  title = "{$smarty.const._CLEANUP}" alt = "{$smarty.const._CLEANUP}" onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGFOLDERS}:{$T_ORPHAN_LESSON_FOLDERS|@eF_truncate:30}{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&cleanup=orphan_lesson_folders'"/>
	            {else}
	                <img src = "images/16x16/success.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
	            {/if}
	        </td></tr>
	        <tr><td>{$smarty.const._LESSONSWITHOUTFOLDERSCHECK}:&nbsp;</td>
	            <td>
	            {if $T_ORPHAN_LESSONS}
	                <img src = "images/16x16/warning.png"    title = "{$smarty.const._PROBLEM}"      alt = "{$smarty.const._PROBLEM}"/>&nbsp;
	                <img src = "images/16x16/help.png"      title = "{$smarty.const._INFO}"         alt = "{$smarty.const._INFO}"         onclick = "eF_js_showDivPopup('{$smarty.const._LESSONSWITHOUTFOLDERSCHECK}', 0, 'lessons_without_folders')"/>&nbsp;
	                <img src = "images/16x16/error_delete.png"     title = "{$smarty.const._CLEANUP}"      alt = "{$smarty.const._CLEANUP}"      onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGLESSONS}:\n\n{$T_ORPHAN_LESSONS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&cleanup=lessons_without_folders'"/>&nbsp;
	                <img src = "images/16x16/folders.png" title = "{$smarty.const._CREATEFOLDER}" alt = "{$smarty.const._CREATEFOLDER}" onclick = "if (confirm('{$smarty.const._CREATEFOLLOWINGLESSONFOLDERS}:\n\n{$T_ORPHAN_LESSONS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&create=lesson_folders'"/>
	            {else}
	                <img src = "images/16x16/success.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
	            {/if}
	        </td></tr>
	        <tr><td colspan = "2">&nbsp;</td></tr>
	        <tr><td></td><td><input class = "flatButton" type = "button" value = "{$smarty.const._CHECKAGAIN}" onclick = "location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup'">&nbsp;<input class = "flatButton" type = "button" value = "{$smarty.const._CLEANUPALL}" onclick = "if (confirm('{$smarty.const._THISOPERATIONALTERSYSTEM}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&cleanup=all'"></td></tr>
	    </table>
	    <div id = "orphan_user_folders" style = "display:none;">
	    {capture name = 't_orphan_user_folders_code'}
	        {$T_ORPHAN_USER_FOLDERS}
	    {/capture}
	    {eF_template_printBlock title=$smarty.const._FOLDERSWITHOUTAUSERASSOCIATED data=$smarty.capture.t_orphan_user_folders_code image='32x32/cleanup.png'}
	    </div>
	    <div id = "users_without_folders" style = "display:none;">
	    {capture name = 't_orphan_users_code'}
	        {$T_ORPHAN_USERS}
	    {/capture}
	    {eF_template_printBlock title=$smarty.const._USERSWITHOUTAFOLDER data=$smarty.capture.t_orphan_users_code image='32x32/cleanup.png'}
	    </div>
	    <div id = "orphan_lesson_folders" style = "display:none;">
	    {capture name = 't_orphan_lesson_folders_code'}
	        {$T_ORPHAN_LESSON_FOLDERS}
	    {/capture}
	    {eF_template_printBlock title=$smarty.const._FOLDERSWITHOUTALESSONASSOCIATED data=$smarty.capture.t_orphan_lesson_folders_code image='32x32/cleanup.png'}
	    </div>
	    <div id = "lessons_without_folders" style = "display:none;">
	    {capture name = 't_lessons_without_folders_code'}
	        {$T_ORPHAN_LESSONS}
	    {/capture}
	    {eF_template_printBlock title=$smarty.const._LESSONSWITHOUTAFOLDER data=$smarty.capture.t_lessons_without_folders_code image='32x32/cleanup.png'}
	    </div>
	{/capture}       
	{capture name = "t_maintenance_code"}       
	<table class = "formElements">
		<tr><td class = "labelCell">{$smarty.const._VERSION}:&nbsp;</td>
			<td class = "elementCell">{$smarty.const.G_VERSION_NUM}</td></tr>
		<tr><td class = "labelCell">{$smarty.const._DATABASEVERSION}:&nbsp;</td>
			<td class = "elementCell">{$T_CONFIGURATION.database_version}</td></tr>
		{if $T_DIFFERENT_VERSIONS}
		<tr><td></td>
			<td class = "infoCell" style = "vertical-align:middle"><img src = "images/16x16/warning.png" class = "ajaxHandle" title = "{$smarty.const._WARNING}" alt = "{$smarty.const._WARNING}"/><span style = "vertical-align:middle"> {$smarty.const._DIFFERENTVERSIONSUPGRADENEEDED|replace:"%link":"<a href = 'install/install.php?step=1&upgrade=1' style = 'vertical-align:middle'>`$smarty.const._UPGRADE`</a>"}</span></td></tr>
		{/if}
		<tr><td class = "labelCell">{$smarty.const._BUILD}:&nbsp;</td>
			<td class = "elementCell">{$smarty.const.G_BUILD}</td></tr>
	</table>      
	<div class = "tabber">
	    <div class = "tabbertab">
	        <h3>{$smarty.const._ENVIRONMENTALCHECK}</h3>
	        {include file = 'includes/check_status.tpl'}
	    </div>
		<div class = "tabbertab {if $smarty.get.tab=='phpinfo'}tabbertabdefault{/if}" title = "{$smarty.const._PHPINFO}">
    {capture name = 't_php_info_code'}
		<div class = "phpinfodisplay">{$T_PHPINFO}</div>
	{/capture}
	{eF_template_printBlock title=$smarty.const._PHPINFO data=$smarty.capture.t_php_info_code image='32x32/php.png'}
	</div>
    
    <div class = "tabbertab {if $smarty.get.tab=='lock_down'}tabbertabdefault{/if}">
        <h3>{$smarty.const._LOCKDOWN}</h3>
		{capture name = 't_lock_down_code'}
	        {$T_LOCKDOWN_FORM.javascript}
	        <form {$T_LOCKDOWN_FORM.attributes}>
			    {$T_LOCKDOWN_FORM.hidden}
		    	<table class = "formElements">
			        {if $T_CONFIGURATION.lock_down}
			        <tr><td class = "labelCell severeWarning">{$smarty.const._THESYSTEMISCURRENTLYLOCKED}&nbsp;</td>
			            <td class = "elementCell">{$T_LOCKDOWN_FORM.submit_unlock.html}</td></tr>
			        {else}
			        <tr><td class = "labelCell">{$smarty.const._LOCKDOWNMESSAGE}:&nbsp;</td>
			            <td class = "elementCell">{$T_LOCKDOWN_FORM.lock_message.html}</td>
			        <tr><td class = "labelCell">{$smarty.const._LOGOUTUSERS}:&nbsp;</td>
			            <td class = "elementCell">{$T_LOCKDOWN_FORM.logout_users.html}</td>
			        <tr><td colspan = "2">&nbsp;</td></tr>
			        <tr><td class = "labelCell"></td>
			            <td class = "elementCell">{$T_LOCKDOWN_FORM.submit_lockdown.html}</td></tr>
			        {/if}
		        </table>
	        </form>
		{/capture}
		{eF_template_printBlock title=$smarty.const._LOCKDOWN data=$smarty.capture.t_lock_down_code image='32x32/key.png'}
    </div>
    
    
	{if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
	    <div class = "tabbertab {if $smarty.get.tab=='cleanup'}tabbertabdefault{/if}">
	        
	        <h3>{$smarty.const._CLEANUP}</h3>
	        {eF_template_printBlock title=$smarty.const._CLEANUP data=$smarty.capture.t_cleanup_code image='32x32/cleanup.png'}
	    </div>
	    <script>var reindexcomplete = '{$smarty.const._OPERATIONCOMPLETEDSUCCESSFULLY}';</script>
	    <div class = "tabbertab {if $smarty.get.tab=='reindex'}tabbertabdefault{/if}">
	        <h3>{$smarty.const._RECREATESEARCHTABLE}</h3>
	        {capture name = 't_reindex_code'}
	        <table>
	    <tr><td class = "labelCell">{$smarty.const._CLICKHERETOREINDEX}:&nbsp;</td>
	        <td><input type = "button" class = "flatButton" value = "{$smarty.const._RECREATE}" onclick = "reIndex(this)"/></td></tr>
	        </table>
	        {/capture}
	
	        {eF_template_printBlock title=$smarty.const._RECREATESEARCHTABLE data=$smarty.capture.t_reindex_code image='32x32/import_export.png'}
	    </div>
	{/if}
	    <div class = "tabbertab {if $smarty.get.tab=='reindex'}tabbertabdefault{/if}">
	        <h3>{$smarty.const._CLEARCACHE}</h3>
	        {capture name = 't_clear_cache_code'}
	        <table>
	    <tr><td class = "labelCell">{$smarty.const._CLEARTEMPLATESCACHE}:&nbsp;</td>
	        <td><input class = "flatButton" type = "button" value = "{$smarty.const._CLEAR}" onclick = "clearCache(this, 'templates')"/></td></tr>
	    <tr><td class = "labelCell">{$smarty.const._CLEARTESTSCACHE}:&nbsp;</td>
	        <td><input class = "flatButton" type = "button" value = "{$smarty.const._CLEAR}" onclick = "clearCache(this, 'tests')"/></td></tr>
	        </table>
	        {/capture}
	
	        {eF_template_printBlock title=$smarty.const._CLEARCACHE data=$smarty.capture.t_clear_cache_code image='32x32/error_delete.png'}
	    </div>
	</div>
	{/capture}
	{eF_template_printBlock title=$smarty.const._MAINTENANCE data=$smarty.capture.t_maintenance_code image='32x32/maintenance.png'}

</td></tr>
{/capture}