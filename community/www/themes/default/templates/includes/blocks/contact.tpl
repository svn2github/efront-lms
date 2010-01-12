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