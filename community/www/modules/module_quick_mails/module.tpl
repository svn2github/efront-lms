{*Smarty template*}
{if $T_MESSAGE_MAIL_TYPE == 'success' || $T_MESSAGE_MAIL_TYPE == 'failure'}
    <script>
      parent.location = parent.location+'&message={$T_MESSAGE_MAIL}&message_type={$T_MESSAGE_MAIL_TYPE}';
	  window.close();            
    </script>
{/if}

{capture name = 't_module_email_code}
<div id="module_mail_main" style = "width:100%">
{$T_MODULE_MAIL_FORM.javascript}
<form {$T_MODULE_MAIL_FORM.attributes}">
{$T_MODULE_MAIL_FORM.hidden}
	<table class = "formElements" width="100%">
    	 <tr><td class = "labelCell">{$smarty.const._SUBJECT}:&nbsp;</td>
                        <td class = "elementCell">{$T_MODULE_MAIL_FORM.subject.html}</td>
		</tr>
		<tr><td class = "labelCell">{$T_MODULE_MAIL_FORM.email.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_MODULE_MAIL_FORM.email.html}</td>
		</tr>
        <tr><td class = "labelCell">{$smarty.const._BODY}:&nbsp;</td>
            <td class = "elementCell">{$T_MODULE_MAIL_FORM.body.html}</td></tr>
                        {if $T_MODULE_MAIL_FORM.body.error}<tr><td></td><td class = "formError">{$T_MODULE_MAIL_FORM.body.error}</td></tr>{/if}
		 <tr><td class = "labelCell">{$smarty.const._ATTACHMENTS}:&nbsp;</td>
                        <td class = "elementCell">{$T_MODULE_MAIL_FORM.attachment.0.html}</td></tr>
                        {if $T_MODULE_MAIL_FORM.attachment.0.error}<tr><td></td><td class = "formError">{$T_MODULE_MAIL_FORM.attachment.0.error}</td></tr>{/if}   
		 <tr><td colspan="2">&nbsp;</td></tr>
		 <tr><td></td>
            <td class = "elementCell">{$T_MODULE_MAIL_FORM.submit_mail.html}</td></tr>
</form>
</div>
{/capture}


{eF_template_printBlock title=$smarty.const._MAILS_MODULEMAILS data=$smarty.capture.t_module_email_code image='32x32/mail.png'}