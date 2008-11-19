{*smarty template for directory.php*}
{include file = "includes/header.tpl"}

{assign var = "title" value = '<a class = "titleLink" title = "'|cat:$smarty.const._HOME|cat:'" href ="'|cat:$T_CURRENT_USER->user.user_type|cat:'.php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`'>`$smarty.const._LESSONSDIRECTORY`</a>"}

{*Block for displaying the lessons list*}
{capture name = 't_lessons_code'}
	{$T_DIRECTIONS_TREE}
{/capture}

{*Block for displaying the selected lessons list (cart)*}
{capture name = 't_selectedLessons_code'}
	{include file = "includes/blocks/selected_lessons.tpl"}
{/capture}

{*Lesson or course information block*}
{capture name = 't_lesson_info_code'}
	{include file = "includes/blocks/lessons_info.tpl"}
    {if $T_LESSON_INFO}
    	{assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?lessons_ID=`$smarty.get.lessons_ID`'>`$smarty.const._INFOFORLESSON`: &quot;`$T_LESSON->lesson.name`&quot;</a>"}
    {elseif $T_COURSE_INFO}
    	{assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?courses_ID=`$smarty.get.courses_ID`'>`$smarty.const._INFOFORCOURSE`: &quot;`$T_COURSE->course.name`&quot;</a>"}
    {/if}
{/capture}	  

{*main table contents*}
{assign var = "layout" value = "hideLeft"}
{capture name = "center_code"}
    {if $T_LESSON_INFO}
    	{eF_template_printBlock title = "`$smarty.const._INFORMATIONFORLESSON`: <span class = 'innerTableName'>&quot;`$T_LESSON->lesson.name`&quot;</span>" content = $smarty.capture.t_lesson_info_code}
    {elseif $T_COURSE_INFO}
    	{eF_template_printBlock title = "`$smarty.const._INFORMATIONFORCOURSE`: <span class = 'innerTableName'>&quot;`$T_COURSE->course.name`&quot;</span>" content = $smarty.capture.t_lesson_info_code}
    {elseif $smarty.get.fct == 'cartPreview'}
    	{assign var = "layout" value = "hideLeft hideRight"}
    	{eF_template_printBlock title = $smarty.const._SELECTEDLESSONS  content = $smarty.capture.t_selectedLessons_code}
    {else}
		{eF_template_printBlock title = $smarty.const._LESSONS  content = $smarty.capture.t_lessons_code}
	{/if}
{/capture}
{capture name = "right_code"}
	{eF_template_printBlock title = $smarty.const._SELECTEDLESSONS content = $smarty.capture.t_selectedLessons_code}
{/capture}

{*main table layout*}
<table class = "centerTable layout index {$layout}">
	<tr class = "topTitle"><td colspan = "3" class = "topTitle">{$title}</td></tr>
        {if $T_MESSAGE}
        	<tr><td colspan = "3">{eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}</td></tr>
        {elseif $smarty.get.message}
            <tr><td colspan = "3">{eF_template_printMessage message=$smarty.get.message type=$smarty.get.message_type}</td></tr>        
        {/if}
	<tr><td class = "left"></td>
		<td class = "center">{$smarty.capture.center_code}</td>
		<td class = "right">{$smarty.capture.right_code}</td></tr>
{if $T_CONFIGURATION.show_footer && !$smarty.get.popup && !$T_POPUP_MODE}
    {include file = "includes/footer.tpl"}
{/if}
</table>












{*
<table class = "mainTable">
    <tr><td style = "vertical-align: top;">
            <table  class = "layout index centerTable">
                <tr class = "topTitle"><td colspan = "2" class = "topTitle">{$title}</td></tr>
                <tr><td class = "singleColumn" id = "singleColumn" >
                        <table class = "singleColumnData">
                        	<tr><td class = "moduleCell">     
									{$smarty.capture.t_lessons_code}
                        	</td></tr>
                        </table>
                    </td>
                    <td class = "sideMenu" id = "sideMenu_td">
                    	{eF_template_printBlock title = $smarty.const._SELECTEDLESSONS content = $smarty.capture.t_selectedLessons_code}
                    </td></tr>
            </table>
        </td></tr>
{if $T_CONFIGURATION.show_footer && !$smarty.get.popup && !$T_POPUP_MODE}
    {include file = "includes/footer.tpl"}
{/if}
</table>
*}


{*Cart preview block*}
{*
{capture name = 't_cart_preview_code'}

    {foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $T_CART_LESSONS}
        <div style = "border-bottom:1px dotted #DDDDDD;padding:3px 0px 3px 0px;height:25px;vertical-align:middle">
        	<div style = "float:left"><a href = "directory.php?lessons_ID={$cartlist.id}" title = "{$cartlist.name}">{$cartlist.name}</a></div>
            <div style = "float:right">
                <span style = "vertical-align:middle">{if $cartlist.price == 0}{$smarty.const._FREEOFCHARGE}{else}{$cartlist.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</span>
                <a href = "javascript:void(0)" onclick = "ajaxPostRemove('{$cartlist.id}', this);">
                    <img src = "images/16x16/delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" style = "vertical-align:middle;border-width:0px;"></a>
            </div>
        </div>
        {assign var = "totalPrice" value = $totalPrice+$cartlist.price}
    {/foreach}
        <div style = "padding:3px 0px 8px 0px;height:25px;vertical-align:middle">
            <div style = "text-align:right">                        	
                <span style = "vertical-align:middle">{$smarty.const._PAYPALFINALPRICE}: {if $totalPrice == 0}{$smarty.const._FREEOFCHARGE}{else}{$totalPrice} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</span>
				<a href = "javascript:void(0)" onclick = "ajaxPostRemoveAll('', this);">
                	<img src = "images/16x16/delete.png" alt = "{$smarty.const._REMOVEALLFROMCART}" title = "{$smarty.const._REMOVEALLFROMCART}" style = "vertical-align:middle;border-width:0px;"></a>							
            </div>
        </div>
*}
{*	
	<table style = "width:100%">
		{if $T_CART_LESSONS_SIZE }
                {assign var = "totalPrice" value = 0}
                {foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $T_CART_LESSONS}
                    <tr class = "defaultRowHeight {cycle values = "oddRowColor,evenRowColor"}">
                    	<td><a href = "directory.php?lessons_ID={$cartlist.id}" title = "{$cartlist.name}">{$cartlist.name}</a></td>
                        <td style = "text-align:right;white-space:nowrap">
	                        <span style = "vertical-align:middle">{if $cartlist.price == 0}{$smarty.const._FREEOFCHARGE}{else}{$cartlist.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</span>
                            <a href = "javascript:void(0)" onclick = "ajaxPostRemove('{$cartlist.id}', this);">
                                <img src = "images/16x16/delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" style = "vertical-align:middle;border-width:0px;"></a>
                        </td></tr>
                    {assign var = "totalPrice" value = $totalPrice+$cartlist.price}
                {/foreach}
                	<tr class = "defaultRowHeight {cycle values = "oddRowColor,evenRowColor"}">
                		<td style = "text-align:right;width:100%;">{$smarty.const._PAYPALFINALPRICE}:</td>
                		<td>
							<span style = "vertical-align:middle">{if $totalPrice == 0}{$smarty.const._FREEOFCHARGE}{else}{$totalPrice} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</span>
							<a href = "javascript:void(0)" onclick = "ajaxPostRemoveAll('', this);">
                            	<img src = "images/16x16/delete.png" alt = "{$smarty.const._REMOVEALLFROMCART}" title = "{$smarty.const._REMOVEALLFROMCART}" style = "vertical-align:middle;border-width:0px;">
                        	</a>							
						</td></tr>	
					<tr class = "defaultRowHeight {cycle values = "oddRowColor,evenRowColor"}">
						<td colspan = "2" style = "text-align:center"><input class = "flatButton" type = "submit" value = "{$smarty.const._CONTINUE}"></td></tr>
		{else}
			<tr class = "defaultRowHeight oddRowColor"><td class = "centerAlign emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
		{/if}
	</table>
{/capture}
*}	






{*
{if preg_match("/index.php/i",$smarty.server.PHP_SELF)}
<script type="text/javascript">if (top.location.href != window.location.href) top.location.href = window.location.href</script>
{/if}
<table style = "width:100%">
	<tr class = "messageRow"><td>
        {if $T_MESSAGE}
            {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}
        {elseif $smarty.get.message}
            {eF_template_printMessage message=$smarty.get.message type=$smarty.get.message_type}        
        {/if}
	</td></tr>
</table>
{if $smarty.session.s_login}
	{assign var = "user_logged_in" value = "1"}
{else}
	{assign var = "user_logged_in" value = "0"}
{/if}

{capture name = 't_main_capture'}
    {if !$smarty.session.s_login}
    <table>
        <tr><td>
            <a href = "index.php" style = "vertical-align:middle"><img src = "images/16x16/arrow_left_blue.png" alt = "{$smarty.const._BACKTOINDEX}" title = "{$smarty.const._BACKTOINDEX}" style = "vertical-align:middle;border-width:0px"></a>&nbsp;
            <a href = "index.php" style = "vertical-align:middle">{$smarty.const._BACKTOINDEX}</a>
        </td></tr>
    </table>
    {/if}
    {if $smarty.session.s_type == 'student'}
        {literal}
        <script>
        function ajaxPost(id, el, login) {
            Element.extend(el);
            var baseUrl  =  '{/literal}{$smarty.server.PHP_SELF}{literal}?fct=addLessonToCart';
            var url      = baseUrl + '&id='+id;
            var img_id   = 'img_'+id;
            var img      = document.createElement("img");

            img.style.position = 'absolute';
            img.style.top      = Element.positionedOffset(Element.extend(el)).top  + -1 +'px';
            img.style.left     = Element.positionedOffset(Element.extend(el)).left + -36 + Element.getDimensions(Element.extend(el)).width + 'px';

            img.setAttribute("id", img_id);
            img.setAttribute('src', 'images/others/progress1.gif');

            el.parentNode.appendChild(img);

            new Ajax.Request(url, {
                method:'get',
                asynchronous:true,
                onSuccess: function (transport) {
                    $('cart').innerHTML = transport.responseText;
                    $('cart').down().setStyle({height: $('cart').getHeight() + 'px'});
                    img.style.display = 'none';
                    img.setAttribute('src', 'images/16x16/check.png');
                    new Effect.Appear(img_id);
                    window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                }
                });
        }
        function ajaxPostRemove(id) {
            var baseUrl  =  '{/literal}{$smarty.server.PHP_SELF}{literal}?fct=removeLessonFromCart';
            var url      = baseUrl + '&id='+id;
            new Ajax.Request(url, {
                method:'get',
                asynchronous:true,
                onSuccess: function (transport) {
                     $('cart').innerHTML = transport.responseText;
                }
                });
        }
        function ajaxPostRemoveAll(id) {
            var url  =  '{/literal}{$smarty.server.PHP_SELF}{literal}?fct=removeLessonAllFromCart';
            new Ajax.Request(url, {
                method:'get',
                asynchronous:true,
                onSuccess: function (transport) {
                     $('cart').innerHTML = transport.responseText;
                }
                });
        }
        </script>
        {/literal}
    {/if}

    {capture name = 't_cart_form'}
    <form action = "{$smarty.server.PHP_SELF}?fct=cartPreview" method = "post">
        <table border = "0" width = "100%">
	        <tr class="topTitle"><td class="topTitle" colspan="10">{$smarty.const._LESSONSLIST}</td></tr>
    	    <tr><td style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;">
                <div id="cart">
                    {if $T_CART_LESSONS_SIZE }
                    <table border = "0" width = "100%">
                    {assign var = "totalPrice" value = 0}
                    {foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $T_CART_LESSONS}
                        <tr>
                            <td width="70%">
                                <a href="directory.php?direction={$cartlist.did}&lesson={$cartlist.id}" title = "{$cartlist.name}">
                                {$cartlist.name|eF_truncate:23:"...":true}
                                </a>
                            </td>
                            <td align="right" width="30%">
                            {if $cartlist.price == 0}
                                {$smarty.const._FREEOFCHARGE}
                            {else}
                                {$cartlist.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}
                            {/if}
                            <a style="cursor:pointer;" onclick = "ajaxPostRemove('{$cartlist.id}', this);">
                                <img src = "images/16x16/delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" border = "0">
                            </a>
                            </td>
                        </tr>
                        {assign var = "totalPrice" value = $totalPrice+$cartlist.price}
                        {/foreach}
                        <tr>
                            <td colspan="2" align="right" style="background:#F8F8F8 none repeat scroll 0%;">
                            <b>{$smarty.const._PAYPALFINALPRICE}:
                            {if $totalPrice == 0}
                                {$smarty.const._FREEOFCHARGE}
                            {else}
                            {$totalPrice} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}
                            {/if}
                            </b>
                            <a style="cursor:pointer;" onclick = "ajaxPostRemoveAll('', this);">
                                <img src = "images/16x16/delete.png" alt = "{$smarty.const._REMOVEALLFROMCART}" title = "{$smarty.const._REMOVEALLFROMCART}" border = "0">
                            </a>
                            </td>
                        </tr>
                    	<tr><td colspan="2" align="right"><br /><input  class = "flatButton" name="cms_page" type="submit" value="{$smarty.const._CONTINUE}"></td></tr>
                    </table>
                    {else}
                    <table border = "0" width = "100%"><tr><td>{$smarty.const._NODATAFOUND}</td></tr></table>
                    {/if}
                </div>
        	</td></tr>
        </table>
    </form>
    {/capture}

{if !$user_logged_in}
    {capture name = 't_path'}
        <div style="background:#FCFCFC none repeat scroll 0%; padding: 5px 0 5px 0; margin: 5px 0 5px 0;">
        <a href="{$smarty.server.PHP_SELF}">{$smarty.const._HOME}</a>
        {foreach name = 'directions_path' key = 'key' item = 'directions_path' from = $T_DIRECTIONS_PATH}
            &raquo; <a href="{$smarty.server.PHP_SELF}?direction={$directions_path.id}">{$directions_path.name}</a>
        {/foreach}
        {if $smarty.get.lesson}
	        &raquo; <a href="{$smarty.server.PHP_SELF}?direction={$smarty.get.direction}&lesson={$smarty.get.lesson}">{$T_CURRENT_LESSON->lesson.name}</a>
        {/if}
        {if $smarty.get.course}
    	    &raquo; <a href="{$smarty.server.PHP_SELF}?direction={$smarty.get.direction}&course={$smarty.get.course}">{$T_CURRENT_COURSE->course.name}</a>
        {/if}
        </div>
    {/capture}
{else}
		{assign var = "lessons_title" value = ""}
        {foreach name = 'directions_path' key = 'key' item = 'directions_path' from = $T_DIRECTIONS_PATH}
            {assign var = "lessons_title" value = "`$lessons_title` &raquo; <a href = '`$smarty.server.PHP_SELF`?direction=`$directions_path.id`'>`$directions_path.name`</a>"}
        {/foreach}
        {if $smarty.get.lesson}
	        {assign var = "lessons_title" value = "`$lessons_title` &raquo; <a href = '`$smarty.server.PHP_SELF`?direction=`$smarty.get.direction`&lesson=`$smarty.get.lesson`'>`$T_CURRENT_LESSON->lesson.name`</a>"}
        {/if}
        {if $smarty.get.course}
	        {assign var = "lessons_title" value = "`$lessons_title` &raquo; <a href = '`$smarty.server.PHP_SELF`?direction=`$smarty.get.direction`&course=`$smarty.get.course`'>`$T_CURRENT_COURSE->course.name`</a>"}
        {/if}
{/if}

    {capture name = 't_list_of_lessons'}
    {if $T_DIRECTIONS_LESSONS_NUM > 0}
        <table border = "0" width = "100%">
        <tr class="topTitle">
            <td class = "topTitle">{$smarty.const._LESSONNAME}</td>
            <td class = "topTitle">{$smarty.const._LANGUAGE}</td>
            <td class = "topTitle centerAlign">{$smarty.const._PRICE}</td>
            <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
        </tr>
        {foreach name = 'lessons_list' key = 'key' item = 'lessons_list' from = $T_DIRECTIONS_LESSONS}
            {if $lessons_list->lesson.active == 1}
            <tr class = "{cycle name = "su_info" values = "oddRowColor, evenRowColor"}">
                <td>{$lessons_list->lesson.link}</td>
                <td>{assign var = "language" value = $lessons_list->lesson.languages_NAME}{$T_LANGUAGES.$language}</td>
                <td class = "centerAlign">
                    {if $lessons_list->lesson.price == 0}
                        {$smarty.const._FREEOFCHARGE}
                    {else}
                        {$lessons_list->lesson.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}
                    {/if}
                </td>
                <td class = "centerAlign">
                {if $smarty.session.s_type == 'student' && $lessons_list->lesson.my == '0'}
                    {if $lessons_list->lesson.price == 0 || ($lessons_list->lesson.price > 0 && ($T_CONFIGURATION.paypal == '1' && $T_CONFIGURATION.version_type == 'educational'))}
                    <a style="cursor:pointer;" onclick = "ajaxPost('{$lessons_list->lesson.id}', this, '{$smarty.session.s_type}');">
                        <img src = "images/16x16/cart-add.png" alt = "{$smarty.const._ADDTOLESSONS}" title = "{$smarty.const._ADDTOLESSONS}" border = "0">
                    </a>
                    {/if}
                {/if}
                    <a href = "{$smarty.server.PHP_SELF}?direction={$lessons_list->lesson.directions_ID}&lesson={$lessons_list->lesson.id}" class = "editLink">
                    <img src = "images/16x16/information2.png" alt = "{$smarty.const._LESSONINFORMATION}" title = "{$smarty.const._LESSONINFORMATION}" border = "0">
                    </a>
                </td>
            </tr>
            {/if}
        {/foreach}
        </table>
        <br />
    {/if}
    {/capture}

    {capture name = 't_list_of_directions'}
    {if $T_DIRECTIONS_DATA_NUM > 0}
        <table border = "0" width = "100%" sortBy = "0">
        <tr class="topTitle"><td class="topTitle">{$smarty.const._DIRECTIONS}</td></tr>
        {foreach name = 'directions_list' key = 'key' item = 'direction' from = $T_DIRECTIONS_DATA}
            {if $direction.active == 1}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td><a href = "{$smarty.server.PHP_SELF}?direction={$direction.id}" class = "editLink">{$direction.name}</a> ({$direction.lessons})</td>
                </tr>
            {/if}
        {/foreach}
        </table><br /><br />
    {/if}
    {/capture}

    {capture name = 't_list_of_courses'}
    {if $T_COURSE_DATA_NUM > 0}
        <table border = "0" width = "100%" sortBy = "0">
            <tr class="topTitle">
                <td class = "topTitle">{$smarty.const._COURSENAME}</td>
                <td class = "topTitle">{$smarty.const._LANGUAGE}</td>
                <td class = "topTitle centerAlign">{$smarty.const._PRICE}</td>
                <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
            </tr>
            {foreach name = 'courseslist' key = 'key' item = 'courseslist' from = $T_COURSE_DATA}
            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                <td>{$courseslist->course.link}</td>
                <td>{assign var = "language" value = $courseslist->course.languages_NAME}{$T_LANGUAGES.$language}</td>
                <td class = "centerAlign">
                {if $courseslist->course.price == 0}
                    {$smarty.const._FREEOFCHARGE}
                {else}
                    {$courseslist->course.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}
                {/if}
                </td>
                <td class = "centerAlign">
                {if $smarty.session.s_type == 'student' && $courseslist->course.my == '0'}
                    {if $courseslist->course.price == 0 || ($courseslist->course.price > 0 && ($T_CONFIGURATION.paypal == '1' && $T_CONFIGURATION.version_type == 'educational'))}
                    <a style="cursor:pointer;" onclick = "ajaxPost('{$courseslist->course.id+100000}', this, '{$smarty.session.s_type}');">
                        <img src = "images/16x16/cart-add.png" alt = "{$smarty.const._ADDTOLESSONS}" title = "{$smarty.const._ADDTOLESSONS}" border = "0">
                    </a>
                    {/if}
                {/if}
                    <a href = "{$smarty.server.PHP_SELF}?direction={$courseslist->course.directions_ID}&course={$courseslist->course.id}" class = "editLink">
                        <img src = "images/16x16/information2.png" alt = "{$smarty.const._EDITCOURSE}" title = "{$smarty.const._EDITCOURSE}" border = "0">
                    </a>
                </td>
            </tr>
            {/foreach}
        </table><br />
    {/if}
    {/capture}

    {capture name = 't_search_form'}
        <form action = "{$smarty.server.PHP_SELF}?fct=searchResults" method = "post">
        <table border = "0" width = "220">
                <tr class="topTitle"><td class="topTitle" colspan="10">{$smarty.const._SEARCH}</td></tr>
                <tr>
                    <td style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;" align="center">
                    <p><input class="inputSearchText" type="text" name="name"/><br />
                    <br /><input  class = "flatButton" name="cms_page" type="submit" value="{$smarty.const._SEARCH}"></p>
                    </td>
                </tr>
        </table>
        </form>
    {/capture}

    {capture name = 't_search_results'}
        <p>{$T_TITLE_SEARCH}: <b>{$smarty.post.name}</b>
        {if $T_DIRECTIONS_LESSONS_NUM > 0 || $T_COURSE_DATA_NUM > 0 || $T_DIRECTIONS_DATA_NUM > 0}
            {if $T_DIRECTIONS_LESSONS_NUM+$T_COURSE_DATA_NUM+$T_DIRECTIONS_DATA_NUM > 1}
            ({$T_DIRECTIONS_LESSONS_NUM+$T_COURSE_DATA_NUM+$T_DIRECTIONS_DATA_NUM} {$smarty.const._RESULTS})</p>
            {else}
            ({$T_DIRECTIONS_LESSONS_NUM+$T_COURSE_DATA_NUM+$T_DIRECTIONS_DATA_NUM} {$smarty.const._RESULT})</p>
            {/if}
        {elseif $T_DIRECTIONS_LESSONS_NUM == 0 && $T_COURSE_DATA_NUM == 0 && $T_DIRECTIONS_DATA_NUM == 0}
            </p>
            <div style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;">
            {$smarty.const._NORESULTSFOUND}
            </div>
        {/if}
        {$smarty.capture.t_list_of_directions}
        {$smarty.capture.t_list_of_lessons}
        {$smarty.capture.t_list_of_courses}        
    {/capture}

    {capture name = 't_lesson_view'}
            <table border = "0" width = "100%">
                <tr class="topTitle"><td class="topTitle" colspan="2">{$T_CURRENT_LESSON->lesson.name}</td></tr>
                {if $T_CURRENT_LESSON_INFO_NUM > 0}
                <tr><td style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;"  colspan="2"><b>{$smarty.const._INFO}</b></td></tr>
                <tr>
                    <td valign="top"  colspan="2">
                    {if $T_CURRENT_LESSON_INFO.general_description}
                        <strong>{$smarty.const._GENERALDESCRIPTION}:</strong> {$T_CURRENT_LESSON_INFO.general_description}<br />
                    {/if}
                    {if $T_CURRENT_LESSON_INFO.assessment}<strong>{$smarty.const._ASSESSMENT}:</strong> {$T_CURRENT_LESSON_INFO.assessment}<br />{/if}
                    {if $T_CURRENT_LESSON_INFO.objectives}<strong>{$smarty.const._OBJECTIVES}:</strong> {$T_CURRENT_LESSON_INFO.objectives}<br />{/if}
                    {if $T_CURRENT_LESSON_INFO.lesson_topics}<strong>{$smarty.const._LESSONTOPICS}:</strong> {$T_CURRENT_LESSON_INFO.lesson_topics}<br />{/if}
                    {if $T_CURRENT_LESSON_INFO.resources}<strong>{$smarty.const._RESOURCES}:</strong> {$T_CURRENT_LESSON_INFO.resources}<br />{/if}
                    {if $T_CURRENT_LESSON_INFO.other_info}<strong>{$smarty.const._OTHERINFO}:</strong> {$T_CURRENT_LESSON_INFO.other_info}<br />{/if}
                    <br />
                    </td>
                <tr>
                {/if}
                <tr><td style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;"  colspan="2"><b>{$smarty.const._CURRENTCONTENT}</b></td></tr>
                <tr>
                    <td valign="top" width = "50%" colspan="2">
                    <table border = "0" width = "100%">
                    {foreach name = 'name' key = 'key' item = 'item' from = $T_CURRENT_LESSON_TREE}
                    <tr><td>{$item}</td></tr>
                    {/foreach}
                    </table>
                    <br />
                    </td>
                </tr>
                <tr style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;">
                    <td align="right" width="98%">
                            <div style="font-size:18px;">

                            {if $T_CURRENT_LESSON->lesson.price == 0}
                                {$smarty.const._FREEOFCHARGE}
                            {else}
                                {$T_CURRENT_LESSON->lesson.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}
                            {/if}
                            </div>
                    </td>
                    {if $smarty.session.s_type == 'student' && $T_CURRENT_LESSON->lesson.my == '0'}
                    <td align="right">
                            {if $T_CURRENT_LESSON->lesson.price == 0 || ($T_CURRENT_LESSON->lesson.price > 0 && ($T_CONFIGURATION.paypal == '1' && $T_CONFIGURATION.version_type == 'educational'))}
                            <a style="cursor:pointer;" onclick = "ajaxPost('{$smarty.get.lesson}', this, '{$smarty.session.s_type}');">
                                <img src = "images/32x32/add2.png" alt = "{$smarty.const._ADDTOLESSONS}" title = "{$smarty.const._ADDTOLESSONS}" border = "0">
                            </a>
                            {/if}
                    </td>
                    {/if}
                </tr>
            </table>
    {/capture}

    {capture name = 't_course_view'}
            <table border = "0" width = "100%">
                <tr>
                    <td class = "topTitle" colspan = "2">{$T_CURRENT_COURSE->course.name}</td></tr>
                {if $T_CURRENT_COURSE_INFO_NUM > 0}
                <tr><td style = "background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;"  colspan="2"><b>{$smarty.const._INFO}</b></td></tr>
                <tr>
                    <td valign="top"  colspan="2">
                    {if $T_CURRENT_COURSE_INFO.general_description}
                        <strong>{$smarty.const._GENERALDESCRIPTION}:</strong> {$T_CURRENT_COURSE_INFO.general_description}<br />
                    {/if}
                    {if $T_CURRENT_COURSE_INFO.assessment}<strong>{$smarty.const._ASSESSMENT}:</strong> {$T_CURRENT_COURSE_INFO.assessment}<br />{/if}
                    {if $T_CURRENT_COURSE_INFO.objectives}<strong>{$smarty.const._OBJECTIVES}:</strong> {$T_CURRENT_COURSE_INFO.objectives}<br />{/if}
                    {if $T_CURRENT_COURSE_INFO.lesson_topics}<strong>{$smarty.const._LESSONTOPICS}:</strong> {$T_CURRENT_COURSE_INFO.lesson_topics}<br />{/if}
                    {if $T_CURRENT_COURSE_INFO.resources}<strong>{$smarty.const._RESOURCES}:</strong> {$T_CURRENT_COURSE_INFO.resources}<br />{/if}
                    {if $T_CURRENT_COURSE_INFO.other_info}<strong>{$smarty.const._OTHERINFO}:</strong> {$T_CURRENT_COURSE_INFO.other_info}<br />{/if}
                    <br />
                    </td>
                <tr>
                {/if}
                <tr><td style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;"  colspan="2"><b>{$smarty.const._COURSELESSONS}</b></td></tr>
                {foreach name = 'course_lessons_list' key = "id" item = "lesson" from = $T_CURRENT_COURSE->lessons}
                <tr><td>{$lesson.name}</td></tr>
                {/foreach}
                <tr style = "background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;">
                    <td align = "right" width = "98%">
                            <div style = "font-size:18px;">
                            {if $T_CURRENT_LESSON->lesson.price == 0}
                                {$smarty.const._FREEOFCHARGE}
                            {else}
                                {$T_CURRENT_COURSE->course.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}
                            {/if}
                            </div>
                    </td>
                    {if $smarty.session.s_type == 'student' && $T_CURRENT_COURSE->course.my == '0'}
                    <td align="right">
                            {if $T_CURRENT_COURSE->course.price == 0 || ($T_CURRENT_COURSE->course.price > 0 && ($T_CONFIGURATION.paypal == '1' && $T_CONFIGURATION.version_type == 'educational'))}
                            <a style="cursor:pointer;" onclick = "ajaxPost('{$smarty.get.course+100000}', this, '{$smarty.session.s_type}');">
                                <img src = "images/32x32/add2.png" alt = "{$smarty.const._ADDTOLESSONS}" title = "{$smarty.const._ADDTOLESSONS}" border = "0">
                            </a>
                            {/if}
                    </td>
                    {/if}
                </tr>
            </table>
    {/capture}
    
    {capture name = 't_login_form'}
        {$T_LOGIN_FORM.javascript}
        <form {$T_LOGIN_FORM.attributes}>
        <div>{$T_LOGIN_FORM.hidden}</div>
        <table border = "0" width = "220">
                <tr class="topTitle"><td class="topTitle" colspan="10">{$smarty.const._EFRONTLOGIN}</td></tr>
                <tr>
                    <td style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;" align="left">
                    <table>
                    <tr><td>{$smarty.const._LOGIN}:</td><td><input name="login" type="text" /></td></tr>
                    <tr><td>{$smarty.const._PASSWORD}:</td><td><input name="password" type="password" /></td></tr>
                    <tr><td>{$smarty.const._REMEMBERME}:</td><td>{$T_LOGIN_FORM.remember.html}</td></tr>
                    <tr><td></td><td>{$T_LOGIN_FORM.submit_login.html}</td></tr>
                    <tr><td colspan="2" align="center"><a href = "{$smarty.server.PHP_SELF}?ctg=reset_pwd{$T_BYPASSLANG}">{$smarty.const._PASSWORDLOST}</a></td></tr>
                    </table>
                    </td>
                </tr>
        </table>
        </form>
    {/capture}
    
    {capture name = 't_latest_news'}
    {if sizeof($T_NEWS) > 0}
        <table border = "0" width = "100%">
            <tr class="topTitle"><td class="topTitle" colspan="10">{$smarty.const._SYSTEMANNOUNCEMENTS}</td></tr>
            {assign var="news_counter"  value = "0"}
            {if isset($smarty.get.ctg) && $smarty.get.ctg == "news"}
                {assign var="news_counter_limit"  value = "10"}
            {else}
                {assign var="news_counter_limit"  value = "3"}
            {/if}
            {foreach name = 'news_list' key = key item = item from = $T_NEWS}
            {if $news_counter < $news_counter_limit}
            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                <td>
                    <b>{$item.title}</b>
                    <div class="small">#filter:timestamp_time-{$item.timestamp}#</div>
                    <p>{$item.data}</p>
                </td>
            </tr>
            {/if}
            {assign var="news_counter"  value = $news_counter+1}
            {/foreach}
        </table>
        <br>
    {/if}
    {/capture}
    
    {capture name = 't_contact'}
        {$T_CONTACT_FORM.javascript}
        <table border = "0" width = "100%">
            <tr class="topTitle"><td class="topTitle" colspan="10">{$smarty.const._CONTACT}</td></tr>
            <tr>
                <td style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;" align="center">
                <form {$T_CONTACT_FORM.attributes}>
                    <div>{$T_CONTACT_FORM.hidden}</div>
                    <table class = "formElements" align="center" width="100%">
                        <tr><td class = "labelCell">{$T_CONTACT_FORM.email.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_CONTACT_FORM.email.html}</td></tr>
                        {if $T_CONTACT_FORM.email.error}<tr><td></td><td class = "formError">{$T_CONTACT_FORM.email.error}</td></tr>{/if}

                        <tr><td class = "labelCell">{$T_CONTACT_FORM.message_subject.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_CONTACT_FORM.message_subject.html}</td></tr>
                        {if $T_CONTACT_FORM.message_subject.error}<tr><td></td><td class = "formError">{$T_CONTACT_FORM.message_subject.error}</td></tr>{/if}

                        <tr><td class = "labelCell">{$T_CONTACT_FORM.message_body.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_CONTACT_FORM.message_body.html}</td></tr>

                        <tr><td></td><td class = "formRequired">{$T_CONTACT_FORM.requirednote}</td></tr>

                        <tr><td></td></td><td class = "submitCell">
                                 {eF_template_printBackButton}
                                {$T_CONTACT_FORM.submit_contact.html}</td></tr>
                    </table>
                </form>
                </td>
            </tr>
        </table>
    {/capture}
    
    {capture name = 't_reset_pwd'}
        {$T_RESET_PASSWORD_FORM.javascript}
        <table border = "0" width = "100%">
            <tr class="topTitle"><td class="topTitle" colspan="10">{$smarty.const._RESETPASSWORD}</td></tr>
            <tr>
                <td style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;" align="center">
                <form {$T_RESET_PASSWORD_FORM.attributes}>
                    <div>{$T_RESET_PASSWORD_FORM.hidden}</div>
                    <table class = "loginForm" width="100%">
                        <tr><td class = "labelCell">{$T_RESET_PASSWORD_FORM.login_or_pwd.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_RESET_PASSWORD_FORM.login_or_pwd.html}</td></tr>
                        {if $T_RESET_PASSWORD_FORM.login_or_pwd.error}<tr><td></td><td class = "formError">{$T_RESET_PASSWORD_FORM.login_or_pwd.error}</td></tr>{/if}

                        <tr><td>&nbsp;</td></tr>
                        <tr><td></td><td class = "submitCell leftAlign">
                               {eF_template_printBackButton} {$T_RESET_PASSWORD_FORM.submit_reset_password.html}</td>
                </tr>
                    </table>
                </form>
                </td>
            </tr>
        </table>
    {/capture}
    
    {capture name = 't_signup'}
        {$T_RESET_PASSWORD_FORM.javascript}
        <table border = "0" width = "100%">
            <tr class="topTitle"><td class="topTitle" colspan="10">{$smarty.const._REGISTERANEWACCOUNT}</td></tr>
            <tr>
                <td style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;" align="center">
                <form {$T_PERSONAL_INFO_FORM.attributes}>
                    <div>{$T_PERSONAL_INFO_FORM.hidden}</div>
                    <table class = "formElements" width="100%">
                        <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.languages_NAME.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_PERSONAL_INFO_FORM.languages_NAME.html}</td></tr>

                        <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>

                        <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.login.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_PERSONAL_INFO_FORM.login.html}</td></tr>
                        <tr><td></td><td class = "infoCell">{$smarty.const._ONLYALLOWEDCHARACTERSLOGIN}</td></tr>
                        {if $T_PERSONAL_INFO_FORM.login.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.login.error}</td></tr>{/if}

                        <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.password.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_PERSONAL_INFO_FORM.password.html}</td></tr>
                        <tr><td></td><td class = "infoCell">{$smarty.const._PASSWORDMUSTBE6CHARACTERS}</td></tr>
                        {if $T_PERSONAL_INFO_FORM.password.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.password.error}</td></tr>{/if}

                        <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.passrepeat.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_PERSONAL_INFO_FORM.passrepeat.html}</td></tr>
                        {if $T_PERSONAL_INFO_FORM.passrepeat.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.passrepeat.error}</td></tr>{/if}

                        <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>

                        <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.email.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_PERSONAL_INFO_FORM.email.html}</td></tr>
                        {if $T_PERSONAL_INFO_FORM.email.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.email.error}</td></tr>{/if}

                        <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.firstName.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_PERSONAL_INFO_FORM.firstName.html}</td></tr>

                        <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.lastName.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_PERSONAL_INFO_FORM.lastName.html}</td></tr>

            {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_FIELDS }
                        <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.$item.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_PERSONAL_INFO_FORM.$item.html}</td></tr>
                        {if $T_PERSONAL_INFO_FORM.$item.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.$item.error}</td></tr>{/if}
            {/foreach}

                        <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.comments.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_PERSONAL_INFO_FORM.comments.html}</td></tr>

                        <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>

                            <tr><td></td><td class = "formRequired">{$T_PERSONAL_INFO_FORM.requirednote}</td></tr>

                        <tr><td></td><td class = "submitCell leftAlign"><br>
     {eF_template_printBackButton} {$T_PERSONAL_INFO_FORM.submit_register.html} </td></tr>
                    </table>
                </form>
                </td>
            </tr>
        </table>
    {/capture}



<table class="innerTable" border = "0" width = "100%" cellpadding="2" cellspacing="0">
<tbody>
    {if (!isset($smarty.session.s_login) && $T_CONFIGURATION.interface_view == 2) || preg_match("/index.php/i",$smarty.server.PHP_SELF)}

    <tr>
        <td colspan="3">
        <table border = "0" width = "100%" cellpadding="2" cellspacing="0">
            <tr>
                <td align = "left" style = "vertical-align:middle;white-space:nowrap;">
                    <a href = "{$T_LOGOLINK}"><img src = "images/{$T_LOGO}" border="0" style = "vertical-align:middle"/></a>
                </td>
                <td style = "width:100%">
                    <span style = "font-size:24px;vertical-align:middle">{if $T_CONFIGURATION.site_name}{$T_CONFIGURATION.site_name}{else}{$smarty.const._EFRONT}{/if}</span>
                    <br/><span style = "font-size:12px">{if $T_CONFIGURATION.site_moto}{$T_CONFIGURATION.site_moto}{else}{$smarty.const._THENEWFORMOFADDITIVELEARNING}{/if}</span>
                </td>
            </tr>
            <tr>
                <td style="background:#ffffff none repeat scroll 0%; border:1px dotted #D0D0D0;" align="left" colspan="3" width = "100%">
                <table cellpadding="0" cellspacing="0" width = "100%">
                    <tr style="background:url(images/logo-right_bg.png);">
                        <td valign="top" width="90%">
                            <form action="{$smarty.server.PHP_SELF}" method="get">
                            <div style="margin:5px; float:left;">
                                <a href="{$smarty.server.PHP_SELF}">{$smarty.const._HOME}</a> |
                                {if $T_EXTERNALLYSIGNUP && !$T_ONLY_LDAP}
                                    <a href = "{$smarty.server.PHP_SELF}?ctg=signup{$T_BYPASSLANG}">{$smarty.const._REGISTERANEWACCOUNT}</a>
                                {else}
                                    <a href = "#" title = "
                                    {if $T_ONLY_LDAP}{$smarty.const._USELDAPACCOUNT}
                                    {else}
                                    {$smarty.const._YOUMAYNOTSIGNUPCONTACTADMINISTRATOR}
                                    {/if}
                                    ">{$smarty.const._REGISTERANEWACCOUNT}</a>
                                {/if}
                                 | <select name="bypass_language" onchange="this.form.submit();">
                                {assign var="selected"  value = ''}
                                    {foreach name = 'news_list' key = key item = item from = $T_LANGUAGES}
                                    {if (isset($smarty.get.bypass_language) && $smarty.get.bypass_language == $key) || (!isset($smarty.get.bypass_language) && $T_LANGUAGES_DEFAULT == $key)}
                                        {assign var="selected"  value = ' selected="selected"'}
                                    {/if}
                                    <option{$selected} value="{$key}">{$item}</option>
                                    {assign var="selected"  value = ''}
                                    {/foreach}
                                </select> |
                                <a href="{$smarty.server.PHP_SELF}?ctg=news">{$smarty.const._SYSTEMANNOUNCEMENTS}</a> | <a href="{$smarty.server.PHP_SELF}?ctg=contact">{$smarty.const._CONTACT}</a>
                            </div>
                            </form>
                        </td>
                        <td align="right" width="10%">#filter:timestamp-{$T_TIMESTAMP}#&nbsp;&nbsp;
                        </td>
                    </tr>
                </table>
                </td>
            </tr>
        </table>
        </td>
    </tr>
    {/if}
    <tr>
        {if (!isset($smarty.session.s_login) && $T_CONFIGURATION.interface_view == 2) || preg_match("/index.php/i",$smarty.server.PHP_SELF)}
        <td valign="top" width="220">
            {$smarty.capture.t_login_form}
        </td>
        {/if}
        <td valign="top" width="100%">
        {if $T_CONFIGURATION.lessons_directory == '2' || (isset($smarty.session.s_login) && $T_CONFIGURATION.lessons_directory != '3')}
            {if $smarty.get.direction || $smarty.get.course || $smarty.get.fct == 'searchResults'}
                {$smarty.capture.t_path}
            {/if}
        {/if}
        {if $T_CONFIGURATION.interface_view == 2}
        {if (sizeof($smarty.get) == 0 && sizeof($smarty.post) == 0) || (isset($smarty.get.index_efront) || isset($smarty.get.message) || isset($smarty.get.logout) ||
            isset($smarty.get.bypass_language) && !isset($smarty.get.ctg))  || (isset($smarty.get.ctg) && ($smarty.get.ctg == "news"))}
            {$smarty.capture.t_latest_news}
        {/if}
        {/if}
        {if $smarty.get.ctg == 'contact'}
            {$smarty.capture.t_contact}
        {/if}
        {if $smarty.get.ctg == 'reset_pwd'}
            {$smarty.capture.t_reset_pwd}
        {/if}
        {if $smarty.get.ctg == 'signup'}
            {$smarty.capture.t_signup}
        {/if}


        {if ($T_CONFIGURATION.lessons_directory == '2') || (isset($smarty.session.s_login)  && $T_CONFIGURATION.lessons_directory == '1')}
            {if $smarty.get.fct != 'searchResults' && !isset($smarty.get.ctg)}
                
            {/if}

            {if $smarty.get.fct == 'searchResults'}
                {$smarty.capture.t_search_results}
            {/if}
            {if !isset($smarty.get.ctg)}
            {$T_DIRECTIONS_TREE}
            {if $smarty.get.lesson}
                {$smarty.capture.t_lesson_view}
            {/if}

            {if $smarty.get.course}
                {$smarty.capture.t_course_view}
            {/if}
            {/if}
        {/if}
        </td>
        {if ($T_CONFIGURATION.lessons_directory == '2') || (isset($smarty.session.s_login)  && $T_CONFIGURATION.lessons_directory == '1')}
        <td valign="top" width = "220px">
            {$smarty.capture.t_search_form} <br />
            {if $smarty.session.s_type == 'student'}
                {$smarty.capture.t_cart_form}
            {/if}
        </td>
        {/if}
    </tr>
</tbody>
</table>
{/capture}

{capture name = 't_cart_preview'}
    {if $T_SESSION_DATA >0}
        <table style = "width:100%" size = "{$T_LESSONS_SIZE}" id = "lessonsTable">
            <tr class = "topTitle">
            <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
            <td class = "topTitle" name = "price">{$smarty.const._PRICE}</td>
            </tr>
        {foreach name = 'name' key = 'key' item = 'item' from = $T_LESSONS_DATA}
        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
            <td>{$item.name}</td><td>
            {if $item.price == 0}
                {$smarty.const._FREEOFCHARGE}
            {else}
                {$item.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}
            {/if}
        </td>
        </tr>
        {/foreach}
        <tr style="height:25px; background:#D3D3D3 none repeat scroll 0%; border-bottom:1px solid #AAAAAA; border-top:1px solid #AAAAAA;">
            <td align="right"><b>{$smarty.const._PAYPALFINALPRICE}:</b> </td><td><b>
            {if $T_LESSONS_FINAL_PRICE == 0}
                {$smarty.const._FREEOFCHARGE}
            {else}
                {$T_LESSONS_FINAL_PRICE} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}
            {/if}

            </b></td>
        </tr>
        </table>
        {if $T_LESSONS_FINAL_PRICE == 0 || ($T_LESSONS_FINAL_PRICE > 0 && ($T_CONFIGURATION.paypal == '1' && $T_CONFIGURATION.version_type == 'educational'))}
            <form {$T_ORDER_LESSONS_FORM.attributes}>
                {$T_ORDER_LESSONS_FORM.hidden}
                <div align="center" style="padding: 15px;">
                {$T_ORDER_LESSONS_FORM.order.html}
                </div>
            </form>
            <form {$T_ORDER_LESSONS_FORMDATA.attributes}>
                {$T_ORDER_LESSONS_FORMDATA.hidden}
            </form>
        {else}
            {$smarty.const._ERROROCCURED}
        {/if}
    {else}
        <table border = "0" width = "100%"><tr><td>{$smarty.const._NOCART}</td></tr></table>
    {/if}
{/capture}


{assign var = "title" value = "<a class = 'titleLink' href = 'student.php?ctg=lessons'>`$smarty.const._MYLESSONS`</a>"}
{capture name = "t_directory"}
    {if $smarty.get.fct == 'cartPreview'}
    	{assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?fct=cartPreview">'|cat:$smarty.const._LESSONSLIST|cat:'</a>'}
        {if isset($smarty.session.s_login)}
            {eF_template_printInnerTable title = $smarty.const._LESSONSLIST data = $smarty.capture.t_cart_preview image = '32x32/cart.png'}
        {else}
            {$smarty.capture.t_cart_preview}
        {/if}
    {elseif $smarty.get.fct == 'searchResults'}
    	{assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?fct=searchResults">'|cat:$smarty.const._SEARCH|cat:'</a>'}
        {if isset($smarty.session.s_login)  || !preg_match("/index.php/i",$smarty.server.PHP_SELF)}
            {eF_template_printInnerTable title = $smarty.const._SEARCH data = $smarty.capture.t_main_capture image = '32x32/find_text.png'}
        {else}
            {$smarty.capture.t_main_capture}
        {/if}
    {else}
    	{assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'">'|cat:$smarty.const._LESSONSDIRECTORY|cat:'</a>'|cat:$lessons_title}
        {if (isset($smarty.session.s_login) && $T_INDEXPAGE == 0) || $T_INDEXPAGE == 0}
            {eF_template_printInnerTable title = $smarty.const._LESSONSDIRECTORY data = $smarty.capture.t_main_capture image = '32x32/cabinet.png' link = directory.php}
        {else}
            {$smarty.capture.t_main_capture}
        {/if}
    {/if}
{/capture}

{if $user_logged_in}
    <table class = "mainTable">
        <tr>
            <td style = "vertical-align: top;">
                <table  class = "centerTable">
                    <tr class = "topTitle">
                        <td colspan = "2" class = "topTitle">{$title}</td>
                   </tr>
                    <tr>
                        <td class = "singleColumn" id = "singleColumn" >
                            <table class = "singleColumnData">
                            	<tr><td class = "moduleCell">     
    									{$smarty.capture.t_directory}
                            	</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    {if $T_CONFIGURATION.show_footer && !$smarty.get.popup && !$T_POPUP_MODE}
        {include file = "includes/footer.tpl"}
    {/if}
    </table>
{else}
	{$smarty.capture.t_directory}
{/if}
*}

{include file = "includes/closing.tpl"}
