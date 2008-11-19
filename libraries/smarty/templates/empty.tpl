{*smarty template for empty.php*}
{include file = "includes/header.tpl"}

{capture name = 't_main_capture}
	{literal}
	<script>
	function ajaxPost(id) {
		var baseUrl  =  '{/literal}{$smarty.server.PHP_SELF}{literal}?fct=addLessonToCart';
		var url      = baseUrl + '&id='+id;
		new Ajax.Request(url, {
			method:'get',
			asynchronous:true,
			onSuccess: function (transport) {  
			     $('cart').innerHTML = transport.responseText;
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

	{capture name = 't_cart_form'}
		<table border = "0" width = "100%">
		<tr class="topTitle"><td class="topTitle" colspan="10">{$smarty.const._CART}</td></tr>
		<tr><td style="background:#F1F1F1 none repeat scroll 0%; border:1px dotted #D0D0D0;">
		<div id="cart">
			{if $T_CART_LESSONS_SIZE }
			<table border = "0" width = "100%">
			{assign var = "totalPrice" value = 0}
			{foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $T_CART_LESSONS}	
			<tr>
				<td>{$cartlist.name}</td>
				<td align="right">
				{$cartlist.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]} 
				<a style="cursor:pointer;" onclick = "ajaxPostRemove('{$cartlist.id}', this);"><img src = "images/16x16/cart_delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" border = "0"></a>
				</td>
			</tr>
			{assign var = "totalPrice" value = $totalPrice+$cartlist.price}
			{/foreach}
			<tr>
				<td colspan="2" align="right" style="background:#F8F8F8 none repeat scroll 0%;">
				<b>{$smarty.const._PAYPALFINALPRICE}: {$totalPrice} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</b>
				<a style="cursor:pointer;" onclick = "ajaxPostRemoveAll('', this);"><img src = "images/16x16/cart_delete.png" alt = "{$smarty.const._REMOVEALLFROMCART}" title = "{$smarty.const._REMOVEALLFROMCART}" border = "0"></a>
				</td>
			</tr>
			<tr><td colspan="2" align="right"><br /><input  class = "flatButton" name="cms_page" type="submit" value="{$smarty.const._PAYPALPAYNOW}"></td></tr>
			</table>
			{else}
			<table border = "0" width = "100%"><tr><td>{$smarty.const._NOCART}</td></tr></table>
			{/if}
		</div>
		</td></tr>
		</table>
	{/capture}

	{capture name = 't_path'}
		<p><strong>{$T_TITLE}</strong></p>
		<div style="background:#FCFCFC none repeat scroll 0%; padding: 5px 0 5px 0; margin: 0 0 5px 0;">
		<a href="{$smarty.server.PHP_SELF}">{$smarty.const._HOME}</a>
		{foreach name = 'directions_path' key = 'key' item = 'directions_path' from = $T_DIRECTIONS_PATH}
			&raquo; 
			{if $directions_path.id == $smarty.get.direction && !$smarty.get.lesson}
				{$directions_path.name}
			{else}
				<a href="empty.php?direction={$directions_path.id}">{$directions_path.name}</a>
			{/if}
		{/foreach}
		{if $smarty.get.lesson}
		&raquo; <a href="empty.php?direction={$smarty.get.direction}&lesson={$smarty.get.lesson}">{$T_CURRENT_LESSON}</a>
		{/if}
		</div>
	{/capture}

	{capture name = 't_list_of_lessons'}
		<table border = "0" width = "100%">
		<tr class="topTitle"><td class="topTitle" colspan="10">{$smarty.const._LESSONS}</td></tr>
		
		{foreach name = 'lessons_list' key = 'key' item = 'lessons_list' from = $T_DIRECTIONS_LESSONS}
			{if $lessons_list->lesson.active == 1}
				<tr class = "oddRowColor">
					<td>
					<table border = "0" width = "100%">
						<tr><td><a href = "{$smarty.server.PHP_SELF}?direction={$smarty.get.direction}&lesson={$lessons_list->lesson.id}" class = "editLink">{$lessons_list->lesson.name}</a></td><td colspan="3"></td></tr>
						<tr class="evenRowColor"><td width="10%"></td>
							<td align="left">
								<div style="margin:1px;"><a style="cursor:pointer;" onclick = "ajaxPost('{$lessons_list->lesson.id}', this);"><img src = "images/16x16/cart_add.png" alt = "{$smarty.const._ADDTOCART}" title = "{$smarty.const._ADDTOCART}" border = "0"></a> {$smarty.const._ADDTOCART}</div>
								<div style="margin:1px;"><a href=""><img src = "images/16x16/information2.png" alt = "{$smarty.const._LESSONINFORMATION}" title = "{$smarty.const._LESSONINFORMATION}" border = "0"></a> {$smarty.const._LESSONINFORMATION}</div>
							</td>
							<td align="left" valign="top">
							{if $lessons_list->lesson.information.general_description}<strong>{$smarty.const._GENERALDESCRIPTION}:</strong> {$lessons_list->lesson.information.general_description}<br />{/if}
							{if $lessons_list->lesson.information.assessment}<strong>{$smarty.const._ASSESSMENT}:</strong> {$lessons_list->lesson.information.assessment}<br />{/if}
							{if $lessons_list->lesson.information.objectives}<strong>{$smarty.const._OBJECTIVES}:</strong> {$lessons_list->lesson.information.objectives}<br />{/if}
							{if $lessons_list->lesson.information.lesson_topics}<strong>{$smarty.const._LESSONTOPICS}:</strong> {$lessons_list->lesson.information.lesson_topics}<br />{/if}
							{if $lessons_list->lesson.information.resources}<strong>{$smarty.const._RESOURCES}:</strong> {$lessons_list->lesson.information.resources}<br />{/if}
							{if $lessons_list->lesson.information.other_info}<strong>{$smarty.const._OTHERINFO}:</strong> {$lessons_list->lesson.information.other_info}<br />{/if}
							</td>
							<td align="right" valign="top">{$smarty.const._PRICE}: 
							{if $lessons_list->lesson.price == 0}
								{$smarty.const._FREEOFCHARGE}
							{else}
							{$lessons_list->lesson.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}
							{/if}
							</td>
						</tr>
					</table>
					</td>
				</tr>
			{/if}
		{/foreach}
		</table>
		<br />
	{/capture}
	
	{capture name = 't_list_of_directions'}
		<table border = "0" width = "100%" sortBy = "0">
		<tr class="topTitle"><td class="topTitle">{$smarty.const._DIRECTIONS}</td></tr>
		{foreach name = 'directions_list' key = 'key' item = 'direction' from = $T_DIRECTIONS_DATA}
			{if $direction.active == 1}
				<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
					<td><a href = "{$smarty.server.PHP_SELF}?direction={$direction.id}" class = "editLink">{$direction.name}</a> ({$direction.lessons})</td>
				</tr>
			{/if}
		{/foreach}
		</table><br />
	{/capture}

	{capture name = 't_list_of_courses'}
		<table border = "0" width = "100%" sortBy = "0">
			<tr class="topTitle"><td class="topTitle">{$smarty.const._COURSES}</td></tr>
		</table><br />
	{/capture}

	{capture name = 't_search_form'}
		<form action = "{$smarty.server.PHP_SELF}?fct=searchResults" method = "post">
		<table border = "0" width = "100%">
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
		{$smarty.capture.t_list_of_directions}
		{$smarty.capture.t_list_of_lessons}
		{$smarty.capture.t_list_of_courses}
	{/capture}

	{*MAIN PART*}


	{if $smarty.get.direction || $smarty.get.fct == 'searchResults'}
		{$smarty.capture.t_path}
	{/if}
	
		<table border = "0" width = "100%">
			<tr>
				<td valign="top">

	{if $T_DIRECTIONS_DATA_NUM > 0}
		{$smarty.capture.t_list_of_directions}
	{/if}
	
	{if $smarty.get.fct == 'searchResults'}
		{$smarty.capture.t_search_results}
	{/if}

	{if $smarty.get.direction}
		
		{if $T_DIRECTIONS_LESSONS_NUM > 0}
			{$smarty.capture.t_list_of_lessons}
		{/if}

		{if $smarty.get.lesson}
			<table border = "0" width = "100%">
			<tr class="topTitle"><td class="topTitle" colspan="10">{$T_CURRENT_LESSON}</td></tr>
			</table>
		{/if}	

		</td>
		<td width="1"></td> 
		<td valign="top" width="200">
			{$smarty.capture.t_search_form} <br />
			{$smarty.capture.t_cart_form}
		</td>
		</tr>
	</table>
	{else}
		</td>
		<td width="1"></td> 
		<td valign="top" width="200">
			{$smarty.capture.t_search_form} <br />
			{$smarty.capture.t_cart_form}
		</td>
		</tr>
	</table>
	{/if}

{/capture}

{eF_template_printInnerTable title = $smarty.const._LESSONSDIRECTORY data = $smarty.capture.t_main_capture image = '32x32/cabinet.png'}

{include file = "includes/closing.tpl"}
