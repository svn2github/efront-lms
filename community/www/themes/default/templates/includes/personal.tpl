{* Smarty template for includes/personal.php *}
<script>{if $T_BROWSER == 'IE6'}{assign var='globalImageExtension' value='gif'}var globalImageExtension = 'gif';{else}{assign var='globalImageExtension' value='png'}var globalImageExtension = 'png';{/if}</script>
<script>

 var areYouSureYouWantToCancelConst ='{$smarty.const._AREYOUSUREYOUWANTTOCANCELJOB}';
 var sessionType 					='{$smarty.session.s_type}';
 var editUserLogin					='{$smarty.get.edit_user}';
 var operationCategory				='{$smarty.get.op}';
 var jobAlreadyAssignedConst 		='{$smarty.const._JOBALREADYASSIGNED}';
 var noPlacementsAssigned 			='{$smarty.const._NOPLACEMENTSASSIGNEDYET}';
 var onlyImageFilesAreValid			='{$smarty.const._ONLYIMAGEFILESAREVALID}';
 var areYouSureYouWantToDeleteHist	='{$smarty.const._AREYOUSUREYOUWANTTODELETETHEHISOTYRECORD}';
 var userHasLesson 					='{$smarty.const._USERHASTHELESSON}';
 var serverName						='{$smarty.const.G_SERVERNAME}';
 
 var msieBrowser					='{$smarty.const.MSIE_BROWSER}';
 var sessionLogin					='{$smarty.session.s_login}';
 var clickToChangeStatus			='{$smarty.const._CLICKTOCHANGESTATUS}';
 var youHaventSetAdditionalAccounts	='{$smarty.const._MAPPEDACCOUNTSUCCESSFULLYDELETED}';
 var openFacebookSession			='{$T_OPEN_FACEBOOK_SESSION}';
 var currentOperation				='{$T_OP}';
 
				 	
var jobsRows 			 = new Array();
var branchesValues 		 = new Array();
var jobValues 			 = new Array();
var branchPositionValues = new Array();

var tabberLoadingConst = "{$smarty.const._LOADINGDATA}";
var enableMyJobSelect = false;					
</script>


{************************************************** My Account **********************************************}
{******* contains: my Settings|my Profile, mapped accounts, HCD tabs, my Payments ***************************}
{if $smarty.get.add_user || $T_OP == "account"}

	{*** User settings ***}
	{capture name = 't_personal_data_code'}
		{$T_PERSONAL_DATA_FORM.javascript}
		<form {$T_PERSONAL_DATA_FORM.attributes}>
			{$T_PERSONAL_DATA_FORM.hidden}
			
			{if !(isset($smarty.get.add_user))}
			<fieldset class = "fieldsetSeparator">
			<legend>{$T_TITLES.account.edituser}</legend>
			{/if}
			
			<table class = "formElements" width="90%">

			{* enterprise edition: Insert a second column - new table *}
			{if $smarty.const.G_VERSIONTYPE == 'enterprise'} {* #cpp#ifdef ENTERPRISE *}
				<tr><td>
				<table width = "50%">
			{/if} {* #cpp#endif *}

				{if (isset($smarty.get.add_user))}

					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.new_login.label}:&nbsp;</td>
						<td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.new_login.html}</td></tr>
					 <tr><td></td><td class = "infoCell">{$smarty.const._ONLYALLOWEDCHARACTERSLOGIN}</td></tr>
					{if $T_PERSONAL_DATA_FORM.new_login.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.new_login.error}</td></tr>{/if}
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.password_.label}:&nbsp;</td>
						<td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.password_.html}</td></tr>
					<tr><td></td><td class = "infoCell">{$smarty.const._PASSWORDMUSTBE6CHARACTERS|replace:"%x":$T_CONFIGURATION.password_length}</td></tr>
					{if $T_PERSONAL_DATA_FORM.password_.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.password_.error}</td></tr>{/if}

					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.passrepeat.label}:&nbsp;</td>
						<td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.passrepeat.html}</td></tr>
					{if $T_PERSONAL_DATA_FORM.passrepeat.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.passrepeat.error}</td></tr>{/if}
				{else}
					{if !$T_LDAP_USER}
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.password_.label}:&nbsp;</td>
							<td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.password_.html}</td></tr>
						<tr><td></td><td class = "infoCell">{$smarty.const._PASSWORDMUSTBE6CHARACTERS|replace:"%x":$T_CONFIGURATION.password_length}</td></tr>
						{if $T_PERSONAL_DATA_FORM.password_.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.password_.error}</td></tr>{/if}

						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.passrepeat.label}:&nbsp;</td>
							<td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.passrepeat.html}</td></tr>
						{if $T_PERSONAL_DATA_FORM.passrepeat.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.passrepeat.error}</td></tr>{/if}
					{else}
						<tr><td class = "labelCell">{$smarty.const._PASSWORD}:&nbsp;</td>
							<td style="white-space:nowrap;">{$smarty.const._LDAPUSER}</td></tr>
					{/if}
				{/if}
				<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.name.label}:&nbsp;</td>
					<td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.name.html}</td></tr>
				{if $T_PERSONAL_DATA_FORM.name.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.name.error}</td></tr>{/if}

				<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.surname.label}:&nbsp;</td>
					<td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.surname.html}</td></tr>
				{if $T_PERSONAL_DATA_FORM.surname.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.surname.error}</td></tr>{/if}

				
				{if $smarty.const.G_VERSIONTYPE == 'enterprise'} {* #cpp#ifdef ENTERPRISE *}
					{* enterprise version: Insert fields here so that two columns are about equal *}
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.father.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.father.html}</td></tr>
					{if $T_PERSONAL_DATA_FORM.father.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.father.error}</td></tr>{/if}
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.sex.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.sex.html}</td></tr>
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.marital_status.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.marital_status.html}</td></tr>
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.birthday.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.birthday.html}</td></tr>
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.birthplace.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.birthplace.html}</td></tr>
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.birthcountry.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.birthcountry.html}</td></tr>
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.mother_tongue.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.mother_tongue.html}</td></tr>
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.nationality.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.nationality.html}</td></tr>
					<tr><td colspan=2>&nbsp;</td></tr>

					{if $smarty.get.ctg != 'personal' || $smarty.session.s_type == 'administrator'}
						<script>
						 var branchesHTML					='{$T_PERSONAL_DATA_FORM.branches.html|replace:"\n":""}';
 						 var jobDescriptionsHTML			='{$T_PERSONAL_DATA_FORM.job_descriptions.html|replace:"\n":""}';
 						 var branchPositionHTML				='{$T_PERSONAL_DATA_FORM.branch_position.html|replace:"\n":""}';
 						 var detailsConst					='{$smarty.const._DETAILS}';
 						 var deleteConst					='{$smarty.const._DELETE}'; 
 						 sessionType						='{$smarty.session.s_type}';
 						 editUserLogin						='{$smarty.get.edit_user}';
						</script>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.branches_main.label}:&nbsp;</td>
						<td>
						<table>
							 <tr><td>{$T_PERSONAL_DATA_FORM.branches_main.html}</td>
								 <td align="right"><a id="details_link" name="details_link" {$T_BRANCH_INFO} {if ($T_BRANCH_INFO == "") || ($smarty.get.add_branch == 1 && !isset($smarty.get.add_branch_to))}style="visibility:hidden"{/if}><img src="images/16x16/search.png" title="{$smarty.const._DETAILS}" alt="{$smarty.const.DETAILS}" border="0"></a></td>
							 </tr>
						</table>
						{if $T_PERSONAL_DATA_FORM.all_jobs.html}
							<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.all_jobs.label}:&nbsp;</td><td><span id="jobs_main_span">{$T_PERSONAL_DATA_FORM.all_jobs.html}</span></td></tr>
						{/if}
						
						{if $T_PERSONAL_DATA_FORM.all_supervisors.html}
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.all_supervisors.label}:&nbsp;</td><td><span id="supervisors_main_span">{$T_PERSONAL_DATA_FORM.all_supervisors.html}</span></td></tr>
						{/if}
						
						<script>
							{if isset($my_jobs_html)}
							enableMyJobSelect = true;
							{/if}
						</script>
						
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.placement.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.placement.html}</td></tr>

						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.group.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.group.html}</td></tr>
					{/if}
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.office.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.office.html}</td></tr>
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.company_internal_phone.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.company_internal_phone.html}</td></tr>
				{/if} {* #cpp#endif *}

				<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.email.label}:&nbsp;</td>
					<td>{$T_PERSONAL_DATA_FORM.email.html}</td></tr>
				{if $T_PERSONAL_DATA_FORM.email.error && $smarty.const.G_VERSIONTYPE != 'enterprise'}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.email.error}</td></tr>{/if}

				{if ($smarty.session.s_type == "administrator" || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_CTG != "personal"))}
					{if $smarty.const.G_VERSIONTYPE != 'enterprise'} {* #cpp#ifndef ENTERPRISE *}
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.group.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.group.html}</td></tr>
					{/if} {* #cpp#endif *}
					{* if $T_CURRENTUSERROLEID == 0*}  <!--  Removed in order to allowed to subadmins to change user type	  -->
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.user_type.label}:&nbsp;</td>
						<td>{$T_PERSONAL_DATA_FORM.user_type.html}</td></tr>
						{if $T_PERSONAL_DATA_FORM.user_type.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.user_type.error}</td></tr>{/if}
					{*/if*}
				{/if}
				
				{if $T_PERSONAL_DATA_FORM.languages_NAME.label != ""}
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.languages_NAME.label}:&nbsp;</td>
						<td>{$T_PERSONAL_DATA_FORM.languages_NAME.html}</td></tr>
						{if $T_PERSONAL_DATA_FORM.languages_NAME.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.languages_NAME.error}</td></tr>{/if}
				{/if}

				<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.timezone.label}:&nbsp;</td>
										<td>{$T_PERSONAL_DATA_FORM.timezone.html}</td></tr>							
				
				{if ($smarty.session.s_type == "administrator" || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_CTG != "personal"))}
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.active.label}:&nbsp;</td>
						<td>{$T_PERSONAL_DATA_FORM.active.html}</td></tr>
						{if $T_PERSONAL_DATA_FORM.active.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.active.error}</td></tr>{/if}
				{/if}


				{foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_FIELDS }
					<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.$item.label}:&nbsp;</td>
						<td class = "elementCell">{$T_PERSONAL_DATA_FORM.$item.html}</td></tr>
					{if $T_PERSONAL_DATA_FORM.$item.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.$item.error}</td></tr>{/if}
				{/foreach}
				{foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_DATES }
					<tr><td class = "labelCell">{$item.name}:&nbsp;</td>
						<td class = "elementCell">{eF_template_html_select_date prefix=$item.prefix emptyvalues="1" time=$item.value start_year="-10" end_year="+10" field_order = $T_DATE_FORMATGENERAL}</td></tr>
				{/foreach}


				{if (!isset($smarty.get.add_user))}
				<tr><td class = "labelCell">{$smarty.const._REGISTRATIONDATE}:&nbsp;</td>
					<td>#filter:timestamp-{$T_REGISTRATION_DATE}#</td></tr>
			   {/if}

				{* enterprise version: If no module then submit button here, else insert the second column of data and submit will be inserted later elsewhere *}
				{if $smarty.const.G_VERSIONTYPE != 'enterprise'} {* #cpp#ifndef ENTERPRISE *}
					<tr><td></td><td class = "submitCell" style = "text-align:left">
							 {$T_PERSONAL_DATA_FORM.submit_personal_details.html}</td></tr>
				{else} {* #cpp#else *}
					<tr><td></td><td class = "submitCell">{$T_PERSONAL_DATA_FORM.submit_personal_details.html}</td></tr>
					</table>
				</td><td>
					<table width="50%" align="left">
						<tr><td colspan=2>&nbsp;</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.address.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.address.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.city.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.city.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.country.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.country.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.homephone.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.homephone.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.mobilephone.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.mobilephone.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.hired_on.label}:&nbsp;</td><td>{eF_template_html_select_date prefix="hired_on_" emptyvalues="1" time=$T_EMPLOYEE.hired_on start_year="-45" end_year="+1" field_order = $T_DATE_FORMATGENERAL}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.left_on.label}:&nbsp;</td><td>{eF_template_html_select_date prefix="left_on_" emptyvalues="1" time=$T_EMPLOYEE.left_on start_year="-45" end_year="+1" field_order = $T_DATE_FORMATGENERAL}</td></tr>
						<tr><td colspan=2>&nbsp;</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.employement_type.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.employement_type.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.way_of_working.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.way_of_working.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.work_permission_data.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.work_permission_data.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.police_id_number.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.police_id_number.html}</td></tr>
						<tr><td colspan=2>&nbsp;</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.afm.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.afm.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.doy.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.doy.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.wage.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.wage.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.bank.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.bank.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.bank_account.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.bank_account.html}</td></tr>
						<tr><td colspan=2>&nbsp;</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.driving_licence.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.driving_licence.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.national_service_completed.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.national_service_completed.html}</td></tr>
						<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.transport.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.transport.html}</td></tr>
					</table>
				</td></tr>

				{/if} {* #cpp#endif *}
			</table>
		</form>
		
		{if !(isset($smarty.get.add_user))}
					
			{*** User profile ***}					
			{if (isset($T_PERSONAL_CTG) || ($smarty.session.s_type == "administrator" || $smarty.session.employee_type == $smarty.const._SUPERVISOR) ) && isset($T_SOCIAL_INTERFACE)}
			{/if}
					
			
			<fieldset class = "fieldsetSeparator">
			<legend>{$T_TITLES.account.profile}</legend>
			
			
			{$T_AVATAR_FORM.javascript}
			<form {$T_AVATAR_FORM.attributes}>
				{$T_AVATAR_FORM.hidden}
				
				
				<table class = "formElements">
				
					{if isset($T_SOCIAL_INTERFACE)}
						{if ($smarty.get.personal) || ($smarty.get.edit_user == $smarty.session.s_login)}
							{*@TODO: FILE UPLOAD MISSING HERE*}
						{/if}				
						<tr><td class = "labelCell">{$T_AVATAR_FORM.short_description.label}:&nbsp;</td>
							<td class = "elementCell">{$T_AVATAR_FORM.short_description.html}</td></tr>
						<tr><td colspan = "2">&nbsp;</td></tr>					
					{/if}
					
	
					<tr><td class = "labelCell">{$smarty.const._CURRENTAVATAR}:&nbsp;</td>
						<td class = "elementCell"><img src = "view_file.php?file={$T_AVATAR}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if} /></td></tr>
											
				{if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
					<tr><td class = "labelCell">{$T_AVATAR_FORM.delete_avatar.label}:&nbsp;</td>
						<td class = "elementCell">{$T_AVATAR_FORM.delete_avatar.html}</td></tr>
					<tr><td class = "labelCell">{$T_AVATAR_FORM.file_upload.label}:&nbsp;</td>
						<td class = "elementCell">{$T_AVATAR_FORM.file_upload.html}</td></tr>
					<tr><td class = "labelCell">{$T_AVATAR_FORM.system_avatar.label}:&nbsp;</td>
						<td class = "elementCell">{$T_AVATAR_FORM.system_avatar.html}&nbsp;(<a href = "{$smarty.server.PHP_SELF}?{if $smarty.get.ctg == "users"}ctg=users&edit_user={$smarty.get.edit_user}{else}ctg=personal{/if}&show_avatars_list=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._VIEWLIST}', 2)">{$smarty.const._VIEWLIST}</a>)</td></tr>
					<tr><td colspan = "2">&nbsp;</td></tr>
					<tr><td></td>
						<td class = "elementCell">{$T_AVATAR_FORM.submit_upload_file.html}</td></tr>
				{/if}
				</table>
			</form>
			</fieldset>
		{/if}
	{/capture}
{/if}


{if $T_OP == "account"}
	{*** Mapped accounts ***}
	{if isset($T_ADDITIONAL_ACCOUNTS) && $T_CONFIGURATION.mapped_accounts == 0 || ($T_CONFIGURATION.mapped_accounts == 1 && $T_CURRENT_USER->user.user_type != 'student') || ($T_CONFIGURATION.mapped_accounts == 2 && $T_CURRENT_USER->user.user_type == 'administrator')}
	{capture name = "t_additional_accounts_code"}
		<div class = "headerTools">
			<span>
				<img src = "images/16x16/add.png" alt = "{$smarty.const._ADDACCOUNT}" title = "{$smarty.const._ADDACCOUNT}">
				<a href = "javascript:void(0)" onclick = "$('add_account').show();">{$smarty.const._ADDACCOUNT}</a>
			</span>
		</div>
		<div id = "add_account" style = "display:none">
			{$smarty.const._LOGIN}: <input type = "text" name = "account_login" id = "account_login">
			{$smarty.const._PASSWORD}: <input type = "password" name = "account_password" id = "account_password">
			<img class = "ajaxHandle" src = "images/16x16/success.png" alt = "{$smarty.const._ADD}" title = "{$smarty.const._ADD}" onclick = "addAccount(this)">
			<img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._CANCEL}" title = "{$smarty.const._CANCEL}" onclick = "$('add_account').hide();">
		</div>
		<br/>
		<fieldset class = "fieldsetSeparator">
			<legend>{$smarty.const._ADDITIONALACCOUNTS}</legend>
			<table id = "additional_accounts">
			{foreach name = 'additional_accounts_list' item = "item" key = "key" from = $T_ADDITIONAL_ACCOUNTS}
				<tr><td>{$item}</td>
					<td><img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEACCOUNT}" title = "{$smarty.const._DELETEACCOUNT}" onclick = "deleteAccount(this, '{$item}')"></td>
			{foreachelse}
			<tr id = "empty_accounts"><td class = "emptyCategory">{$smarty.const._YOUHAVENTSETADDITIONALACCOUNTS}</td></tr>
			{/foreach}
			</table>
		</fieldset>
		
		{if $T_FACEBOOK_ENABLED}
		<fieldset class = "fieldsetSeparator" id = "facebook_accounts">
			<legend>{$smarty.const._FACEBOOKMAPPEDACCOUNT}</legend>
			{if $T_FB_ACCOUNT}
			<div>{$T_FB_ACCOUNT.fb_name} <img  style = "vertical-align:middle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEACCOUNT}" title = "{$smarty.const._DELETEACCOUNT}" onclick = "deleteFacebookAccount(this, '{$T_FB_ACCOUNT.users_LOGIN}')"></div>
			{else}
			<div class = "emptyCategory" id = "empty_fb_accounts">{$smarty.const._YOUHAVENTSETFACEBOOKACCOUNT}</div>
			{/if}
		</fieldset>						
		{/if}
		
		<script>
		{if $smarty.get.ctg == 'personal'}var additionalAccountsUrl = '{$smarty.server.PHP_SELF}?ctg=personal';{else}var additionalAccountsUrl = '{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}';{/if}
		</script>
	{/capture}
	{/if}


	{* #cpp#ifndef COMMUNITY *}	
	{if $T_USER_TRANSACTIONS_NUM > 0}
	{*** Payments ***} 
	{capture name = "t_my_payments_code"} 
				<table border = "0" width = "100%" class = "sortedTable" sortBy = "0">
					<tr class = "topTitle">
					<td class = "topTitle" width="25%">{$smarty.const._PAYPALTRANSACTIONCODE}</td>
					<td class = "topTitle" width="20%">{$smarty.const._PAYPALTABLEDATEPAYPAL}</td>
					<td class = "topTitle" width="20%">{$smarty.const._PAYPALTABLEPRICE}</td>
					<td class = "topTitle" width="20%">{$smarty.const._STATUS}</td>
					<td class = "topTitle" width="15%" align="center">{$smarty.const._PAYPALORDERINFO}</td>
					</tr>
					{foreach name = 'user_transactions' key = 'key' item = 'trans' from = $T_USER_TRANSACTIONS}
					<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
					<td>{$trans.txn_id}</td>
					<td>#filter:timestamp_time-{$trans.timestamp_finish}#</td>
					<td>{$trans.mc_gross} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</td>
					<td>{$trans.payment_status}</td>
					<td align="center">
					<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._PAYPALPURCHASEORDER}', 1,
						'payment_view_{$trans.id}')" title = "{$smarty.const._PAYPALORDERINFO}">
						<img src = "images/16x16/information.png" alt = "{$smarty.const._PAYPALORDERINFO}" title = "{$smarty.const._PAYPALORDERINFO}" border = "0"/>
					</a>
					<div id = "payment_view_{$trans.id}" style = "display:none;">
					<table style = "width:100%">
						<tr>
							<td align="left" height="50" width="50%"><b>{$smarty.const._PAYPALPURCHASEORDERFOR}:</b></td><td align="left" height="50">{$trans.business}</td>
						</tr>
						<tr>
							<td colspan="2" align="left" style="background:#D3D3D3 none repeat scroll 0%;"><strong>{$smarty.const._PAYPALORDERINFO}</strong></td>
						</tr>
						<tr>
							<td colspan="2" class="horizontalSeparator"></td>
						</tr>
						<tr>
							<td colspan="2" width=100%>
							<table width=100%>
								<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td align="left" width="50%"><b>{$smarty.const._PAYPALTRANSACTIONCODE}:</b></td><td align="left" width="50%">{$trans.txn_id}</td>
								</tr>
								<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td align="left" width="50%"><b>{$smarty.const._PAYPALTABLEDATEPAYPAL}:</b></td><td align="left" width="50%">#filter:timestamp_time-{$trans.timestamp_finish}#</td>
								</tr>
								<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td align="left" width="50%"><b>{$smarty.const._STATUS}:</b></td><td align="left" width="50%">{$trans.payment_status}</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr><td colspan="2"><p></p></td></tr>
						<tr>
							<td colspan="2" align="left" style="background:#D3D3D3 none repeat scroll 0%;"><strong>{$smarty.const._PAYPALCUSTOMERINFO}</strong></td>
						</tr>
						<tr>
							<td colspan="2" class="horizontalSeparator"></td>
						</tr>
						<tr>
							<td colspan="2" width=100%>
							<table width=100%>
								<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td align="left" width="25%"><b>{$smarty.const._SURNAME}:</b></td><td align="left" width="25%">{$trans.last_name}</td>
									<td align="left" width="25%"><b>{$smarty.const._ADDRESS}:</b></td><td align="left" width="25%">{$trans.address_street}</td>

								</tr>
								<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td align="left" width="25%"><b>{$smarty.const._NAME}:</b></td><td align="left" width="25%">{$trans.first_name}</td>
									<td align="left" width="25%"><b>{$smarty.const._POSTCODE}:</b></td><td align="left" width="25%">{$trans.address_zip}</td>
								</tr>
								<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td align="left" width="25%"><b>{$smarty.const._COUNTRY}:</b></td><td align="left" width="25%">{$trans.address_country}</td>
									<td align="left" width="25%"><b>{$smarty.const._CITY}:</b></td><td align="left" width="25%">{$trans.address_city}</td>
								</tr>
								<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td align="left" width="25%"><b>{$smarty.const._EMAILADDRESS}:</b></td><td align="left" width="75%" colspan="3">{$trans.payer_email}</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr><td colspan="2"><p></p></td></tr>
						<tr>
							<td colspan="2" align="left" style="background:#D3D3D3 none repeat scroll 0%;"><strong>{$smarty.const._PAYPALITEMSINFO}</strong></td>
						</tr>
						<tr>
							<td colspan="2" class="horizontalSeparator"></td>
						</tr>
						<tr>
							<td colspan="2" width=100%>
							<table width=100%>
								<tr>
									<td align="left" width="60%"><b>{$smarty.const._NAME}</b></td>
									<td align="left" width="20%"><b>{$smarty.const._PAYPALITEMCODE}</b></td>
									<td align="left" width="20%"><b>{$smarty.const._PRICE}</b></td>
								</tr>
								<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
									<td align="left">{$trans.item_name}</td>
									<td align="left">{$trans.item_number}</td>
									<td align="left">{$trans.mc_gross} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
					</div>
					</td>
				   </tr>
					   {/foreach}
				</table>
	{/capture}
	{/if} 
	{* #cpp#endif *}

	
	{if $smarty.const.G_VERSIONTYPE == 'enterprise'} {* #cpp#ifdef ENTERPRISE *}
	{************************************************** HCD information **********************************************}
	
		{*** Job descriptions ***}
		{capture name = 't_employee_jobs'}
		
			{* Check permissions for allowing user to assign a new job *}
			{if isset($T_PERSONAL_DATA_FORM.branches) && ($smarty.session.s_type == "administrator" || ($smarty.session.employee_type == $smarty.const._SUPERVISOR && $T_CTG != 'personal'))}
			<table>
				<tr>
					<td><a href="javascript:void(0);" onclick="add_new_job_row({$T_PLACEMENTS_SIZE});"><img src="images/16x16/add.png" title="{$smarty.const._NEWJOBPLACEMENT}" alt="{$smarty.const._NEWJOBPLACEMENT}"/ border="0"></a></td><td><a href="javascript:void(0);" onclick="add_new_job_row({$T_PLACEMENTS_SIZE})">{$smarty.const._NEWJOBPLACEMENT}</a></td>
				</tr>
			</table>
			{/if}

			<table border = "0" width = "100%" class = "sortedTable" id="jobsTable" noFooter="true">
				<tr class = "topTitle">
					<td class = "topTitle">{$smarty.const._BRANCHNAME}</td>
					<td class = "topTitle">{$smarty.const._JOBDESCRIPTION}</td>
					<td class = "topTitle">{$smarty.const._EMPLOYEEPOSITION}</td>
					<td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
				</tr>

			{if !isset($T_PERSONAL_DATA_FORM.branches) && $T_CTG != "personal"}
				<tr>
					<td colspan=4 class = "emptyCategory" id = "noBranches">{$smarty.const._NOBRANCHESHAVEBEENREGISTERED}</td>
				</tr>
			{else}
				{if isset($T_PLACEMENTS)}
				
					{assign var = "jobs_found" value = '1'}
					{foreach name = 'users_list' key = 'key' item = 'placement' from = $T_PLACEMENTS}
					<tr id = "row_{$jobs_found}">

						{if ($T_CTG != "personal" || $smarty.session.s_type == 'administrator')}
							<td><table><tr><td>{$T_PERSONAL_DATA_FORM.branches.html|replace:"row":$jobs_found|replace:"\'":"'"}</td><td align="right"><a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$placement.branch_ID}" id="branches_details_link_{$jobs_found}"><img src="images/16x16/search.png" title="{$smarty.const._DETAILS}" alt="{$smarty.const.DETAILS}" border="0"></a></td></tr></table></td>
							<td><span id = "job_descriptions_{$jobs_found}_span">{$T_PERSONAL_DATA_FORM.job_descriptions.html|replace:"row":$jobs_found|replace:"\'":"'"}</span></td>
							<td>{$T_PERSONAL_DATA_FORM.branch_position.html|replace:"row":$jobs_found|replace:"\'":"'"}</td>
							<td align = "center"><a id="job_{$jobs_found}" href = "javascript:void(0);" onclick="ajaxPostDelJob('{$jobs_found}', this);" class = "deleteLink"><img id="del_img{$jobs_found}" border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a></td>
						{else}
							<td>{$placement.name}</td>
							<td>{$placement.description}</td>
							<td>{if $placement.supervisor == 0} {$smarty.const._EMPLOYEE} {else} {$smarty.const._SUPERVISOR} {/if}</td>
							<td align = "center"><img border = "0" class = "inactiveImage" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a></td>
						{/if}
					</tr>

						{if $T_CTG != "personal" || $smarty.session.s_type == 'administrator'}
							
							<script type = "text/JavaScript">

								jobsRows.push("{$jobs_found}");
								branchesValues.push("{$placement.branch_ID}");								
								jobValues.push("{$placement.description}");
								branchPositionValues.push("{$placement.supervisor}");
							</script>
							
						{/if}
						{math assign="jobs_found" equation="x+1" x=$jobs_found}
					{/foreach}
					{literal}
					<script type = "text/JavaScript">
						document.getElementById('jobsTable').setAttribute('preSelectedJob', {/literal}{$jobs_found}{literal}-1);
					</script>
					{/literal}
				{else}
					 <tr id="no_jobs_found">
						<td colspan=4 class = "emptyCategory">{$smarty.const._NOPLACEMENTSASSIGNEDYET}</td>
					 </tr>
				{/if}

			{/if}
			</table>
			
		{/capture}



		{*** Employee skills ***}
		{capture name = 't_employee_skills'}
				{if $_admin_}
				<div class = "headerTools">
					<span>
						<img src = "images/16x16/add.png" alt = "{$smarty.const._NEWSKILL}" title = "{$smarty.const._NEWSKILL}">
						<a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1" >{$smarty.const._NEWSKILL}</a>
					</span>
				</div>				
				{/if}

<!--ajax:skillsTable-->
				<table style = "width:100%" class = "sortedTable" size = "{$T_SKILLS_SIZE}" sortBy = "0" id = "skillsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}&op={$smarty.get.op}&tabberajax={$T_TABBERAJAX.skills}&">
					<tr class = "topTitle">
						<td class = "topTitle" name="description"   width="25%">{$smarty.const._SKILL}</td>
						<td class = "topTitle" name="category" width="15%">{$smarty.const._CATEGORY}</td>
						<td class = "topTitle" name="specification" width="*">{$smarty.const._SPECIFICATION}</td>
						<td class = "topTitle" name="skill_ID"	  width="10%" align="center">{$smarty.const._CHECK}</td>
					</tr>

			{if isset($T_SKILLS)}
				{foreach name = 'skill_list' key = 'key' item = 'skill' from = $T_SKILLS}
					<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
						<td>
							{if $smarty.session.s_type == "administrator"}
							<a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&edit_skill={$skill.skill_ID}">{$skill.description}</a>
							{else}
							{$skill.description}
							{/if}
						</td>
						<td>{$skill.category}</td>
						<td><input class = "inputText" type="text" name="spec_skill_{$skill.skill_ID}"  id="spec_skill_{$skill.skill_ID}" onchange="ajaxUserPost('skill','{$skill.skill_ID}', this , '{$skill.categories_ID}');" value="{$skill.specification}"{if $skill.users_login != $smarty.get.edit_user} style="width:97%;visibility:hidden"{else}style="width:97%"{/if}></td>
						<td align="center"><input class = "inputCheckBox" type = "checkbox" name = "{$skill.skill_ID}" id = "skill_{$skill.skill_ID}" onclick="javascript:show_hide_spec({$skill.skill_ID}); ajaxUserPost('skill','{$skill.skill_ID}', this, '{$skill.categories_ID}');" {if $skill.users_login == $smarty.get.edit_user} checked {/if} ></td>
					</tr>
				{/foreach}
				</table>
<!--/ajax:skillsTable-->
			{else}
					<tr><td colspan = 3>
						<table width = "100%">
							<tr><td class = "emptyCategory">{$smarty.const._NOSKILLSHAVEBEENREGISTERED}</td></tr>
						</table>
						</td>
					</tr>
				</table>
<!--/ajax:skillsTable-->
			{/if}
		{/capture}


	 	{*** File manager ***}
		{capture name = 't_file_record_code'}
			{$T_FILE_MANAGER}
		{/capture}


		{*** Employee evaluations ***}
		{capture name = 't_employee_evaluations_code'}

		   {if ($T_CTG != "personal")}
			<div class = "headerTools">
				<span>
					<img src = "images/16x16/add.png" alt = "{$smarty.const._NEWEVALUATION}" title = "{$smarty.const._NEWEVALUATION}">
					<a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.edit_user}&add_evaluation=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWEVALUATION}', 1)">{$smarty.const._NEWEVALUATION}</a>
				</span>
			</div>				
		   {/if}

			<table border = "0" width = "100%" class = "sortedTable">
				<tr class = "topTitle">
					<td class = "topTitle" width = "15%">{$smarty.const._DATE}</td>
					<td class = "topTitle" width = "*">{$smarty.const._SUBJECT}</td>
					<td class = "topTitle" width = "10%">{$smarty.const._AUTHOR}</td>
					<td class = "topTitle noSort" width = "10%" align="center">{$smarty.const._OPERATIONS}</td>
				</tr>

			{if isset($T_EVALUATION)}
				{foreach name = 'users_list' key = 'key' item = 'evaluation' from = $T_EVALUATION}
				<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
					<td><span style="display:none">{$evaluation.timestamp}</span>#filter:timestamp_time-{$evaluation.timestamp}#</td>
					<td>{$evaluation.specification}</td>
					<td>{$evaluation.author}</td>
					<td align = "center">
						<table>
							<tr>
							<td width="45%">
								<a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.edit_user}&edit_evaluation={$evaluation.event_ID}&popup=1" target = "POPUP_FRAME" class = "editLink" onclick = "eF_js_showDivPopup('{$smarty.const._NEWEVALUATION}', 1)"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
							</td>
							<td width="45%">
								<a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.edit_user}&delete_evaluation={$evaluation.event_ID}&tab=evaluations" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEEVALUATION}')" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
							</td>
							</tr>
						</table>
					</td>
				</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan=4 class = "emptyCategory">{$smarty.const._NOEVALUATIONSASSIGNEDYET}</td>
				</tr>
			{/if}

			</table>
		{/capture}
						
		{*** Employee history ***}
		{capture name = 't_history_code'}
		
<!--ajax:historyFormTable-->
			<table width="100%" size = "{$T_HISTORY_SIZE}" id = "historyFormTable" sortBy = "0" class = "sortedTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" order="asc" url = "{$smarty.server.PHP_SELF}?{if $T_CTG != 'personal' || $smarty.session.s_type == "administrator"}ctg=users&edit_user={$smarty.get.edit_user}{else}ctg=personal{/if}&history=1&tabberajax=3&">
			<tr class = "topTitle">
				<td class = "topTitle" name="timestamp" width = "15%">{$smarty.const._DATE}</td>
				<td class = "topTitle" name="specification" width = "*">{$smarty.const._MESSAGE}</td>
				{if $smarty.session.s_type == "administrator" || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_CTG != "personal")}
				<td class = "topTitle noSort" width = "10%" align="center">{$smarty.const._OPERATIONS}</td>
				{/if}
			</tr>

			{if isset($T_HISTORY)}
			{foreach name = 'history_list' key = 'key' item = 'history' from = $T_HISTORY}
			<tr class = "{cycle values = "oddRowColor, evenRowColor"}">
				<td><span style="display:none">{$history.timestamp}</span>#filter:timestamp_time-{$history.timestamp}#</td>
				<td>{$history.message}</td>
				{if $smarty.session.s_type == "administrator" || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_CTG != "personal")}
				<td align="center"><a href = "javascript:void(0)" onclick = "deleteHistory(this, '{$history.event_ID}')" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a></td>
				{/if}
			</tr>
			{/foreach}
			{else}
				<tr><td colspan = 5>
			<table width = "100%">
				<tr><td class = "emptyCategory">{$smarty.const._NOHISTORYREGARDINGTHISEMPLOYEE}</td></tr>
			</table>
			</td></tr>
			{/if}
			</table>
<!--/ajax:historyFormTable-->

		{/capture}

	{/if} {* #cpp#endif *}
{/if}



{*---------------------------------- My Status ----------------------------------*}
{*------- contains: my Lessons, my Courses, my Groups, my Certifications -------*}
{if $T_OP == "status"}

	{if ($smarty.session.s_type == "administrator") || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $smarty.session.employee_type == "Supervisor")}
		{assign var = "courses_url" value = "`$smarty.server.PHP_SELF`?ctg=users&edit_user=`$smarty.get.edit_user`&op=`$smarty.get.op`&lessons=1&"}
		{assign var = "_change_handles_" value = $_change_}
	{else}
		{assign var = "courses_url" value = "`$smarty.server.PHP_SELF`?ctg=personal&op=`$smarty.get.op`&lessons=1&"}
		{assign var = "_change_handles_" value = false}
	{/if}

	{capture name = "t_courses_list_code"}
		{include file = "includes/common/courses_list.tpl"}
	{/capture}

	{capture name = "t_lessons_code"}
	{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'lessonsTable'}
<!--ajax:lessonsTable-->
		<table id = "lessonsTable" size = "{$T_TABLE_SIZE}" class = "sortedTable" useAjax = "1" url = "{$courses_url}">
		{$smarty.capture.lessons_list}
		</table>
<!--/ajax:lessonsTable-->	
	{/if}
	{/capture}

	{if $smarty.const.G_VERSIONTYPE != 'community'} {* #cpp#ifndef COMMUNITY *}
	{*** User certificates ***}
	{capture name = 't_users_to_certificates_code'}
  		{if isset($T_USER_TO_CERTIFICATES)}
				<table border = "0" width = "100%" id = "certificatesTable" class = "sortedTable" >
					<tr class = "topTitle">						
						<td class = "topTitle" name = "course_name">{$smarty.const._COURSE}</td>
						<td class = "topTitle centerAlign" name = "score">{$smarty.const._COURSESCORE}</td>
						<td class = "topTitle" name = "certificate_key">{$smarty.const._CERTIFICATEKEY}</td>
						<td class = "topTitle" name = "issue_date">{$smarty.const._CERTIFICATEISSUEDON}</td>
						<td class = "topTitle" name = "expiry_date">{$smarty.const._CERTIFICATEEXPIRESON}</td>
						{if !$T_PERSONAL_CTG}
						<td class = "topTitle centerAlign noSort" >{$smarty.const._FUNCTIONS}</td>
						{/if}
					</tr>

				{foreach name = 'users_to_certificates_list' key = 'key' item = 'certificate' from = $T_USER_TO_CERTIFICATES}
					{strip}
					<tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$certificate.active}deactivatedTableElement{/if}">
						<td>{$certificate.course_name}</td>
						<td align = "center">{$certificate.grade}</td>
						<td>{$certificate.serial_number}</td>
						<td>#filter:timestamp-{$certificate.issue_date}#</td>
						<td>{if !is_numeric($certificate.expiration_date)}{$smarty.const._NEVER}{else}#filter:timestamp-{$certificate.expiration_date}#{/if}</td>
						{if !$T_PERSONAL_CTG}
						<td class = "centerAlign">
							<a href = "{$smarty.server.PHP_SELF}?ctg=courses&op=course_certificates&revoke_certificate={$smarty.get.edit_user}&course={$certificate.courses_ID}" title = "{$smarty.const._REVOKECERTIFICATE}">
								<img src = "images/16x16/certificate.png" title = "{$smarty.const._REVOKECERTIFICATE}" alt = "{$smarty.const._REVOKECERTIFICATE}" border = "0"/>
							</a>&nbsp;
							
							<a href = "{$smarty.server.PHP_SELF}?ctg=courses&op=course_certificates&export=rtf&user={$smarty.get.edit_user}&course={$certificate.courses_ID}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
								<img src = "images/16x16/search.png" title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}" border = "0"/>
							</a>&nbsp;
						</td>
						{/if}
					</tr>
					{/strip}
				{/foreach}
				</table>
	{else}		
		<table width = "100%">
			<tr><td class = "emptyCategory">{$smarty.const._NOCERTIFICATESHAVEBEENISSUEDYET}</td></tr>
		</table>
	{/if}
	{/capture}
	{/if} {* #cpp#endif *}

	{*** User groups ***}
	{capture name = 't_users_to_groups_code'}
  		{if isset($T_USER_TO_GROUP_FORM)}
				<table border = "0" width = "100%" id = "groupsTable" class = "sortedTable" sortBy = "0">
					<tr class = "topTitle">
						<td class = "topTitle" width="30%">{$smarty.const._NAME}</td>
						<td class = "topTitle" width="50%">{$smarty.const._DESCRIPTION}</td>
						<td class = "topTitle centerAlign" width="20%">{$smarty.const._CHECK}</td>
					</tr>

				{foreach name = 'users_to_groups_list' key = 'key' item = 'group' from = $T_USER_TO_GROUP_FORM}
					{strip}
					<tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$group.active}deactivatedTableElement{/if}">
						<td width="30%">{$group.name}</td>
						<td width="50%">{$group.description}</td>
						<td align = "center" width="20%">
							{if ($smarty.get.ctg == "personal" && $smarty.session.s_type != 'administrator') || (isset($T_CURRENT_USER->coreAccess.users) && $T_CURRENT_USER->coreAccess.users != 'change')}
							{if $group.partof == 1}
								<img src = "images/16x16/success.png" alt = "{$smarty.const._PARTOFTHISGROUP}" title = "{$smarty.const._PARTOFTHISGROUP}" />
							{/if}
						{else}
							{if $group.partof == 1}
								<input class = "inputCheckBox" type = "checkbox" id = "group_{$group.id}" name = "{$group.id}" onclick ="ajaxUserPost('group', '{$group.id}', this);" checked>
							{else}
								<input class = "inputCheckBox" type = "checkbox" id = "group_{$group.id}" name = "{$group.id}" onclick ="ajaxUserPost('group', '{$group.id}', this);">
							{/if}
						{/if}
						</td>
					</tr>
					{/strip}
				{/foreach}
				</table>
	{else}		
		<table width = "100%">
			<tr><td class = "emptyCategory">{$smarty.const._NOGROUPSAREDEFINED}</td></tr>
		</table>
	{/if}
	{/capture}



	{* #cpp#ifndef COMMUNITY *}
	{*This is the form that contains the user personal data - to be shown in educational (T_SHOW_USER_FORM==1) and enterprise (T_ENTERPRISE) *}
	{if $smarty.const.G_VERSIONTYPE == 'enterprise' || $T_SHOW_USER_FORM}
	
	{*** User form ***}
	{capture name = 't_personal_form_data_code'}
		{*$T_PERSONAL_DATA_FORM.javascript*}
		<form {$T_PERSONAL_DATA_FORM.attributes}>
			{*$T_PERSONAL_DATA_FORM.hidden*}
			<table style="white-space:nowrap;">
				<tr>
					<td width = "30px">&nbsp</td>
					<td width = "*">
						<table width="100%">
							<tr><td>
								<table width = "100%">
									<tr><td colspan=2 width="300px">&nbsp;</td></tr>
									<tr><td width="35%" align = "center" style="min-width:100px;"><img src = "{if ($T_AVATAR)}view_file.php?file={$T_AVATAR}{else}{$smarty.const.G_SYSTEMAVATARSPATH}unknown_small.{$globalImageExtension}{/if}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" /></td>
										<td width="*">
											<table>
												<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.name.label}:&nbsp;</td><td class="elementFormCell">{$T_USERNAME}</td></tr>
												{if $T_EMPLOYEE.birthday}<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.birthday.label}:&nbsp;</td><td class="elementFormCell">{$T_EMPLOYEE.birthday}</td></tr>{/if}
												{if $T_EMPLOYEE.address}<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.address.label}:&nbsp;</td><td class="elementFormCell">{$T_EMPLOYEE.address}</td></tr>{/if}
												{if $T_EMPLOYEE.city}<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.city.label}:&nbsp;</td><td class="elementFormCell">{$T_EMPLOYEE.city}</td></tr>{/if}
												{if $T_EMPLOYEE.hired_on}<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.hired_on.label}:&nbsp;</td><td class="elementFormCell">#filter:timestamp-{$T_EMPLOYEE.hired_on}#</td></tr>{/if}
												{if $T_EMPLOYEE.left_on}<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.left_on.label}:&nbsp;</td><td class="elementFormCell">#filter:timestamp-{$T_EMPLOYEE.left_on}#</td></tr>{/if}
											</table>
										</td>
									</tr>
								</table>
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>

							{if $smarty.const.G_VERSIONTYPE == 'enterprise'} {* #cpp#ifdef ENTERPRISE *}
							{* Jobs and skills only in enterprise *}

							<tr><td class="labelFormCellTitle">{$smarty.const._PLACEMENTS}</td><td></td></tr>
							<tr><td>
							
							<table width="100%" id = "JobsFormTable" class = "sortedTable" noFooter="true">
							<tr display="style:none"><td class = "labelFormCell noSort" name="name"></td><td class = "elementFormCell noSort" name="description"></td><td class = "elementFormCell noSort" name="supervisor"></td></tr>
							{foreach name = 'placements' item = 'placement' from = $T_FORM_PLACEMENTS}
							<tr><td class = "userFormCellLabel" name="name">{$placement.name}:&nbsp;</td><td name="description">{$placement.description}&nbsp;{if $placement.supervisor}({$smarty.const._SUPERVISOR}){/if}</td><td class="elementFormCell" name="description" width="1%">&nbsp;</td></tr>
							{foreachelse}
							<tr><td colspan=3>{$smarty.const._NOPLACEMENTSASSIGNEDYET}</td></tr>
							{/foreach}
							</table>

								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>

							<tr><td class="labelFormCellTitle">{$smarty.const._EVALUATIONS}</td></tr>
							<tr><td><table width="100%">
								{foreach name = 'evaluation' item = 'evaluation' from = $T_EVALUATIONS}
										<tr><td class = "userFormCellLabel">#filter:timestamp-{$evaluation.timestamp}#:&nbsp;</td><td class = "elementFormCell">{$evaluation.specification}&nbsp;[{$evaluation.surname}&nbsp;{$evaluation.name}]</td></tr>
								{foreachelse}
										<tr><td colspan=3>{$smarty.const._NOEVALUATIONSASSIGNEDYET}</td></tr>
								{/foreach}
									</table>
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>
							<tr><td class="labelFormCellTitle">{$smarty.const._SKILLS}</td></tr>
							{foreach name = 'skill_categories' item = 'skill_category' from = $T_SKILL_CATEGORIES}
							<tr><td>							
<!--ajax:{$skill_category.id}skillFormTable-->
							<table {if $skill_category.size == 0}style="display:none"{/if} width="100%" size = "{$skill_category.size}" id = "{$skill_category.id}skillFormTable" class = "sortedTable" noFooter="true" {if $smarty.get.print != 1}useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}&op={$smarty.get.op}&skills=1&op={$smarty.get.op}&tabberajax={$T_TABBERAJAX.form}&"{/if}>							
								<tr {if $skill_category.size == 0}style="display:none"{/if} ><td class = "labelFormCell noSort" style="font-weight:bold;">{$skill_category.description}</td><td></td></tr>
								<tr {if $skill_category.size == 0}style="display:none"{/if} ><td>

							
									<tr height="1px"><td class = "labelFormCell noSort" name="description"></td><td class = "elementFormCell noSort" name="specification"></td></tr>
									{foreach name = 'skill_$skill_category.id' item = 'skill' from = $skill_category.skills}
									<tr><td class = "userFormCellLabel" name="description">{$skill.description}:</td><td class="elementFormCell" name="specification">&nbsp;{$skill.specification}&nbsp;[{$skill.surname}&nbsp;{$skill.name}]</td></tr>
									{foreachelse}
									<tr><td>{$smarty.const._NOSKILLSASSIGNED}</td></tr>
									{/foreach}

								</td>
							</tr>
							<tr {if $skill_category.size == 0}style="display:none"{/if} ><td>&nbsp;</td></tr>
							</table>
							</td></tr>
<!--/ajax:{$skill_category.id}skillFormTable-->
							{foreachelse}
							<tr><td>{$smarty.const._NOSKILLSHAVEBEENREGISTERED}</td></tr>
							{/foreach}
							{/if}   {******Jobs and skills were only in enterprise********} {* #cpp#endif {**}


							{if !isset($T_NOTRAINING)}
							<tr><td class="labelFormCellTitle">{$smarty.const._TRAININGCAP}</td></tr>
							<tr><td>
									<table>
										<tr><td>&nbsp;</td></tr>
										{foreach name = 'courses_list' item = 'course' from = $T_COURSES}
										<tr><td class="labelFormCellTitle">{$course.name}{if $course.completed}&nbsp;{$course.score}%{/if}</td></tr>
										<tr><td><table width="100%">
											{foreach name = 'lessons_list' item = 'lesson' from = $course.lessons}
													<tr><td class="labelForm" style="font-weight:bold;">{$lesson.name}{if $lesson.completed}&nbsp;{$lesson.score}%{/if}</td></tr>
													<tr><td>{$smarty.const._COMPLETED}:&nbsp;{if $lesson.completed}#filter:timestamp-{$lesson.to_timestamp}#{else}-{/if}</td></tr>
													<tr><td><table width="100%">
																{foreach name = 'tests_list' item = 'test' from = $lesson.tests}
																<tr><td class = "labelFormCell">{$test.name}:</td><td><table><tr><td><table bgcolor = {if $test.score > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$test.score}%</td></tr></table></td></tr></table></td><td>(#filter:timestamp-{$test.timestamp}#)</td><td>{if $test.comments != ''}({$test.comments}){/if}</td></tr>
																{/foreach}
																{if $lesson.tests_count > 0}
																<tr><td><b>{$smarty.const._AVERAGESCORE}:</b></td><td><table><tr><td><table bgcolor = {if $lesson.tests_average > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$lesson.tests_average}%</td></tr></table></td></tr></table></td></tr>
																{/if}
															</table>
														</td>
													</tr>

													<tr><td>&nbsp;</td></tr>
											{/foreach}
												</table>
											</td>
										</tr>
										{/foreach}
								   </table>
								</td>
							</tr>

							<tr><td><table width="100%">
								{foreach name = 'lessons_list' item = 'lesson' from = $T_LESSONS}
										<tr><td class="labelFormCellTitle">{$lesson.name}{if $lesson.completed}&nbsp;{$lesson.score}%{/if}</td></tr>
										<tr><td>{$smarty.const._COMPLETED}:&nbsp;{if $lesson.completed}#filter:timestamp-{$lesson.to_timestamp}#{else}-{/if}</td></tr>
										<tr><td>
												<table>
													{foreach name = 'tests_list' key = 'key' item = 'test' from = $lesson.tests}
													<tr><td class = "labelFormCell">{$test.name}:</td>
														<td>
															<table>
																<tr><td>
																		<table bgcolor = {if $test.score > 60} "#00FF00" {else}"#FF0000"{/if} border="1">
																			<tr><td>{$test.score}%</td></tr>
																		</table>
																	</td></tr>
															</table>
														</td><td>(#filter:timestamp-{$test.timestamp}#)</td>
															 <td>{if $test.comments != ''}({$test.comments}){/if}</td></tr>
													{/foreach}
													{if $lesson.tests_count > 0}
													<tr><td><b>{$smarty.const._AVERAGESCORE}:</b></td>
														<td>
															<table>
																<tr><td>
																		<table bgcolor = {if $lesson.tests_average > 60} "#00FF00" {else}"#FF0000"{/if} border="1">
																			<tr><td>{$lesson.tests_average}%</td></tr>
																		</table>
																	</td></tr>
															</table>
														</td></tr>
													{/if}
												</table>
											</td>
										</tr>
										<tr><td>&nbsp;</td></tr>
								{/foreach}
									</table>
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									<table>
										{foreach name = 'averages_list' item = 'average' from = $T_AVERAGES}
										<tr><td class="labelForm" style="font-weight:bold;">{$average.title}:&nbsp;<td><table bgcolor = {if $average.avg > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$average.avg}%</td></tr></table></td></tr>
										{/foreach}
									</table>
								</td>
							</tr>
							{/if}
						</table>
					</td>
					<td width="30px">&nbsp;</td>
				</tr>
			</table>
		</form>
		</td>
	</tr>
	{/capture}
	{/if}
	{* #cpp#endif *}
{/if}	
	
{if $T_OP == "dashboard"}	
	{if $T_SOCIAL_INTERFACE}
		{capture name = "t_status_change_interface"}
			<table class = "horizontalBlock">
				<tr><td>
			{if $smarty.session.s_type != "administrator"}
						<span class = "rightOption smallHeader">
							<img class = "ajaxHandle" src = "images/32x32/catalog.png" title = "{$smarty.const._MYCOURSES}" alt = "{$smarty.const._MYCOURSES}">
							<a class = "titleLink" href = "{$smarty.server.PHP_SELF}?ctg=lessons" title = "{$smarty.const._MYCOURSES}">{$smarty.const._MYCOURSES}</a>
						</span>
			{else}
						<span class = "rightOption smallHeader">
							<img class = "ajaxHandle" src = "images/32x32/home.png" title = "{$smarty.const._HOME}" alt = "{$smarty.const._HOME}">
							<a class = "titleLink" href = "{$smarty.server.PHP_SELF}?ctg=control_panel" title = "{$smarty.const._HOME}">{$smarty.const._HOME}</a>
						</span>
			{/if}
						<span class = "leftOption">{$T_SIMPLEUSERNAME}&nbsp;</span>
			{if ($smarty.const.G_VERSIONTYPE != 'community')} 	{* #cpp#ifndef COMMUNITY *}
				{if !$T_HIDE_USER_STATUS}
					{if $T_PERSONAL_CTG}
						<span id = "statusText" onclick="javascript:showStatusChange()">
							<i>{if $T_USER_STATUS}"{$T_USER_STATUS}"{else}...{$smarty.const._WRITESOMETHINGABOUTYOURSELF}{/if}</i>
						</span>
						<input class="inputText" id="inputStatusText" style="display:none;" value="{if $T_USER_STATUS}{$T_USER_STATUS}{/if}" onBlur="changeStatus()" onKeypress="checkIfEnter(event)"/>
						<img class = "ajaxHandle" id = "statusTextProgressImg" src = "images/32x32/edit.png" alt = "{$smarty.const._CLICKTOCHANGESTATUS}" title = "{$smarty.const._CLICKTOCHANGESTATUS}" onclick="javascript:showStatusChange()">
					{elseif $T_USER_STATUS}
						<span class = "leftOption"><i>{$T_USER_STATUS}</i></span>
					{/if}					
				{/if}					
			{/if} {* #cpp#endif *}
					</td>
				</tr>
			</table>
		{/capture}
	{/if}
{/if}

{if (isset($smarty.get.add_evaluation) || isset($smarty.get.edit_evaluation))} 

{*** Employee edit evaluations ***}
{capture name = 't_evaluations_code'}
		 {$T_EVALUATIONS_FORM.javascript}
		 <table width = "75%">
			 <tr>
				 <td width="70%">
					  <form {$T_EVALUATIONS_FORM.attributes}>
					  {$T_EVALUATIONS_FORM.hidden}
						  <table class = "formElements">
							  <tr>
								  <td class = "labelCell">{$T_EVALUATIONS_FORM.specification.label}:&nbsp;</td>
								  <td style="white-space:nowrap;">{$T_EVALUATIONS_FORM.specification.html}</td>
							  </tr>
							  {if $T_EVALUATIONS_FORM.specification.error}<tr><td></td><td class = "formError">{$T_EVALUATIONS_FORM.specification.error}</td></tr>{/if}

							  <tr><td colspan = "2">&nbsp;</td></tr>

							  <tr><td></td><td class = "submitCell" style = "text-align:left">
								  {$T_EVALUATIONS_FORM.submit_evaluation_details.html}</td>
							  </tr>

					 </table>
					</form>
				</td>
			</tr>
		</table>
{/capture}
{/if}

{*----------------------------------------- PRESENTATION SETUP ACCORDING TO TYPE OF MANAGEMENT ----------------------------------------------*}
{capture name = 't_user_code'}	
	
	{****** ADD USER PAGE ******}
	{if isset($smarty.get.add_user)}
		{$smarty.capture.t_personal_data_code}
		
		
	{****** PERSONAL MANAGEMENT PAGE ******}	
	{elseif $T_PERSONAL_CTG}
	
		{*** Dashboard ***}
		{if !$T_OP || $T_OP == "dashboard"}

			{include file = "social.tpl"}
			
		{*** Account ***}	
		{elseif $T_OP == "account"}
			<div class="tabber">
				
				<div class="tabbertab" title="{$T_TITLES.account.edituser}">
					{eF_template_printBlock title = $T_TITLES.account.edituser data = $smarty.capture.t_personal_data_code image = '32x32/profile.png'}
				</div>
						
				{if $smarty.const.G_VERSIONTYPE == 'enterprise'} {* #cpp#ifdef ENTERPRISE *}
					{if !$_admin_} {* Admins don't have job descriptions or histories/evaluations *}
						<div class="tabbertab {if ($smarty.get.tab == "placements"  || isset($smarty.post.employee_to_job)) } tabbertabdefault {/if}" title ="{$T_TITLES.account.placements}">
							{eF_template_printBlock title = $T_TITLES.account.placements data = $smarty.capture.t_employee_jobs image = '32x32/organization.png'}
						</div>
					{*
						<div class="tabbertab {if ($smarty.get.tab == "evaluations")} tabbertabdefault {/if}" title="{$T_TITLES.account.history}">
							{eF_template_printBlock title = $T_TITLES.account.history data = $smarty.capture.t_history_code image = '32x32/generic.png'}
						</div>
					 *}   
					{/if}
					
					<div id = "filemanagerTab" class="tabbertab {if ($smarty.get.tab == "file_record"  || isset($smarty.post.t_file_record)) } tabbertabdefault {/if}" title="{$T_TITLES.account.files}">
						{eF_template_printBlock title = $T_TITLES.account.files data = $smarty.capture.t_file_record_code image = '32x32/file_explorer.png'}
					</div>	
				{/if} {* #cpp#endif *}	
				
				{if isset($T_ADDITIONAL_ACCOUNTS) && $T_CONFIGURATION.mapped_accounts == 0 || ($T_CONFIGURATION.mapped_accounts == 1 && $T_CURRENT_USER->user.user_type != 'student') || ($T_CONFIGURATION.mapped_accounts == 2 && $_admin_)}
				<div class="tabbertab" title = "{$T_TITLES.account.mapped}">
					{eF_template_printBlock title = $T_TITLES.account.mapped data = $smarty.capture.t_additional_accounts_code image = '32x32/users.png'}
				</div>
				{/if}
				
				{* #cpp#ifndef COMMUNITY *}
		   		{if $T_USER_TRANSACTIONS_NUM > 0}  
				<div class="tabbertab" title="{$T_TITLES.account.payments}">
					{$smarty.capture.t_my_payments_code}
				</div>
				{/if}
				{* #cpp#endif *}						

			</div>	
			
		{*** Status ***}	
		{elseif $T_OP == "status"}
			<div class="tabber">
			{if !$_admin_}
				{if $T_CONFIGURATION.lesson_enroll}
					{eF_template_printBlock tabber="lessons" title = $T_TITLES.status.lessons data = $smarty.capture.t_lessons_code image = '32x32/lessons.png'}				
				{/if}
				{eF_template_printBlock tabber="courses" title = $T_TITLES.status.courses data = $smarty.capture.t_courses_list_code image = '32x32/courses.png'}
			{/if}
				
				{if isset($T_USER_TO_GROUP_FORM)}
				<div class="tabbertab" title="{$T_TITLES.status.groups}">
					{eF_template_printBlock tabber="groups" title = $T_TITLES.status.groups data = $smarty.capture.t_users_to_groups_code image = '32x32/users.png'}
				</div>
				{/if}				
				
				{if $smarty.const.G_VERSIONTYPE != 'community'} {* #cpp#ifndef COMMUNITY *}
				{if !$_admin_}
				<div class="tabbertab" title="{$T_TITLES.status.certifications}">
					{eF_template_printBlock tabber="certifications" title = $T_TITLES.status.certifications data = $smarty.capture.t_users_to_certificates_code image = '32x32/certificate.png'}										
				</div>				
				{/if}
				{/if} {* #cpp#endif *}
			</div>
		{/if}
		
		
	{****** USER MANAGEMENT BY THIRD PARTIES ******}			
	{else}
	
		{*** Account ***}		
		{if $T_OP == "account"}
		<div class="tabber">
				
			<div class="tabbertab" title="{$T_TITLES.account.edituser}">
				{eF_template_printBlock title = $T_TITLES.account.edituser data = $smarty.capture.t_personal_data_code image = '32x32/profile.png'}
			</div>
			
		
			{if $smarty.const.G_VERSIONTYPE == 'enterprise'} {* #cpp#ifdef ENTERPRISE *}
			
			<div class="tabbertab {if ($smarty.get.tab == "placements"  || isset($smarty.post.employee_to_job)) } tabbertabdefault {/if}" title ="{$T_TITLES.account.placements}">
				{eF_template_printBlock title = $T_TITLES.account.placements data = $smarty.capture.t_employee_jobs image = '32x32/organization.png'}
			</div>
				   
									
			<div class="tabbertab {if ($smarty.get.tab == "skills"  || isset($smarty.post.employee_to_skills)) } tabbertabdefault {/if} useAjax" id="tabbertab{$T_TABBERAJAX.skills}" title="{$smarty.const._SKILLS}">
			
<!--tabberajax:tabbertab{$T_TABBERAJAX.skills}-->
				<h3>Skills</h3>
				{if $smarty.get.tabberajax == $T_TABBERAJAX.skills || ($smarty.get.tab == "skills"  || isset($smarty.post.employee_to_skills))}
					<script>var myform = "employee_to_skills";</script>
					{eF_template_printBlock title = $smarty.const._SKILLS data = $smarty.capture.t_employee_skills image = '32x32/tools.png'}
				{/if}
				
<!--/tabberajax:tabbertab{$T_TABBERAJAX.skills}-->
			 </div>


			<div class="tabbertab {if ($smarty.get.tab == "evaluations")} tabbertabdefault {/if} useAjax" id="tabbertab{$T_TABBERAJAX.history}" title="{$smarty.const._HISTORY}">
			
<!--tabberajax:tabbertab{$T_TABBERAJAX.history}-->
				{if $smarty.get.tabberajax == $T_TABBERAJAX.history || ($smarty.get.tab == "evaluations")}
					{eF_template_printBlock title = $smarty.const._EVALUATIONOFEMPLOYEE|cat:'&nbsp;'|cat:$smarty.get.edit_user data = $smarty.capture.t_employee_evaluations_code image = '32x32/catalog.png'}
					{eF_template_printBlock title = $smarty.const._HISTORYOFEMPLOYEE|cat:'&nbsp;'|cat:$smarty.get.edit_user data = $smarty.capture.t_history_code image = '32x32/catalog.png'}
				{/if}
				
<!--/tabberajax:tabbertab{$T_TABBERAJAX.history}-->
			</div>
		  
			
			<div id = "filemanagerTab" class="tabbertab {if ($smarty.get.tab == "file_record"  || isset($smarty.post.t_file_record)) } tabbertabdefault {/if}" title="{$T_TITLES.account.files}">
				{eF_template_printBlock title = $T_TITLES.account.files data = $smarty.capture.t_file_record_code image = '32x32/file_explorer.png'}
			</div>			  
			{/if} {* #cpp#endif *}						


			{* #cpp#ifndef COMMUNITY *}
	   		{if $T_USER_TRANSACTIONS_NUM > 0}  
			<div class="tabbertab" title="{$T_TITLES.account.payments}">
				{$smarty.capture.t_my_payments_code}
			</div>
			{/if} 
			{* #cpp#endif *}	


		{*** Status ***}		
		{elseif $T_OP == "status"}

			<div class="tabber">
			{if $T_CONFIGURATION.lesson_enroll}
					{eF_template_printBlock tabber="lessons" title = $T_TITLES.status.lessons data = $smarty.capture.t_lessons_code image = '32x32/lessons.png'}				
			{/if}
					{eF_template_printBlock tabber="courses" title = $T_TITLES.status.courses data = $smarty.capture.t_courses_list_code image = '32x32/courses.png'}
				
								
				{if $smarty.const.G_VERSIONTYPE != 'community'} {* #cpp#ifndef COMMUNITY *}
				{if $smarty.const.G_VERSIONTYPE != 'standard'} {* #cpp#ifndef STANDARD *}
				<div class="tabbertab" title="{$T_TITLES.status.certifications}">
					{eF_template_printBlock tabber="certifications" title = $T_TITLES.status.certifications data = $smarty.capture.t_users_to_certificates_code image = '32x32/certificate.png'}										
				</div>
					
				
				{if ($smarty.const.G_VERSIONTYPE == 'enterprise' || $T_SHOW_USER_FORM)}  
				<div class="tabbertab {if ($smarty.get.tab == "plaisio_form") || ($smarty.get.tabberajax == $T_TABBERAJAX.form) } tabbertabdefault {/if} useAjax" id="tabbertab{$T_TABBERAJAX.form}" title="{$smarty.const._EMPLOYEEFORM}">

<!--tabberajax:tabbertab{$T_TABBERAJAX.form}-->

					{if ($smarty.get.tabberajax == $T_TABBERAJAX.form) || ($smarty.get.tab == "plaisio_form")}
					{eF_template_printBlock alt= $T_USERNAME title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
					{/if}

<!--/tabberajax:tabbertab{$T_TABBERAJAX.form}-->

				</div>
				{/if}
				{/if} {* #cpp#endif *}
				{/if} {* #cpp#endif *}
			   
				{if isset($T_USER_TO_GROUP_FORM)}
				<div class="tabbertab" title="{$T_TITLES.status.groups}">
					{eF_template_printBlock tabber="groups" title = $T_TITLES.status.groups data = $smarty.capture.t_users_to_groups_code image = '32x32/users.png'}
				</div>
				{/if}
			</div>		

		{/if}		
	{/if}

{/capture}


{*------------------------------------------------------- ACTUAL PRESENTATION ---------------------------------------------------------------*}
{*** Evaluations popup (maybe this should leave from here) ***}
{if (isset($smarty.get.add_evaluation) || isset($smarty.get.edit_evaluation))} 
	{eF_template_printBlock title = $smarty.const._EVALUATIONOFEMPLOYEE|cat:'&nbsp;'|cat:$smarty.get.edit_user data = $smarty.capture.t_evaluations_code image = '32x32/catalog.png'}

{*** System avatars popup (maybe this should leave from here) ***}	
{elseif $smarty.get.show_avatars_list}
	<table width = "100%" cellpadding = "5" class = "filemanagerBlock"> 
		<tr>{foreach name = "avatars_list" item = "item" key = "key" from = $T_SYSTEM_AVATARS}
				<td align = "center"><a href = "javascript:void(0)" onclick = "parent.document.getElementById('select_avatar').selectedIndex = {$smarty.foreach.avatars_list.index}{if $T_SOCIAL_INTERFACE}+1{/if};parent.document.getElementById('popup_close').onclick();window.close();"><img src = "{$smarty.const.G_SYSTEMAVATARSURL}{$item}" border = "0" / ><br/>{$item}</a></td>
				{if $smarty.foreach.avatars_list.iteration % 4 == 0}</tr><tr>{/if}
			{/foreach}
		</tr>
	</table>	
{else}

{*** The user page appearance ***}
	{if isset($smarty.get.add_user)}
		{eF_template_printBlock title = $smarty.const._NEWUSER data = $smarty.capture.t_user_code image = '32x32/user.png'}
	{elseif $T_PERSONAL_CTG}
			{* Change user status interface *}
			{if $T_SOCIAL_INTERFACE}
			{$smarty.capture.t_status_change_interface}
			{/if}		 
		{eF_template_printBlock title = $smarty.const._PERSONALDATA data = $smarty.capture.t_user_code image = '32x32/profile.png' main_options = $T_TABLE_OPTIONS help = 'Dashboard'}
	{else}
	
		{if $smarty.get.print_preview == 1}
			{eF_template_printBlock alt= $T_USERNAME title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
		{elseif $smarty.get.print == 1}
			{eF_template_printBlock alt= $T_USERNAME title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
			{if $smarty.const.MSIE_BROWSER == 0}
			<script>window.print();</script>
			{/if}
		{else}	
			{eF_template_printBlock title = "`$smarty.const._USEROPTIONSFOR`<span class = 'innerTableName'>&nbsp;&quot;`$T_SIMPLEUSERNAME`&quot;</span>" data = $smarty.capture.t_user_code image = '32x32/profile.png' main_options = $T_TABLE_OPTIONS}
		{/if}
	{/if}
{/if}	