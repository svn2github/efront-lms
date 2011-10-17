{include file = "includes/header.tpl"}
{strip}

{assign var = "title" value = "<a class='titleLink' href = '`$smarty.server.PHP_SELF`'>`$smarty.const._STARTPAGE`</a>"}

{if $smarty.get.ctg == 'contact'}
    {capture name = 't_contact_code'}
  {include file = "includes/blocks/contact.tpl"}
    {/capture}
    {assign var = "layoutClass" value = "hideBoth"}
{elseif $smarty.get.ctg == 'signup' && $T_CONFIGURATION.signup}
    {capture name = 't_signup_code'}
  {include file = "includes/blocks/signup.tpl"}
    {/capture}
 {assign var = "layoutClass" value = "hideBoth"}
{elseif $smarty.get.ctg == 'reset_pwd'}
    {capture name = 't_reset_pwd_code'}
  {include file = "includes/blocks/reset_pwd.tpl"}
    {/capture}
 {assign var = "layoutClass" value = "hideBoth"}
{elseif $smarty.get.ctg == 'expired'}
    {capture name = 't_session_expired_code'}
  {include file = "includes/blocks/expired.tpl"}
    {/capture}
 {assign var = "layoutClass" value = "hideBoth"}
{elseif $smarty.get.ctg == 'lesson_info' && $T_CONFIGURATION.lessons_directory == 1}
    {capture name = 't_lesson_info_code'}
     {include file = "includes/blocks/lessons_info.tpl"}
    {/capture}
{elseif $smarty.get.ctg == 'lessons'}

{elseif $smarty.get.ctg == 'login'}
 {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=login'>`$smarty.const._LOGINENTRANCE`</a>"}

{elseif $smarty.get.ctg == 'checker'}
{capture name='t_checker_code'}
 {include file = "includes/blocks/checker.tpl"}
{/capture}
{/if}

{*Block for displaying the system anouncements*}
{capture name='t_links_code'}
 {include file = "includes/blocks/links.tpl"}
{/capture}

{*Block for displaying the system anouncements*}
{capture name='t_news_code'}
 {include file = "includes/blocks/news.tpl"}
{/capture}

{*Block for displaying the login form*}
{capture name = 't_login_code'}
 {include file = "includes/blocks/login.tpl"}
{/capture}

{*Block for displaying the online users*}
{capture name = "t_online_code"}
 {include file = "includes/blocks/online_users.tpl"}
{/capture}

{*Block for displaying the search field*}
{capture name = 't_search_code'}
 {include file = "includes/blocks/search.tpl"}
{/capture}

{*Block for displaying the system anouncements*}
{capture name='t_checker_code'}
 {include file = "includes/blocks/checker.tpl"}
{/capture}

{if $T_CONFIGURATION.lessons_directory == 1}
 {*Block for displaying the lessons list*}
 {capture name = 't_lessons_code'}
  {$T_DIRECTIONS_TREE}
 {/capture}

 {*Block for displaying the selected lessons list (cart)*}
 {capture name = 't_selectedLessons_code'}
  {include file = "includes/blocks/cart.tpl"}
 {/capture}
{/if}


{capture name = "left_code"}
    {if $T_POSITIONS.leftList || !$T_POSITIONS}
        {if isset($T_POSITIONS.leftList)}
            {foreach name = 'left_list' item = "item" key = "key" from = $T_POSITIONS.leftList}
             {assign var = "capture_name" value = "t_"|cat:$item|cat:"_code"}
          {if $smarty.capture.$capture_name}
           {eF_template_printBlock title = $T_BLOCKS[$item].title content = $smarty.capture.$capture_name image = "`$T_BLOCKS[$item].image`"}
          {else if $T_CUSTOM_BLOCKS[$item].content}
     {insert name = "customBlock" file = "`$T_CUSTOM_BLOCKS[$item].name`.tpl" assign = "content"}
           {eF_template_printBlock title = $T_BLOCKS[$item].title content = $content image = "`$T_BLOCKS[$item].image`"}
          {/if}
            {/foreach}
        {else}
         {eF_template_printBlock title = $smarty.const._LOGINENTRANCE content = $smarty.capture.t_login_code image = $T_BLOCKS.login.image}
   {if $T_CONFIGURATION.disable_online_users != 1}
    {eF_template_printBlock title = $smarty.const._USERSONLINE content = $smarty.capture.t_online_code image = $T_BLOCKS.online.image}
   {/if}
  {/if}
    {/if}
{/capture}

{capture name = "center_code"}
    {if $T_MESSAGE && !$T_FACEBOOK_ACCOUNT_MERGE_POPUP}
      {eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}
    {/if}
    {if $smarty.get.ctg == 'contact'}
     {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=contact'>`$smarty.const._CONTACTUS`</a>"}
        {eF_template_printBlock title = $smarty.const._CONTACTUS content = $smarty.capture.t_contact_code image = "32x32/mail.png"}
    {elseif $smarty.get.ctg == 'signup' && $T_CONFIGURATION.signup}
        {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=signup'>`$smarty.const._SIGNUP`</a>"}
  {eF_template_printBlock title = $smarty.const._REGISTERANEWACCOUNT content = $smarty.capture.t_signup_code image = "32x32/user.png"}
     {elseif $smarty.get.ctg == 'checker'}
        {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=checker'>`$smarty.const._OPTIONSCHECKER`</a>"}
  {eF_template_printBlock title = $smarty.const._OPTIONSCHECKER content = $smarty.capture.t_checker_code image = "32x32/success.png"}
 {elseif $smarty.get.ctg == 'reset_pwd'}
     {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=reset_pwd'>`$smarty.const._RESETPASSWORD`</a>"}
        {eF_template_printBlock title = $smarty.const._RESETPASSWORD content = $smarty.capture.t_reset_pwd_code image = "32x32/exclamation.png"}
    {elseif $smarty.get.ctg == 'expired'}
     {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=expired'>`$smarty.const._SESSIONEXPIRED`</a>"}
        {eF_template_printBlock title = $smarty.const._SESSIONEXPIRED content = $smarty.capture.t_session_expired_code image = "32x32/exclamation.png"}
    {elseif $smarty.get.ctg == 'lessons' && $T_CONFIGURATION.lessons_directory == 1}
     {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=lessons'>`$smarty.const._COURSES`</a>"}
     {eF_template_printBlock title = $smarty.const._COURSES content = $smarty.capture.t_lessons_code image = $T_BLOCKS.lessons.image}
    {elseif $smarty.get.ctg == 'lesson_info' && $T_CONFIGURATION.lessons_directory == 1}
     {if $T_LESSON_INFO}
   {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=lessons'>`$smarty.const._COURSES`</a><span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=lesson_info&lessons_ID=`$T_LESSON->lesson.id`&course=`$T_COURSE->course.id`'>`$smarty.const._INFOFORLESSON`: &quot;`$T_LESSON->lesson.name`&quot;</a>"}
   {assign var = "lesson_title" value = "`$smarty.const._INFORMATIONFORLESSON` <span class = 'innerTableName'>&quot;`$T_LESSON->lesson.name`&quot;</span>"}
  {elseif $T_COURSE_INFO}
   {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=lessons'>`$smarty.const._COURSES`</a><span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=lesson_info&courses_ID=`$T_COURSE->course.id`'>`$smarty.const._INFOFORCOURSE`: &quot;`$T_COURSE->course.name`&quot;</a>"}
   {assign var = "lesson_title" value = "`$smarty.const._INFORMATIONFORCOURSE` <span class = 'innerTableName'>&quot;`$T_COURSE->course.name`&quot;</span>"}
  {/if}
        {eF_template_printBlock title = $lesson_title content = $smarty.capture.t_lesson_info_code image = "32x32/information.png"}
    {elseif $smarty.get.ctg == 'login'}
        {eF_template_printBlock title = $smarty.const._LOGINENTRANCE content = $smarty.capture.t_login_code image = "32x32/keys.png"}
        {assign var = "layoutClass" value = "hideBoth"}
    {elseif isset($smarty.get.ctg) && $T_POSITIONS.enabled[$smarty.get.ctg]}
     {* Changed because of #896. Problem when block contained {,} *}
     {insert name = "customBlock" file = "`$T_CUSTOM_BLOCKS[$smarty.get.ctg].name`.tpl" assign = "content"}
  {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=`$smarty.get.ctg`'>`$T_CUSTOM_BLOCKS[$smarty.get.ctg].title`</a>"}
     {eF_template_printBlock title = $T_CUSTOM_BLOCKS[$smarty.get.ctg].title content = $content image = "32x32/generic.png"}
 {else}
     {if isset($T_POSITIONS.centerList)}
            {foreach name = 'center_list' item = "item" key = "key" from = $T_POSITIONS.centerList}
             {assign var = "capture_name" value = "t_"|cat:$item|cat:"_code"}
          {if $smarty.capture.$capture_name}
           {eF_template_printBlock title = $T_BLOCKS[$item].title content = $smarty.capture.$capture_name image = "`$T_BLOCKS[$item].image`"}
          {else if $T_CUSTOM_BLOCKS[$item].content}
           {insert name = "customBlock" file = "`$T_CUSTOM_BLOCKS[$item].name`.tpl" assign = "content"}
           {eF_template_printBlock title = $T_BLOCKS[$item].title content = $content image = "`$T_BLOCKS[$item].image`"}
          {/if}
            {/foreach}
        {else}
         {if $T_CONFIGURATION.lessons_directory == 1}
          {eF_template_printBlock title = $smarty.const._COURSES content = $smarty.capture.t_lessons_code image = $T_BLOCKS.lessons.image}
         {/if}
        {/if}
    {/if}
{/capture}

{capture name = "right_code"}
    {if $T_POSITIONS.rightList || !$T_POSITIONS}
        {if isset($T_POSITIONS.rightList)}
            {foreach name = 'right_list' item = "item" key = "key" from = $T_POSITIONS.rightList}
             {assign var = "capture_name" value = "t_"|cat:$item|cat:"_code"}
          {if $smarty.capture.$capture_name}
           {eF_template_printBlock title = $T_BLOCKS[$item].title content = $smarty.capture.$capture_name image = "`$T_BLOCKS[$item].image`"}
          {else if $T_CUSTOM_BLOCKS[$item].content}
           {insert name = "customBlock" file = "`$T_CUSTOM_BLOCKS[$item].name`.tpl" assign = "content"}
           {eF_template_printBlock title = $T_BLOCKS[$item].title content = $content image = "`$T_BLOCKS[$item].image`"}
          {/if}
            {/foreach}
        {else}

      {if $T_CONFIGURATION.disable_news != 1}
    {eF_template_printBlock title = $smarty.const._SYSTEMNEWS content = $smarty.capture.t_news_code image = $T_BLOCKS.news.image}
      {/if}
   {if $T_CONFIGURATION.lessons_directory == 1}
       {eF_template_printBlock title = $smarty.const._SELECTEDLESSONS content = $smarty.capture.t_selectedLessons_code image = $T_BLOCKS.selectedLessons.image}
      {/if}
        {/if}
    {/if}
{/capture}

{*{eF_template_printBlock title = $smarty.const._SYSTEMNEWS content = $smarty.capture.t_custom_block_links_code image = $T_BLOCKS.news.image}*}


{if !$layoutClass && $T_POSITIONS.layout == 'left'}{assign var = "layoutClass" value = "hideRight"}
{elseif !$layoutClass && $T_POSITIONS.layout == 'right'}{assign var = "layoutClass" value = "hideLeft"}
{elseif !$layoutClass && $T_POSITIONS.layout == 'simple'}{assign var = "layoutClass" value = "hideBoth"}
{/if}

{if $smarty.session.login_mode == 1 || $smarty.get.login_mode == 1}{assign var = "layoutClass" value = "hideBoth"}{/if} {*If we are in "buy lessons" mode, hide all other information*}

{if $smarty.get.ctg == 'agreement' && $smarty.session.s_login}
    {capture name = "center_code"}
     {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=agreement'>`$smarty.const._LICENSENOTE`</a>"}
        {if $T_MESSAGE && !$T_FACEBOOK_ACCOUNT_MERGE_POPUP}
          {eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}
        {/if}
  {capture name = 'agreement_code'}
      <div class = "license">{$T_CONFIGURATION.license_note}</div>
      <div class = "licenseHandles">
                {$T_AGREEMENT_FORM.javascript}
                <form {$T_AGREEMENT_FORM.attributes}>
                 {$T_AGREEMENT_FORM.hidden}
                 {$T_AGREEMENT_FORM.submit_decline.html} {$T_AGREEMENT_FORM.submit_accept.html}
                </form>
      </div>
     {/capture}
     {eF_template_printBlock title = $smarty.const._IMPORTANTNOTICE content = $smarty.capture.agreement_code image = "32x32/exclamation.png"}
     {assign var = "layoutClass" value = "hideBoth"}
    {/capture}
{elseif $smarty.get.ctg == 'password_change' && $smarty.session.s_login}
    {capture name = "center_code"}
        {if $T_MESSAGE}
          {eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}
        {/if}
  {capture name = 'change_password_code'}
   {eF_template_printForm form = $T_CHANGE_PASSWORD_FORM}
     {/capture}
     {eF_template_printBlock title = $smarty.const._CHANGEPASSWORD content = $smarty.capture.change_password_code image = "32x32/exclamation.png"}
     {assign var = "layoutClass" value = "hideBoth"}
    {/capture}
{/if}

{if $T_CONFIGURATION.lock_down}
    {capture name = "center_code"}
        {if $T_MESSAGE && !$T_FACEBOOK_ACCOUNT_MERGE_POPUP}
          {eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}
        {/if}
     {capture name = "lockdown_code"}
      <div class = "lock">{$T_CONFIGURATION.lock_message}. <a href = "index.php?ctg=login">{$smarty.const._LOGINASADMIN}</a></div>
     {/capture}
     {eF_template_printBlock title = $smarty.const._LOCKDOWN content = $smarty.capture.lockdown_code image = "32x32/lock.png"}
     {if $smarty.get.ctg == 'login'}
   {eF_template_printBlock title = $smarty.const._LOGINENTRANCE content = $smarty.capture.t_login_code image = "32x32/keys.png"}
  {/if}
     {assign var = "layoutClass" value = "hideBoth"}
    {/capture}
{/if}

{if $smarty.get.checkout}
 {assign var = "title" value = "`$title`<a class='titleLink' href = '`$smarty.server.PHP_SELF`?ctg=checkout&checkout=1'>`$smarty.const._REVIEWANDCHECKOUT`</a>"}
    {capture name = "center_code"}
        {if $T_MESSAGE && !$T_FACEBOOK_ACCOUNT_MERGE_POPUP}
          {eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}
        {/if}
  {eF_template_printBlock title = $smarty.const._SELECTEDLESSONS content = $smarty.capture.t_selectedLessons_code image = $T_BLOCKS.selectedLessons.image}
 {/capture}
{/if}

{capture name = "header_language_code"}
  {if !$T_CONFIGURATION.onelanguage}
   <select onchange = "var new_location = location.toString().replace(/(\?|&)bypass_language=.*/, '');location = new_location+(new_location.toString().match(/\?/) ? '&' : '?')+'bypass_language='+this.options[this.selectedIndex].value">
    {foreach name = 'languages_list' item = "item" key = "key" from = $T_LANGUAGES}
     <option value = "{$key}" {if $key == $T_LANGUAGE}selected{/if}>{$item}</option>
    {/foreach}
   </select>
  {/if}
{/capture}

{include file = "includes/common_layout.tpl"}

{if $T_FACEBOOK_ACCOUNT_MERGE_POPUP}
 <div id = 'facebook_login' style = "display:none" align = "left">
  {if $T_MESSAGE}
      {eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}
  {/if}
  {capture name = 't_facebook_login'}
   {include file = "includes/blocks/facebook_login.tpl"}
     {/capture}
     {eF_template_printBlock title = $smarty.const._FACEBOOKCONNECT content = $smarty.capture.t_facebook_login image = "32x32/facebook.png"}
 </div>
{/if}

{/strip}

<script type = "text/javascript">
translations['_COUPON'] = '{$smarty.const._COUPON}';
translations['_CLICKTOENTERDISCOUNTCOUPON'] = '{$smarty.const._CLICKTOENTERDISCOUNTCOUPON}';
{if $smarty.session.s_login}
 {if $smarty.server.PHP_SELF|basename|replace:'.php':'' == 'index'}
  redirectLocation ='index.php?ctg=checkout&checkout=1&register_lessons=1';
 {else}
  redirectLocation ='{$smarty.server.PHP_SELF}?ctg=lessons&catalog=1&checkout=1';
 {/if}
{else}
 redirectLocation ='index.php?ctg=login&register_lessons=1';
{/if}
</script>

{include file = "includes/closing.tpl"}
