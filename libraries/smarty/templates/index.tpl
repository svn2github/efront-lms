{include file = "includes/header.tpl"}

{assign var = "title" value = "<a href = '`$smarty.server.PHP_SELF`'>`$smarty.const._STARTPAGE`</a>"}
   
{if $smarty.get.ctg == 'contact'}
    {capture name = 't_contact_code'}
        {$T_CONTACT_FORM.javascript}
        <form {$T_CONTACT_FORM.attributes}>
            {$T_CONTACT_FORM.hidden}
    		<div class = "formRow">
        		<div class = "formLabel">			
                    <div class = "header">{$T_CONTACT_FORM.email.label}</div>
                    {*<div class = "explanation">{$smarty.const._ENTERYOUREMAILADDRESS}</div>*}
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_CONTACT_FORM.email.html}</div>
            		{if $T_CONTACT_FORM.email.error}<div class = "error">{$T_CONTACT_FORM.email.error}</div>{/if}
        	    </div>
        	</div>
    		<div class = "formRow">
        		<div class = "formLabel">			
                    <div class = "header">{$T_CONTACT_FORM.message_subject.label}</div>
                    {*<div class = "explanation">{$smarty.const._ENTERMESSAGESUBJECT}</div>*}
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_CONTACT_FORM.message_subject.html}</div>
            		{if $T_CONTACT_FORM.message_subject.error}<div class = "error">{$T_CONTACT_FORM.message_subject.error}</div>{/if}
        	    </div>
        	</div>
    		<div class = "formRow">
        		<div class = "formLabel">			
                    <div class = "header">{$T_CONTACT_FORM.message_body.label}</div>
                    {*<div class = "explanation">{$smarty.const._ENTERMESSAGE}</div>*}
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_CONTACT_FORM.message_body.html}</div>
            		{if $T_CONTACT_FORM.message_body.error}<div class = "error">{$T_CONTACT_FORM.message_body.error}</div>{/if}
        	    </div>
        	</div>
    		<div class = "formRow">	    
            	<div class = "formLabel">			
                    <div class = "header">&nbsp;</div>
                    {*<div class = "explanation"></div>*}
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_CONTACT_FORM.submit_contact.html}</div>
        	    </div>      		
        	</div>		
        </form>
        
        {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=contact'>`$smarty.const._CONTACTUS`</a>"}
    {/capture}

{elseif $smarty.get.ctg == 'signup' && $T_CONFIGURATION.signup}
    {capture name = 't_signup_code'}
        {$T_PERSONAL_INFO_FORM.javascript}
        <form {$T_PERSONAL_INFO_FORM.attributes}>
    		{$T_PERSONAL_INFO_FORM.hidden}
    		<div class = "formRow">
        		<div class = "formLabel">			
                    <div class = "header">{$T_PERSONAL_INFO_FORM.login.label}</div>
                    <div class = "explanation">{$smarty.const._ONLYALLOWEDCHARACTERSLOGIN}</div>
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_PERSONAL_INFO_FORM.login.html}</div>
            		{if $T_PERSONAL_INFO_FORM.login.error}<div class = "error">{$T_PERSONAL_INFO_FORM.login.error}</div>{/if}
        	    </div>
        	</div>
    		<div class = "formRow">	    
        		<div class = "formLabel">			
                    <div class = "header">{$T_PERSONAL_INFO_FORM.password.label}</div>
                    <div class = "explanation">{$smarty.const._PASSWORDMUSTBE6CHARACTERS}</div>
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_PERSONAL_INFO_FORM.password.html}</div>
            		{if $T_PERSONAL_INFO_FORM.password.error}<div class = "error">{$T_PERSONAL_INFO_FORM.password.error}</div>{/if}
        	    </div>
        	</div>
    		<div class = "formRow">	    		
        		<div class = "formLabel">			
                    <div class = "header">{$T_PERSONAL_INFO_FORM.passrepeat.label}</div>
                    <div class = "explanation"></div>
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_PERSONAL_INFO_FORM.passrepeat.html}</div>
            		{if $T_PERSONAL_INFO_FORM.passrepeat.error}<div class = "error">{$T_PERSONAL_INFO_FORM.passrepeat.error}</div>{/if}
        	    </div>
        	</div>
    		<div class = "formRow">	    	    
        		<div class = "formLabel">			
                    <div class = "header">{$T_PERSONAL_INFO_FORM.email.label}</div>
                    <div class = "explanation"></div>
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_PERSONAL_INFO_FORM.email.html}</div>
            		{if $T_PERSONAL_INFO_FORM.email.error}<div class = "error">{$T_PERSONAL_INFO_FORM.email.error}</div>{/if}
        	    </div>
        	</div>
    		<div class = "formRow">	    	    
        		<div class = "formLabel">			
                    <div class = "header">{$T_PERSONAL_INFO_FORM.firstName.label}</div>
                    <div class = "explanation"></div>
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_PERSONAL_INFO_FORM.firstName.html}</div>
            		{if $T_PERSONAL_INFO_FORM.firstName.error}<div class = "error">{$T_PERSONAL_INFO_FORM.firstName.error}</div>{/if}
        	    </div>
        	</div>
    		<div class = "formRow">	    	    
        		<div class = "formLabel">			
                    <div class = "header">{$T_PERSONAL_INFO_FORM.lastName.label}</div>
                    <div class = "explanation"></div>
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_PERSONAL_INFO_FORM.lastName.html}</div>
            		{if $T_PERSONAL_INFO_FORM.lastName.error}<div class = "error">{$T_PERSONAL_INFO_FORM.lastName.error}</div>{/if}
        	    </div>
        	</div>
        {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_FIELDS }
    		<div class = "formRow">	    
        		<div class = "formLabel">			
                    <div class = "header">{$T_PERSONAL_INFO_FORM.$item.label}</div>
                    <div class = "explanation"></div>
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_PERSONAL_INFO_FORM.$item.html}</div>
            		{if $T_PERSONAL_INFO_FORM.$item.error}<div class = "error">{$T_PERSONAL_INFO_FORM.$item.error}</div>{/if}
        	    </div>
        	</div>
        {/foreach}  
    		<div class = "formRow">	    
            	<div class = "formLabel">			
                    <div class = "header">{$T_PERSONAL_INFO_FORM.comments.label}</div>
                    {*<div class = "explanation">{$smarty.const._ENTERANYCOMMENTS}</div>*}
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_PERSONAL_INFO_FORM.comments.html}</div>
            		{if $T_PERSONAL_INFO_FORM.comments.error}<div class = "error">{$T_PERSONAL_INFO_FORM.comments.error}</div>{/if}
        	    </div>      		
        	</div>
    		<div class = "formRow">	    
            	<div class = "formLabel">			
                    <div class = "header">&nbsp;</div>
                    <div class = "explanation"></div>
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_PERSONAL_INFO_FORM.submit_register.html}</div>
        	    </div>      		
        	</div>		
        </form>
    {/capture}
    
	{assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=signup'>`$smarty.const._SIGNUP`</a>"}

{elseif $smarty.get.ctg == 'reset_pwd'}
    {capture name = 't_reset_pwd_code'}
        {$T_RESET_PASSWORD_FORM.javascript}
        <form {$T_RESET_PASSWORD_FORM.attributes}>
            {$T_RESET_PASSWORD_FORM.hidden}
    		<div class = "formRow">
        		<div class = "formLabel">			
                    <div class = "header">{$T_RESET_PASSWORD_FORM.login_or_pwd.label}</div>
                    {*<div class = "explanation">{$smarty.const._ENTERYOUREMAILADDRESS}</div>*}
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_RESET_PASSWORD_FORM.login_or_pwd.html}</div>
            		{if $T_RESET_PASSWORD_FORM.login_or_pwd.error}<div class = "error">{$T_RESET_PASSWORD_FORM.login_or_pwd.error}</div>{/if}
        	    </div>
        	</div>
    		<div class = "formRow">	    
            	<div class = "formLabel">			
                    <div class = "header">&nbsp;</div>
                    <div class = "explanation"></div>
            	</div>
        		<div class = "formElement">
                	<div class = "field">{$T_RESET_PASSWORD_FORM.submit_reset_password.html}</div>
        	    </div>      		
        	</div>		
    	</form>
    {/capture}

	{assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=reset_pwd'>`$smarty.const._RESETPASSWORD`</a>"}

{elseif $smarty.get.ctg == 'lesson_info'}
    {capture name = 't_lesson_info_code'}
    	{include file = "includes/blocks/lessons_info.tpl"}
    {/capture}	  
    {if $T_LESSON_INFO}
		{assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=lessons'>`$smarty.const._LESSONS`</a><span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=lesson_info&lessons_ID=`$smarty.get.lessons_ID`'>`$smarty.const._INFOFORLESSON`: &quot;`$T_LESSON->lesson.name`&quot;</a>"}
    {elseif $T_COURSE_INFO}        
		{assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=lessons'>`$smarty.const._LESSONS`</a><span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=lesson_info&courses_ID=`$smarty.get.courses_ID`'>`$smarty.const._INFOFORCOURSE`: &quot;`$T_COURSE->course.name`&quot;</a>"}
	{/if}
{elseif $smarty.get.ctg == 'lessons'}
    	{assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=lessons'>`$smarty.const._LESSONS`</a>"}    	
{/if}


{*Block for displaying the system anouncements*}
{capture name='t_news_code'}
    {foreach name = 'news_list' item = "item" key = "key" from = $T_NEWS}
    	<div class = "newsTitle"><div>#filter:timestamp-{$item.timestamp}#</div>{$item.title}</div>
    	<div class = "newsContent">{$item.data}</div>
    {foreachelse}
    	<span class = "small">{$smarty.const._NOSYSTEMANNOUNCEMENTS}</span>
    {/foreach}
{/capture}

{*Block for displaying the login form*}
{capture name = 't_login_code'}
    {$T_LOGIN_FORM.javascript}
    <form {$T_LOGIN_FORM.attributes}>
    	{$T_LOGIN_FORM.hidden}
		<div class = "formRow">
    		<div class = "formLabel">			
                <div class = "header">{$T_LOGIN_FORM.login.label}</div>
                {*<div class = "explanation centerOnly"><a href = "{$smarty.server.PHP_SELF}?ctg=signup">{$smarty.const._DONTHAVEACCOUNT}</a></div>*}
        	</div>
    		<div class = "formElement">
            	<div class = "field">{$T_LOGIN_FORM.login.html}</div>
        		{if $T_LOGIN_FORM.login.error}<div class = "error">{$T_LOGIN_FORM.login.error}</div>{/if}
    	    </div>
    	</div>
		<div class = "formRow">
    		<div class = "formLabel">			
                <div class = "header">{$T_LOGIN_FORM.password.label}</div>
                {*<div class = "explanation centerOnly"><a href = "{$smarty.server.PHP_SELF}?ctg=reset_pwd">{$smarty.const._FORGOTPASSWORD}</a></div>*}
        	</div>
    		<div class = "formElement">
            	<div class = "field">{$T_LOGIN_FORM.password.html}</div>
        		{if $T_LOGIN_FORM.password.error}<div class = "error">{$T_LOGIN_FORM.password.error}</div>{/if}
    	    </div>
    	</div>
		<div class = "formRow">	    
        	<div class = "formLabel">			
                <div class = "header">&nbsp;</div>
                <div class = "explanation"></div>
        	</div>
    		<div class = "formElement">
            	<div class = "field">{$T_LOGIN_FORM.submit_login.html}</div>
            	{if $T_CONFIGURATION.signup}<div class = "small note"><a href = "{$smarty.server.PHP_SELF}?ctg=signup">{$smarty.const._DONTHAVEACCOUNT}</a></div>{/if}
            	<div class = "small note"><a href = "{$smarty.server.PHP_SELF}?ctg=reset_pwd">{$smarty.const._FORGOTPASSWORD}</a></div>
            	<div class = "small note"><a href = "{$smarty.server.PHP_SELF}?ctg=contact">{$smarty.const._CONTACTUS}</a></div>
    	    </div>      		
    	</div>		
     </form>

{/capture}
{*Block for displaying the online users*}
{capture name = "t_online_code"}
	{foreach name = "online_users" item = "item" key = "key" from = $T_ONLINE_USERS_LIST}
		{$item.login}{if !$smarty.foreach.online_users.last},&nbsp;{/if}
    {foreachelse}
    	<span class = "small">{$smarty.const._NOONELOGGEDIN}</span>
	{/foreach}
{/capture}
{*Block for displaying the search field*}
{capture name = 't_search_code'}
    <form action = "{$smarty.server.PHP_SELF}?fct=searchResults" method = "post">
    	<input class = "inputSearchText" type = "text" name = "name" />
    	<input name = "cms_page" type = "image" src = "images/16x16/arrow_right_blue.png" value = "{$smarty.const._SEARCH}" />
    </form>
{/capture}
{*Block for displaying the lessons list*}
{capture name = 't_lessons_code'}
	{$T_DIRECTIONS_TREE}
{/capture}
	

{*Block for displaying the selected lessons list (cart)*}

{capture name = 't_selectedLessons_code'}
	{include file = "includes/blocks/selected_lessons.tpl"}
{/capture}

{capture name = "header_code"}
	<div id = "logo">
		<a href = ""><img src = "images/{$T_LOGO}" title = "{$smarty.const._EFRONT}" alt = "{$smarty.const._EFRONT}" border = "0" style = "width:{$T_NEWWIDTH}px;height:{$T_NEWHEIGHT}px"></a>
	</div>
	<div id = "info">
		<div id = "siteName">{$T_CONFIGURATION.site_name}</div>		
		{if $T_CONFIGURATION.site_moto}<div id = "site_moto">{$T_CONFIGURATION.site_moto}</div>{/if}
	</div>
	<div id = "path" style = "clear:left;padding-top:2px;background:#FAFAFA url('images/others/grey1.png') repeat-x top;height:35px;order-bottom: 1px solid #999999;border-top: 1px solid #999999;">
		<div style = "float:left;margin-left:10px">{$title}</div>
	{if !$T_CONFIGURATION.onelanguage}
		<div style = "float:right">
		{if $smarty.get.bypass_language}{assign var = "selected_language" value = $smarty.get.bypass_language}{else}{assign var = "selected_language" value = $T_CONFIGURATION.default_language}{/if}
			<select onchange = "var new_location = location.toString().replace(/(\?|&)bypass_language=.*/, '');location = new_location+(new_location.toString().match(/\?/) ? '&' : '?')+'bypass_language='+this.options[this.selectedIndex].value">
				{foreach name = 'languages_list' item = "item" key = "key" from = $T_LANGUAGES} 
					<option value = "{$key}" {if $key == $T_LANGUAGE}selected{/if}>{$item}</option>
				{/foreach}
			</select>
		</div>
	{/if}
	</div>
{/capture}
{capture name = "left_code"}
    {if $T_POSITIONS.leftList || !$T_POSITIONS}
        {if isset($T_POSITIONS.leftList)}
            {foreach name = 'left_list' item = "item" key = "key" from = $T_POSITIONS.leftList}
            	{assign var = "capture_name" value = "t_"|cat:$item|cat:"_code"}
        		{if $smarty.capture.$capture_name}
        			{eF_template_printBlock title = $T_BLOCKS[$item] content = $smarty.capture.$capture_name}
        		{else if $T_CUSTOM_BLOCKS[$item].content}
        			{eF_template_printBlock title = $T_BLOCKS[$item] content = $T_CUSTOM_BLOCKS[$item].content}
        		{/if}		
            {/foreach}	
        {else}
        	{eF_template_printBlock title = $smarty.const._LOGINENTRANCE content = $smarty.capture.t_login_code}
        	{eF_template_printBlock title = $smarty.const._USERSONLINE content = $smarty.capture.t_online_code image = "images/32x32/users3.png"}		
        {/if}    
    {/if}
{/capture}
{capture name = "center_code"}
    {if $T_MESSAGE}
    		{eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}
    {/if}
    {if $smarty.get.ctg == 'contact'}
        	{eF_template_printBlock title = $smarty.const._CONTACTUS  content = $smarty.capture.t_contact_code}
    {elseif $smarty.get.ctg == 'signup' && $T_CONFIGURATION.signup}
        	{eF_template_printBlock title = $smarty.const._REGISTERANEWACCOUNT  content = $smarty.capture.t_signup_code}
    {elseif $smarty.get.ctg == 'reset_pwd'}
        	{eF_template_printBlock title = $smarty.const._RESETPASSWORD  content = $smarty.capture.t_reset_pwd_code}
    {elseif $smarty.get.ctg == 'lessons'}
    		{eF_template_printBlock title = $smarty.const._LESSONS  content = $smarty.capture.t_lessons_code}
    {elseif $smarty.get.ctg == 'lesson_info'}
    	{if $T_LESSON_INFO}
			{assign var = "lesson_title" value = "`$smarty.const._INFORMATIONFORLESSON` <span class = 'innerTableName'>&quot;`$T_LESSON->lesson.name`&quot;</span>"}
		{else}
			{assign var = "lesson_title" value = "`$smarty.const._INFORMATIONFORCOURSE` <span class = 'innerTableName'>&quot;`$T_COURSE->course.name`&quot;</span>"}
		{/if}
        {eF_template_printBlock title = $lesson_title content = $smarty.capture.t_lesson_info_code}
    {elseif $smarty.get.ctg == 'login'}
        	{eF_template_printBlock title = $smarty.const._LOGINENTRANCE content = $smarty.capture.t_login_code}
    {else}
    	{if isset($T_POSITIONS.centerList)}
            {foreach name = 'center_list' item = "item" key = "key" from = $T_POSITIONS.centerList}
            	{assign var = "capture_name" value = "t_"|cat:$item|cat:"_code"}
        		{if $smarty.capture.$capture_name}
        			{eF_template_printBlock title = $T_BLOCKS[$item] content = $smarty.capture.$capture_name}
        		{else if $T_CUSTOM_BLOCKS[$item].content}
        			{eF_template_printBlock title = $T_BLOCKS[$item] content = $T_CUSTOM_BLOCKS[$item].content}
        		{/if}		
            {/foreach}	
        {else}
        	{eF_template_printBlock title = $smarty.const._LESSONS  content = $smarty.capture.t_lessons_code}
        {/if}
    {/if}
{/capture}
{capture name = "right_code"}
    {if $T_POSITIONS.rightList || !$T_POSITIONS}
        {if isset($T_POSITIONS.rightList)}
            {foreach name = 'right_list' item = "item" key = "key" from = $T_POSITIONS.rightList}
            	{assign var = "capture_name" value = "t_"|cat:$item|cat:"_code"}
        		{if $smarty.capture.$capture_name}
        			{eF_template_printBlock title = $T_BLOCKS[$item] content = $smarty.capture.$capture_name}
        		{else if $T_CUSTOM_BLOCKS[$item].content}
        			{eF_template_printBlock title = $T_BLOCKS[$item] content = $T_CUSTOM_BLOCKS[$item].content}
        		{/if}		
            {/foreach}	
        {else}
    		{eF_template_printBlock title = $smarty.const._SYSTEMNEWS content = $smarty.capture.t_news_code}
    		{eF_template_printBlock title = $smarty.const._SELECTEDLESSONS content = $smarty.capture.t_selectedLessons_code}		
        {/if}    
    {/if}
{/capture}
{capture name = "footer_code"}
	<a href = "http://www.efrontlearning.net">eFront </a> (version {$smarty.const.G_VERSION_NUM}) &bull; {$smarty.const._VERSIONTYPE}: {$smarty.const.G_VERSIONTYPE} &bull; <a href = "index.php?ctg=contact">{$smarty.const._CONTACTUS}</a>
{/capture}


{if $T_POSITIONS.layout == 'left'}{assign var = "layoutClass" value = "hideRight"}
{elseif $T_POSITIONS.layout == 'right'}{assign var = "layoutClass" value = "hideLeft"}
{elseif $T_POSITIONS.layout == 'simple'}{assign var = "layoutClass" value = "hideBoth"}	
{/if}

<table class = "layout index {$layoutClass}">
	<tr><td class = "header" colspan = "3">{$smarty.capture.header_code}</td></tr>
	<tr><td class = "left">{$smarty.capture.left_code}</td>
		<td class = "center">{$smarty.capture.center_code}</td>
		<td class = "right">{$smarty.capture.right_code}</td></tr>
	{if $T_CONFIGURATION.show_footer}<tr><td class = "footer" colspan = "3">{$smarty.capture.footer_code}</td></tr>{/if}
</table>

{literal}
<script type = "text/javascript">
	function toggleBlock(el) {
		Element.extend(el);
		hideBlock = el.up().select('div.content')[0];
		if (el.hasClassName('open')) {
			new Effect.BlindUp(hideBlock, {duration:0.5});
			el.removeClassName('open');
			el.addClassName('close');
		} else {
			new Effect.BlindDown(hideBlock, {duration:0.5});
			el.removeClassName('close');
			el.addClassName('open');
		}
	}

</script>
{/literal}

{include file = "includes/closing.tpl"}