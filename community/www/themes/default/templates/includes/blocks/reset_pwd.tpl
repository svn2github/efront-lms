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
