{if $smarty.get.add}
	    {capture name = 't_add_code'}
			{$T_ENTITY_FORM.javascript}
			<form {$T_ENTITY_FORM.attributes}>
			    {$T_ENTITY_FORM.hidden}
			    <table class = "formElements">
			        <tr><td class = "labelCell">{$smarty.const._DATE}:&nbsp;</td>
			            <td class = "elementCell">{eF_template_html_select_date prefix="payment_" start_year="-2" end_year="+2" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="payment_" display_seconds = false}</td></tr>
	                <tr><td class = "labelCell">{$smarty.const._USER}:&nbsp;</td>
	                    <td class = "elementCell">
	                        <input type = "text" id = "autocomplete" class = "autoCompleteTextBox" name = "user"/>
	                        <img id = "busy" src = "images/16x16/clock.png" style = "display:none;" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}"/> 
	                        <div id = "autocomplete_users" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
	                    </td>    
	                </tr>
	                <tr><td></td>
	                    <td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td>
	            	</tr>
			        <tr><td class = "labelCell">{$T_ENTITY_FORM.amount.label} ({$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}):&nbsp;</td>
			            <td class = "elementCell">{$T_ENTITY_FORM.amount.html}</td></tr>
			        <tr><td class = "labelCell">{$T_ENTITY_FORM.comments.label}:&nbsp;</td>
			            <td class = "elementCell">{$T_ENTITY_FORM.comments.html}</td></tr>
			        <tr><td></td>
			            <td class = "submitCell">{$T_ENTITY_FORM.submit.html}</td></tr>
			    </table>
			</form>	    	
		{if $T_MESSAGE_TYPE == 'success'}
		    <script>parent.location = parent.location;</script>
		{/if}
		{/capture}
		
		{eF_template_printBlock title = $smarty.const._PAYMENT data = $smarty.capture.t_add_code image = '32x32/shopping_basket_add.png'}

{else}
	{capture name = "t_payments_history_code"}
			<div class = "headerTools">
                <span>
                    <img src = "images/16x16/add.png" title = "{$smarty.const._ADDMANUALPAYMENT}" alt = "{$smarty.const._ADDMANUALPAYMENT}">
                    <a href = "{$smarty.server.PHP_SELF}?ctg=payments&add=1&popup=1" title = "{$smarty.const._ADDMANUALPAYMENT}" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDMANUALPAYMENT}', 2)">{$smarty.const._ADDMANUALPAYMENT}</a>                                                    
                </span>
                <span>
                    <img src = "images/16x16/search.png" title = "{$smarty.const._VIEWTRANSACTIONLOGFILE}" alt = "{$smarty.const._VIEWTRANSACTIONLOGFILE}">
                    <a href = "view_file.php?file={$T_TRANSACTIONS_LOG_FILE}&download=1" title = "{$smarty.const._VIEWTRANSACTIONLOGFILE}" >{$smarty.const._VIEWTRANSACTIONLOGFILE}</a>                                                    
                </span>
            </div>
<!--ajax:paymentsTable-->	
            <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "paymentsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=payments&">
                <tr class = "topTitle defaultRowHeight">
                    <td class = "topTitle">{$smarty.const._DATE}</td>
                    <td class = "topTitle">{$smarty.const._USER}</td>
                    <td class = "topTitle centerAlign">{$smarty.const._AMOUNT}</td>
                    <td class = "topTitle">{$smarty.const._METHOD}</td>
                    <td class = "topTitle">{$smarty.const._STATUS}</td>
                    <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                </tr>
                {foreach name = 'users_list' key = 'key' item = 'payment' from = $T_DATA_SOURCE}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                	<td>#filter:timestamp_time-{$payment.timestamp}#</td>
                	<td>#filter:login-{$payment.users_LOGIN}#</td>
                	<td class = "centerAlign">{$payment.amount|formatPrice}</td>
                	<td>{$T_PAYMENT_METHODS[$payment.method]}</td>
                	<td>{$payment.status}</td>
                	<td class = "centerAlign">
                		<div style = "display:none" id = "details_div_{$payment.id}">
                			{eF_template_printBlock title = $smarty.const._DETAILS data = $payment.comments image='32x32/information.png'}
                		</div>
                		<img class = "handle" src = "images/16x16/information.png" title = "{$smarty.const._DETAILS}" alt = "{$smarty.const._DETAILS}" onclick = "eF_js_showDivPopup('{$smarty.const._DETAILS}', 1, 'details_div_{$payment.id}')"/>
                		<img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteEntity(this, '{$payment.id}')"/>
					</td>
                </tr>
				{foreachelse}
                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
				{/foreach}
			</table>
<!--/ajax:paymentsTable-->			
	{/capture}

{*
	{capture name = "t_successfull_transactions_code"}
            <table style = "width:100%" class = "sortedTable">
                <tr class = "topTitle defaultRowHeight">
                    <td class = "topTitle">{$smarty.const._PAYPALTABLEUSER}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLELESSONS}</td>
                    <td class = "topTitle centerAlign">{$smarty.const._PAYPALTABLEPRICE}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLEDATESUBMIT}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLEDATEPAYPAL}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLESTATUS}</td>
                    <td class = "topTitle centerAlign">{$smarty.const._FUNCTIONS}</td>
                </tr>
                {foreach name = 'users_list' key = 'key' item = 'orders' from = $T_PAYPALDATA_S}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>{$orders.user}</td>
                    <td>{$orders.item_name}</td>
                    <td class = "centerAlign">{$orders.mc_gross} {$T_CURRENCYSYMBOLS[$orders.mc_currency]}</td>
                    <td>#filter:timestamp_time-{$orders.timestamp}#</td>
                    <td>#filter:timestamp_time-{$orders.timestamp_finish}#</td>
                    <td>{$orders.payment_status}</td>
                    <td class = "centerAlign">
                        <img class = "ajaxHandle" src = "images/16x16/information.png" alt = "{$smarty.const._DESCRIPTION}" title = "{$smarty.const._DESCRIPTION}" onclick = "eF_js_showDivPopup('{$smarty.const._PAYPALORDERINFO}', 1,'payment_view_{$orders.id}')"/>
                        <div id = "payment_view_{$orders.id}" style = "display:none;">
	                        <table>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>id:</b></td>
									<td>{$orders.id}&nbsp;</td>
									<td><b>mc_gross:</b></td>
									<td>{$orders.mc_gross}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>settle_amount:</b></td>
									<td>{$orders.settle_amount}&nbsp;</td>
									<td><b>address_status:</b></td>
									<td>{$orders.address_status}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>payer_id:</b></td>
									<td>{$orders.payer_id}&nbsp;</td>
									<td><b>tax:</b></td>
									<td>{$orders.tax}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>address_street:</b></td>
									<td>{$orders.address_street}&nbsp;</td>
									<td><b>payment_date:</b></td>
									<td>{$orders.payment_date}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>payment_status:</b></td>
									<td>{$orders.payment_status}&nbsp;</td>
									<td><b>charset:</b></td>
									<td>{$orders.charset}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>address_zip:</b></td>
									<td>{$orders.address_zip}&nbsp;</td>
									<td><b>first_name:</b></td>
									<td>{$orders.first_name}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>mc_fee:</b></td>
									<td>{$orders.mc_fee}&nbsp;</td>
									<td><b>address_country_code:</b></td>
									<td>{$orders.address_country_code}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>exchange_rate:</b></td>
									<td>{$orders.exchange_rate}&nbsp;</td>
									<td><b>address_name:</b></td>
									<td>{$orders.address_name}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>notify_version:</b></td>
									<td>{$orders.notify_version}&nbsp;</td>
									<td><b>settle_currency:</b></td>
									<td>{$orders.settle_currency}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>custom:</b></td>
									<td>{$orders.custom}</td>
									<td><b>payer_status:</b></td>
									<td>{$orders.payer_status}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>business:</b></td>
									<td>{$orders.business}&nbsp;</td>
									<td><b>address_country:</b></td>
									<td>{$orders.address_country}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>address_city:</b></td>
									<td>{$orders.address_city}&nbsp;</td>
									<td><b>quantity:</b></td>
									<td>{$orders.quantity}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>verify_sign:</b></td>
									<td>{$orders.verify_sign}&nbsp;</td>
									<td><b>payer_email:</b></td>
									<td>{$orders.payer_email}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>txn_id:</b></td>
									<td>{$orders.txn_id}&nbsp;</td>
									<td><b>payment_type:</b></td>
									<td>{$orders.payment_type}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>last_name:</b></td>
									<td>{$orders.last_name}&nbsp;</td>
									<td><b>address_state:</b></td>
									<td>{$orders.address_state}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>receiver_email:</b></td>
									<td>{$orders.receiver_email}&nbsp;</td>
									<td><b>payment_fee:</b></td>
									<td>{$orders.payment_fee}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>receiver_id:</b></td>
									<td>{$orders.receiver_id}&nbsp;</td>
									<td><b>txn_type:</b></td>
									<td>{$orders.txn_type}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>item_name:</b></td>
									<td>{$orders.item_name}&nbsp;</td>
									<td><b>mc_currency:</b></td>
									<td>{$orders.mc_currency}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>item_number:</b></td>
									<td>{$orders.item_number}&nbsp;</td>
									<td><b>residence_country:</b></td>
									<td>{$orders.residence_country}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>test_ipn:</b></td>
									<td>{$orders.test_ipn}&nbsp;</td>
									<td><b>payment_gross:</b></td>
									<td>{$orders.payment_gross}</td></tr>
	                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td><b>shipping:</b></td>
									<td>{$orders.shipping}&nbsp;</td>
									<td><b>status:</b></td>
									<td>{$orders.status}</td></tr>
	                        </table>
                        </div>
                    </td>
                </tr>
                {foreachelse}
                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                {/foreach}
            </table>
	{/capture}
*}
{*	    
	{capture name = "t_unsuccessfull_transactions_code"}
            <table style = "width:100%" class = "sortedTable">
                <tr class = "topTitle defaultRowHeight">
                    <td class = "topTitle">{$smarty.const._PAYPALTABLEUSER}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLELESSONS}</td>
                    <td class = "topTitle centerAlign">{$smarty.const._PAYPALTABLEPRICE}</td>
                    <td class = "topTitle">{$smarty.const._TYPE}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLEDATESUBMIT}</td>
                    <td class = "topTitle">{$smarty.const._STATUS}</td>
                    <td class = "topTitle centerAlign">{$smarty.const._FUNCTIONS}</td>
                </tr>
                {foreach name = 'users_list' key = 'key' item = 'orders' from = $T_PAYPALDATA_NS}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>{$orders.user}</td>
                    <td>{$orders.item_name}</td>
                    <td align="center">{$orders.mc_gross} {$T_CURRENCYSYMBOLS[$orders.mc_currency]}</td>
                    <td>{$orders.txn_type}</td>
                    <td>#filter:timestamp_time-{$orders.timestamp}#</td>
                    <td>{$orders.status}</td>
                    <td class = "centerAlign">
                        <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._PAYPALORDERINFO}', 1,
                            'payment_view_{$orders.id}')" title = "{$smarty.const._DESCRIPTION}">
                            <img src = "images/16x16/information.png" alt = "{$smarty.const._DESCRIPTION}" title = "{$smarty.const._DESCRIPTION}" border = "0"/>
                        </a>
                        <div id = "payment_view_{$orders.id}" style = "display:none;">
                        <table style = "width:100%">
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>id:</b></td>
								<td>{$orders.id}&nbsp;</td>
								<td><b>mc_gross:</b></td>
								<td>{$orders.mc_gross}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>settle_amount:</b></td>
								<td>{$orders.settle_amount}&nbsp;</td>
								<td><b>address_status:</b></td>
								<td>{$orders.address_status}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>payer_id:</b></td>
								<td>{$orders.payer_id}&nbsp;</td>
								<td><b>tax:</b></td>
								<td>{$orders.tax}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>address_street:</b></td>
								<td>{$orders.address_street}&nbsp;</td>
								<td><b>payment_date:</b></td>
								<td>{$orders.payment_date}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>payment_status:</b></td>
								<td>{$orders.payment_status}&nbsp;</td>
								<td><b>charset:</b></td>
								<td>{$orders.charset}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>address_zip:</b></td>
								<td>{$orders.address_zip}&nbsp;</td>
								<td><b>first_name:</b></td>
								<td>{$orders.first_name}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>mc_fee:</b></td>
								<td>{$orders.mc_fee}&nbsp;</td>
								<td><b>address_country_code:</b></td>
								<td>{$orders.address_country_code}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>exchange_rate:</b></td>
								<td>{$orders.exchange_rate}&nbsp;</td>
								<td><b>address_name:</b></td>
								<td>{$orders.address_name}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>notify_version:</b></td>
								<td>{$orders.notify_version}&nbsp;</td>
								<td><b>settle_currency:</b></td>
								<td>{$orders.settle_currency}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>custom:</b></td>
								<td>{$orders.custom}</td>
								<td><b>payer_status:</b></td>
								<td>{$orders.payer_status}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>business:</b></td>
								<td>{$orders.business}&nbsp;</td>
								<td><b>address_country:</b></td>
								<td>{$orders.address_country}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>address_city:</b></td>
								<td>{$orders.address_city}&nbsp;</td>
								<td><b>quantity:</b></td>
								<td>{$orders.quantity}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>verify_sign:</b></td>
								<td>{$orders.verify_sign}&nbsp;</td>
								<td><b>payer_email:</b></td>
								<td>{$orders.payer_email}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>txn_id:</b></td>
								<td>{$orders.txn_id}&nbsp;</td>
								<td><b>payment_type:</b></td>
								<td>{$orders.payment_type}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>last_name:</b></td>
								<td>{$orders.last_name}&nbsp;</td>
								<td><b>address_state:</b></td>
								<td>{$orders.address_state}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>receiver_email:</b></td>
								<td>{$orders.receiver_email}&nbsp;</td>
								<td><b>payment_fee:</b></td>
								<td>{$orders.payment_fee}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>receiver_id:</b></td>
								<td>{$orders.receiver_id}&nbsp;</td>
								<td><b>txn_type:</b></td>
								<td>{$orders.txn_type}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>item_name:</b></td>
								<td>{$orders.item_name}&nbsp;</td>
								<td><b>mc_currency:</b></td>
								<td>{$orders.mc_currency}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>item_number:</b></td>
								<td>{$orders.item_number}&nbsp;</td>
								<td><b>residence_country:</b></td>
								<td>{$orders.residence_country}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>test_ipn:</b></td>
								<td>{$orders.test_ipn}&nbsp;</td>
								<td><b>payment_gross:</b></td>
								<td>{$orders.payment_gross}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
								<td><b>shipping:</b></td>
								<td>{$orders.shipping}&nbsp;</td>
								<td><b>status:</b></td>
								<td>{$orders.status}</td></tr>
                        </table>
                        </div>
                    </td>
                </tr>
                {foreachelse}
                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                {/foreach}
            </table>
	{/capture}    
*}
{*	
	{capture name = 't_paypal_data'}
		<div class = "tabber">
	        <div class = "tabbertab">
	            <h3>{$smarty.const._COMPLETED}</h3>
				{$smarty.capture.t_successfull_transactions_code}
	        </div>
	        <div class="tabbertab">
	            <h3>{$smarty.const._PENDING}</h3>
				{$smarty.capture.t_unsuccessfull_transactions_code}
	        </div>
	    </div>
	{/capture}	
*}
	{capture name = 't_payment_accounts_code'}
<!--ajax:usersTable-->	
            <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=payments&">
                <tr class = "topTitle defaultRowHeight">
                    <td class = "topTitle">{$smarty.const._USER}</td>
                    <td class = "topTitle centerAlign">{$smarty.const._BALANCE}</td>
                </tr>
                {foreach name = 'users_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                	<td>{eF_template_printUserName name = $user.name surname = $user.surname login = $user.login}</td>
                	<td class = "centerAlign">
                		<span style = "display:none">
                			<input type = "text" value = "{$user.balance}">
	                		<img class = "handle" src = "images/16x16/success.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}" onclick = "editBalance(this, '{$user.login}')"/>
	                		<img class = "handle" src = "images/16x16/error_delete.png" title = "{$smarty.const._CANCEL}" alt = "{$smarty.const._CANCEL}" onclick = "Element.extend(this).up().hide();this.up().next().show();"/>
                		</span>
                		<span>                		
                			<span>{$user.balance|formatPrice}</span>                		
		                	<img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" onclick = "Element.extend(this).up().hide();this.up().previous().show();"/>
                		</span>                		
                	</td>
                </tr>
				{foreachelse}
                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "5">{$smarty.const._NODATAFOUND}</td></tr>
				{/foreach}
			</table>
<!--/ajax:usersTable-->				
	{/capture}
	
	{capture name = 't_payment_settings_code'}
        {$T_CONFIG_FORM_DEFAULT.javascript}
        <form {$T_CONFIG_FORM_DEFAULT.attributes}>
            {$T_CONFIG_FORM_DEFAULT.hidden}
            <table class = "formElements" align="center">
                <tr><td class = "labelCell">{$smarty.const._CURRENCY}:&nbsp;</td>
                    <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.currency.html}</td></tr>
                <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.currency_order.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.currency_order.html}</td></tr>
                <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.enable_balance.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.enable_balance.html}</td></tr>
                <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.voucher.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.voucher.html} <img src = "images/16x16/wizard.png" alt = "{$smarty.const._CREATEVOUCHER}" title = "{$smarty.const._CREATEVOUCHER}" onclick = "Element.extend(this).previous().value = '{$T_NEW_UNIQUE_KEY}'" /></td></tr>
                <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.voucher_discount.label}  (%):&nbsp;</td>
                    <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.voucher_discount.html}</td></tr>
                <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.total_discount.label} (%):&nbsp;</td>
                    <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.total_discount.html}</td></tr>
                <tr><td class = "labelCell">{$smarty.const._DISCOUNTSTARTSAT}:&nbsp;</td>
                    <td class = "elementCell">{eF_template_html_select_date prefix="discount_" time = $T_CONFIGURATION.discount_start start_year="-2" end_year="+2" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._ANDLASTS} {$T_CONFIG_FORM_DEFAULT.discount_period.html} {$smarty.const._DAYS}</td></tr>
                <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.paypalbusiness.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.paypalbusiness.html}</td></tr>
                <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.paypalmode.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.paypalmode.html}</td></tr>
                <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.paypaldebug.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.paypaldebug.html}</td></tr>
                <tr><td></td>
                    <td class = "submitCell">{$T_CONFIG_FORM_DEFAULT.submit_config.html}</td>
                </tr>
            </table>
        </form>
	
	{/capture}
	
	{capture name = 't_payments_code'}
	<div class = "tabber">
        <div class = "tabbertab" title = "{$smarty.const._HISTORY}">
			{eF_template_printBlock title = $smarty.const._HISTORY data = $smarty.capture.t_payments_history_code image='32x32/generic.png'}
        </div>
        <div class = "tabbertab {if $smarty.get.tab == 'balance'}tabbertabdefault{/if}" title = "{$smarty.const._BALANCE}">
			{eF_template_printBlock title = $smarty.const._BALANCE data = $smarty.capture.t_payment_accounts_code image='32x32/users.png'}
        </div>
{*
        <div class = "tabbertab {if $smarty.get.tab == 'paypal'}tabbertabdefault{/if}" title = "{$smarty.const._PAYPAL}">
			{eF_template_printBlock title = $smarty.const._PAYPALTITLE data = $smarty.capture.t_paypal_data image='32x32/paypal.png'}
        </div>
*}
        <div class = "tabbertab {if $smarty.get.tab == 'settings'}tabbertabdefault{/if}" title = "{$smarty.const._SETTINGS}">
			{eF_template_printBlock title = $smarty.const._SETTINGS data = $smarty.capture.t_payment_settings_code image='32x32/settings.png'}
        </div>
    </div>
    {/capture}
    {eF_template_printBlock title = $smarty.const._PAYMENTS data = $smarty.capture.t_payments_code image='32x32/shopping_basket.png'}
{/if}

